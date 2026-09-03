<div class="mb-8">
    <a href="<?= e(url('/books')) ?>" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Buku
    </a>
</div>

<div class="max-w-5xl">
    <div class="grid gap-6 lg:grid-cols-3">

        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <?php $cUrl = cover_url($book['cover'] ?? null); ?>
                <?php if ($cUrl): ?>
                    <img src="<?= e($cUrl) ?>" alt="Cover <?= e($book['title']) ?>" class="h-64 w-full rounded-xl object-cover">
                <?php else: ?>
                    <div class="flex h-64 items-center justify-center rounded-xl bg-gradient-to-br from-gray-50 to-gray-100" aria-hidden="true">
                        <span class="text-7xl">📖</span>
                    </div>
                <?php endif; ?>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="inline-block rounded-full <?= (int) $book['stock'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> px-3 py-1 text-sm font-medium">
                        <?= (int) $book['stock'] > 0 ? 'Tersedia (Stok: ' . (int) $book['stock'] . ')' : 'Tidak Tersedia' ?>
                    </span>
                    <?php if (($rating['count'] ?? 0) > 0): ?>
                        <span class="inline-block rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800">★ <?= e((string) $rating['avg']) ?> (<?= (int) $rating['count'] ?>)</span>
                    <?php endif; ?>
                </div>
                <?php if (isAuth()): ?>
                    <form method="POST" action="<?= e(url('/wishlist/toggle')) ?>" class="mt-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                        <button class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium hover:bg-gray-50"><?= !empty($inWishlist) ? '♥ Hapus dari Wishlist' : '♡ Tambah ke Wishlist' ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-8">

                <p class="mb-2 text-xs font-medium uppercase tracking-wider text-gray-400">
                    <?= e($book['category_name'] ?? 'Umum') ?>
                </p>

                <h1 class="text-2xl font-bold tracking-tight">
                    <?= e($book['title']) ?>
                </h1>

                <p class="mt-2 text-gray-600">
                    oleh <?= e($book['author']) ?>
                </p>

                <dl class="mt-6 grid grid-cols-2 gap-4 text-sm">
                    <?php if (!empty($book['publisher'])): ?>
                        <div><dt class="text-gray-400 text-xs uppercase">Penerbit</dt><dd class="font-medium"><?= e($book['publisher']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['year'])): ?>
                        <div><dt class="text-gray-400 text-xs uppercase">Tahun</dt><dd class="font-medium"><?= (int) $book['year'] ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['pages'])): ?>
                        <div><dt class="text-gray-400 text-xs uppercase">Halaman</dt><dd class="font-medium"><?= (int) $book['pages'] ?> hlm</dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['language'])): ?>
                        <div><dt class="text-gray-400 text-xs uppercase">Bahasa</dt><dd class="font-medium"><?= e($book['language']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['rack'])): ?>
                        <div><dt class="text-gray-400 text-xs uppercase">Rak</dt><dd class="font-medium font-mono"><?= e($book['rack']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['isbn'])): ?>
                        <div><dt class="text-gray-400 text-xs uppercase">ISBN</dt><dd class="font-medium font-mono"><?= e($book['isbn']) ?></dd></div>
                    <?php endif; ?>
                </dl>

                <?php if (!empty($book['description'])): ?>
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <?= nl2br(e($book['description'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6">
                    <?php if (isAuth()): ?>
                        <?php if (($hasBorrowed ?? false) === true): ?>
                            <span class="inline-flex items-center rounded-xl bg-amber-100 px-6 py-3 text-sm font-medium text-amber-800">Sedang Anda pinjam</span>
                            <a href="<?= e(url('/my-borrowings')) ?>" class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Lihat Peminjaman</a>
                        <?php elseif ((int) $book['stock'] > 0): ?>
                            <form method="POST" action="<?= e(url('/borrowings')) ?>" class="inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                <button type="submit" class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white hover:bg-gray-800 transition">Pinjam Langsung</button>
                            </form>
                            <?php if (cart_has((int) $book['id'])): ?>
                                <a href="<?= e(url('/cart')) ?>" class="rounded-xl border border-gray-900 px-6 py-3 text-sm font-medium text-gray-900 hover:bg-gray-50 transition">Di Keranjang — Lihat</a>
                            <?php else: ?>
                                <form method="POST" action="<?= e(url('/cart/add')) ?>" class="inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                    <button type="submit" class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">+ Keranjang</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if (!empty($hasReservation)): ?>
                                <span class="inline-flex rounded-xl bg-blue-100 px-6 py-3 text-sm font-medium text-blue-800">Anda dalam antrean (<?= (int) ($queueCount ?? 0) ?> menunggu)</span>
                                <a href="<?= e(url('/reservations')) ?>" class="rounded-xl border px-6 py-3 text-sm hover:bg-gray-50">Lihat Antrean</a>
                            <?php else: ?>
                                <form method="POST" action="<?= e(url('/reservations')) ?>" class="inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                    <button class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700">Reservasi (Antre <?= (int) ($queueCount ?? 0) ?>)</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isAdmin()): ?>
                        <a href="<?= e(url('/books/' . $book['id'] . '/edit')) ?>" class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Edit</a>
                        <form method="POST" action="<?= e(url('/books/' . $book['id'] . '/delete')) ?>" onsubmit="return confirm('Yakin ingin menghapus buku ini?')" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="rounded-xl border border-red-200 px-6 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition">Hapus</button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if (isAdmin() && !empty($copies)): ?>
                    <div class="mt-6 rounded-xl bg-gray-50 p-4">
                        <h4 class="text-sm font-semibold mb-2">Eksemplar (<?= count($copies) ?>)</h4>
                        <div class="grid gap-2 text-xs font-mono">
                            <?php foreach ($copies as $c): ?>
                                <div class="flex justify-between rounded-lg bg-white px-3 py-2 border">
                                    <span><?= e($c['barcode']) ?></span>
                                    <span class="<?= $c['status'] === 'available' ? 'text-green-600' : 'text-amber-600' ?>"><?= e($c['status']) ?> · <?= e($c['condition']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Reviews -->
            <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-8">
                <h3 class="font-semibold mb-1">Ulasan (<?= (int) ($rating['count'] ?? 0) ?>)</h3>
                <?php if (($rating['count'] ?? 0) > 0): ?>
                    <p class="text-sm text-gray-500 mb-4">Rata-rata ★ <?= e((string) $rating['avg']) ?>/5</p>
                <?php endif; ?>
                <?php if (isAuth()): ?>
                    <form method="POST" action="<?= e(url('/reviews')) ?>" class="mb-6 rounded-xl bg-gray-50 p-4 space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium">Rating Anda:</label>
                            <select name="rating" class="rounded-lg border px-3 py-2 text-sm">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i ?>" <?= ((int) ($userReview['rating'] ?? 0) === $i) ? 'selected' : '' ?>><?= $i ?> ★</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <textarea name="comment" rows="2" maxlength="1000" placeholder="Tulis ulasan..." class="w-full rounded-xl border px-4 py-3 text-sm"><?= e($userReview['comment'] ?? '') ?></textarea>
                        <button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm text-white">Kirim Ulasan</button>
                    </form>
                <?php endif; ?>
                <div class="space-y-4">
                    <?php if (empty($reviews)): ?>
                        <p class="text-sm text-gray-500">Belum ada ulasan. Jadilah yang pertama.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $rv): ?>
                            <div class="border-b border-gray-100 pb-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium"><?= e($rv['user_name']) ?> <span class="text-amber-500"><?= str_repeat('★', (int) $rv['rating']) ?></span></p>
                                    <?php if (isAdmin() || (isAuth() && (int) $rv['user_id'] === (int) Session::get('user_id'))): ?>
                                        <form method="POST" action="<?= e(url('/reviews/' . $rv['id'] . '/delete')) ?>" onsubmit="return confirm('Hapus ulasan?')">
                                            <?= csrf_field() ?>
                                            <button class="text-xs text-red-500 hover:underline">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($rv['comment'])): ?>
                                    <p class="mt-1 text-sm text-gray-600"><?= nl2br(e($rv['comment'])) ?></p>
                                <?php endif; ?>
                                <p class="mt-1 text-xs text-gray-400"><?= e(time_ago($rv['created_at'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($related)): ?>
                <div class="mt-6">
                    <h3 class="font-semibold mb-3">Buku Terkait</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <?php foreach ($related as $rel): ?>
                            <a href="<?= e(url('/books/' . $rel['id'])) ?>" class="rounded-2xl border bg-white p-4 hover:shadow-sm">
                                <p class="font-medium text-sm truncate"><?= e($rel['title']) ?></p>
                                <p class="text-xs text-gray-500"><?= e($rel['author']) ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>
