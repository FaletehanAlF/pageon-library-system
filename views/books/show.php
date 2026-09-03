<div class="mb-6">
    <a href="<?= e(url('/books')) ?>" class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-500 transition hover:bg-gray-100 hover:text-gray-800">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Katalog Buku
    </a>
</div>

<div class="mx-auto w-full max-w-5xl">
    <div class="grid items-start gap-6 lg:grid-cols-3 lg:gap-8">

        <!-- Cover -->
        <div class="lg:col-span-1">
            <div class="card card-pad lg:sticky lg:top-24">
                <?php $cUrl = book_cover_url($book); ?>
                <img src="<?= e($cUrl) ?>" alt="Cover <?= e($book['title']) ?>" class="aspect-[4/3] w-full rounded-2xl bg-gray-100 object-cover ring-1 ring-gray-100">
                <div class="mt-5 flex flex-wrap gap-2">
                    <?= stock_badge((int) ($book['stock'] ?? 0)) ?>
                    <?php if (($rating['count'] ?? 0) > 0): ?>
                        <span class="badge bg-amber-100 text-amber-800"><?= stars((float) ($rating['avg'] ?? 0), 'h-3.5 w-3.5') ?> <?= e((string) $rating['avg']) ?> (<?= (int) $rating['count'] ?>)</span>
                    <?php endif; ?>
                </div>
                <?php if (isAuth()): ?>
                    <form method="POST" action="<?= e(url('/wishlist/toggle')) ?>" class="mt-4 border-t border-gray-100 pt-4 text-center">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                        <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-[0.83rem] font-semibold text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"><?= icon(!empty($inWishlist) ? 'heart-solid' : 'heart', 'h-4 w-4') ?> <?= !empty($inWishlist) ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail -->
        <div class="lg:col-span-2">
            <div class="card card-pad">

                <p class="text-[0.72rem] font-bold uppercase tracking-[0.1em] text-gray-400">
                    <?= e($book['category_name'] ?? 'Umum') ?>
                </p>

                <h1 class="mt-2 text-2xl font-extrabold leading-tight tracking-tight sm:text-[1.7rem]">
                    <?= e($book['title']) ?>
                </h1>

                <p class="mt-2.5 text-[0.95rem] text-gray-600">
                    oleh <span class="font-semibold text-gray-900"><?= e($book['author']) ?></span>
                </p>

                <dl class="mt-7 grid grid-cols-2 gap-x-6 gap-y-5 text-sm sm:grid-cols-3">
                    <?php if (!empty($book['publisher'])): ?>
                        <div><dt class="text-[0.7rem] font-bold uppercase tracking-wider text-gray-400">Penerbit</dt><dd class="mt-1 font-semibold text-gray-900"><?= e($book['publisher']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['year'])): ?>
                        <div><dt class="text-[0.7rem] font-bold uppercase tracking-wider text-gray-400">Tahun</dt><dd class="mt-1 font-semibold text-gray-900"><?= (int) $book['year'] ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['pages'])): ?>
                        <div><dt class="text-[0.7rem] font-bold uppercase tracking-wider text-gray-400">Halaman</dt><dd class="mt-1 font-semibold text-gray-900"><?= (int) $book['pages'] ?> hlm</dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['language'])): ?>
                        <div><dt class="text-[0.7rem] font-bold uppercase tracking-wider text-gray-400">Bahasa</dt><dd class="mt-1 font-semibold text-gray-900"><?= e($book['language']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['rack'])): ?>
                        <div><dt class="text-[0.7rem] font-bold uppercase tracking-wider text-gray-400">Rak</dt><dd class="mt-1 font-mono font-semibold text-gray-900"><?= e($book['rack']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($book['isbn'])): ?>
                        <div><dt class="text-[0.7rem] font-bold uppercase tracking-wider text-gray-400">ISBN</dt><dd class="mt-1 font-mono text-[0.83rem] font-semibold text-gray-900"><?= e($book['isbn']) ?></dd></div>
                    <?php endif; ?>
                </dl>

                <?php if (!empty($book['description'])): ?>
                    <div class="mt-7 rounded-2xl bg-gray-50/80 p-5">
                        <h3 class="text-sm font-bold text-gray-900">Deskripsi</h3>
                        <p class="mt-2 text-[0.9rem] leading-relaxed text-gray-600">
                            <?= nl2br(e($book['description'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="mt-7 flex flex-col gap-3 border-t border-gray-100 pt-6 sm:pt-7">
                    <?php if (isAuth()): ?>
                        <?php if (($hasBorrowed ?? false) === true): ?>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <span class="badge bg-amber-100 px-4 py-2.5 text-sm text-amber-800">Sedang Anda pinjam</span>
                                <a href="<?= e(url('/my-borrowings')) ?>" class="btn btn-secondary">Lihat Pinjaman</a>
                            </div>
                        <?php elseif ((int) $book['stock'] > 0): ?>
                            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                                <form method="POST" action="<?= e(url('/borrowings')) ?>" onsubmit="return confirm('Pinjam &quot;<?= e(addslashes($book['title'])) ?>&quot;? Kembalikan sebelum <?= e(format_date(date('Y-m-d', strtotime('+' . max(1, (int) ($loanDays ?? 7)) . ' days')))) ?> agar GRATIS — telat kena denda progresif mulai <?= e(format_rupiah((int) ($finePerDay ?? 1000))) ?>/hari.')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto"><?= icon('inbox', 'h-5 w-5') ?> Pinjam Sekarang — Gratis</button>
                                </form>
                            </div>
                            <p class="flex items-start gap-2 rounded-xl bg-green-50/70 p-3.5 text-xs leading-relaxed text-gray-600 ring-1 ring-green-100"><?= icon('check', 'h-4 w-4 mt-0.5 shrink-0 text-green-600') ?><span>Gratis jika dikembalikan sebelum <strong><?= e(format_date(date('Y-m-d', strtotime('+' . max(1, (int) ($loanDays ?? 7)) . ' days')))) ?></strong>. Telat = denda progresif mulai <?= e(format_rupiah((int) ($finePerDay ?? 1000))) ?>/hari<?php if ((int) ($fineIncrement ?? 0) > 0): ?>, naik <?= e(format_rupiah((int) $fineIncrement)) ?> tiap harinya<?php endif; ?>.</span></p>
                        <?php else: ?>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <?php if (!empty($hasReservation)): ?>
                                    <span class="badge bg-blue-100 px-4 py-2.5 text-sm text-blue-800">Anda dalam antrean (<?= (int) ($queueCount ?? 0) ?> menunggu)</span>
                                    <a href="<?= e(url('/reservations')) ?>" class="btn btn-secondary">Lihat Antrean</a>
                                <?php else: ?>
                                    <form method="POST" action="<?= e(url('/reservations')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                                        <button class="btn btn-primary">Reservasi (Antre <?= (int) ($queueCount ?? 0) ?>)</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isAdmin()): ?>
                        <div class="flex flex-col gap-2.5 sm:flex-row">
                            <a href="<?= e(url('/books/' . $book['id'] . '/edit')) ?>" class="btn btn-secondary">Edit Buku</a>
                            <form method="POST" action="<?= e(url('/books/' . $book['id'] . '/delete')) ?>" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger w-full sm:w-auto">Hapus</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isAdmin() && !empty($copies)): ?>
                    <div class="mt-7 rounded-2xl bg-gray-50/80 p-5">
                        <h4 class="text-sm font-bold">Eksemplar (<?= count($copies) ?>)</h4>
                        <div class="mt-3 grid gap-2 font-mono text-xs">
                            <?php foreach ($copies as $c): ?>
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-3.5 py-2.5">
                                    <span class="truncate"><?= e($c['barcode']) ?></span>
                                    <span class="shrink-0 font-sans font-semibold <?= $c['status'] === 'available' ? 'text-green-600' : 'text-amber-600' ?>"><?= e($c['status']) ?> · <?= e($c['condition']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Ulasan -->
            <div class="card card-pad mt-6">
                <h3 class="text-[1.05rem] font-bold tracking-tight">Ulasan <span class="font-medium text-gray-400">(<?= (int) ($rating['count'] ?? 0) ?>)</span></h3>
                <?php if (($rating['count'] ?? 0) > 0): ?>
                    <p class="mt-2 flex items-center gap-2 text-sm text-gray-500"><?= stars((float) ($rating['avg'] ?? 0)) ?> <span class="font-semibold text-gray-700"><?= e((string) $rating['avg']) ?>/5</span></p>
                <?php endif; ?>
                <?php if (isAuth()): ?>
                    <form method="POST" action="<?= e(url('/reviews')) ?>" class="mb-7 mt-5 space-y-4 rounded-2xl bg-gray-50/80 p-5">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                        <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                            <label for="rating" class="text-sm font-semibold">Rating Anda:</label>
                            <select id="rating" name="rating" class="form-input !w-auto">
                                <?php foreach ([5 => 'Sangat bagus', 4 => 'Bagus', 3 => 'Cukup', 2 => 'Kurang', 1 => 'Buruk'] as $i => $label): ?>
                                    <option value="<?= $i ?>" <?= ((int) ($userReview['rating'] ?? 0) === $i) ? 'selected' : '' ?>><?= $i ?> — <?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <textarea id="comment" name="comment" rows="3" maxlength="1000" placeholder="Ceritakan pengalaman membaca buku ini…" class="form-input resize-y"><?= e($userReview['comment'] ?? '') ?></textarea>
                        <button class="btn btn-primary btn-sm !px-5 !py-2.5 !text-sm">Kirim Ulasan</button>
                    </form>
                <?php endif; ?>
                <div class="space-y-5">
                    <?php if (empty($reviews)): ?>
                        <p class="rounded-2xl bg-gray-50/80 p-5 text-center text-sm text-gray-500">Belum ada ulasan. Jadilah yang pertama memberi penilaian.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $rv): ?>
                            <div class="border-b border-gray-100 pb-5 last:border-0 last:pb-0">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="flex flex-wrap items-center gap-2 text-sm font-semibold"><?= e($rv['user_name']) ?> <?= stars((int) $rv['rating'], 'h-3.5 w-3.5') ?></p>
                                    <?php if (isAdmin() || (isAuth() && (int) $rv['user_id'] === (int) Session::get('user_id'))): ?>
                                        <form method="POST" action="<?= e(url('/reviews/' . $rv['id'] . '/delete')) ?>" onsubmit="return confirm('Hapus ulasan?')">
                                            <?= csrf_field() ?>
                                            <button class="rounded-lg px-2 py-1 text-xs font-medium text-red-500 transition hover:bg-red-50 hover:underline">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($rv['comment'])): ?>
                                    <p class="mt-2 text-[0.9rem] leading-relaxed text-gray-600"><?= nl2br(e($rv['comment'])) ?></p>
                                <?php endif; ?>
                                <p class="mt-1.5 text-xs text-gray-400"><?= e(time_ago($rv['created_at'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($related)): ?>
                <div class="mt-6">
                    <h3 class="mb-4 font-bold tracking-tight">Buku Terkait</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <?php foreach ($related as $rel): ?>
                            <a href="<?= e(url('/books/' . $rel['id'])) ?>" class="card card-pad-sm transition hover:shadow-sm">
                                <p class="truncate font-semibold leading-snug"><?= e($rel['title']) ?></p>
                                <p class="mt-1 text-[0.83rem] text-gray-500"><?= e($rel['author']) ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>
