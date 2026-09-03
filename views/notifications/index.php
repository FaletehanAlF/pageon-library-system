<div class="page-header sm:items-end">
<div><h1 class="page-title">Notifikasi</h1><p class="page-subtitle">Tagihan, pengingat, dan info perpustakaan untuk Anda.</p></div>
<form method="POST" action="<?= e(url('/notifications/read-all')) ?>"><?= csrf_field() ?><button class="btn btn-secondary btn-sm">Tandai semua dibaca</button></form>
</div>
<div class="space-y-3">
<?php if (empty($items)): ?><div class="empty-state text-gray-500">Tidak ada notifikasi.</div>
<?php else: ?>
<?php foreach ($items as $n): ?>
<?php
$title = (string) ($n['title'] ?? '');
if (stripos($title, 'denda') !== false || stripos($title, 'tagihan') !== false) {
    $accent = 'border-red-300 bg-red-50/50';
    $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-red-600 px-2.5 py-0.5 text-xs font-bold text-white">' . icon('wallet', 'h-3 w-3') . ' Tagihan</span> ';
} elseif (stripos($title, 'tempo') !== false || stripos($title, 'terlambat') !== false) {
    $accent = 'border-amber-300 bg-amber-50/50';
    $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-amber-500 px-2.5 py-0.5 text-xs font-bold text-white">' . icon('clock', 'h-3 w-3') . ' Pengingat</span> ';
} elseif (stripos($title, 'reservasi') !== false || stripos($title, 'siap') !== false) {
    $accent = 'border-blue-300 bg-blue-50/50';
    $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-bold text-white">' . icon('book', 'h-3 w-3') . ' Reservasi</span> ';
} elseif (stripos($title, 'lunas') !== false || stripos($title, 'bebas') !== false) {
    $accent = 'border-green-300 bg-green-50/50';
    $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-green-600 px-2.5 py-0.5 text-xs font-bold text-white">' . icon('check', 'h-3 w-3') . ' Beres</span> ';
} else {
    $accent = '';
    $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-bold text-gray-700">' . icon('bell', 'h-3 w-3') . ' Info</span> ';
}
?>
<div class="rounded-2xl border bg-white p-4 flex items-start justify-between gap-3 <?= (int) $n['is_read'] === 0 ? $accent !== '' ? $accent : 'border-gray-900' : '' ?>">
<div><p class="font-medium text-sm"><?= $badge ?><?= e($n['title']) ?></p><p class="text-sm text-gray-600 mt-1"><?= e($n['message']) ?></p><p class="text-xs text-gray-400 mt-1"><?= e(time_ago($n['created_at'])) ?></p></div>
<form method="POST" action="<?= e(url('/notifications/' . $n['id'] . '/read')) ?>"><?= csrf_field() ?><button class="text-xs font-medium text-gray-600 hover:text-gray-900"><?= !empty($n['link']) ? 'Buka' : 'Tandai dibaca' ?></button></form>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
