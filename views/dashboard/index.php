<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang kembali, <span class="font-semibold text-gray-700"><?= e((string) Session::get('user_name', 'User')) ?></span>. Pantau koleksi dan pinjaman dari satu tempat.</p>
    </div>
    <?php if (isAdmin()): ?>
        <a href="<?= e(url('/reports')) ?>" class="btn btn-secondary shrink-0"><?= icon('chart', 'h-4 w-4') ?> Lihat Laporan</a>
    <?php endif; ?>
</div>

<?php if (!empty($announcements)): ?>
<div class="space-y-3">
    <?php foreach ($announcements as $ann): ?>
        <div class="card flex items-start gap-3.5 border-blue-200/80 bg-blue-50/70 px-5 py-4 sm:px-6 sm:py-5">
            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white"><?= icon('bell', 'h-5 w-5') ?></span>
            <div class="min-w-0">
                <p class="font-semibold text-blue-950"><?= e($ann['title']) ?></p>
                <p class="mt-1 text-[0.9rem] leading-relaxed text-blue-900/80"><?= nl2br(e($ann['message'])) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card overflow-hidden !border-gray-900 bg-gray-900 text-white">
    <div class="p-6 sm:p-8 lg:p-10">
        <p class="text-xs font-bold uppercase tracking-[0.12em] text-gray-400">Mulai dari sini</p>
        <h2 class="mt-2 text-xl font-bold tracking-tight sm:text-2xl">Mau pinjam buku? Gampang, 3 langkah saja.</h2>
        <div class="mt-6 grid gap-3.5 sm:gap-4 md:grid-cols-3">
            <div class="flex items-start gap-3.5 rounded-2xl bg-white/[0.07] p-4 sm:p-5 ring-1 ring-white/10">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-sm font-extrabold text-gray-900">1</span>
                <p class="leading-relaxed"><strong class="block">Cari bukunya</strong><span class="text-sm text-gray-300">Ketik judul di halaman Katalog Buku.</span></p>
            </div>
            <div class="flex items-start gap-3.5 rounded-2xl bg-white/[0.07] p-4 sm:p-5 ring-1 ring-white/10">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-sm font-extrabold text-gray-900">2</span>
                <p class="leading-relaxed"><strong class="block">Tekan Pinjam</strong><span class="text-sm text-gray-300">Buku langsung tercatat atas nama Anda.</span></p>
            </div>
            <div class="flex items-start gap-3.5 rounded-2xl bg-white/[0.07] p-4 sm:p-5 ring-1 ring-white/10">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-sm font-extrabold text-gray-900">3</span>
                <p class="leading-relaxed"><strong class="block">Kembalikan tepat waktu</strong><span class="text-sm text-gray-300">Cek tanggalnya di Pinjaman Saya.</span></p>
            </div>
        </div>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <a href="<?= e(url('/books')) ?>" class="btn btn-lg bg-white text-gray-900 hover:bg-gray-100"><?= icon('search', 'h-4 w-4') ?> Cari Buku Sekarang</a>
            <a href="<?= e(url('/my-borrowings')) ?>" class="btn btn-lg border border-white/25 text-white hover:bg-white/10"><?= icon('clipboard', 'h-4 w-4') ?> Pinjaman Saya</a>
            <a href="<?= e(url('/bantuan')) ?>" class="btn btn-lg border border-white/25 text-white hover:bg-white/10"><?= icon('help', 'h-4 w-4') ?> Bantuan</a>
        </div>
    </div>
</div>

<?php if (!empty($showTips) && !empty($firstSteps)): ?>
<div class="card card-pad">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="flex items-center gap-2 text-[1.05rem] font-bold tracking-tight"><?= icon('flag', 'h-5 w-5') ?> Target pemula — selesaikan 3 ini</h2>
            <p class="mt-1.5 text-sm leading-relaxed text-gray-500">Centang otomatis begitu Anda melakukannya.</p>
        </div>
        <form method="POST" action="<?= e(url('/tips/hide')) ?>" class="shrink-0"><?= csrf_field() ?><button class="text-xs font-medium text-gray-400 transition hover:text-gray-600 hover:underline">Sembunyikan</button></form>
    </div>
    <div class="mt-5 grid gap-3.5 sm:grid-cols-3">
        <?php foreach ($firstSteps as $idx => $step): ?>
            <div class="flex items-center gap-3.5 rounded-2xl p-4 sm:p-5 <?= !empty($step['done']) ? 'border border-green-200 bg-green-50/70' : 'border border-gray-200 bg-gray-50/60' ?>">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold <?= !empty($step['done']) ? 'bg-green-600 text-white' : 'border border-gray-300 bg-white text-gray-400' ?>"><?= !empty($step['done']) ? icon('check', 'h-4 w-4') : ($idx + 1) ?></span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold leading-snug <?= !empty($step['done']) ? 'text-green-800 line-through' : 'text-gray-800' ?>"><?= e($step['label']) ?></p>
                    <?php if (empty($step['done'])): ?>
                        <a href="<?= e(url($step['href'])) ?>" class="mt-1 inline-block text-xs font-semibold text-gray-900 underline underline-offset-2"><?= e($step['cta']) ?> &rarr;</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="grid gap-4 sm:gap-5 sm:grid-cols-2 xl:grid-cols-4">
    <div class="card card-pad-sm">
        <p class="text-sm font-medium text-gray-500">Total Buku</p>
        <p class="mt-1.5 text-3xl font-extrabold tracking-tight"><?= (int) $totalBooks ?></p>
        <p class="mt-1 text-xs text-gray-400">koleksi di perpustakaan</p>
    </div>
    <div class="card card-pad-sm">
        <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
        <p class="mt-1.5 text-3xl font-extrabold tracking-tight"><?= (int) $totalBorrowed ?></p>
        <p class="mt-1 text-xs text-gray-400">belum dikembalikan</p>
    </div>
    <div class="card card-pad-sm">
        <p class="text-sm font-medium text-gray-500">Terlambat</p>
        <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-red-600"><?= (int) ($totalOverdue ?? 0) ?></p>
        <p class="mt-1 text-xs text-gray-400">perlu segera ditindak</p>
    </div>
    <div class="card card-pad-sm">
        <p class="text-sm font-medium text-gray-500">Dikembalikan</p>
        <p class="mt-1.5 text-3xl font-extrabold tracking-tight"><?= (int) $totalReturned ?></p>
        <p class="mt-1 text-xs text-gray-400">transaksi selesai</p>
    </div>
