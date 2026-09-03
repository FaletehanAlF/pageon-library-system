<?php

declare(strict_types=1);

/**
 * Pageon — Library Management System
 * Front Controller
 */

define('BASE_PATH', __DIR__);

// ── Error handling ──────────────────────────────────────────
// In production, hide errors; in debug mode, show them.
$appConfig = file_exists(BASE_PATH . '/config/app.php') ? require BASE_PATH . '/config/app.php' : [];
$debug = (bool) ($appConfig['debug'] ?? false);

if ($debug) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// Global exception handler — log and show 500 page
set_exception_handler(static function (Throwable $e): void {
    error_log('[Pageon] Uncaught: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    if (!headers_sent()) {
        http_response_code(500);
    }

    $debugLocal = (bool) (config('app.debug', false) ?? false);
    if ($debugLocal) {
        echo '<pre style="padding:2rem;font-family:monospace;white-space:pre-wrap;background:#fff;color:#111">'
            . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            . "\n\n" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8')
            . '</pre>';
    } else {
        $view500 = BASE_PATH . '/views/errors/500.php';
        if (file_exists($view500)) {
            // Ensure helpers are available for url()/e()
            if (!function_exists('e')) {
                require BASE_PATH . '/helpers/functions.php';
            }
            require $view500;
        } else {
            echo 'Internal Server Error';
        }
    }
    exit;
});

// ── Bootstrap ───────────────────────────────────────────────
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Session.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/helpers/functions.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Book.php';
require_once BASE_PATH . '/models/BookCopy.php';
require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/Borrowing.php';
require_once BASE_PATH . '/models/Setting.php';
require_once BASE_PATH . '/models/Reservation.php';
require_once BASE_PATH . '/models/Review.php';
require_once BASE_PATH . '/models/Wishlist.php';
require_once BASE_PATH . '/models/Notification.php';
require_once BASE_PATH . '/models/Announcement.php';
require_once BASE_PATH . '/controllers/AdminPortalController.php';
require_once BASE_PATH . '/middleware/AuthMiddleware.php';

Session::start();

// ── Dispatch ────────────────────────────────────────────────
require BASE_PATH . '/routes/web.php';
