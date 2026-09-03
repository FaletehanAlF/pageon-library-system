<?php

declare(strict_types=1);

/**
 * Generate portal admin rahasia (JALANKAN LOKAL SAJA).
 *
 *   php generate-admin-portal.php              — buat baru (tidak menimpa yang ada)
 *   php generate-admin-portal.php --regenerate — putar slug + kode baru
 *
 * Hasil (KEDUANYA gitignore, tidak ikut push):
 *   - config/admin-secret.php   → slug + invite code
 *   - routes/admin-portal.php   → pendaftaran route rahasia
 */

$regenerate = in_array('--regenerate', $argv ?? [], true);

$secretFile = __DIR__ . '/config/admin-secret.php';
$portalFile = __DIR__ . '/routes/admin-portal.php';

if (file_exists($secretFile) && !$regenerate) {
    fwrite(STDERR, "Sudah ada: config/admin-secret.php\nGunakan --regenerate untuk memutar baru.\n");
    exit(1);
}

$slug = 'kelola-' . bin2hex(random_bytes(8));
$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$invite = '';
for ($i = 0; $i < 12; $i++) {
    $invite .= $alphabet[random_int(0, strlen($alphabet) - 1)];
}
$invite = substr($invite, 0, 4) . '-' . substr($invite, 4, 4) . '-' . substr($invite, 8, 4);

$secretContent = <<<PHP
<?php

declare(strict_types=1);

// RAHASIA — JANGAN commit / push file ini (sudah masuk .gitignore).
// Hanya owner yang menyimpan URL + kode di bawah ini.
return [
    'slug' => '{$slug}',
    'invite_code' => '{$invite}',
    'max_admins' => 2,
];
PHP;

$portalContent = <<<PHP
<?php

declare(strict_types=1);

// RAHASIA — file ini di-generate, masuk .gitignore, jangan di-push.
// Daftarkan route portal admin di slug rahasia.
\$__slug = '/{$slug}';
\$router->get(\$__slug, 'AdminPortalController@loginForm');
\$router->post(\$__slug, 'AdminPortalController@login');
\$router->get(\$__slug . '/register', 'AdminPortalController@registerForm');
\$router->post(\$__slug . '/register', 'AdminPortalController@register');
unset(\$__slug);
PHP;

file_put_contents($secretFile, $secretContent . "\n");
file_put_contents($portalFile, $portalContent . "\n");

echo "Portal admin rahasia dibuat.\n";
echo "URL login admin : /pageon/{$slug}\n";
echo "URL daftar admin: /pageon/{$slug}/register\n";
echo "Kode invite     : {$invite}\n";
echo "Maks admin      : 2\n";
echo "SIMPAN baik-baik. File config/admin-secret.php + routes/admin-portal.php tidak ikut push (gitignore).\n";
