<?php

declare(strict_types=1);

final class ActivityLog extends Model
{
    protected string $table = 'activity_logs';

    public function record(?int $userId, string $action, string $detail = ''): void
    {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO activity_logs (user_id, action, detail, ip) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId,
                mb_substr($action, 0, 50),
                mb_substr($detail, 0, 2000),
                mb_substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45) ?: null,
            ]);
        } catch (Throwable) {
            // Logging tidak boleh merusak alur utama
        }
    }

    /**
     * @return array{data: array, total: int}
     */
    public function paginate(string $action = '', int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $where = '';
        $params = [];
        if ($action !== '' && preg_match('/^[a-z_]+$/', $action)) {
            $where = 'WHERE l.action = ?';
            $params[] = $action;
        }
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM activity_logs l $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT l.*, u.name AS user_name
            FROM activity_logs l
            LEFT JOIN users u ON u.id = l.user_id
            $where
            ORDER BY l.id DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total];
    }

    /** @return string[] */
    public function distinctActions(): array
    {
        return $this->db->query('SELECT DISTINCT action FROM activity_logs ORDER BY action')->fetchAll(PDO::FETCH_COLUMN);
    }
}
