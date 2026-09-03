<div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
        <p class="mt-1 text-gray-500">Selamat datang kembali, <?= e((string) Session::get('user_name', 'User')) ?>.</p>
    </div>
    <?php if (isAdmin()): ?>
        <a href="<?= e(url('/reports')) ?>" class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium hover:bg-gray-50">Lihat Laporan</a>
    <?php endif; ?>
</div>

<?php if (!empty($announcements)): ?>
<div class="mb-8 space-y-3">
    <?php foreach ($announcements as $ann): ?>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <p class="font-semibold text-blue-900"><?= e($ann['title']) ?></p>
            <p class="mt-1 text-sm text-blue-800"><?= nl2br(e($ann['message'])) ?></p>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 text-sm text-gray-600">
    <strong class="text-gray-900">Cara pakai:</strong>
    1) Cari buku di <a class="underline" href="<?= e(url('/books')) ?>">Buku</a> →
    2) Pinjam langsung / + <a class="underline" href="<?= e(url('/cart')) ?>">Keranjang</a> / Reservasi jika habis →
    3) Pantau &amp; kembalikan di <a class="underline" href="<?= e(url('/my-borrowings')) ?>">Peminjaman Saya</a> · cek <a class="underline" href="<?= e(url('/fines')) ?>">Denda</a>.
    Bingung? Buka <a class="underline font-medium text-gray-900" href="<?= e(url('/bantuan')) ?>">Bantuan</a>.
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Total Buku</p>
        <h2 class="text-2xl font-bold"><?= (int) $totalBooks ?></h2>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Sedang Dipinjam</p>
        <h2 class="text-2xl font-bold"><?= (int) $totalBorrowed ?></h2>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Terlambat</p>
        <h2 class="text-2xl font-bold text-red-600"><?= (int) ($totalOverdue ?? 0) ?></h2>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Dikembalikan</p>
        <h2 class="text-2xl font-bold"><?= (int) $totalReturned ?></h2>
    </div>
</div>

<div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold">Tren Peminjaman (14 hari)</h2>
    </div>
    <div class="h-64"><canvas id="borrowChart"></canvas></div>
</div>

<?php if (!empty($overdueBorrowings) && isAdmin()): ?>
<div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-6" role="alert">
    <h3 class="font-semibold text-red-800">Peminjaman Terlambat (<?= count($overdueBorrowings) ?>)</h3>
    <p class="text-sm text-red-700 mt-1 mb-2">Segera hubungi peminjam berikut.</p>
    <a href="<?= e(url('/borrowings')) ?>" class="text-sm font-medium text-red-800 underline hover:no-underline">Lihat Semua &rarr;</a>
</div>
<?php endif; ?>

<?php
$low = array_filter(($lowStock ?? []), static fn($b) => (int) $b['stock'] <= 2);
$low = array_slice(array_values($low), 0, 5);
?>
<?php if (!empty($low) && isAdmin()): ?>
<div class="mb-8 rounded-2xl border border-amber-200 bg-amber-50 p-6">
    <h3 class="font-semibold text-amber-800">Stok Menipis</h3>
    <ul class="mt-2 text-sm text-amber-800 list-disc list-inside">
        <?php foreach ($low as $b): ?>
            <li><a class="underline" href="<?= e(url('/books/' . $b['id'])) ?>"><?= e($b['title']) ?></a> — sisa <?= (int) $b['stock'] ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="grid gap-8 lg:grid-cols-2">
    <div>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">Buku Terbaru</h2>
            <a href="<?= e(url('/books')) ?>" class="text-sm font-medium text-gray-600 hover:text-gray-900">Lihat Semua &rarr;</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <?php foreach (array_slice($latestBooks, 0, 4) as $book): ?>
                <?php $cUrl = cover_url($book['cover'] ?? null); ?>
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-md">
                    <?php if ($cUrl): ?>
                        <img src="<?= e($cUrl) ?>" class="h-32 w-full object-cover" loading="lazy" alt="">
                    <?php else: ?>
                        <div class="flex h-32 items-center justify-center bg-gray-100 text-3xl">📖</div>
                    <?php endif; ?>
                    <div class="p-4">
                        <p class="text-xs text-gray-400 uppercase"><?= e($book['category_name'] ?? 'Umum') ?></p>
                        <h3 class="font-semibold text-sm line-clamp-1"><?= e($book['title']) ?></h3>
                        <p class="text-xs text-gray-500"><?= e($book['author']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">Terpopuler</h2>
        </div>
        <div class="space-y-3">
            <?php foreach (($popularBooks ?? []) as $i => $book): ?>
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 hover:shadow-sm">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white"><?= $i + 1 ?></span>
                    <div class="min-w-0">
                        <p class="font-medium truncate"><?= e($book['title']) ?></p>
                        <p class="text-xs text-gray-500"><?= e($book['author']) ?> · <?= (int) ($book['borrow_count'] ?? 0) ?>x dipinjam</p>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($popularBooks)): ?>
                <p class="text-sm text-gray-500">Belum ada data peminjaman.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    var el = document.getElementById('borrowChart');
    if (!el || typeof Chart === 'undefined') return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels ?? []) ?>,
            datasets: [{ label: 'Peminjaman', data: <?= json_encode($chartData ?? []) ?>, backgroundColor: '#111827', borderRadius: 6 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
})();
</script>
