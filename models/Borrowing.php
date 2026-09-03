<?php

declare(strict_types=1);

final class Borrowing extends Model
{
    protected string $table = 'borrowings';
    protected array $allowedOrderColumns = ['id', 'created_at', 'borrow_date', 'due_date', 'status'];

    /**
     * @param array<string,mixed> $conditions  Keys may be qualified (e.g. "borrowings.status")
     */
    public function getAllWithDetails(array $conditions = []): array
    {
        $sql = "
            SELECT
                borrowings.*,
                users.name  AS user_name,
                users.email AS user_email,
                books.title  AS book_title,
                books.author AS book_author
            FROM borrowings
            INNER JOIN users ON borrowings.user_id = users.id
            INNER JOIN books ON borrowings.book_id = books.id
        ";
        $params = [];

        if ($conditions !== []) {
            $where = [];
            foreach ($conditions as $column => $value) {
                // Qualify unqualified columns with borrowings.
                if (!str_contains($column, '.')) {
                    $column = "borrowings.`{$column}`";
                }
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY borrowings.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserBorrowings(int $userId): array
    {
        return $this->getAllWithDetails(['borrowings.user_id' => $userId]);
    }

    public function getActiveBorrowings(): array
    {
        return $this->getAllWithDetails(['borrowings.status' => 'borrowed']);
    }

    public function getOverdueBorrowings(): array
    {
        $sql = "
            SELECT
                borrowings.*,
                users.name  AS user_name,
                users.email AS user_email,
                books.title  AS book_title,
                books.author AS book_author
            FROM borrowings
            INNER JOIN users ON borrowings.user_id = users.id
            INNER JOIN books ON borrowings.book_id = books.id
            WHERE borrowings.status = 'borrowed' AND borrowings.due_date < CURDATE()
            ORDER BY borrowings.due_date ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasActiveBorrowing(int $userId, int $bookId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM `{$this->table}` WHERE user_id = ? AND book_id = ? AND status = 'borrowed' LIMIT 1"
        );
        $stmt->execute([$userId, $bookId]);

        return (bool) $stmt->fetchColumn();
    }

    public function returnBook(int $borrowingId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET status = 'returned', return_date = CURDATE() WHERE id = ? AND status = 'borrowed'"
        );
        $stmt->execute([$borrowingId]);

        return $stmt->rowCount() > 0;
    }

    public function getTotalBorrowed(): int
    {
        return $this->count(['status' => 'borrowed']);
    }

    public function getTotalReturned(): int
    {
        return $this->count(['status' => 'returned']);
    }

    public function getTotalOverdue(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM `{$this->table}` WHERE status = 'borrowed' AND due_date < CURDATE()");
        return (int) $stmt->fetchColumn();
    }

    public function countActiveByUser(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` WHERE user_id = ? AND status = 'borrowed'");
        $stmt->execute([$userId]);

        return (int) $stmt->fetchColumn();
    }

    public function countOverdueByUser(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` WHERE user_id = ? AND status = 'borrowed' AND due_date < CURDATE()");
        $stmt->execute([$userId]);

        return (int) $stmt->fetchColumn();
    }

    public function totalFineByUser(int $userId, int $perDay): int
    {
        $stmt = $this->db->prepare("SELECT due_date FROM `{$this->table}` WHERE user_id = ? AND status = 'borrowed' AND due_date < CURDATE()");
        $stmt->execute([$userId]);
        $total = 0;
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $due) {
            $total += days_overdue((string) $due) * $perDay;
        }

        return $total;
    }

    public function renew(int $id, string $newDueDate): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET due_date = ?, renew_count = renew_count + 1 WHERE id = ? AND status = 'borrowed'"
        );
        $stmt->execute([$newDueDate, $id]);

        return $stmt->rowCount() > 0;
    }

    /** @return array{labels: array, data: array} last N days borrow counts */
    public function getDailyStats(int $days = 14): array
    {
        $days = max(7, min(60, $days));
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($d));
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM borrowings WHERE DATE(created_at) = ?');
            $stmt->execute([$d]);
            $data[] = (int) $stmt->fetchColumn();
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
