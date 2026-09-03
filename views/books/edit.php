<div class="mb-2">
    <a href="<?= e(url('/books/' . $book['id'])) ?>" class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-500 transition hover:bg-gray-100 hover:text-gray-800">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Detail
    </a>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Buku</h1>
        <p class="page-subtitle">Perbarui data buku “<?= e($book['title']) ?>”.</p>
    </div>
</div>

<div class="max-w-3xl">
    <div class="card card-pad">

        <form method="POST" action="<?= e(url('/books/' . $book['id'] . '/update')) ?>" enctype="multipart/form-data" class="space-y-6" novalidate>
            <?= csrf_field() ?>

            <?php $curCover = cover_url($book['cover'] ?? null); ?>
            <?php if ($curCover): ?>
                <div class="flex flex-col gap-4 rounded-2xl bg-gray-50 p-4 sm:flex-row sm:items-center sm:p-5">
                    <img src="<?= e($curCover) ?>" alt="Cover saat ini" class="aspect-[4/3] w-20 shrink-0 rounded-xl bg-gray-100 object-cover ring-1 ring-gray-200">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Cover saat ini</p>
                        <p class="mt-0.5 text-xs text-gray-500">Gambar ini yang tampil di katalog sekarang.</p>
                        <label class="mt-2 flex cursor-pointer items-center gap-2.5 text-sm text-gray-600">
                            <input type="checkbox" name="remove_cover" value="1" class="h-4 w-4 rounded border-gray-300"> Hapus cover saat ini
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="title" class="form-label">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="<?= e($book['title']) ?>" required maxlength="255"
                        class="form-input">
                </div>

                <div>
                    <label for="author" class="form-label">Penulis <span class="text-red-500">*</span></label>
                    <input type="text" id="author" name="author" value="<?= e($book['author']) ?>" required maxlength="100"
                        class="form-input">
                </div>

                <div>
                    <label for="publisher" class="form-label">Penerbit</label>
                    <input type="text" id="publisher" name="publisher" value="<?= e($book['publisher'] ?? '') ?>" maxlength="100"
                        class="form-input">
                </div>

                <div>
                    <label for="year" class="form-label">Tahun Terbit</label>
                    <input type="number" id="year" name="year" value="<?= e((string) ($book['year'] ?? '')) ?>" min="1000" max="<?= (int) date('Y') + 1 ?>"
                        class="form-input">
                </div>

                <div>
                    <label for="pages" class="form-label">Jumlah Halaman</label>
                    <input type="number" id="pages" name="pages" value="<?= e((string) ($book['pages'] ?? '')) ?>" min="1" max="10000"
                        class="form-input">
                </div>

                <div>
                    <label for="language" class="form-label">Bahasa</label>
                    <input type="text" id="language" name="language" value="<?= e($book['language'] ?? 'Indonesia') ?>" maxlength="30"
                        class="form-input">
                </div>

                <div>
                    <label for="rack" class="form-label">Lokasi Rak</label>
                    <input type="text" id="rack" name="rack" value="<?= e($book['rack'] ?? '') ?>" maxlength="20"
                        class="form-input">
                </div>

                <div>
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="<?= e($book['isbn'] ?? '') ?>" maxlength="20"
                        class="form-input">
                </div>

                <div>
                    <label for="category_id" class="form-label">Kategori <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required
                        class="form-input">
                        <option value="">Pilih kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= (int) $book['category_id'] === (int) $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="stock" class="form-label">Stok <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" value="<?= (int) $book['stock'] ?>" min="0" max="9999" required
                        class="form-input">
                    <p class="form-hint">Mengubah stok menyesuaikan jumlah eksemplar otomatis.</p>
                </div>

                <div class="sm:col-span-2">
                    <span class="form-label" id="cover-label">Ganti Cover</span>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div data-preview-wrap class="hidden w-40 shrink-0 overflow-hidden rounded-2xl bg-gray-100 ring-1 ring-gray-200">
                            <img id="cover-preview" alt="Pratinjau cover baru" class="aspect-[4/3] w-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <label for="cover" class="btn btn-secondary cursor-pointer"><?= icon('image', 'h-4 w-4') ?> Pilih gambar baru…</label>
                                <button type="button" id="cover-clear" class="btn btn-ghost btn-sm hidden"><?= icon('x', 'h-3.5 w-3.5') ?> Batalkan</button>
                            </div>
                            <input type="file" id="cover" name="cover" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="sr-only" aria-labelledby="cover-label"
                                data-cover-input data-preview="cover-preview" data-filename="cover-filename" data-error="cover-error" data-clear="cover-clear">
                            <p id="cover-filename" class="mt-2.5 truncate text-xs text-gray-400">Belum ada gambar baru dipilih.</p>
                            <p id="cover-error" class="mt-1.5 hidden text-xs font-semibold text-red-600" role="alert"></p>
                            <p class="form-hint">Kosongkan jika cover tidak berubah. Gambar baru langsung tampil di katalog setelah disimpan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" rows="5" maxlength="2000"
                    class="form-input resize-y"
                    placeholder="Deskripsi singkat tentang buku..."><?= e($book['description'] ?? '') ?></textarea>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row">
                <a href="<?= e(url('/books/' . $book['id'])) ?>" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary sm:min-w-[180px]">
                    Perbarui Buku
                </button>
            </div>

        </form>

    </div>
</div>
