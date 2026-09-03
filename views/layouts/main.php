<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle ?? 'Pageon') ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/output.css')) ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#f4f5f7] font-sans text-gray-900 antialiased">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-gray-900 focus:px-4 focus:py-2 focus:text-sm focus:text-white">
        Lewati ke konten utama
    </a>

    <div class="flex min-h-screen">

        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="flex min-w-0 flex-1 flex-col">

            <?php require __DIR__ . '/navbar.php'; ?>

            <main id="main-content" class="w-full flex-1">
                <div class="app-container py-6 sm:py-8 lg:py-10">

                    <?php
                    $flashSuccess = Session::getFlash('success');
                    $flashError   = Session::getFlash('error');
                    $flashErrors  = Session::getFlash('errors');
                    ?>

                    <?php if ($flashSuccess): ?>
                        <div id="flash-success" role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-[0.9rem] leading-relaxed text-green-800 shadow-sm">
                            <span class="mt-0.5 shrink-0"><?= icon('check-circle', 'h-5 w-5') ?></span>
                            <p class="flex-1"><?= e((string) $flashSuccess) ?></p>
                            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup notifikasi" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-lg leading-none text-green-600 transition hover:bg-green-100 hover:text-green-900">&times;</button>
                        </div>
                    <?php endif; ?>

                    <?php if ($flashError): ?>
                        <div id="flash-error" role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-[0.9rem] leading-relaxed text-red-800 shadow-sm">
                            <span class="mt-0.5 shrink-0"><?= icon('alert', 'h-5 w-5') ?></span>
                            <p class="flex-1"><?= e((string) $flashError) ?></p>
                            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup notifikasi" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-lg leading-none text-red-500 transition hover:bg-red-100 hover:text-red-900">&times;</button>
                        </div>
                    <?php endif; ?>

                    <?php if (is_array($flashErrors) && $flashErrors !== []): ?>
                        <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-[0.9rem] text-red-800 shadow-sm">
                            <p class="font-semibold">Periksa kembali isian Anda:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5 leading-relaxed">
                                <?php foreach ($flashErrors as $msg): ?>
                                    <li><?= e((string) $msg) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="page-stack">
                        <?= $content ?>
                    </div>

                    <?php
                    // Clear old-input flash after rendering — it was peeked via old()
                    Session::getFlash('old');
                    ?>

                </div>
            </main>

            <footer class="border-t border-gray-200/80 bg-white">
                <div class="app-container flex flex-col items-center justify-between gap-2 py-5 text-[0.8rem] text-gray-400 sm:flex-row">
                    <p>&copy; <?= date('Y') ?> <span class="font-semibold text-gray-500"><?= e(setting('library_name', 'Pageon')) ?></span> — Library Management System</p>
                    <p class="flex items-center gap-1.5">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        Sistem berjalan normal
                    </p>
                </div>
            </footer>

        </div>

    </div>

    <script src="<?= e(url('/assets/js/app.js')) ?>"></script>

</body>
</html>
