<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Laporan Peminjaman</h1>
        <p class="mt-1 text-gray-500">Filter periode dan status, lalu cetak atau unduh CSV.</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Print</button>
        <a href="<?= e(url('/reports?from=' . urlencode($from) . '&to=' . urlencode($to) . '&status=' . urlencode($status) . '&export=csv')) ?>" class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Export CSV</a>
    </div>
</div>

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 no-print">
    <form method="GET" action="<?= e(url('/reports')) ?>" class="grid gap-3 sm:grid-cols-4">
        <div>
            <label class="mb-1.5 block text-xs font-medium text-gray-500">Dari</label>
            <input type="date" name="from" value="<?= e($from) ?>" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-medium text-gray-500">Sampai</label>
            <input type="date" name="to" value="<?= e($to) ?>" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-medium text-gray-500">Status</label>
            <select name="status" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm">
                <option value="">Semua</option>
                <option value="borrowed" <?= $status === 'borrowed' ? 'selected' : '' ?>>Dipinjam</option>
                <option value="returned" <?= $status === 'returned' ? 'selected' : '' ?>>Dikembalikan</option>
            </select>
        </div>
        <div class="flex items-end">
            <button class="w-full rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Tampilkan</button>
        </div>
    </form>
</div>

<div class="print-header hidden">
    <h1 class="text-xl font-bold"><?= e(setting('library_name', 'Pageon')) ?> — Laporan <?= e($from) ?> s/d <?= e($to) ?></h1>
    <p class="text-sm">Total transaksi: <?= count($rows) ?> · Estimasi denda berjalan: <?= e(format_rupiah((int) $totalFine)) ?></p>
</div>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b bg-gray-50 text-left text-gray-500">
                <th class="px-6 py-3 font-medium">Tgl Pinjam</th>
                <th class="px-6 py-3 font-medium">Peminjam</th>
                <th class="px-6 py-3 font-medium">Buku</th>
                <th class="px-6 py-3 font-medium">Jatuh Tempo</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Denda</th>
            </tr></thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada data pada periode ini.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <tr class="border-b border-gray-50">
                        <td class="px-6 py-3"><?= e($r['borrow_date']) ?></td>
                        <td class="px-6 py-3"><?= e($r['user_name']) ?></td>
                        <td class="px-6 py-3"><?= e($r['book_title']) ?></td>
                        <td class="px-6 py-3"><?= e($r['due_date']) ?></td>
                        <td class="px-6 py-3"><?= e($r['status']) ?></td>
                        <td class="px-6 py-3"><?= (int) $r['fine'] > 0 ? e(format_rupiah((int) $r['fine'])) : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .no-print, aside, header, footer { display: none !important; }
    main { padding: 0 !important; }
    .print-header { display: block !important; margin-bottom: 1rem; }
    body { background: #fff; }
}
</style>
