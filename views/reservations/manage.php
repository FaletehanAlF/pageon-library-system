<div class="mb-8"><h1 class="text-2xl font-bold">Kelola Reservasi</h1><p class="text-gray-500 mt-1">Semua antrean user.</p></div>
<div class="rounded-2xl border bg-white overflow-hidden"><table class="w-full text-sm">
<thead><tr class="bg-gray-50 text-left text-gray-500 border-b"><th class="px-6 py-3 font-medium">User</th><th class="px-6 py-3 font-medium">Buku</th><th class="px-6 py-3 font-medium">Status</th><th class="px-6 py-3 font-medium">Tanggal</th><th class="px-6 py-3 text-right font-medium">Aksi</th></tr></thead>
<tbody>
<?php foreach (($reservations ?? []) as $r): ?>
<tr class="border-b border-gray-50"><td class="px-6 py-3"><?= e($r['user_name']) ?></td><td class="px-6 py-3"><?= e($r['book_title']) ?></td><td class="px-6 py-3"><?= e($r['status']) ?></td><td class="px-6 py-3 text-gray-500"><?= e($r['created_at']) ?></td>
<td class="px-6 py-3 text-right"><?php if (in_array($r['status'], ['waiting', 'ready'], true)): ?><form method="POST" action="<?= e(url('/reservations/' . $r['id'] . '/cancel')) ?>" class="inline"><?= csrf_field() ?><button class="text-xs text-red-500">Batalkan</button></form><?php endif; ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
