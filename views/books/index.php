<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Cari &amp; Pinjam Buku 📖</h1>
        <p class="mt-1 text-gray-500">Klik salah satu buku untuk melihat detail, lalu tekan <strong>Pinjam</strong>.<?= isset($totalCount) ? ' Ada <span class="font-semibold text-gray-700">' . (int) $totalCount . '</span> buku.' : '' ?></p>
    </div>
    <?php if (isAdmin()): ?>
        <a href="<?= e(url('/books/create')) ?>" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition">
            + Tambah Buku
        </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4">
    <form method="GET" action="<?= e(url('/books')) ?>" role="search" class="grid gap-3 md:grid-cols-12">
        <div class="relative md:col-span-5">
            <svg class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="search"
                name="q"
                value="<?= e($filters['q'] ?? ($keyword ?? '')) ?>"
                placeholder="Cari judul, penulis, penerbit, kategori..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-11 pr-4 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                aria-label="Cari buku"
            >
        </div>
        <div class="md:col-span-3">
            <select name="category_id" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none focus:border-gray-900 focus:bg-white" aria-label="Filter kategori">
                <option value="0">Semua kategori</option>
                <?php foreach (($categories ?? []) as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= ((int) ($filters['category_id'] ?? 0) === (int) $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2">
            <select name="availability" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none focus:border-gray-900 focus:bg-white" aria-label="Filter ketersediaan">
                <option value="">Semua stok</option>
                <option value="available" <?= (($filters['availability'] ?? '') === 'available') ? 'selected' : '' ?>>Tersedia</option>
                <option value="empty" <?= (($filters['availability'] ?? '') === 'empty') ? 'selected' : '' ?>>Habis</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <select name="sort" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none focus:border-gray-900 focus:bg-white" aria-label="Urutkan">
                <option value="newest" <?= (($filters['sort'] ?? 'newest') === 'newest') ? 'selected' : '' ?>>Terbaru</option>
                <option value="oldest" <?= (($filters['sort'] ?? '') === 'oldest') ? 'selected' : '' ?>>Terlama</option>
                <option value="title_asc" <?= (($filters['sort'] ?? '') === 'title_asc') ? 'selected' : '' ?>>Judul A-Z</option>
                <option value="title_desc" <?= (($filters['sort'] ?? '') === 'title_desc') ? 'selected' : '' ?>>Judul Z-A</option>
            </select>
        </div>
        <div class="md:col-span-12 flex items-center gap-2">
            <button type="submit" class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Filter</button>
            <?php if (!empty($filters['q']) || !empty($filters['category_id']) || !empty($filters['availability'])): ?>
                <a href="<?= e(url('/books')) ?>" class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm text-gray-600 hover:bg-gray-50">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Books Grid -->
<?php if (empty($books)): ?>
    <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center">
        <div class="mx-auto max-w-sm">
            <p class="text-gray-500">
                <?= !empty($filters['q']) ? '😕 Tidak ketemu. Coba kata lain yang lebih pendek, misalnya judul atau nama penulisnya saja.' : '📭 Belum ada buku tersedia.' ?>
            </p>
            <?php if (isAdmin() && empty($filters['q'])): ?>
                <a href="<?= e(url('/books/create')) ?>" class="mt-4 inline-flex rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Tambah Buku Pertama</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        <?php foreach ($books as $book): ?>
            <?php $cUrl = book_cover_url($book); ?>
            <div class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-md">
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="block focus:outline-none">
                    <img src="<?= e($cUrl) ?>" alt="Cover <?= e($book['title']) ?>" class="h-44 w-full object-cover" loading="lazy">
                </a>
                <div class="flex flex-1 flex-col p-4">
                    <p class="mb-1 text-xs font-medium text-gray-400 uppercase tracking-wider">
                        <?= e($book['category_name'] ?? 'Umum') ?>
                    </p>
                    <a href="<?= e(url('/books/' . $book['id'])) ?>" class="focus:outline-none">
                        <h3 class="font-semibold line-clamp-1 group-hover:text-blue-600 transition">
                            <?= e($book['title']) ?>
                        </h3>
                    </a>
                    <p class="mt-1 text-sm text-gray-500 line-clamp-1">
                        <?= e($book['author']) ?><?= !empty($book['year']) ? ' · ' . (int) $book['year'] : '' ?><?= !empty($book['rack']) ? ' · Rak ' . e($book['rack']) : '' ?>
                    </p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="inline-block rounded-full <?= (int) $book['stock'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> px-2.5 py-1 text-xs font-medium">
                            <?= (int) $book['stock'] > 0 ? '✅ Bisa dipinjam' : '⏳ Habis — bisa reservasi' ?>
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="<?= e(url('/books/' . $book['id'])) ?>" class="rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-700">Lihat &amp; Pinjam →</a>
                            <?php if (isAdmin()): ?>
                                <span class="text-gray-200">·</span>
                                <a href="<?= e(url('/books/' . $book['id'] . '/edit')) ?>" class="text-xs font-medium text-gray-500 hover:text-gray-900">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <?= pagination_links(url('/books'), ['q' => $filters['q'] ?? '', 'category_id' => $filters['category_id'] ?? 0, 'availability' => $filters['availability'] ?? '', 'sort' => $filters['sort'] ?? 'newest'], $currentPage ?? 1, $totalPages ?? 1) ?>
<?php endif; ?>
