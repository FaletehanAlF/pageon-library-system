<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle ?? 'Pageon') ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/output.css')) ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <div class="flex min-h-screen">

        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="flex flex-1 flex-col min-w-0">

            <?php require __DIR__ . '/navbar.php'; ?>

            <main class="flex-1 p-6 lg:p-8 overflow-auto">

                <?php
                $flashSuccess = Session::getFlash('success');
                $flashError   = Session::getFlash('error');
                $flashErrors  = Session::getFlash('errors');
                ?>

                <?php if ($flashSuccess): ?>
                    <div id="flash-success" role="alert" class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                        <span><?= e((string) $flashSuccess) ?></span>
                        <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup" class="ml-4 text-green-600 hover:text-green-800">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if ($flashError): ?>
                    <div id="flash-error" role="alert" class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        <span><?= e((string) $flashError) ?></span>
                        <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup" class="ml-4 text-red-600 hover:text-red-800">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if (is_array($flashErrors) && $flashErrors !== []): ?>
                    <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        <p class="font-medium mb-2">Periksa kembali isian Anda:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($flashErrors as $field => $msg): ?>
                                <li><?= e((string) $msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= $content ?>

                <?php
                // Clear old-input flash after rendering — it was peeked via old()
                Session::getFlash('old');
                ?>

            </main>

            <footer class="border-t border-gray-100 bg-white px-6 py-4 text-center text-xs text-gray-400">
                &copy; <?= date('Y') ?> Pageon — Library Management System
            </footer>

        </div>

    </div>

    <script src="<?= e(url('/assets/js/app.js')) ?>"></script>

</body>
</html>
