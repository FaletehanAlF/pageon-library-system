<?php $isAdminView = isAdmin(); ?>

<?php if (!$isAdminView): ?>
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Denda Saya</h1>
        <p class="mt-1 text-gray-500">Tagihan denda yang belum lunas. Lunasi ke petugas/admin sebelum meminjam lagi.</p>
    </div>

    <div class="mb-6 rounded-2xl border <?= ((int) ($myTotal ?? 0) > 0) ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' ?> p-6">
        <p class="text-sm <?= ((int) ($myTotal ?? 0) > 0) ? 'text-red-700' : 'text-green-700' ?>">Total belum lunas</p>
        <p class="text-2xl font-bold <?= ((int) ($myTotal ?? 0) > 0) ? 'text-red-800' : 'text-green-800' ?>"><?= e(format_rupiah((int) ($myTotal ?? 0))) ?></p>
        <?php if ((int) ($myTotal ?? 0) > 0): ?>
            <p class="mt-1 text-xs text-red-600">Anda tidak bisa meminjam / checkout keranjang selama masih ada tagihan.</p>
        <?php else: ?>
            <p class="mt-1 text-xs text-green-700">Bersih. Anda bisa meminjam seperti biasa.</p>
        <?php endif; ?>
    </div>

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
