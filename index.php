<?php

require_once "config/database.php";
require_once "models/Book.php";

$bookModel = new Book($pdo);

$totalBooks = $bookModel->getTotalBooks();
$latestBooks = $bookModel->getLatestBooks();

$title = "Dashboard";

require_once "views/layouts/header.php";
?>

<div class="flex min-h-screen">

    <?php require_once "views/layouts/sidebar.php"; ?>

    <div class="flex flex-1 flex-col">

        <?php require_once "views/layouts/navbar.php"; ?>

        <main class="flex-1 p-6 lg:p-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight">
                    Welcome Pageon 
                </h1>

                <p class="mt-1 text-gray-500">
                    Find your next book and manage your borrowing.
                </p>
            </div>

            <!-- Search -->
            <div class="mb-8">
                <input
                    type="text"
                    placeholder="Search books..."
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none transition focus:border-gray-400"
                >
            </div>

            <!-- Statistics -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                <!-- Total Books -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6">
                    <p class="text-sm text-gray-500">
                        Total Books
                    </p>

                    <h2 class="mt-2 text-3xl font-bold">
                        <?= $totalBooks ?>
                    </h2>
                </div>

                <!-- Currently Borrowed -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6">
                    <p class="text-sm text-gray-500">
                        Currently Borrowed
                    </p>

                    <h2 class="mt-2 text-3xl font-bold">
                        0
                    </h2>
                </div>

                <!-- Returned Books -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6">
                    <p class="text-sm text-gray-500">
                        Returned Books
                    </p>

                    <h2 class="mt-2 text-3xl font-bold">
                        0
                    </h2>
                </div>

            </div>

            <!-- Recently Added -->
            <div class="mt-10">

                <div class="mb-4 flex items-center justify-between">

                    <h2 class="text-lg font-semibold">
                        Recently Added
                    </h2>

                    <a
                        href="#"
                        class="text-sm font-medium text-gray-600 hover:text-black"
                    >
                        View all
                    </a>

                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

                    <?php if (empty($latestBooks)): ?>

                        <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-gray-500">
                                No books available yet.
                            </p>
                        </div>

                    <?php else: ?>

                        <?php foreach ($latestBooks as $book): ?>

                            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">

                                <div class="flex h-48 items-center justify-center bg-gray-100">
                                    <span class="text-sm text-gray-400">
                                        No Cover
                                    </span>
                                </div>

                                <div class="p-4">

                                    <p class="mb-1 text-xs text-gray-500">
                                        <?= htmlspecialchars($book['category_name']) ?>
                                    </p>

                                    <h3 class="font-semibold">
                                        <?= htmlspecialchars($book['title']) ?>
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        <?= htmlspecialchars($book['author']) ?>
                                    </p>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </main>

    </div>

</div>

<?php require_once "views/layouts/footer.php"; ?>