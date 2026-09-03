<?php

declare(strict_types=1);

final class FinePayment extends Model
{
    protected string $table = 'fine_payments';

    public function unpaidByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT fp.*, b.title AS book_title
            FROM fine_payments fp
            LEFT JOIN books b ON b.id = fp.book_id
            WHERE fp.user_id = ? AND fp.status = 'unpaid'
            ORDER BY fp.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function historyByUser(int $userId, int $limit = 20): array
    {
        $limit = max(1, min(50, $limit));
        $stmt = $this->db->prepare("
            SELECT fp.*, b.title AS book_title
            FROM fine_payments fp
            LEFT JOIN books b ON b.id = fp.book_id
            WHERE fp.user_id = ? AND fp.status != 'unpaid'
            ORDER BY fp.created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function unpaidTotalByUser(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM fine_payments WHERE user_id = ? AND status = 'unpaid'");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function existsUnpaidForBorrowing(int $borrowingId, string $type = 'overdue'): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM fine_payments WHERE borrowing_id = ? AND type = ? AND status = 'unpaid' LIMIT 1");
        $stmt->execute([$borrowingId, $type]);
        return (bool) $stmt->fetchColumn();
    }

    /** Semua tagihan untuk satu peminjaman (untuk halaman riwayat). */
    public function forBorrowing(int $borrowingId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM fine_payments WHERE borrowing_id = ? ORDER BY created_at ASC");
        $stmt->execute([$borrowingId]);
        return $stmt->fetchAll();
    }

    public function createUnpaid(int $userId, int $amount, string $type, ?int $borrowingId = null, ?int $bookId = null, ?string $note = null): int
    {
        return $this->create([
            'borrowing_id' => $borrowingId,
            'user_id' => $userId,
            'book_id' => $bookId,
            'amount' => max(0, $amount),
            'type' => $type,
            'status' => 'unpaid',
            'note' => $note,
            'created_by' => Session::get('user_id'),
        ]);
    }

    public function markPaid(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE fine_payments SET status = 'paid', paid_at = NOW() WHERE id = ? AND status = 'unpaid'");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function markWaived(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE fine_payments SET status = 'waived', paid_at = NOW() WHERE id = ? AND status = 'unpaid'");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * @return array{data: array, total: int}
     */
    public function paginateAll(string $status = '', int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $where = '';
        $params = [];
        if (in_array($status, ['unpaid', 'paid', 'waived'], true)) {
            $where = "WHERE fp.status = ?";
            $params[] = $status;
        }
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM fine_payments fp $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT fp.*, u.name AS user_name, b.title AS book_title
            FROM fine_payments fp
            INNER JOIN users u ON u.id = fp.user_id
            LEFT JOIN books b ON b.id = fp.book_id
            $where
            ORDER BY fp.created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total];
    }

    public function totals(): array
    {
        $row = $this->db->query("
            SELECT
                COALESCE(SUM(CASE WHEN status='unpaid' THEN amount ELSE 0 END),0) AS unpaid,
                COALESCE(SUM(CASE WHEN status='paid' THEN amount ELSE 0 END),0) AS paid
            FROM fine_payments
        ")->fetch();
        return ['unpaid' => (int) ($row['unpaid'] ?? 0), 'paid' => (int) ($row['paid'] ?? 0)];
    }
}
