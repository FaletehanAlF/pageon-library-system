<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Pageon') ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/output.css')) ?>">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased">
    <div class="no-print mx-auto max-w-2xl px-4 pt-6 flex items-center justify-between">
        <a href="javascript:history.back()" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali</a>
        <button onclick="window.print()" class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white">Print</button>
    </div>
    <main class="mx-auto max-w-2xl px-4 py-6">
        <?= $content ?>
    </main>
</body>
</html>
