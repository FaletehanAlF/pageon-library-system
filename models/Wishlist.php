<?php

declare(strict_types=1);

final class Wishlist extends Model
{
    protected string $table = 'wishlists';

    public function has(int $userId, int $bookId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM wishlists WHERE user_id = ? AND book_id = ? LIMIT 1');
        $stmt->execute([$userId, $bookId]);
        return (bool) $stmt->fetchColumn();
    }

    public function toggle(int $userId, int $bookId): bool
    {
        if ($this->has($userId, $bookId)) {
            $stmt = $this->db->prepare('DELETE FROM wishlists WHERE user_id = ? AND book_id = ?');
            $stmt->execute([$userId, $bookId]);
            return false;
        }
        $stmt = $this->db->prepare('INSERT IGNORE INTO wishlists (user_id, book_id) VALUES (?, ?)');
        $stmt->execute([$userId, $bookId]);
        return true;
    }

    public function getUserWishlist(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT wishlists.*, books.*, categories.name AS category_name
            FROM wishlists
            INNER JOIN books ON books.id = wishlists.book_id
            LEFT JOIN categories ON categories.id = books.category_id
            WHERE wishlists.user_id = ? ORDER BY wishlists.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
