<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Pageon') ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/output.css')) ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <?= $content ?>

    <?php Session::getFlash('old'); ?>

    <script src="<?= e(url('/assets/js/app.js')) ?>"></script>
</body>
</html>
