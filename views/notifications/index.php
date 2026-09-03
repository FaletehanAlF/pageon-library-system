<div class="mb-8 flex items-center justify-between">
<div><h1 class="text-2xl font-bold">Notifikasi</h1><p class="text-gray-500 mt-1">Info reservasi dan pengumuman.</p></div>
<form method="POST" action="<?= e(url('/notifications/read-all')) ?>"><?= csrf_field() ?><button class="rounded-xl border px-4 py-2 text-sm hover:bg-gray-50">Tandai semua dibaca</button></form>
</div>
<div class="space-y-3">
<?php if (empty($items)): ?><div class="rounded-2xl border bg-white p-10 text-center text-gray-500">Tidak ada notifikasi.</div>
<?php else: ?>
<?php foreach ($items as $n): ?>
<div class="rounded-2xl border bg-white p-4 flex items-start justify-between gap-3 <?= (int) $n['is_read'] === 0 ? 'border-gray-900' : '' ?>">
<div><p class="font-medium text-sm"><?= e($n['title']) ?></p><p class="text-sm text-gray-600 mt-1"><?= e($n['message']) ?></p><p class="text-xs text-gray-400 mt-1"><?= e(time_ago($n['created_at'])) ?></p></div>
<form method="POST" action="<?= e(url('/notifications/' . $n['id'] . '/read')) ?>"><?= csrf_field() ?><button class="text-xs font-medium text-gray-600 hover:text-gray-900"><?= !empty($n['link']) ? 'Buka' : 'Tandai dibaca' ?></button></form>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
