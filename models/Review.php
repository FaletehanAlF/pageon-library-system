<?php

declare(strict_types=1);

final class Review extends Model
{
    protected string $table = 'reviews';

    public function getByBook(int $bookId): array
    {
        $stmt = $this->db->prepare("
            SELECT reviews.*, users.name AS user_name
            FROM reviews INNER JOIN users ON users.id = reviews.user_id
            WHERE book_id = ? ORDER BY created_at DESC
        ");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public function getUserReview(int $userId, int $bookId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM reviews WHERE user_id = ? AND book_id = ? LIMIT 1');
        $stmt->execute([$userId, $bookId]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function avgRating(int $bookId): array
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS cnt, AVG(rating) AS avg FROM reviews WHERE book_id = ?');
        $stmt->execute([$bookId]);
        $r = $stmt->fetch();
        return ['count' => (int) ($r['cnt'] ?? 0), 'avg' => $r['avg'] !== null ? round((float) $r['avg'], 1) : 0];
    }

    public function upsert(int $userId, int $bookId, int $rating, string $comment): void
    {
        $stmt = $this->db->prepare('
            INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
        ');
        $stmt->execute([$userId, $bookId, $rating, $comment !== '' ? $comment : null]);
    }
}
