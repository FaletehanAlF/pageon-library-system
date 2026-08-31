<?php

$title = "Dashboard";

require_once "views/layouts/header.php";
?>

<div class="flex min-h-screen">

    <?php require_once "views/layouts/sidebar.php"; ?>

    <div class="flex flex-1 flex-col">

        <?php require_once "views/layouts/navbar.php"; ?>

        <main class="flex-1 p-6">

            <h2 class="text-2xl font-bold">
                Dashboard
            </h2>

            <p class="mt-2 text-gray-500">
                Welcome to Pageon.
            </p>

        </main>

    </div>

</div>

<?php require_once "views/layouts/footer.php"; ?>