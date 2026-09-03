<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Keranjang Pinjam</h1>
        <p class="mt-1 text-gray-500">Kumpulkan beberapa buku, lalu pinjam sekaligus. Maks <span class="font-semibold text-gray-700"><?= (int) ($maxLoans ?? 3) ?> buku aktif</span>.</p>
    </div>
    <?php if (!empty($items)): ?>
        <form method="POST" action="<?= e(url('/cart/clear')) ?>" onsubmit="return confirm('Kosongkan keranjang?')">
            <?= csrf_field() ?>
            <button class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">Kosongkan</button>
        </form>
    <?php endif; ?>
</div>

<?php if (empty($items)): ?>
    <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center">
        <div class="mx-auto max-w-sm">
            <div class="text-5xl">🛒</div>
            <p class="mt-4 font-medium">Keranjang masih kosong.</p>
            <p class="mt-1 text-sm text-gray-500">Cari buku di katalog, lalu klik “+ Keranjang” atau “Pinjam”.</p>
            <a href="<?= e(url('/books')) ?>" class="mt-5 inline-flex rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Jelajahi Buku &rarr;</a>
        </div>
    </div>
<?php else: ?>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($items as $b): ?>
            <?php $cUrl = book_cover_url($b); ?>
            <div class="flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <a href="<?= e(url('/books/' . $b['id'])) ?>">
                    <img src="<?= e($cUrl) ?>" alt="Cover <?= e($b['title']) ?>" class="h-40 w-full object-cover" loading="lazy">
                </a>
                <div class="flex flex-1 flex-col p-4">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400"><?= e($b['category_name'] ?? 'Umum') ?></p>
                    <a href="<?= e(url('/books/' . $b['id'])) ?>" class="font-semibold line-clamp-1 hover:text-blue-600"><?= e($b['title']) ?></a>
                    <p class="mt-1 text-sm text-gray-500 line-clamp-1"><?= e($b['author']) ?></p>
                    <p class="mt-2 text-xs <?= (int) $b['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?> font-medium">
                        <?= (int) $b['stock'] > 0 ? 'Stok: ' . (int) $b['stock'] : 'Stok habis — keluarkan atau reservasi' ?>
                    </p>
                    <form method="POST" action="<?= e(url('/cart/remove')) ?>" class="mt-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= (int) $b['id'] ?>">
                        <button class="text-xs font-medium text-red-500 hover:underline">Keluarkan</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-6 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-600">
            <span class="font-semibold text-gray-900"><?= count($items) ?> buku</span> di keranjang.
            Pastikan belum melebihi batas <?= (int) ($maxLoans ?? 3) ?> pinjaman aktif.
        </div>
        <form method="POST" action="<?= e(url('/cart/checkout')) ?>" onsubmit="return confirm('Pinjam <?= count($items) ?> buku ini sekaligus?')">
            <?= csrf_field() ?>
            <button class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white hover:bg-gray-800">Pinjam Semua Sekaligus</button>
        </form>
    </div>
<?php endif; ?>
