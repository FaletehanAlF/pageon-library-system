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

/**
 * Hitung denda keterlambatan PROGRESIF: tiap lewat 1 hari, tarif naik.
 * Hari ke-1 = base, hari ke-2 = base+inc, hari ke-3 = base+2*inc, dst.
 * Total(n) = n*base + inc*n*(n-1)/2. Jika inc = 0 → datar seperti dulu.
 */
function calc_fine(string $dueDate, ?int $perDay = null, ?int $increment = null, ?string $asOf = null): int
{
    $perDay ??= setting_int('fine_per_day', 1000);
    $increment ??= setting_int('fine_increment', 0);
    $perDay = max(0, $perDay);
    $increment = max(0, $increment);

    $n = days_overdue($dueDate, $asOf);
    if ($n <= 0) {
        return 0;
    }

    return $n * $perDay + $increment * (int) ($n * ($n - 1) / 2);
}

/**
 * Perkiraan denda jika telat tepat $days hari (untuk tampilan info).
 */
function fine_preview(int $days, ?int $perDay = null, ?int $increment = null): int
{
    $perDay ??= setting_int('fine_per_day', 1000);
    $increment ??= setting_int('fine_increment', 0);
    $days = max(0, $days);

    return $days * max(0, $perDay) + max(0, $increment) * (int) ($days * ($days - 1) / 2);
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
 * URL cover buku — pakai file upload bila ada, kalau tidak ada
 * buatkan gambar SVG otomatis (judul + penulis + warna per buku).
 * Jadi semua buku selalu punya gambar tanpa perlu upload & tanpa ubah SQL.
 *
 * @param array<string,mixed> $book  Baris buku (id, title, author, category_name, cover)
 */
function book_cover_url(array $book): string
{
    $uploaded = cover_url(isset($book['cover']) ? (string) $book['cover'] : null);
    if ($uploaded !== null) {
        return $uploaded;
    }

    $title = trim((string) ($book['title'] ?? 'Tanpa Judul'));
    if ($title === '') {
        $title = 'Tanpa Judul';
    }
    $author = trim((string) ($book['author'] ?? ''));
    $category = trim((string) ($book['category_name'] ?? ''));
    $seed = isset($book['id']) ? (int) $book['id'] : (isset($book['book_id']) ? (int) $book['book_id'] : abs(crc32($title)));

    // Palet gradien (tetap untuk tiap buku karena dipilih dari id)
    $palettes = [
        ['#1e3a8a', '#3b82f6'],
        ['#065f46', '#10b981'],
        ['#7c2d12', '#f59e0b'],
        ['#701a75', '#d946ef'],
        ['#7f1d1d', '#ef4444'],
        ['#0c4a6e', '#06b6d4'],
        ['#3b0764', '#8b5cf6'],
        ['#422006', '#eab308'],
    ];
    [$c1, $c2] = $palettes[$seed % count($palettes)];

    // Inisial besar dari huruf pertama judul
    $initial = mb_strtoupper(mb_substr($title, 0, 1, 'UTF-8'), 'UTF-8');

    // Bungkus judul jadi beberapa baris pendek
    $words = preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [$title];
    $lines = [];
    $current = '';
    foreach ($words as $w) {
        $candidate = $current === '' ? $w : $current . ' ' . $w;
        if (mb_strlen($candidate, 'UTF-8') > 16 && $current !== '') {
            $lines[] = $current;
            $current = $w;
        } else {
            $current = $candidate;
        }
        if (count($lines) === 3) {
            break;
        }
    }
    if ($current !== '' && count($lines) < 4) {
        $lines[] = $current;
    }
    $lines = array_slice($lines, 0, 4);

    $esc = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
    $y = 330;
    $titleSvg = '';
    foreach ($lines as $line) {
        $titleSvg .= '<text x="200" y="' . $y . '" text-anchor="middle" font-family="Georgia, serif" font-size="30" font-weight="bold" fill="#ffffff">' . $esc(mb_substr($line, 0, 22, 'UTF-8')) . '</text>';
        $y += 38;
    }

    $authorSvg = $author !== ''
        ? '<text x="200" y="510" text-anchor="middle" font-family="Verdana, sans-serif" font-size="17" fill="#e5e7eb">' . $esc(mb_substr($author, 0, 30, 'UTF-8')) . '</text>'
        : '';
    $catSvg = $category !== ''
        ? '<text x="200" y="80" text-anchor="middle" font-family="Verdana, sans-serif" font-size="15" letter-spacing="3" fill="#e5e7eb">' . $esc(mb_strtoupper(mb_substr($category, 0, 20, 'UTF-8'), 'UTF-8')) . '</text>'
        : '';

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="600" viewBox="0 0 400 600">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="' . $c1 . '"/><stop offset="1" stop-color="' . $c2 . '"/>'
        . '</linearGradient></defs>'
        . '<rect width="400" height="600" fill="url(#g)"/>'
        . '<circle cx="340" cy="60" r="90" fill="#ffffff" opacity="0.12"/>'
        . '<circle cx="40" cy="540" r="110" fill="#000000" opacity="0.15"/>'
        . '<rect x="28" y="28" width="344" height="544" rx="14" fill="none" stroke="#ffffff" stroke-opacity="0.45" stroke-width="3"/>'
        . $catSvg
        . '<text x="200" y="250" text-anchor="middle" font-family="Georgia, serif" font-size="130" font-weight="bold" fill="#ffffff" opacity="0.9">' . $esc($initial) . '</text>'
        . '<line x1="120" y1="290" x2="280" y2="290" stroke="#ffffff" stroke-opacity="0.6" stroke-width="2"/>'
        . $titleSvg
        . $authorSvg
        . '<text x="200" y="560" text-anchor="middle" font-family="Verdana, sans-serif" font-size="14" fill="#e5e7eb" opacity="0.8">Pageon Library</text>'
        . '</svg>';

    return 'data:image/svg+xml;base64,' . base64_encode($svg);
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

/**
 * Pengingat tagihan denda belum lunas (tagih terus sampai dibayar).
 * Dipanggil saat dashboard dibuka; anti-duplikat (1 notif belum dibaca).
 */
function ensure_unpaid_fine_reminder(int $userId): void
{
    try {
        $total = (new FinePayment())->unpaidTotalByUser($userId);
        if ($total <= 0) {
            return;
        }
        $notif = new Notification();
        $link = url('/fines');
        $stmt = Database::getInstance()->prepare(
            "SELECT 1 FROM notifications WHERE user_id = ? AND is_read = 0 AND link = ? AND title LIKE 'Tagihan denda%' LIMIT 1"
        );
        $stmt->execute([$userId, $link]);
        if ($stmt->fetchColumn()) {
            return;
        }
        $notif->notify(
            $userId,
            'Tagihan denda ' . format_rupiah($total),
            'Anda punya tagihan ' . format_rupiah($total) . ' yang belum dibayar. SEGERA bayar ke petugas — denda telat naik setiap hari dan Anda belum bisa pinjam lagi.',
            $link
        );
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
