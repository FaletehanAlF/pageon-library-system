<?php

declare(strict_types=1);

abstract class Model
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    /** Columns allowed for ORDER BY — override in child if needed */
    protected array $allowedOrderColumns = ['id', 'created_at', 'name', 'title'];

    public function __construct()
    {
        $this->db = Database::getInstance();

        if ($this->table === '') {
            throw new LogicException(static::class . '::$table must be defined');
        }
    }

    /**
     * Safely build ORDER BY clause from whitelisted columns.
     */
    protected function sanitizeOrderBy(string $orderBy, string $default = 'created_at DESC'): string
    {
        // Expected format: "column [ASC|DESC]" or "table.column [ASC|DESC]"
        $orderBy = trim($orderBy);
        if ($orderBy === '') {
            return $default;
        }

        // Allow comma-separated orderings
        $parts = explode(',', $orderBy);
        $safe = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if (!preg_match('/^([\w\.]+)(?:\s+(ASC|DESC))?$/i', $part, $m)) {
                continue;
            }
            $col = $m[1];
            $dir = isset($m[2]) ? strtoupper($m[2]) : 'ASC';

            // Extract bare column name for whitelist check (strip table prefix)
            $bare = $col;
            if (str_contains($col, '.')) {
                $bare = substr($col, (int) strrpos($col, '.') + 1);
            }

            if (!in_array($bare, $this->allowedOrderColumns, true)) {
                continue;
            }

            if (!in_array($dir, ['ASC', 'DESC'], true)) {
                $dir = 'ASC';
            }

            $safe[] = $col . ' ' . $dir;
        }

        return $safe !== [] ? implode(', ', $safe) : $default;
    }

    public function findAll(array $conditions = [], string $orderBy = 'created_at DESC', int $limit = 0): array
    {
        $orderBy = $this->sanitizeOrderBy($orderBy);

        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];

        if ($conditions !== []) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "`{$column}` = ?";
                $params[] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= " ORDER BY {$orderBy}";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1");
        $stmt->execute([$id]);

        return (bool) $stmt->fetchColumn();
    }

    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        $params = [];

        if ($conditions !== []) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "`{$column}` = ?";
                $params[] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        if ($data === []) {
            throw new InvalidArgumentException('Create data cannot be empty');
        }

        $columns = array_map(static fn(string $c): string => "`{$c}`", array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            $placeholders
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if ($data === []) {
            return false;
        }

        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "`{$column}` = ?";
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `%s` = ?',
            $this->table,
            implode(', ', $set),
            $this->primaryKey
        );

        $stmt = $this->db->prepare($sql);
        $params = array_merge(array_values($data), [$id]);

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?");

        return $stmt->execute([$id]);
    }
}
