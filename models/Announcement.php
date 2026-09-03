<?php

declare(strict_types=1);

final class Announcement extends Model
{
    protected string $table = 'announcements';

    public function active(int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));
        $stmt = $this->db->prepare("SELECT announcements.*, users.name AS author_name FROM announcements LEFT JOIN users ON users.id = announcements.created_by WHERE is_active = 1 ORDER BY created_at DESC LIMIT {$limit}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT announcements.*, users.name AS author_name FROM announcements LEFT JOIN users ON users.id = announcements.created_by ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
}
