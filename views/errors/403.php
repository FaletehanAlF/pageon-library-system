<?php http_response_code(403); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/output.css')) ?>">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="text-center max-w-md">
        <p class="text-sm font-medium text-gray-400">Error 403</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Akses ditolak</h1>
        <p class="mt-3 text-gray-600">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="<?= e(url('/')) ?>" class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white hover:bg-gray-800">Ke Dashboard</a>
            <a href="<?= e(url('/login')) ?>" class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-medium text-gray-600 hover:bg-white">Login</a>
        </div>
    </div>
</body>
</html>
