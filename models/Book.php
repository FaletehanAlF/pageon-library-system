<?php

declare(strict_types=1);

final class Book extends Model
{
    protected string $table = 'books';
    protected array $allowedOrderColumns = ['id', 'created_at', 'title', 'author', 'stock', 'year'];

    public function getLatestBooks(int $limit = 5): array
    {
        $limit = max(1, min(50, $limit));

        $sql = "
            SELECT
                books.*,
                categories.name AS category_name
            FROM books
            LEFT JOIN categories ON books.category_id = categories.id
            ORDER BY books.created_at DESC
            LIMIT {$limit}
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWithCategory(int $id): ?array
    {
        $sql = "
            SELECT
                books.*,
                categories.name AS category_name
            FROM books
            LEFT JOIN categories ON books.category_id = categories.id
            WHERE books.id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * @param array<string,mixed> $conditions  Supports "books.stock" => value syntax for disambiguation
     */
    public function getAllWithCategory(array $conditions = [], string $orderBy = 'books.created_at DESC'): array
    {
        $orderBy = $this->sanitizeOrderBy($orderBy, 'books.created_at DESC');

        $sql = "
            SELECT
                books.*,
                categories.name AS category_name
            FROM books
            LEFT JOIN categories ON books.category_id = categories.id
        ";
        $params = [];

        if ($conditions !== []) {
            $where = [];
            foreach ($conditions as $column => $value) {
                // Allow qualified column names; otherwise prefix with books.
                if (str_contains($column, '.')) {
                    $where[] = "{$column} = ?";
                } else {
                    $where[] = "books.`{$column}` = ?";
                }
                $params[] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= " ORDER BY {$orderBy}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $this->getAllWithCategory();
        }

        // Limit length to prevent abuse
        $keyword = mb_substr($keyword, 0, 100);

        $sql = "
            SELECT
                books.*,
                categories.name AS category_name
            FROM books
            LEFT JOIN categories ON books.category_id = categories.id
            WHERE books.title LIKE ? OR books.author LIKE ? OR categories.name LIKE ?
            ORDER BY books.created_at DESC
        ";

        $like = "%{$keyword}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$like, $like, $like]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existsByCategory(int $categoryId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM books WHERE category_id = ? LIMIT 1');
        $stmt->execute([$categoryId]);

        return (bool) $stmt->fetchColumn();
    }

    public function hasActiveBorrowings(int $bookId): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM borrowings WHERE book_id = ? AND status = 'borrowed' LIMIT 1");
        $stmt->execute([$bookId]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Atomically decrement stock only if stock > 0.
     */
    public function decrementStock(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET stock = stock - 1 WHERE id = ? AND stock > 0');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function incrementStock(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET stock = stock + 1 WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * @deprecated Use decrementStock / incrementStock instead.
     */
    public function updateStock(int $id, int $change): bool
    {
        if ($change === -1) {
            return $this->decrementStock($id);
        }
        if ($change === 1) {
            return $this->incrementStock($id);
        }

        $stmt = $this->db->prepare('UPDATE books SET stock = stock + ? WHERE id = ? AND (stock + ? >= 0)');
        return $stmt->execute([$change, $id, $change]);
    }

    public function getTotalBooks(): int
    {
        return $this->count();
    }

    /**
     * Advanced filtered search with pagination.
     * $filters: q, category_id, availability (available|empty), sort
     * @return array{data: array, total: int}
     */
    public function paginate(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(48, $perPage));

        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $q = mb_substr($q, 0, 100);
            $like = "%{$q}%";
            $where[] = '(books.title LIKE ? OR books.author LIKE ? OR books.publisher LIKE ? OR categories.name LIKE ?)';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $catId = (int) ($filters['category_id'] ?? 0);
        if ($catId > 0) {
            $where[] = 'books.category_id = ?';
            $params[] = $catId;
        }

        $avail = (string) ($filters['availability'] ?? '');
        if ($avail === 'available') {
            $where[] = 'books.stock > 0';
        } elseif ($avail === 'empty') {
            $where[] = 'books.stock <= 0';
        }

        $sortMap = [
            'newest' => 'books.created_at DESC',
            'oldest' => 'books.created_at ASC',
            'title_asc' => 'books.title ASC',
            'title_desc' => 'books.title DESC',
        ];
        $orderBy = $sortMap[$filters['sort'] ?? ''] ?? 'books.created_at DESC';
        $orderBy = $this->sanitizeOrderBy($orderBy, 'books.created_at DESC');

        $whereSql = $where !== [] ? ' WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) FROM books LEFT JOIN categories ON books.category_id = categories.id' . $whereSql;
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = "
            SELECT books.*, categories.name AS category_name
            FROM books
            LEFT JOIN categories ON books.category_id = categories.id
            {$whereSql}
            ORDER BY {$orderBy}
            LIMIT {$perPage} OFFSET {$offset}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['data' => $data, 'total' => $total];
    }

    public function getPopular(int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));
        $sql = "
            SELECT books.*, categories.name AS category_name, COUNT(borrowings.id) AS borrow_count
            FROM books
            LEFT JOIN categories ON books.category_id = categories.id
            LEFT JOIN borrowings ON borrowings.book_id = books.id
            GROUP BY books.id
            ORDER BY borrow_count DESC, books.created_at DESC
            LIMIT {$limit}
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
