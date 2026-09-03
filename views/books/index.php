<div class="page-header">
    <div>
        <h1 class="page-title">Katalog Buku</h1>
        <p class="page-subtitle">Klik salah satu buku untuk melihat detail, lalu tekan <strong class="text-gray-700">Pinjam</strong>.<?= isset($totalCount) ? ' Ada <span class="font-bold text-gray-800">' . (int) $totalCount . '</span> buku di katalog.' : '' ?></p>
    </div>
    <?php if (isAdmin()): ?>
        <a href="<?= e(url('/books/create')) ?>" class="btn btn-primary shrink-0">
            + Tambah Buku
        </a>
    <?php endif; ?>
</div>

<!-- Filter: kategori, stok, urutan (pencarian via ikon di bilah atas) -->
<div class="filter-bar">
    <form method="GET" action="<?= e(url('/books')) ?>" role="search" class="grid gap-4 md:grid-cols-3">
        <?php if (!empty($filters['q'])): ?>
            <input type="hidden" name="q" value="<?= e((string) $filters['q']) ?>">
        <?php endif; ?>
        <div>
            <label for="category_id" class="form-label">Kategori</label>
            <select id="category_id" name="category_id" onchange="this.form.submit()" class="form-input" aria-label="Filter kategori">
                <option value="0">Semua kategori</option>
                <?php foreach (($categories ?? []) as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= ((int) ($filters['category_id'] ?? 0) === (int) $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="availability" class="form-label">Stok</label>
            <select id="availability" name="availability" onchange="this.form.submit()" class="form-input" aria-label="Filter ketersediaan">
                <option value="">Semua stok</option>
                <option value="available" <?= (($filters['availability'] ?? '') === 'available') ? 'selected' : '' ?>>Tersedia</option>
                <option value="empty" <?= (($filters['availability'] ?? '') === 'empty') ? 'selected' : '' ?>>Habis</option>
            </select>
        </div>
        <div>
            <label for="sort" class="form-label">Urutkan</label>
            <select id="sort" name="sort" onchange="this.form.submit()" class="form-input" aria-label="Urutkan">
                <option value="newest" <?= (($filters['sort'] ?? 'newest') === 'newest') ? 'selected' : '' ?>>Terbaru</option>
                <option value="oldest" <?= (($filters['sort'] ?? '') === 'oldest') ? 'selected' : '' ?>>Terlama</option>
                <option value="title_asc" <?= (($filters['sort'] ?? '') === 'title_asc') ? 'selected' : '' ?>>Judul A-Z</option>
                <option value="title_desc" <?= (($filters['sort'] ?? '') === 'title_desc') ? 'selected' : '' ?>>Judul Z-A</option>
            </select>
        </div>
        <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center md:col-span-3">
            <?php if (!empty($filters['q']) || !empty($filters['category_id']) || !empty($filters['availability'])): ?>
                <a href="<?= e(url('/books')) ?>" class="btn btn-secondary"><?= icon('x', 'h-4 w-4') ?> Reset filter</a>
            <?php endif; ?>
            <p class="flex items-center gap-1.5 text-xs text-gray-400 sm:ml-auto"><?= icon('search', 'h-3.5 w-3.5') ?> Gunakan ikon pencarian di bilah atas atau tekan <kbd class="rounded-md border border-gray-200 bg-gray-50 px-1.5 py-0.5 font-semibold">/</kbd> untuk mencari</p>
        </div>
    </form>
</div>

<?php if (!empty($filters['q'])): ?>
    <?php
    $chipParams = [];
    if (!empty($filters['category_id'])) $chipParams['category_id'] = (int) $filters['category_id'];
    if (!empty($filters['availability'])) $chipParams['availability'] = (string) $filters['availability'];
    if (!empty($filters['sort']) && $filters['sort'] !== 'newest') $chipParams['sort'] = (string) $filters['sort'];
    $chipClear = url('/books') . ($chipParams !== [] ? '?' . http_build_query($chipParams) : '');
    ?>
    <div class="card flex flex-col gap-2.5 px-5 py-4 sm:flex-row sm:items-center sm:justify-between" role="status">
        <p class="flex min-w-0 items-center gap-2 text-sm text-gray-600">
            <?= icon('search', 'h-4 w-4 shrink-0 text-gray-400') ?>
            <span class="truncate">Hasil pencarian untuk <strong class="text-gray-900">“<?= e((string) $filters['q']) ?>”</strong></span>
        </p>
        <a href="<?= e($chipClear) ?>" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-semibold text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"><?= icon('x', 'h-3.5 w-3.5') ?> Hapus kata kunci</a>
    </div>
<?php endif; ?>

<!-- Daftar buku -->
<?php if (empty($books)): ?>
    <div class="empty-state">
        <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-gray-400"><?= icon('book', 'h-8 w-8') ?></span>
        <p class="mt-5 font-semibold text-gray-900">Tidak ada buku yang cocok</p>
        <p class="mt-1.5 max-w-sm text-sm leading-relaxed text-gray-500">
            <?= !empty($filters['q']) ? 'Coba kata kunci yang lebih pendek, misalnya judul atau nama penulisnya saja.' : 'Belum ada buku tersedia di katalog.' ?>
        </p>
        <?php if (isAdmin() && empty($filters['q'])): ?>
            <a href="<?= e(url('/books/create')) ?>" class="btn btn-primary mt-6">Tambah Buku Pertama</a>
        <?php elseif (!empty($filters['q'])): ?>
            <a href="<?= e(url('/books')) ?>" class="btn btn-secondary mt-6">Lihat semua buku</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        <?php foreach ($books as $book): ?>
            <?php $cUrl = book_cover_url($book); ?>
            <article class="card group flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="block focus:outline-none" tabindex="-1" aria-hidden="true">
                    <img src="<?= e($cUrl) ?>" alt="" class="h-48 w-full object-cover" loading="lazy">
                </a>
                <div class="flex flex-1 flex-col p-5">
                    <p class="text-[0.7rem] font-bold uppercase tracking-[0.08em] text-gray-400">
                        <?= e($book['category_name'] ?? 'Umum') ?>
                    </p>
                    <a href="<?= e(url('/books/' . $book['id'])) ?>" class="mt-1.5 focus:outline-none">
                        <h3 class="font-bold leading-snug line-clamp-2 group-hover:text-gray-900">
                            <?= e($book['title']) ?>
                        </h3>
                    </a>
                    <p class="mt-1.5 text-[0.85rem] leading-relaxed text-gray-500 line-clamp-1">
                        <?= e($book['author']) ?><?= !empty($book['year']) ? ' · ' . (int) $book['year'] : '' ?><?= !empty($book['rack']) ? ' · Rak ' . e($book['rack']) : '' ?>
                    </p>
                    <div class="mt-4 flex flex-col gap-3 border-t border-gray-100 pt-4">
                        <span class="badge w-fit <?= (int) $book['stock'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= (int) $book['stock'] > 0 ? icon('check', 'h-3.5 w-3.5') . ' Tersedia · ' . (int) $book['stock'] : icon('clock', 'h-3.5 w-3.5') . ' Habis · bisa reservasi' ?>
                        </span>
                        <div class="flex items-center gap-3">
                            <a href="<?= e(url('/books/' . $book['id'])) ?>" class="btn btn-primary btn-sm flex-1">Lihat &amp; Pinjam &rarr;</a>
                            <?php if (isAdmin()): ?>
                                <a href="<?= e(url('/books/' . $book['id'] . '/edit')) ?>" class="text-xs font-semibold text-gray-500 transition hover:text-gray-900 hover:underline">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

    </div>

    <?= pagination_links(url('/books'), ['q' => $filters['q'] ?? '', 'category_id' => $filters['category_id'] ?? 0, 'availability' => $filters['availability'] ?? '', 'sort' => $filters['sort'] ?? 'newest'], $currentPage ?? 1, $totalPages ?? 1) ?>
<?php endif; ?>
