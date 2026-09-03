<div class="page-header"><div><h1 class="page-title">Kelola Reservasi</h1><p class="page-subtitle">Semua antrean user.</p></div></div>
<div class="table-shell"><div class="table-scroll"><table class="table-base">
<thead><tr class="bg-gray-50 text-left text-gray-500 border-b"><th class="px-5 lg:px-6 py-4 font-medium">User</th><th class="px-5 lg:px-6 py-4 font-medium">Buku</th><th class="px-5 lg:px-6 py-4 font-medium">Status</th><th class="px-5 lg:px-6 py-4 font-medium">Tanggal</th><th class="px-5 lg:px-6 py-4 text-right font-medium">Aksi</th></tr></thead>
<tbody>
<?php foreach (($reservations ?? []) as $r): ?>
<tr class="border-b border-gray-50"><td class="px-5 lg:px-6 py-4"><?= e($r['user_name']) ?></td><td class="px-5 lg:px-6 py-4"><?= e($r['book_title']) ?></td><td class="px-5 lg:px-6 py-4"><?= e($r['status']) ?></td><td class="px-5 lg:px-6 py-4 text-gray-500"><?= e($r['created_at']) ?></td>
<td class="px-5 lg:px-6 py-4 text-right"><?php if (in_array($r['status'], ['waiting', 'ready'], true)): ?><form method="POST" action="<?= e(url('/reservations/' . $r['id'] . '/cancel')) ?>" class="inline"><?= csrf_field() ?><button class="text-xs text-red-500">Batalkan</button></form><?php endif; ?></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
