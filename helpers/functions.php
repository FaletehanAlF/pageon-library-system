<?php

declare(strict_types=1);

/**
 * Escape HTML output.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate URL with base path.
 */
function url(string $path = ''): string
{
    $base = rtrim(config('base_path', '/pageon'), '/');
    $path = '/' . ltrim($path, '/');

    if ($path === '/') {
        return $base . '/';
    }

    return $base . $path;
}

/**
 * Alias for backwards-compat and convenience.
 */
function base_url(string $path = ''): string
{
    return url($path);
}

/**
 * Redirect helper — sends Location header and exits.
 */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Retrieve old input value (from flashed old data or current POST).
 * Uses peek so multiple old() calls within same view don't consume the flash.
 */
function old(string $key, string $default = ''): string
{
    $flashed = Session::peekFlash('old');
    if (is_array($flashed) && array_key_exists($key, $flashed)) {
        return e((string) $flashed[$key]);
    }

    // During the same request (before redirect), $_POST holds the value
    if (isset($_POST[$key])) {
        return e((string) $_POST[$key]);
    }

    return e($default);
}

/**
 * Get config value.
 *
 * @param string $key  Dot-notated key, e.g. "database.host"
 */
function config(string $key, mixed $default = null): mixed
{
    static $cache = null;

    if ($cache === null) {
        $cache = [
            'app' => file_exists(BASE_PATH . '/config/app.php') ? require BASE_PATH . '/config/app.php' : [],
            'database' => file_exists(BASE_PATH . '/config/database.php') ? require BASE_PATH . '/config/database.php' : [],
        ];
    }

    $parts = explode('.', $key);
    $value = $cache;

    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }

    return $value;
}

function isAuth(): bool
{
    return Session::has('user_id');
}

function isAdmin(): bool
{
    return Session::get('user_role') === 'admin';
}

function currentUser(): ?array
{
    if (!isAuth()) {
        return null;
    }

    try {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
        $stmt->execute([Session::get('user_id')]);
        $user = $stmt->fetch();

        return $user ?: null;
    } catch (PDOException) {
        return null;
    }
}

function csrf_token(): string
{
    if (!Session::has('_csrf')) {
        Session::set('_csrf', bin2hex(random_bytes(32)));
    }

    return (string) Session::get('_csrf');
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['_csrf'] ?? '';
    $sessionToken = Session::get('_csrf', '');

    return is_string($token)
        && is_string($sessionToken)
        && hash_equals($sessionToken, $token);
}

function abort(int $code, string $message = ''): never
{
    http_response_code($code);

    $view = match ($code) {
        403 => BASE_PATH . '/views/errors/403.php',
        404 => BASE_PATH . '/views/errors/404.php',
        default => BASE_PATH . '/views/errors/500.php',
    };

    if (file_exists($view)) {
        require $view;
    } else {
        echo $message !== '' ? e($message) : "Error $code";
    }

    exit;
}

function format_date(string $date): string
{
    $ts = strtotime($date);
    if ($ts === false) {
        return e($date);
    }

    return date('d M Y', $ts);
}

function time_ago(string $datetime): string
{
    try {
        $now = new DateTime();
        $ago = new DateTime($datetime);
    } catch (Exception) {
        return e($datetime);
    }

    $diff = $now->diff($ago);

    if ($diff->y > 0) {
        return $diff->y . ' tahun lalu';
    }
    if ($diff->m > 0) {
        return $diff->m . ' bulan lalu';
    }
    if ($diff->d > 0) {
        return $diff->d . ' hari lalu';
    }
    if ($diff->h > 0) {
        return $diff->h . ' jam lalu';
    }
    if ($diff->i > 0) {
        return $diff->i . ' menit lalu';
    }

    return 'Baru saja';
}

/**
 * Sanitize string input — trims and optionally limits length.
 */
function sanitize_string(mixed $value, int $maxLength = 0): string
{
    $str = trim((string) ($value ?? ''));
    if ($maxLength > 0) {
        $str = mb_substr($str, 0, $maxLength);
    }

    return $str;
}

/**
 * Baca config portal admin rahasia (gitignore).
 * Return null jika file belum dibuat (portal nonaktif).
 */
function admin_secret(): ?array
{
    static $cache = null;
    static $loaded = false;

    if ($loaded) {
        return $cache;
    }
    $loaded = true;

    $file = BASE_PATH . '/config/admin-secret.php';
    if (!file_exists($file)) {
        return null;
    }
    $cfg = require $file;
    if (!is_array($cfg)) {
        return null;
    }
    $slug = trim((string) ($cfg['slug'] ?? ''), '/');
    $invite = (string) ($cfg['invite_code'] ?? '');
    if ($slug === '' || $invite === '' || !preg_match('/^[A-Za-z0-9\-\/]+$/', $slug)) {
        return null;
    }
    $cache = [
        'slug' => $slug,
        'invite_code' => $invite,
        'max_admins' => max(1, (int) ($cfg['max_admins'] ?? 2)),
    ];

    return $cache;
}

/**
 * URL portal admin rahasia, atau null jika belum di-generate.
 */
function admin_portal_url(string $sub = ''): ?string
{
    $s = admin_secret();
    if ($s === null) {
        return null;
    }

    return url('/' . $s['slug'] . $sub);
}

function setting(string $key, mixed $default = null): mixed
{
    static $model = null;
    try {
        if ($model === null) {
            $model = new Setting();
        }

        return $model->get($key, $default);
    } catch (Throwable) {
        return $default;
    }
}

