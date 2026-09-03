<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">
        <?= $page === 'borrowings' ? 'Kelola Peminjaman' : 'Peminjaman Saya' ?>
    </h1>
    <p class="mt-1 text-gray-500">
        <?= $page === 'borrowings' ? 'Daftar semua peminjaman buku.' : 'Daftar peminjaman buku Anda.' ?>
        <?php if (isset($finePerDay)): ?>
            · Denda <span class="font-semibold text-gray-700"><?= e(format_rupiah((int) $finePerDay)) ?>/hari</span>
        <?php endif; ?>
    </p>
</div>

<?php
$totalFine = 0;
foreach (($borrowings ?? []) as $tmp) { $totalFine += (int) ($tmp['fine'] ?? 0); }
?>
<?php if ($totalFine > 0): ?>
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        Total denda berjalan: <span class="font-bold"><?= e(format_rupiah($totalFine)) ?></span>. Segera kembalikan buku terlambat.
    </div>
<?php endif; ?>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Buku</th>
                    <?php if (isAdmin()): ?>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Peminjam</th>
                    <?php endif; ?>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Tgl Pinjam</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Batas Kembali</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Denda</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($borrowings)): ?>
                    <tr>
                        <td colspan="<?= isAdmin() ? 7 : 6 ?>" class="px-6 py-12 text-center">
                            <div class="mx-auto max-w-xs">
                                <p class="text-gray-500">Belum ada data peminjaman.</p>
                                <a href="<?= e(url('/books')) ?>" class="mt-3 inline-block text-sm font-medium text-gray-900 underline">Jelajahi Buku &rarr;</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($borrowings as $b): ?>
                        <?php
                        $isOverdue = $b['status'] === 'borrowed' && days_overdue((string) $b['due_date']) > 0;
                        ?>
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <div class="font-medium"><?= e($b['book_title']) ?></div>
                                <div class="text-xs text-gray-500"><?= e($b['book_author']) ?></div>
                                <?php if ((int) ($b['renew_count'] ?? 0) > 0): ?>
                                    <div class="mt-1 text-xs text-blue-600">Perpanjang <?= (int) $b['renew_count'] ?>x</div>
                                <?php endif; ?>
                            </td>
                            <?php if (isAdmin()): ?>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700"><?= e($b['user_name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($b['user_email']) ?></div>
                                </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 text-gray-600">
                                <?= e(format_date((string) $b['borrow_date'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="<?= $isOverdue ? 'text-red-600 font-medium' : 'text-gray-600' ?>">
                                    <?= e(format_date((string) $b['due_date'])) ?>
                                </span>
                                <?php if ($isOverdue): ?>
                                    <div class="text-xs text-red-500"><?= (int) ($b['days_overdue'] ?? 0) ?> hari telat</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ((int) ($b['fine'] ?? 0) > 0): ?>
                                    <span class="font-semibold text-red-600"><?= e(format_rupiah((int) $b['fine'])) ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($b['status'] === 'borrowed'): ?>
                                    <?php if ($isOverdue): ?>
                                        <span class="inline-block rounded-full bg-red-100 text-red-700 px-2.5 py-1 text-xs font-medium">
                                            Terlambat
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block rounded-full bg-amber-100 text-amber-700 px-2.5 py-1 text-xs font-medium">
                                            Dipinjam
                                        </span>
                                    <?php endif; ?>
                                <?php elseif ($b['status'] === 'returned'): ?>
                                    <span class="inline-block rounded-full bg-green-100 text-green-700 px-2.5 py-1 text-xs font-medium">
                                        Dikembalikan
                                    </span>
                                    <?php if (!empty($b['return_date'])): ?>
                                        <div class="text-xs text-gray-400 mt-1"><?= e(format_date((string) $b['return_date'])) ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= e(url('/borrowings/' . $b['id'] . '/receipt')) ?>" class="mr-2 text-xs font-medium text-gray-500 hover:text-gray-900 underline">Struk</a>
                                <?php if ($b['status'] === 'borrowed'): ?>
                                    <div class="mt-1 flex justify-end gap-2">
                                        <?php if (!empty($b['can_renew'])): ?>
                                            <form method="POST" action="<?= e(url('/borrowings/' . $b['id'] . '/renew')) ?>" class="inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-50 transition">
                                                    Perpanjang
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?= e(url('/borrowings/' . $b['id'] . '/return')) ?>" class="inline-flex items-center gap-1" onsubmit="return confirm('Kembalikan buku &quot;<?= e(addslashes($b['book_title'])) ?>&quot;?')">
                                            <?= csrf_field() ?>
                                            <select name="condition" title="Kondisi buku saat kembali" class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs text-gray-600 outline-none focus:border-green-500">
                                                <option value="baik">Baik</option>
                                                <option value="rusak">Rusak</option>
                                                <option value="hilang">Hilang</option>
                                            </select>
                                            <button type="submit" class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition">
                                                Kembalikan
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
