<?php

declare(strict_types=1);

final class User extends Model
{
    protected string $table = 'users';
    protected array $allowedOrderColumns = ['id', 'created_at', 'name', 'email', 'role'];

    public function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function register(array $data): int
    {
        $data['email'] = strtolower(trim((string) ($data['email'] ?? '')));
        $data['password'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        $data['role'] = $data['role'] ?? 'user';

        return $this->create($data);
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if ($user !== null && password_verify($password, (string) $user['password'])) {
            if (($user['status'] ?? 'active') === 'suspended') {
                return null;
            }
            if (password_needs_rehash((string) $user['password'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $this->update((int) $user['id'], ['password' => $newHash]);
            }

            return $user;
        }

        return null;
    }

    public function getAll(): array
    {
        return $this->findAll([], 'created_at DESC');
    }

    public function paginateWithStats(int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $total = $this->count();
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT users.*, COUNT(DISTINCT CASE WHEN borrowings.status = 'borrowed' THEN borrowings.id END) AS active_loans
            FROM users LEFT JOIN borrowings ON borrowings.user_id = users.id
            GROUP BY users.id ORDER BY users.created_at DESC LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute();
        return ['data' => $stmt->fetchAll(), 'total' => $total];
    }

    /** @return array<int> */
    public function allIds(): array
    {
        return array_map('intval', $this->db->query('SELECT id FROM users')->fetchAll(PDO::FETCH_COLUMN));
    }
}
}
