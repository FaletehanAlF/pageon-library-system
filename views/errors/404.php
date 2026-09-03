<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan</title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/output.css')) ?>">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="text-center max-w-md">
        <p class="text-sm font-medium text-gray-400">Error 404</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Halaman tidak ditemukan</h1>
        <p class="mt-3 text-gray-600">URL yang Anda tuju tidak tersedia atau sudah dipindahkan.</p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="<?= e(url('/')) ?>" class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white hover:bg-gray-800">Ke Dashboard</a>
            <a href="javascript:history.back()" class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-medium text-gray-600 hover:bg-white">Kembali</a>
        </div>
    </div>
</body>
</html>
