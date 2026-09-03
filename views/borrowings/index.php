<div class="page-header">
    <div>
        <h1 class="page-title">
            <?= $page === 'borrowings' ? 'Kelola Peminjaman' : 'Pinjaman Saya' ?>
        </h1>
        <p class="page-subtitle">
            <?= $page === 'borrowings' ? 'Daftar semua peminjaman buku di perpustakaan.' : 'Buku yang sedang Anda pinjam dan belum dikembalikan.' ?>
            <?php if (isset($finePerDay)): ?>
                <span class="mt-1.5 block text-[0.85rem]">Denda mulai <span class="font-bold text-gray-700"><?= e(format_rupiah((int) $finePerDay)) ?>/hari</span><?php if (setting_int('fine_increment', 0) > 0): ?>, <span class="font-bold text-red-600">naik <?= e(format_rupiah(setting_int('fine_increment', 0))) ?> tiap harinya</span><?php endif; ?></span>
            <?php endif; ?>
        </p>
    </div>
</div>

<?php if ($page !== 'borrowings'): ?>
<div class="card flex gap-1.5 p-1.5 text-sm font-semibold">
    <a href="<?= e(url('/my-borrowings')) ?>" class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-3 text-center transition <?= ($tab ?? 'aktif') === 'aktif' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' ?>"><?= icon('inbox', 'h-4 w-4') ?> Sedang Dipinjam</a>
    <a href="<?= e(url('/riwayat')) ?>" class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-3 text-center transition <?= ($tab ?? '') === 'riwayat' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' ?>"><?= icon('archive', 'h-4 w-4') ?> Riwayat</a>
</div>
<?php endif; ?>

<?php
$totalFine = 0;
foreach (($borrowings ?? []) as $tmp) { $totalFine += (int) ($tmp['fine'] ?? 0); }
?>
<?php if ($totalFine > 0): ?>
    <div class="flex flex-col gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-[0.9rem] leading-relaxed text-red-800 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-5" role="alert">
        <p>Total denda berjalan: <span class="font-extrabold"><?= e(format_rupiah($totalFine)) ?></span>. Segera kembalikan buku yang terlambat.</p>
        <a href="<?= e(url('/fines')) ?>" class="btn btn-sm !bg-red-600 !px-4 !py-2.5 !text-white hover:!bg-red-700 shrink-0">Lihat Tagihan &rarr;</a>
    </div>
<?php endif; ?>

<div class="table-shell">
    <div class="table-scroll">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Buku</th>
                    <?php if (isAdmin()): ?>
                        <th>Peminjam</th>
                    <?php endif; ?>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Denda</th>
                    <th>Status</th>
                    <th class="!text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($borrowings)): ?>
                    <tr>
                        <td colspan="<?= isAdmin() ? 7 : 6 ?>" class="!px-6 !py-14 text-center">
                            <p class="font-semibold text-gray-900">Belum ada data peminjaman</p>
                            <p class="mt-1 text-sm text-gray-500">Semua pinjaman yang aktif akan muncul di sini.</p>
                            <a href="<?= e(url('/books')) ?>" class="mt-4 inline-block text-sm font-semibold text-gray-900 underline underline-offset-2">Jelajahi Buku &rarr;</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($borrowings as $b): ?>
                        <?php
                        $isOverdue = $b['status'] === 'borrowed' && days_overdue((string) $b['due_date']) > 0;
                        ?>
                        <tr>
                            <td>
                                <div class="min-w-[180px] font-semibold leading-snug text-gray-900"><?= e($b['book_title']) ?></div>
                                <div class="mt-0.5 text-xs text-gray-500"><?= e($b['book_author']) ?></div>
                                <?php if ((int) ($b['renew_count'] ?? 0) > 0): ?>
                                    <div class="mt-1.5 inline-block rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Perpanjang <?= (int) $b['renew_count'] ?>x</div>
                                <?php endif; ?>
                            </td>
                            <?php if (isAdmin()): ?>
                                <td>
                                    <div class="font-medium text-gray-700"><?= e($b['user_name']) ?></div>
                                    <div class="mt-0.5 text-xs text-gray-400"><?= e($b['user_email']) ?></div>
                                </td>
                            <?php endif; ?>
                            <td class="whitespace-nowrap text-gray-600">
                                <?= e(format_date((string) $b['borrow_date'])) ?>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="<?= $isOverdue ? 'font-bold text-red-600' : 'text-gray-600' ?>">
                                    <?= e(format_date((string) $b['due_date'])) ?>
                                </span>
                                <?php if ($isOverdue): ?>
                                    <div class="mt-0.5 text-xs font-semibold text-red-500"><?= (int) ($b['days_overdue'] ?? 0) ?> hari telat</div>
                                <?php endif; ?>
                            </td>
                            <td class="whitespace-nowrap">
                                <?php if ((int) ($b['fine'] ?? 0) > 0): ?>
                                    <span class="font-bold text-red-600"><?= e(format_rupiah((int) $b['fine'])) ?></span>
                                <?php else: ?>
                                    <span class="text-gray-300">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($b['status'] === 'borrowed'): ?>
                                    <?php if ($isOverdue): ?>
                                        <span class="badge bg-red-100 text-red-700">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge bg-amber-100 text-amber-700">Dipinjam</span>
                                    <?php endif; ?>
                                <?php elseif ($b['status'] === 'returned'): ?>
                                    <span class="badge bg-green-100 text-green-700">Dikembalikan</span>
                                    <?php if (!empty($b['return_date'])): ?>
                                        <div class="mt-1.5 whitespace-nowrap text-xs text-gray-400"><?= e(format_date((string) $b['return_date'])) ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="flex flex-col items-end gap-2">
                                    <a href="<?= e(url('/borrowings/' . $b['id'] . '/receipt')) ?>" class="rounded-lg px-2 py-1 text-xs font-semibold text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 hover:underline">Struk</a>
                                    <?php if ($b['status'] === 'borrowed'): ?>
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <?php if (!empty($b['can_renew'])): ?>
                                                <form method="POST" action="<?= e(url('/borrowings/' . $b['id'] . '/renew')) ?>" class="inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-secondary !border-blue-200 !text-blue-700 hover:!bg-blue-50">
                                                        Perpanjang
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="<?= e(url('/borrowings/' . $b['id'] . '/return')) ?>" class="inline-flex flex-wrap items-center justify-end gap-2" onsubmit="return confirm('<?= isAdmin() ? 'Proses pengembalian buku' : 'Kembalikan buku' ?> &quot;<?= e(addslashes($b['book_title'])) ?>&quot;?<?= isAdmin() ? '' : ' Pastikan bukunya sudah diserahkan ke petugas.' ?>')">
                                                <?= csrf_field() ?>
                                                <?php if (isAdmin()): ?>
                                                <select name="condition" title="Kondisi buku saat kembali (diisi petugas)" class="rounded-lg border border-gray-200 bg-white px-2.5 py-2 text-xs text-gray-600 outline-none focus:border-green-500">
                                                    <option value="baik">Baik</option>
                                                    <option value="rusak">Rusak</option>
                                                    <option value="hilang">Hilang</option>
                                                </select>
                                                <?php else: ?>
                                                <input type="hidden" name="condition" value="baik">
                                                <?php endif; ?>
                                                <button type="submit" class="btn btn-sm !bg-green-600 !text-white hover:!bg-green-700">
                                                    Kembalikan
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-300">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
