<?php

declare(strict_types=1);

final class PasswordReset extends Model
{
    protected string $table = 'password_resets';

    public function createForUser(int $userId, string $token, int $ttlMinutes = 30): void
    {
        // Satu token aktif per user — hapus yang lama
        $del = $this->db->prepare('DELETE FROM password_resets WHERE user_id = ?');
        $del->execute([$userId]);

        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            hash('sha256', $token),
            date('Y-m-d H:i:s', time() + $ttlMinutes * 60),
        ]);
    }

    public function findValid(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW() LIMIT 1"
        );
        $stmt->execute([hash('sha256', $token)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