</div>

<div class="card card-pad">
    <div class="mb-5 flex items-center justify-between gap-3">
        <div>
            <h2 class="font-bold tracking-tight">Tren Peminjaman</h2>
            <p class="mt-0.5 text-sm text-gray-500">14 hari terakhir</p>
        </div>
    </div>
    <div class="h-64 sm:h-72"><canvas id="borrowChart"></canvas></div>
</div>

<?php
$low = array_filter(($lowStock ?? []), static fn($b) => (int) $b['stock'] <= 2);
$low = array_slice(array_values($low), 0, 5);
$showAttention = (isAdmin() && (!empty($overdueBorrowings) || !empty($low)));
?>
<?php if ($showAttention): ?>
<div class="card card-pad !border-amber-200 bg-amber-50/40" role="alert">
    <h3 class="font-bold tracking-tight">Perlu Perhatian</h3>
    <?php if (!empty($overdueBorrowings)): ?>
    <div class="mt-4 rounded-2xl bg-red-50 p-4 text-sm leading-relaxed ring-1 ring-red-100 sm:p-5">
        <p class="font-semibold text-red-800">Terlambat (<?= count($overdueBorrowings) ?>) — segera hubungi peminjam.</p>
        <a href="<?= e(url('/borrowings')) ?>" class="mt-2 inline-block font-semibold text-red-800 underline underline-offset-2 hover:no-underline">Lihat Semua &rarr;</a>
    </div>
    <?php endif; ?>
    <?php if (!empty($low)): ?>
    <div class="mt-4 rounded-2xl bg-amber-50 p-4 text-sm leading-relaxed ring-1 ring-amber-100 sm:p-5">
        <p class="font-semibold text-amber-800">Stok menipis:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-amber-800">
            <?php foreach ($low as $b): ?>
                <li><a class="font-medium underline underline-offset-2" href="<?= e(url('/books/' . $b['id'])) ?>"><?= e($b['title']) ?></a> — sisa <?= (int) $b['stock'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
    <section>
        <div class="mb-5 flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold tracking-tight">Buku Terbaru</h2>
            <a href="<?= e(url('/books')) ?>" class="text-sm font-semibold text-gray-600 transition hover:text-gray-900">Lihat Semua &rarr;</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-5">
            <?php foreach (array_slice($latestBooks, 0, 4) as $book): ?>
                <?php $cUrl = book_cover_url($book); ?>
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="card group overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
                    <img src="<?= e($cUrl) ?>" class="h-36 w-full object-cover" loading="lazy" alt="Cover <?= e($book['title']) ?>">
                    <div class="p-4 sm:p-5">
                        <p class="text-[0.7rem] font-bold uppercase tracking-[0.08em] text-gray-400"><?= e($book['category_name'] ?? 'Umum') ?></p>
                        <h3 class="mt-1.5 font-semibold leading-snug line-clamp-2"><?= e($book['title']) ?></h3>
                        <p class="mt-1 text-[0.83rem] text-gray-500"><?= e($book['author']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <section>
        <div class="mb-5 flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold tracking-tight">Terpopuler</h2>
        </div>
        <div class="space-y-3.5">
            <?php foreach (($popularBooks ?? []) as $i => $book): ?>
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="card flex items-center gap-4 p-4 transition hover:shadow-sm sm:p-5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-900 text-sm font-extrabold text-white"><?= $i + 1 ?></span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold leading-snug"><?= e($book['title']) ?></p>
                        <p class="mt-0.5 text-[0.83rem] text-gray-500"><?= e($book['author']) ?> · <?= (int) ($book['borrow_count'] ?? 0) ?>x dipinjam</p>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($popularBooks)): ?>
                <div class="card card-pad-sm text-center text-sm text-gray-500">Belum ada data peminjaman.</div>
            <?php endif; ?>
        </div>
    </section>
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
            datasets: [{ label: 'Peminjaman', data: <?= json_encode($chartData ?? []) ?>, backgroundColor: '#111827', borderRadius: 6, maxBarThickness: 36 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } } }
    });
})();
</script>
