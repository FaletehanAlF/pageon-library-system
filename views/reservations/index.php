<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">Reservasi Saya</h1>
    <p class="mt-1 text-gray-500">Antrean buku yang stoknya habis.</p>
</div>
<div class="rounded-2xl border bg-white overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="border-b bg-gray-50 text-left text-gray-500">
            <th class="px-6 py-3 font-medium">Buku</th><th class="px-6 py-3 font-medium">Status</th><th class="px-6 py-3 font-medium">Tanggal</th><th class="px-6 py-3 text-right font-medium">Aksi</th>
        </tr></thead>
        <tbody>
        <?php if (empty($reservations)): ?>
            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada reservasi. <a class="underline text-gray-900" href="<?= e(url('/books')) ?>">Cari buku</a></td></tr>
        <?php else: ?>
            <?php foreach ($reservations as $r): ?>
                <tr class="border-b border-gray-50">
                    <td class="px-6 py-4"><a class="font-medium hover:underline" href="<?= e(url('/books/' . $r['book_id'])) ?>"><?= e($r['book_title']) ?></a><div class="text-xs text-gray-500"><?= e($r['book_author']) ?></div></td>
                    <td class="px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-medium <?= $r['status'] === 'ready' ? 'bg-green-100 text-green-700' : ($r['status'] === 'waiting' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') ?>"><?= e($r['status']) ?></span></td>
                    <td class="px-6 py-4 text-gray-500"><?= e(format_date($r['created_at'])) ?></td>
                    <td class="px-6 py-4 text-right">
                        <?php if (in_array($r['status'], ['waiting', 'ready'], true)): ?>
                            <form method="POST" action="<?= e(url('/reservations/' . $r['id'] . '/cancel')) ?>" class="inline"><?= csrf_field() ?><button class="text-xs text-red-500 hover:underline">Batalkan</button></form>
                        <?php else: ?><span class="text-xs text-gray-400">—</span><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
