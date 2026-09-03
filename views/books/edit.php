<div class="mb-8">
    <a href="<?= e(url('/books/' . $book['id'])) ?>" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Detail
    </a>
    <h1 class="text-2xl font-bold tracking-tight">Edit Buku</h1>
    <p class="mt-1 text-gray-500">Perbarui data buku "<?= e($book['title']) ?>".</p>
</div>

<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-8">

        <form method="POST" action="<?= e(url('/books/' . $book['id'] . '/update')) ?>" enctype="multipart/form-data" class="space-y-5" novalidate>
            <?= csrf_field() ?>

            <?php $curCover = cover_url($book['cover'] ?? null); ?>
            <?php if ($curCover): ?>
                <div class="flex items-center gap-4 rounded-xl bg-gray-50 p-4">
                    <img src="<?= e($curCover) ?>" alt="Cover saat ini" class="h-20 w-14 rounded-lg object-cover">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remove_cover" value="1" class="rounded"> Hapus cover saat ini
                    </label>
                </div>
            <?php endif; ?>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="<?= e($book['title']) ?>" required maxlength="255"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="author" class="mb-1.5 block text-sm font-medium text-gray-700">Penulis <span class="text-red-500">*</span></label>
                    <input type="text" id="author" name="author" value="<?= e($book['author']) ?>" required maxlength="100"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="publisher" class="mb-1.5 block text-sm font-medium text-gray-700">Penerbit</label>
                    <input type="text" id="publisher" name="publisher" value="<?= e($book['publisher'] ?? '') ?>" maxlength="100"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="year" class="mb-1.5 block text-sm font-medium text-gray-700">Tahun Terbit</label>
                    <input type="number" id="year" name="year" value="<?= e((string) ($book['year'] ?? '')) ?>" min="1000" max="<?= (int) date('Y') + 1 ?>"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="pages" class="mb-1.5 block text-sm font-medium text-gray-700">Jumlah Halaman</label>
                    <input type="number" id="pages" name="pages" value="<?= e((string) ($book['pages'] ?? '')) ?>" min="1" max="10000"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="language" class="mb-1.5 block text-sm font-medium text-gray-700">Bahasa</label>
                    <input type="text" id="language" name="language" value="<?= e($book['language'] ?? 'Indonesia') ?>" maxlength="30"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="rack" class="mb-1.5 block text-sm font-medium text-gray-700">Lokasi Rak</label>
                    <input type="text" id="rack" name="rack" value="<?= e($book['rack'] ?? '') ?>" maxlength="20"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="isbn" class="mb-1.5 block text-sm font-medium text-gray-700">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="<?= e($book['isbn'] ?? '') ?>" maxlength="20"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div>
                    <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                        <option value="">Pilih kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= (int) $book['category_id'] === (int) $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="stock" class="mb-1.5 block text-sm font-medium text-gray-700">Stok <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" value="<?= (int) $book['stock'] ?>" min="0" max="9999" required
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                </div>

                <div class="sm:col-span-2">
                    <label for="cover" class="mb-1.5 block text-sm font-medium text-gray-700">Ganti Cover (kosongkan jika tidak berubah)</label>
                    <input type="file" id="cover" name="cover" accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:text-white">
                </div>
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea id="description" name="description" rows="4" maxlength="2000"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                    placeholder="Deskripsi singkat tentang buku..."><?= e($book['description'] ?? '') ?></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition">
                    Perbarui Buku
                </button>
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>

        </form>

    </div>
</div>
