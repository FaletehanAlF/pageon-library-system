<?php $isAdminView = isAdmin(); ?>

<?php if (!$isAdminView): ?>
    <div class="page-header">
        <div>
            <h1 class="page-title">Denda Saya</h1>
            <p class="page-subtitle">Tagihan denda yang belum lunas. Lunasi ke petugas/admin sebelum meminjam lagi.</p>
        </div>
    </div>

    <?php if ((int) ($myTotal ?? 0) > 0): ?>
        <div class="card !border-red-200 !bg-red-600 p-6 text-white sm:p-7" role="alert">
            <p class="flex items-center gap-2.5 font-bold"><?= icon('alert', 'h-5 w-5') ?> SEGERA BAYAR ke petugas!</p>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-red-100">Selama masih ada tagihan, Anda <strong>belum bisa pinjam buku lagi</strong>. Denda keterlambatan <strong>naik setiap hari</strong>, jadi semakin cepat bayar semakin murah.</p>
            <p class="mt-3 text-3xl font-extrabold tracking-tight"><?= e(format_rupiah((int) ($myTotal ?? 0))) ?></p>
        </div>
    <?php endif; ?>

    <?php if (setting_int('fine_increment', 0) > 0): ?>
        <details class="card group !border-amber-200 !bg-amber-50/70 text-sm text-amber-800">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-5 font-semibold sm:px-6 [&::-webkit-details-marker]:hidden">
                <span class="flex items-center gap-2.5"><?= icon('chart', 'h-4 w-4 shrink-0') ?> Bagaimana denda dihitung? (naik tiap hari)</span>
                <svg class="h-4 w-4 shrink-0 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <div class="px-5 pb-5 leading-relaxed sm:px-6 sm:pb-6">
            Hari ke-1 telat = <?= e(format_rupiah(setting_int('fine_per_day', 1000))) ?>,
            hari ke-2 = <?= e(format_rupiah(setting_int('fine_per_day', 1000) + setting_int('fine_increment', 0))) ?>,
            hari ke-3 = <?= e(format_rupiah(setting_int('fine_per_day', 1000) + 2 * setting_int('fine_increment', 0))) ?>, dan seterusnya.
            Contoh: telat 3 hari = <strong><?= e(format_rupiah(fine_preview(3))) ?></strong>.
            </div>
        </details>
    <?php endif; ?>

    <?php if ((int) ($myTotal ?? 0) <= 0): ?>
    <div class="card !border-green-200 !bg-green-50/70 p-6 sm:p-7">
        <p class="text-sm font-medium text-green-700">Total belum lunas</p>
        <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-green-800"><?= e(format_rupiah(0)) ?></p>
        <p class="mt-1.5 text-[0.83rem] text-green-700">Bersih. Anda bisa meminjam seperti biasa.</p>
    </div>
    <?php endif; ?>

    <?php if ((int) ($myTotal ?? 0) > 0): ?>
    <div class="card card-pad">
        <h2 class="flex items-center gap-2.5 font-bold tracking-tight"><?= icon('card', 'h-5 w-5') ?> Cara membayar (3 langkah)</h2>
        <ol class="mt-3 list-decimal space-y-2 pl-5 text-[0.9rem] leading-relaxed text-gray-600">
            <li>Datang ke <strong>petugas perpustakaan</strong> dan sebutkan <strong>nama + judul buku</strong> yang didenda.</li>
            <li>Bayar tunai sebesar nominal di atas, lalu minta struk/bukti.</li>
            <li>Petugas menandai <strong>Lunas</strong> — tagihan di halaman ini langsung hilang dan Anda bisa pinjam lagi.</li>
        </ol>
    </div>
    <?php endif; ?>

    <div class="table-shell">
        <div class="table-scroll">
            <table class="table-base">
                <thead><tr>
                    <th>Buku</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Catatan</th>
                    <th>Tanggal</th>
                </tr></thead>
                <tbody>
                    <?php if (empty($fines)): ?>
                        <tr><td colspan="5" class="!px-6 !py-12 text-center text-gray-500">Tidak ada tagihan. <a class="font-semibold text-gray-900 underline underline-offset-2" href="<?= e(url('/books')) ?>">Cari buku &rarr;</a></td></tr>
                    <?php else: ?>
                        <?php foreach ($fines as $f): ?>
                            <tr>
                                <td class="font-semibold text-gray-900"><?= e($f['book_title'] ?? ('Peminjaman #' . ($f['borrowing_id'] ?? '-'))) ?></td>
                                <td>
                                    <span class="badge bg-amber-100 text-amber-800"><?= e($f['type']) ?></span>
                                </td>
                                <td class="whitespace-nowrap font-bold text-red-600"><?= e(format_rupiah((int) $f['amount'])) ?></td>
                                <td class="min-w-[160px] text-gray-600"><?= e($f['note'] ?? '-') ?></td>
                                <td class="whitespace-nowrap text-gray-500"><?= e(format_date(substr((string) $f['created_at'], 0, 10))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($history)): ?>
    <h2 class="mt-2 text-lg font-bold tracking-tight">Riwayat (lunas / dibebaskan)</h2>
    <div class="table-shell">
        <div class="table-scroll">
            <table class="table-base">
                <thead><tr>
                    <th>Buku</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr></thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= e($h['book_title'] ?? '-') ?></td>
                            <td class="whitespace-nowrap font-medium"><?= e(format_rupiah((int) $h['amount'])) ?></td>
                            <td><span class="badge <?= $h['status'] === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>"><?= e($h['status']) ?></span></td>
                            <td class="whitespace-nowrap text-gray-500"><?= e(format_date(substr((string) $h['created_at'], 0, 10))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    <div class="page-header">
        <div>
            <h1 class="page-title">Kas Denda</h1>
            <p class="page-subtitle">Kelola tagihan denda semua user. Tandai lunas saat dibayar, atau bebaskan bila perlu.</p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 sm:gap-5">
        <div class="card !border-red-200 !bg-red-50/70 p-6">
            <p class="text-sm font-medium text-red-700">Belum lunas</p>
            <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-red-800"><?= e(format_rupiah((int) (($totals['unpaid'] ?? 0)))) ?></p>
        </div>
        <div class="card !border-green-200 !bg-green-50/70 p-6">
            <p class="text-sm font-medium text-green-700">Sudah dibayar</p>
            <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-green-800"><?= e(format_rupiah((int) (($totals['paid'] ?? 0)))) ?></p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 text-sm font-medium">
        <?php foreach (['' => 'Semua', 'unpaid' => 'Belum lunas', 'paid' => 'Lunas', 'waived' => 'Dibebaskan'] as $k => $label): ?>
            <a href="<?= e(url('/fines' . ($k !== '' ? '?status=' . $k : ''))) ?>"
               class="btn btn-sm !px-4 !py-2.5 !text-[0.83rem] <?= ($status ?? '') === $k ? 'btn-primary' : 'btn-secondary' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="table-shell">
        <div class="table-scroll">
            <table class="table-base">
                <thead><tr>
                    <th>User</th>
                    <th>Buku</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th class="!text-right">Aksi</th>
                </tr></thead>
                <tbody>
                    <?php if (empty($fines)): ?>
                        <tr><td colspan="6" class="!px-6 !py-12 text-center text-gray-500">Tidak ada data.</td></tr>
                    <?php else: ?>
                        <?php foreach ($fines as $f): ?>
                            <tr>
                                <td><p class="font-semibold text-gray-900"><?= e($f['user_name']) ?></p><p class="mt-0.5 whitespace-nowrap text-xs text-gray-400">#<?= (int) $f['user_id'] ?> · <?= e(format_date(substr((string) $f['created_at'], 0, 10))) ?></p></td>
                                <td class="min-w-[160px]"><?= e($f['book_title'] ?? '-') ?><?php if (!empty($f['note'])): ?><p class="mt-0.5 text-xs text-gray-400"><?= e($f['note']) ?></p><?php endif; ?></td>
                                <td class="text-gray-600"><?= e($f['type']) ?></td>
                                <td class="whitespace-nowrap font-bold"><?= e(format_rupiah((int) $f['amount'])) ?></td>
                                <td>
                                    <?php if ($f['status'] === 'unpaid'): ?>
                                        <span class="badge bg-red-100 text-red-700">unpaid</span>
                                    <?php elseif ($f['status'] === 'paid'): ?>
                                        <span class="badge bg-green-100 text-green-700">paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-gray-100 text-gray-600">waived</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php if ($f['status'] === 'unpaid'): ?>
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="<?= e(url('/fines/' . $f['id'] . '/pay')) ?>" class="inline"><?= csrf_field() ?><button class="btn btn-sm !bg-green-600 !text-white hover:!bg-green-700">Lunas</button></form>
                                            <form method="POST" action="<?= e(url('/fines/' . $f['id'] . '/waive')) ?>" class="inline" onsubmit="return confirm('Bebaskan denda ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-secondary">Bebaskan</button></form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-300">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?= pagination_links(url('/fines'), ['status' => $status ?? ''], $currentPage ?? 1, $totalPages ?? 1) ?>
<?php endif; ?>
