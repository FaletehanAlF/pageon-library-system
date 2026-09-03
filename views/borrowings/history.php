<div class="page-header">
    <div>
        <h1 class="page-title">Riwayat Pinjaman</h1>
        <p class="page-subtitle">Semua buku yang sudah Anda kembalikan. Total <span class="font-bold text-gray-700"><?= (int) ($total ?? 0) ?></span> pinjaman selesai.</p>
    </div>
</div>

<div class="card flex gap-1.5 p-1.5 text-sm font-semibold">
    <a href="<?= e(url('/my-borrowings')) ?>" class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-3 text-center transition <?= ($tab ?? '') === 'aktif' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' ?>"><?= icon('inbox', 'h-4 w-4') ?> Sedang Dipinjam</a>
    <a href="<?= e(url('/riwayat')) ?>" class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-3 text-center transition <?= ($tab ?? '') === 'riwayat' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' ?>"><?= icon('archive', 'h-4 w-4') ?> Riwayat</a>
</div>

<div class="grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4">
    <div class="card card-pad-sm">
        <p class="text-sm font-medium text-gray-500">Tepat waktu</p>
        <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-green-700"><?= (int) ($onTime ?? 0) ?></p>
    </div>
    <div class="card card-pad-sm">
        <p class="text-sm font-medium text-gray-500">Pernah telat</p>
        <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-amber-700"><?= (int) ($late ?? 0) ?></p>
    </div>
    <div class="card card-pad-sm">
        <p class="text-sm font-medium text-gray-500">Total denda tercatat</p>
        <p class="mt-1.5 text-2xl font-extrabold tracking-tight"><?= e(format_rupiah((int) ($fineTotal ?? 0))) ?></p>
    </div>
    <div class="card card-pad-sm <?= ((int) ($fineUnpaid ?? 0) > 0) ? '!border-red-200 !bg-red-50/70' : '!border-green-200 !bg-green-50/70' ?>">
        <p class="text-sm font-medium <?= ((int) ($fineUnpaid ?? 0) > 0) ? 'text-red-700' : 'text-green-700' ?>">Denda belum lunas</p>
        <p class="mt-1.5 text-2xl font-extrabold tracking-tight <?= ((int) ($fineUnpaid ?? 0) > 0) ? 'text-red-800' : 'text-green-800' ?>"><?= e(format_rupiah((int) ($fineUnpaid ?? 0))) ?></p>
        <?php if ((int) ($fineUnpaid ?? 0) > 0): ?>
            <a href="<?= e(url('/fines')) ?>" class="mt-2 inline-block text-xs font-bold text-red-700 underline underline-offset-2">Bayar sekarang &rarr;</a>
        <?php endif; ?>
    </div>
</div>

<div class="table-shell">
    <div class="table-scroll">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Dikembalikan</th>
                    <th>Keterlambatan</th>
                    <th>Denda</th>
                    <th class="!text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="!px-6 !py-14 text-center">
                            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-gray-400"><?= icon('archive', 'h-8 w-8') ?></span>
                            <p class="mt-4 font-bold text-gray-900">Belum ada riwayat</p>
                            <p class="mx-auto mt-1.5 max-w-xs text-sm leading-relaxed text-gray-500">Buku yang sudah Anda kembalikan akan tercatat di sini.</p>
                            <a href="<?= e(url('/books')) ?>" class="btn btn-primary mt-5">Pinjam Buku Pertama &rarr;</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td>
                                <div class="min-w-[180px] font-semibold leading-snug text-gray-900"><?= e($r['book_title']) ?></div>
                                <div class="mt-0.5 text-xs text-gray-500"><?= e($r['book_author']) ?></div>
                            </td>
                            <td class="whitespace-nowrap text-gray-600"><?= e(format_date((string) $r['borrow_date'])) ?></td>
                            <td class="whitespace-nowrap text-gray-600">
                                <?= e(format_date((string) ($r['return_date'] ?? $r['due_date']))) ?>
                                <div class="mt-0.5 text-xs text-gray-400">batas <?= e(format_date((string) $r['due_date'])) ?></div>
                            </td>
                            <td>
                                <?php if ((int) ($r['late_days'] ?? 0) > 0): ?>
                                    <span class="badge bg-red-100 text-red-700">Telat <?= (int) $r['late_days'] ?> hari</span>
                                <?php else: ?>
                                    <span class="badge bg-green-100 text-green-700">Tepat waktu</span>
                                <?php endif; ?>
                            </td>
                            <td class="whitespace-nowrap">
                                <?php if ((int) ($r['fine_total'] ?? 0) > 0): ?>
                                    <span class="font-bold <?= (int) ($r['fine_unpaid'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-700' ?>"><?= e(format_rupiah((int) $r['fine_total'])) ?></span>
                                    <?php if ((int) ($r['fine_unpaid'] ?? 0) > 0): ?>
                                        <div class="mt-0.5 text-xs font-semibold text-red-500">belum lunas</div>
                                    <?php else: ?>
                                        <div class="mt-0.5 flex items-center gap-1 text-xs font-semibold text-green-600"><?= icon('check', 'h-3 w-3') ?> lunas</div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-300">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <a href="<?= e(url('/borrowings/' . $r['id'] . '/receipt')) ?>" class="rounded-lg px-2 py-1.5 text-xs font-semibold text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 hover:underline">Struk</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div>
    <a href="<?= e(url('/my-borrowings')) ?>" class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">&larr; Kembali ke Pinjaman Aktif</a>
</div>
