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
}
