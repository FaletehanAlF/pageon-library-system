<?php

declare(strict_types=1);

final class Category extends Model
{
    protected string $table = 'categories';
    protected array $allowedOrderColumns = ['id', 'created_at', 'name'];

    public function getAll(): array
    {
        return $this->findAll([], 'name ASC');
    }

    public function getWithBookCount(): array
    {
        $sql = "
            SELECT
                categories.*,
                COUNT(books.id) AS book_count
            FROM categories
            LEFT JOIN books ON categories.id = books.category_id
            GROUP BY categories.id, categories.name, categories.created_at
            ORDER BY categories.name ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isDeletable(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM books WHERE category_id = ? LIMIT 1');
        $stmt->execute([$id]);

        return $stmt->fetchColumn() === false;
    }
}
