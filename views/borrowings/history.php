<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">Pinjaman Saya</h1>
    <p class="mt-1 text-gray-500">Semua buku yang sudah Anda kembalikan. Total <span class="font-semibold text-gray-700"><?= (int) ($total ?? 0) ?></span> pinjaman selesai.</p>
</div>

<div class="mb-6 flex gap-2 rounded-2xl border border-gray-200 bg-white p-2 text-sm font-medium">
    <a href="<?= e(url('/my-borrowings')) ?>" class="flex-1 rounded-xl px-4 py-2.5 text-center <?= ($tab ?? '') === 'aktif' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50' ?>">📥 Sedang Dipinjam</a>
    <a href="<?= e(url('/riwayat')) ?>" class="flex-1 rounded-xl px-4 py-2.5 text-center <?= ($tab ?? '') === 'riwayat' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50' ?>">📜 Riwayat</a>
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <p class="text-sm text-gray-500">Tepat waktu ✅</p>
        <p class="text-2xl font-bold text-green-700"><?= (int) ($onTime ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <p class="text-sm text-gray-500">Pernah telat ⏰</p>
        <p class="text-2xl font-bold text-amber-700"><?= (int) ($late ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <p class="text-sm text-gray-500">Total denda tercatat</p>
        <p class="text-2xl font-bold"><?= e(format_rupiah((int) ($fineTotal ?? 0))) ?></p>
    </div>
    <div class="rounded-2xl border <?= ((int) ($fineUnpaid ?? 0) > 0) ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' ?> p-5">
        <p class="text-sm <?= ((int) ($fineUnpaid ?? 0) > 0) ? 'text-red-700' : 'text-green-700' ?>">Denda belum lunas</p>
        <p class="text-2xl font-bold <?= ((int) ($fineUnpaid ?? 0) > 0) ? 'text-red-800' : 'text-green-800' ?>"><?= e(format_rupiah((int) ($fineUnpaid ?? 0))) ?></p>
        <?php if ((int) ($fineUnpaid ?? 0) > 0): ?>
            <a href="<?= e(url('/fines')) ?>" class="mt-1 inline-block text-xs font-medium text-red-700 underline">Bayar sekarang →</a>
        <?php endif; ?>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Buku</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Tgl Pinjam</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Dikembalikan</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Keterlambatan</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Denda</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="mx-auto max-w-xs">
                                <div class="text-5xl">📜</div>
                                <p class="mt-4 font-medium">Belum ada riwayat.</p>
                                <p class="mt-1 text-sm text-gray-500">Buku yang sudah Anda kembalikan akan tercatat di sini.</p>
                                <a href="<?= e(url('/books')) ?>" class="mt-4 inline-flex rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Pinjam Buku Pertama →</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <div class="font-medium"><?= e($r['book_title']) ?></div>
                                <div class="text-xs text-gray-500"><?= e($r['book_author']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?= e(format_date((string) $r['borrow_date'])) ?></td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= e(format_date((string) ($r['return_date'] ?? $r['due_date']))) ?>
                                <div class="text-xs text-gray-400">batas <?= e(format_date((string) $r['due_date'])) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ((int) ($r['late_days'] ?? 0) > 0): ?>
                                    <span class="inline-block rounded-full bg-red-100 text-red-700 px-2.5 py-1 text-xs font-medium">Telat <?= (int) $r['late_days'] ?> hari</span>
                                <?php else: ?>
                                    <span class="inline-block rounded-full bg-green-100 text-green-700 px-2.5 py-1 text-xs font-medium">Tepat waktu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ((int) ($r['fine_total'] ?? 0) > 0): ?>
                                    <span class="font-semibold <?= (int) ($r['fine_unpaid'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-700' ?>"><?= e(format_rupiah((int) $r['fine_total'])) ?></span>
                                    <?php if ((int) ($r['fine_unpaid'] ?? 0) > 0): ?>
                                        <div class="text-xs text-red-500">belum lunas</div>
                                    <?php else: ?>
                                        <div class="text-xs text-green-600">lunas ✓</div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= e(url('/borrowings/' . $r['id'] . '/receipt')) ?>" class="text-xs font-medium text-gray-500 hover:text-gray-900 underline">Struk</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="<?= e(url('/my-borrowings')) ?>" class="text-sm font-medium text-gray-600 hover:text-gray-900 underline">← Kembali ke Pinjaman Aktif</a>
</div>
