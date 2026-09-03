<?php

declare(strict_types=1);

final class BookCopy extends Model
{
    protected string $table = 'book_copies';
    protected array $allowedOrderColumns = ['id', 'created_at', 'barcode', 'status'];

    public function getByBook(int $bookId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM book_copies WHERE book_id = ? ORDER BY id ASC');
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public function countAvailable(int $bookId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM book_copies WHERE book_id = ? AND status = 'available'");
        $stmt->execute([$bookId]);
        return (int) $stmt->fetchColumn();
    }

    public function takeAvailable(int $bookId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM book_copies WHERE book_id = ? AND status = 'available' ORDER BY id ASC LIMIT 1");
        $stmt->execute([$bookId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function markBorrowed(int $copyId): void
    {
        $stmt = $this->db->prepare("UPDATE book_copies SET status = 'borrowed' WHERE id = ?");
        $stmt->execute([$copyId]);
    }

    public function markAvailable(int $copyId): void
    {
        $stmt = $this->db->prepare("UPDATE book_copies SET status = 'available' WHERE id = ? AND status = 'borrowed'");
        $stmt->execute([$copyId]);
    }

    public function createCopies(int $bookId, int $n): void
    {
        $stmt = $this->db->prepare("INSERT INTO book_copies (book_id, barcode, `condition`, status) VALUES (?, ?, 'baik', 'available')");
        for ($i = 0; $i < $n; $i++) {
            $barcode = 'BK' . $bookId . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            try {
                $stmt->execute([$bookId, $barcode]);
            } catch (PDOException) {
                $barcode = 'BK' . $bookId . '-' . time() . '-' . strtoupper(bin2hex(random_bytes(4)));
                $stmt->execute([$bookId, $barcode]);
            }
        }
    }

    public function removeAvailable(int $bookId, int $n): void
    {
        $stmt = $this->db->prepare("SELECT id FROM book_copies WHERE book_id = ? AND status = 'available' ORDER BY id DESC LIMIT {$n}");
        // $n is int from trusted code
        $stmt->execute([$bookId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($ids === []) {
            return;
        }
        $in = implode(',', array_map('intval', $ids));
        $this->db->exec("DELETE FROM book_copies WHERE id IN ({$in})");
    }

    public function syncStock(int $bookId, int $newStock): void
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM book_copies WHERE book_id = ?');
        $stmt->execute([$bookId]);
        $have = (int) $stmt->fetchColumn();
        if ($newStock > $have) {
            $this->createCopies($bookId, $newStock - $have);
        } elseif ($newStock < $have) {
            $borrowed = $this->countBorrowed($bookId);
            $maxRemovable = $have - $borrowed;
            $toRemove = min($have - $newStock, max(0, $maxRemovable));
            if ($toRemove > 0) {
                $this->removeAvailable($bookId, $toRemove);
            }
        }
    }

    public function countBorrowed(int $bookId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM book_copies WHERE book_id = ? AND status = 'borrowed'");
        $stmt->execute([$bookId]);
        return (int) $stmt->fetchColumn();
    }
}
