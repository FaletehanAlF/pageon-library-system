<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Pageon') ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/output.css')) ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#f4f5f7] font-sans text-gray-900 antialiased">

    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 sm:py-14">
        <div class="w-full max-w-md">
            <?= $content ?>
            <p class="mt-8 text-center text-xs leading-relaxed text-gray-400">
                &copy; <?= date('Y') ?> <?= e(setting('library_name', 'Pageon')) ?> — Library Management System
            </p>
        </div>
    </main>

    <?php Session::getFlash('old'); ?>

    <script src="<?= e(url('/assets/js/app.js')) ?>"></script>
</body>
</html>
