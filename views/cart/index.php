<div class="page-header">
    <div>
        <h1 class="page-title">Keranjang Pinjam</h1>
        <p class="page-subtitle">Kumpulkan beberapa buku, lalu pinjam sekaligus. Maksimal <span class="font-bold text-gray-700"><?= (int) ($maxLoans ?? 3) ?> buku aktif</span> dalam satu waktu.</p>
    </div>
    <?php if (!empty($items)): ?>
        <form method="POST" action="<?= e(url('/cart/clear')) ?>" onsubmit="return confirm('Kosongkan keranjang?')" class="shrink-0">
            <?= csrf_field() ?>
            <button class="btn btn-secondary">Kosongkan</button>
        </form>
    <?php endif; ?>
</div>

<?php if (empty($items)): ?>
    <div class="empty-state">
        <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-gray-400"><?= icon('cart', 'h-8 w-8') ?></span>
        <p class="mt-5 font-bold">Keranjang masih kosong</p>
        <p class="mt-1.5 max-w-sm text-sm leading-relaxed text-gray-500">Cari buku di katalog, lalu klik “+ Keranjang” atau “Pinjam” pada buku yang Anda mau.</p>
        <a href="<?= e(url('/books')) ?>" class="btn btn-primary mt-6">Jelajahi Buku &rarr;</a>
    </div>
<?php else: ?>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($items as $b): ?>
            <?php $cUrl = book_cover_url($b); ?>
            <article class="card flex flex-col overflow-hidden">
                <a href="<?= e(url('/books/' . $b['id'])) ?>" tabindex="-1" aria-hidden="true">
                    <img src="<?= e($cUrl) ?>" alt="" class="h-44 w-full object-cover" loading="lazy">
                </a>
                <div class="flex flex-1 flex-col p-5">
                    <p class="text-[0.7rem] font-bold uppercase tracking-[0.08em] text-gray-400"><?= e($b['category_name'] ?? 'Umum') ?></p>
                    <a href="<?= e(url('/books/' . $b['id'])) ?>" class="mt-1.5 font-bold leading-snug line-clamp-2 hover:underline"><?= e($b['title']) ?></a>
                    <p class="mt-1 text-[0.85rem] text-gray-500 line-clamp-1"><?= e($b['author']) ?></p>
                    <p class="badge mt-3.5 w-fit <?= (int) $b['stock'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= (int) $b['stock'] > 0 ? 'Stok: ' . (int) $b['stock'] : 'Stok habis — keluarkan / reservasi' ?>
                    </p>
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <form method="POST" action="<?= e(url('/cart/remove')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="book_id" value="<?= (int) $b['id'] ?>">
                            <button class="rounded-lg px-2 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:underline">Keluarkan dari keranjang</button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="card card-pad flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-[0.92rem] leading-relaxed text-gray-600">
            <span class="font-bold text-gray-900"><?= count($items) ?> buku</span> di keranjang.
            <span class="block text-sm text-gray-500 sm:mt-0.5">Pastikan belum melebihi batas <?= (int) ($maxLoans ?? 3) ?> pinjaman aktif.</span>
        </div>
        <form method="POST" action="<?= e(url('/cart/checkout')) ?>" onsubmit="return confirm('Pinjam <?= count($items) ?> buku ini sekaligus? Kembalikan sebelum <?= e(format_date(date('Y-m-d', strtotime('+' . max(1, (int) ($loanDays ?? 7)) . ' days')))) ?> agar GRATIS — telat kena denda mulai <?= e(format_rupiah((int) ($finePerDay ?? 1000))) ?>/hari.')" class="shrink-0">
            <?= csrf_field() ?>
            <button class="btn btn-primary btn-lg w-full sm:w-auto">Pinjam Semua Sekaligus</button>
        </form>
    </div>
<?php endif; ?>
