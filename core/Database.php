<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $cfg = file_exists(BASE_PATH . '/config/database.php')
            ? require BASE_PATH . '/config/database.php'
            : [];

        $host = $cfg['host'] ?? 'localhost';
        $dbname = $cfg['dbname'] ?? 'pageon_db';
        $username = $cfg['username'] ?? 'root';
        $password = $cfg['password'] ?? '';
        $charset = $cfg['charset'] ?? 'utf8mb4';
        $options = $cfg['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

        try {
            self::$instance = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            // Do not expose credentials — log and show friendly error.
            error_log('[Database] Connection failed: ' . $e->getMessage());

            if ((config('app.debug', false) === true)) {
                throw $e;
            }

            http_response_code(500);
            require BASE_PATH . '/views/errors/500.php';
            exit;
        }

        return self::$instance;
    }

    /**
     * For testing: allow resetting the singleton.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    private function __construct() {}
    private function __clone() {}
    public function __wakeup(): void
    {
        throw new LogicException('Cannot unserialize singleton');
    }
}
