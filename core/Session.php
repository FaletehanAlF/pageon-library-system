<?php

declare(strict_types=1);

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent($file, $line)) {
            error_log("[Session] Cannot start — headers already sent in {$file}:{$line}");
            return;
        }

        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $params = session_get_cookie_params();

        session_set_cookie_params([
            'lifetime' => $params['lifetime'],
            'path' => $params['path'] ?: '/',
            'domain' => $params['domain'],
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function regenerate(bool $deleteOld = true): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOld);
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);

        // Clean up empty flash bag
        if (isset($_SESSION['flash']) && empty($_SESSION['flash'])) {
            unset($_SESSION['flash']);
        }

        return $value;
    }

    /**
     * Peek flash without consuming — useful for old() helper reuse.
     */
    public static function peekFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['flash'][$key] ?? $default;
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            // Clear cookie
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    (bool) $params['secure'],
                    (bool) $params['httponly']
                );
            }

            session_destroy();
        }
    }
}
