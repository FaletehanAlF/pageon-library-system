<?php $isAdminView = isAdmin(); ?>

<?php if (!$isAdminView): ?>
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Denda Saya</h1>
        <p class="mt-1 text-gray-500">Tagihan denda yang belum lunas. Lunasi ke petugas/admin sebelum meminjam lagi.</p>
    </div>

    <?php if ((int) ($myTotal ?? 0) > 0): ?>
        <div class="mb-6 rounded-2xl border border-red-300 bg-red-600 p-6 text-white" role="alert">
            <p class="flex items-center gap-2 font-bold"><?= icon('alert', 'h-5 w-5') ?> SEGERA BAYAR ke petugas!</p>
            <p class="mt-1 text-sm text-red-100">Selama masih ada tagihan, Anda <strong>belum bisa pinjam buku lagi</strong>. Denda keterlambatan <strong>naik setiap hari</strong>, jadi semakin cepat bayar semakin murah.</p>
            <p class="mt-2 text-2xl font-bold"><?= e(format_rupiah((int) ($myTotal ?? 0))) ?></p>
        </div>
    <?php endif; ?>

    <?php if (setting_int('fine_increment', 0) > 0): ?>
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            <p class="flex items-start gap-2"><?= icon('chart', 'h-4 w-4 mt-0.5 shrink-0') ?><span><strong>Denda naik tiap hari!</strong> Hari ke-1 telat = <?= e(format_rupiah(setting_int('fine_per_day', 1000))) ?>,
            hari ke-2 = <?= e(format_rupiah(setting_int('fine_per_day', 1000) + setting_int('fine_increment', 0))) ?>,
            hari ke-3 = <?= e(format_rupiah(setting_int('fine_per_day', 1000) + 2 * setting_int('fine_increment', 0))) ?>, dan seterusnya.
            Contoh: telat 3 hari = <strong><?= e(format_rupiah(fine_preview(3))) ?></strong>.</span></p>
        </div>
    <?php endif; ?>

    <?php if ((int) ($myTotal ?? 0) <= 0): ?>
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-6">
        <p class="text-sm text-green-700">Total belum lunas</p>
        <p class="text-2xl font-bold text-green-800"><?= e(format_rupiah(0)) ?></p>
        <p class="mt-1 text-xs text-green-700">Bersih. Anda bisa meminjam seperti biasa.</p>
    </div>
    <?php endif; ?>

    <?php if ((int) ($myTotal ?? 0) > 0): ?>
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6">
        <h2 class="flex items-center gap-2 font-semibold"><?= icon('card', 'h-5 w-5') ?> Cara membayar (3 langkah)</h2>
        <ol class="mt-2 list-decimal list-inside space-y-1 text-sm text-gray-600">
            <li>Datang ke <strong>petugas perpustakaan</strong> dan sebutkan <strong>nama + judul buku</strong> yang didenda.</li>
            <li>Bayar tunai sebesar nominal di atas, lalu minta struk/bukti.</li>
            <li>Petugas menandai <strong>Lunas</strong> — tagihan di halaman ini langsung hilang dan Anda bisa pinjam lagi.</li>
        </ol>
    </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b bg-gray-50 text-left text-gray-500">
                    <th class="px-6 py-3 font-medium">Buku</th>
                    <th class="px-6 py-3 font-medium">Jenis</th>
                    <th class="px-6 py-3 font-medium">Nominal</th>
                    <th class="px-6 py-3 font-medium">Catatan</th>
                    <th class="px-6 py-3 font-medium">Tanggal</th>
                </tr></thead>
                <tbody>
                    <?php if (empty($fines)): ?>
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Tidak ada tagihan. <a class="underline text-gray-900" href="<?= e(url('/books')) ?>">Cari buku</a></td></tr>
                    <?php else: ?>
                        <?php foreach ($fines as $f): ?>
                            <tr class="border-b border-gray-50">
                                <td class="px-6 py-3 font-medium"><?= e($f['book_title'] ?? ('Peminjaman #' . ($f['borrowing_id'] ?? '-'))) ?></td>
                                <td class="px-6 py-3">
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800"><?= e($f['type']) ?></span>
                                </td>
                                <td class="px-6 py-3 font-semibold text-red-600"><?= e(format_rupiah((int) $f['amount'])) ?></td>
                                <td class="px-6 py-3 text-gray-600"><?= e($f['note'] ?? '-') ?></td>
                                <td class="px-6 py-3 text-gray-500"><?= e(format_date(substr((string) $f['created_at'], 0, 10))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($history)): ?>
    <h2 class="mt-8 mb-3 text-lg font-semibold">Riwayat (lunas / dibebaskan)</h2>
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b bg-gray-50 text-left text-gray-500">
                    <th class="px-6 py-3 font-medium">Buku</th>
                    <th class="px-6 py-3 font-medium">Nominal</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Tanggal</th>
                </tr></thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr class="border-b border-gray-50">
                            <td class="px-6 py-3"><?= e($h['book_title'] ?? '-') ?></td>
                            <td class="px-6 py-3"><?= e(format_rupiah((int) $h['amount'])) ?></td>
                            <td class="px-6 py-3"><span class="rounded-full <?= $h['status'] === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?> px-2.5 py-1 text-xs font-medium"><?= e($h['status']) ?></span></td>
                            <td class="px-6 py-3 text-gray-500"><?= e(format_date(substr((string) $h['created_at'], 0, 10))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Kas Denda</h1>
            <p class="mt-1 text-gray-500">Kelola tagihan denda semua user. Tandai lunas saat dibayar, atau bebaskan bila perlu.</p>
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
            <p class="text-sm text-red-700">Belum lunas</p>
            <p class="text-2xl font-bold text-red-800"><?= e(format_rupiah((int) (($totals['unpaid'] ?? 0)))) ?></p>
        </div>
        <div class="rounded-2xl border border-green-200 bg-green-50 p-6">
            <p class="text-sm text-green-700">Sudah dibayar</p>
            <p class="text-2xl font-bold text-green-800"><?= e(format_rupiah((int) (($totals['paid'] ?? 0)))) ?></p>
        </div>
    </div>

    <div class="mb-4 flex gap-2 text-sm">
        <?php foreach (['' => 'Semua', 'unpaid' => 'Belum lunas', 'paid' => 'Lunas', 'waived' => 'Dibebaskan'] as $k => $label): ?>
            <a href="<?= e(url('/fines' . ($k !== '' ? '?status=' . $k : ''))) ?>"
               class="rounded-xl border px-4 py-2 <?= ($status ?? '') === $k ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 hover:bg-gray-50' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b bg-gray-50 text-left text-gray-500">
                    <th class="px-6 py-3 font-medium">User</th>
                    <th class="px-6 py-3 font-medium">Buku</th>
                    <th class="px-6 py-3 font-medium">Jenis</th>
                    <th class="px-6 py-3 font-medium">Nominal</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 text-right font-medium">Aksi</th>
                </tr></thead>
                <tbody>
                    <?php if (empty($fines)): ?>
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada data.</td></tr>
                    <?php else: ?>
                        <?php foreach ($fines as $f): ?>
                            <tr class="border-b border-gray-50">
                                <td class="px-6 py-3"><p class="font-medium"><?= e($f['user_name']) ?></p><p class="text-xs text-gray-400">#<?= (int) $f['user_id'] ?> · <?= e(format_date(substr((string) $f['created_at'], 0, 10))) ?></p></td>
                                <td class="px-6 py-3"><?= e($f['book_title'] ?? '-') ?><?php if (!empty($f['note'])): ?><p class="text-xs text-gray-400"><?= e($f['note']) ?></p><?php endif; ?></td>
                                <td class="px-6 py-3 text-gray-600"><?= e($f['type']) ?></td>
                                <td class="px-6 py-3 font-medium"><?= e(format_rupiah((int) $f['amount'])) ?></td>
                                <td class="px-6 py-3">
                                    <?php if ($f['status'] === 'unpaid'): ?>
                                        <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">unpaid</span>
                                    <?php elseif ($f['status'] === 'paid'): ?>
                                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">paid</span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">waived</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <?php if ($f['status'] === 'unpaid'): ?>
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="<?= e(url('/fines/' . $f['id'] . '/pay')) ?>" class="inline"><?= csrf_field() ?><button class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700">Lunas</button></form>
                                            <form method="POST" action="<?= e(url('/fines/' . $f['id'] . '/waive')) ?>" class="inline" onsubmit="return confirm('Bebaskan denda ini?')"><?= csrf_field() ?><button class="rounded-lg border px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">Bebaskan</button></form>
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

    <?= pagination_links(url('/fines'), ['status' => $status ?? ''], $currentPage ?? 1, $totalPages ?? 1) ?>
<?php endif; ?>
