<?php

declare(strict_types=1);

final class Notification extends Model
{
    protected string $table = 'notifications';

    public function notify(int $userId, string $title, string $message, ?string $link = null): void
    {
        $stmt = $this->db->prepare('INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, mb_substr($title, 0, 150), $message, $link]);
    }

    public function broadcast(array $userIds, string $title, string $message, ?string $link = null): void
    {
        $stmt = $this->db->prepare('INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)');
        foreach ($userIds as $uid) {
            $stmt->execute([(int) $uid, mb_substr($title, 0, 150), $message, $link]);
        }
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getUser(int $userId, int $limit = 20): array
    {
        $limit = max(1, min(50, $limit));
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit}");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markRead(int $userId, int $id): void
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }

    public function markAllRead(int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);
    }

    /**
     * Cek notifikasi belum dibaca untuk borrowing tertentu (anti-duplikat pengingat).
     */
    public function existsUnread(int $userId, string $borrowingId, string $link): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM notifications WHERE user_id = ? AND is_read = 0 AND link = ? AND message LIKE ? LIMIT 1"
        );
        // Cari berdasarkan judul buku tidak praktis; gunakan kecocokan pesan berisi ID via link + judul tetap.
        // Pendekatan: ada notif belum dibaca dengan judul pengingat & link yang sama untuk buku tsb.
        $stmt->execute([$userId, $link, '%"' . str_replace(['%', '_'], '', $this->titleOf((int) $borrowingId)) . '"%']);
        return (bool) $stmt->fetchColumn();
    }

    private function titleOf(int $borrowingId): string
    {
        $stmt = $this->db->prepare(
            'SELECT b.title FROM borrowings br INNER JOIN books b ON b.id = br.book_id WHERE br.id = ? LIMIT 1'
        );
        $stmt->execute([$borrowingId]);
        return (string) ($stmt->fetchColumn() ?: '');
    }
}