function setting_int(string $key, int $default = 0): int
{
    return (int) setting($key, $default);
}

function days_overdue(string $dueDate, ?string $asOf = null): int
{
    $due = strtotime($dueDate);
    $now = strtotime($asOf ?? date('Y-m-d'));
    if ($due === false || $now === false) {
        return 0;
    }
    $diff = (int) floor(($now - $due) / 86400);

    return max(0, $diff);
}

function calc_fine(string $dueDate, ?int $perDay = null, ?string $asOf = null): int
{
    $perDay ??= setting_int('fine_per_day', 1000);

    return days_overdue($dueDate, $asOf) * max(0, $perDay);
}

function format_rupiah(int $amount): string
{
    return 'Rp' . number_format($amount, 0, ',', '.');
}

function cover_url(?string $cover): ?string
{
    if ($cover === null || $cover === '') {
        return null;
    }
    // Prevent path traversal
    $cover = basename($cover);

    return url('/assets/covers/' . $cover);
}

/**
 * Handle book cover upload. Returns filename or null. Throws on invalid.
 * @return array{filename: ?string, error: ?string}
 */
function handle_cover_upload(array $file, ?string $old = null): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['filename' => $old, 'error' => null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['filename' => $old, 'error' => 'Gagal mengupload cover.'];
    }
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return ['filename' => $old, 'error' => 'Ukuran cover maksimal 2MB.'];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        return ['filename' => $old, 'error' => 'Cover harus JPG, PNG, atau WebP.'];
    }
    $dir = BASE_PATH . '/assets/covers';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = 'cover_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
        return ['filename' => $old, 'error' => 'Gagal menyimpan cover.'];
    }
    // Delete old
    if ($old) {
        $oldPath = $dir . '/' . basename($old);
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    return ['filename' => $filename, 'error' => null];
}

function delete_cover(?string $cover): void
{
    if (!$cover) {
        return;
    }
    $path = BASE_PATH . '/assets/covers/' . basename($cover);
    if (is_file($path)) {
        @unlink($path);
    }
}

/**
 * Catat aktivitas ke audit log. Tidak pernah melempar error.
 */
function log_activity(string $action, string $detail = ''): void
{
    try {
        if (!isAuth()) {
            (new ActivityLog())->record(null, $action, $detail);
            return;
        }
        (new ActivityLog())->record((int) Session::get('user_id'), $action, $detail);
    } catch (Throwable) {
        // abaikan
    }
}

/* ── Keranjang pinjam (session) ───────────────────────────── */

function cart_items(): array
{
    $items = Session::get('cart', []);
    return is_array($items) ? array_values(array_unique(array_map('intval', $items))) : [];
}

function cart_count(): int
{
    return count(cart_items());
}

function cart_has(int $bookId): bool
{
    return in_array($bookId, cart_items(), true);
}

function cart_add(int $bookId): void
{
    $items = cart_items();
    if (!in_array($bookId, $items, true)) {
        $items[] = $bookId;
        Session::set('cart', $items);
    }
}

function cart_remove(int $bookId): void
{
    Session::set('cart', array_values(array_filter(
        cart_items(),
        static fn(int $id): bool => $id !== $bookId
    )));
}

function cart_clear(): void
{
    Session::remove('cart');
}

/**
 * Buat notifikasi pengingat untuk pinjaman yang jatuh tempo ≤ 2 hari.
 * Dipanggil saat dashboard dibuka; anti-duplikat via link unik.
 */
function ensure_due_notifications(int $userId): void
{
    try {
        $borrowing = new Borrowing();
        $notif = new Notification();
        foreach ($borrowing->getDueSoonByUser($userId, 2) as $b) {
            $link = url('/my-borrowings');
            if ($notif->existsUnread($userId, (string) $b['id'], $link)) {
                continue;
            }
            $notif->notify(
                $userId,
                'Jatuh tempo segera',
                'Buku "' . $b['book_title'] . '" jatuh tempo ' . format_date((string) $b['due_date']) . '. Segera kembalikan atau perpanjang.',
                $link
            );
        }
    } catch (Throwable) {
        // abaikan
    }
}

function pagination_links(string $baseUrl, array $query, int $page, int $totalPages): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $html = '<nav class="mt-8 flex items-center justify-center gap-2" aria-label="Pagination">';
    $q = $query;
    $mk = static function (int $p) use ($baseUrl, $q): string {
        $q['page'] = $p;

        return e($baseUrl . '?' . http_build_query($q));
    };

    // Prev
    if ($page > 1) {
        $html .= '<a href="' . $mk($page - 1) . '" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">&larr; Prev</a>';
    }

    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    if ($start > 1) {
        $html .= '<a href="' . $mk(1) . '" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">1</a>';
        if ($start > 2) {
            $html .= '<span class="px-1 text-gray-400">…</span>';
        }
    }
    for ($i = $start; $i <= $end; $i++) {
        if ($i === $page) {
            $html .= '<span class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $mk($i) . '" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">' . $i . '</a>';
        }
    }
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<span class="px-1 text-gray-400">…</span>';
        }
        $html .= '<a href="' . $mk($totalPages) . '" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">' . $totalPages . '</a>';
    }

    if ($page < $totalPages) {
        $html .= '<a href="' . $mk($page + 1) . '" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Next &rarr;</a>';
    }
    $html .= '</nav>';

    return $html;
}
