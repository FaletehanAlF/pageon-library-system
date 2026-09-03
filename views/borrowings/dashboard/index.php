<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">
        Dashboard
    </h1>
    <p class="mt-1 text-gray-500">
        Selamat datang kembali, <?= e(Session::get('user_name', 'User')) ?>.
    </p>
</div>

<!-- Statistics -->
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-8">

    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Buku</p>
                <h2 class="text-2xl font-bold"><?= $totalBooks ?></h2>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Sedang Dipinjam</p>
                <h2 class="text-2xl font-bold"><?= $totalBorrowed ?></h2>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Sudah Dikembalikan</p>
                <h2 class="text-2xl font-bold"><?= $totalReturned ?></h2>
            </div>
        </div>
    </div>

</div>

<?php if (!empty($overdueBorrowings) && isAdmin()): ?>
<!-- Overdue Alert -->
<div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-6">
    <div class="flex items-center gap-2 mb-3">
        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <h3 class="font-semibold text-red-800">Peminjaman Terlambat</h3>
    </div>
    <p class="text-sm text-red-700 mb-2">Ada <?= count($overdueBorrowings) ?> peminjaman yang sudah melewati batas waktu.</p>
    <a href="/pageon/borrowings" class="text-sm font-medium text-red-800 underline hover:no-underline">Lihat Semua &rarr;</a>
</div>
<?php endif; ?>

<!-- Recently Added -->
<div>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Buku Terbaru</h2>
        <a href="/pageon/books" class="text-sm font-medium text-gray-600 hover:text-black">
            Lihat Semua &rarr;
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

        <?php if (empty($latestBooks)): ?>
            <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-8 text-center">
                <p class="text-gray-500">Belum ada buku tersedia.</p>
                <?php if (isAdmin()): ?>
                    <a href="/pageon/books/create" class="mt-3 inline-block text-sm font-medium text-gray-900 underline">+ Tambah Buku</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($latestBooks as $book): ?>
                <a href="/pageon/books/<?= $book['id'] ?>" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-md">
                    <div class="flex h-40 items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                        <span class="text-4xl">📖</span>
                    </div>
                    <div class="p-4">
                        <p class="mb-1 text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <?= e($book['category_name'] ?? 'Umum') ?>
                        </p>
                        <h3 class="font-semibold text-sm group-hover:text-blue-600 transition line-clamp-1">
                            <?= e($book['title']) ?>
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 line-clamp-1">
                            <?= e($book['author']) ?>
                        </p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-block rounded-full <?= $book['stock'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> px-2 py-0.5 text-xs font-medium">
                                <?= $book['stock'] > 0 ? "Stok: {$book['stock']}" : 'Habis' ?>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>
