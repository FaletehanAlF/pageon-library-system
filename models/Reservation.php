<?php

declare(strict_types=1);

final class Reservation extends Model
{
    protected string $table = 'reservations';

    public function getQueue(int $bookId): array
    {
        $stmt = $this->db->prepare("
            SELECT reservations.*, users.name AS user_name
            FROM reservations
            INNER JOIN users ON users.id = reservations.user_id
            WHERE book_id = ? AND status IN ('waiting','ready')
            ORDER BY created_at ASC
        ");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public function getUserReservations(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT reservations.*, books.title AS book_title, books.author AS book_author
            FROM reservations
            INNER JOIN books ON books.id = reservations.book_id
            WHERE reservations.user_id = ?
            ORDER BY reservations.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function hasActive(int $userId, int $bookId): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM reservations WHERE user_id = ? AND book_id = ? AND status IN ('waiting','ready') LIMIT 1");
        $stmt->execute([$userId, $bookId]);
        return (bool) $stmt->fetchColumn();
    }

    public function firstWaiting(int $bookId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE book_id = ? AND status = 'waiting' ORDER BY created_at ASC LIMIT 1");
        $stmt->execute([$bookId]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function countWaiting(int $bookId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservations WHERE book_id = ? AND status = 'waiting'");
        $stmt->execute([$bookId]);
        return (int) $stmt->fetchColumn();
    }

    public function positionInQueue(int $bookId, int $reservationId): int
    {
        $queue = $this->getQueue($bookId);
        foreach ($queue as $i => $r) {
            if ((int) $r['id'] === $reservationId) {
                return $i + 1;
            }
        }
        return 0;
    }
}
