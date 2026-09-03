<?php

declare(strict_types=1);

final class Setting extends Model
{
    protected string $table = 'settings';
    protected array $allowedOrderColumns = ['id', 'key', 'updated_at'];

    public function get(string $key, mixed $default = null): mixed
    {
        $stmt = $this->db->prepare('SELECT `value` FROM `settings` WHERE `key` = ? LIMIT 1');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();

        return $val !== false ? $val : $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO `settings` (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        $stmt->execute([$key, (string) $value]);
    }

    /** @return array<string,string> */
    public function all(): array
    {
        $rows = $this->db->query('SELECT `key`, `value` FROM `settings`')->fetchAll(PDO::FETCH_KEY_PAIR);

        return is_array($rows) ? $rows : [];
    }
}
