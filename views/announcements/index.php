<div class="mb-8"><h1 class="text-2xl font-bold">Pengumuman</h1><p class="text-gray-500 mt-1">Broadcast ke semua user (juga masuk notifikasi).</p></div>
<div class="mb-6 rounded-2xl border bg-white p-6">
<h3 class="font-semibold mb-3">Buat Baru</h3>
<form method="POST" action="<?= e(url('/announcements')) ?>" class="space-y-3"><?= csrf_field() ?>
<input type="text" name="title" required maxlength="150" placeholder="Judul" class="w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm">
<textarea name="message" required maxlength="2000" rows="3" placeholder="Isi pengumuman..." class="w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm"></textarea>
<button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm text-white">Publikasi</button>
</form>
</div>
<div class="space-y-3">
<?php foreach (($items ?? []) as $a): ?>
<div class="rounded-2xl border bg-white p-5 flex justify-between gap-4">
<div><p class="font-semibold"><?= e($a['title']) ?> <?php if (!(int) $a['is_active']): ?><span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full">nonaktif</span><?php endif; ?></p><p class="text-sm text-gray-600 mt-1"><?= nl2br(e($a['message'])) ?></p><p class="text-xs text-gray-400 mt-2"><?= e($a['author_name'] ?? '') ?> · <?= e(time_ago($a['created_at'])) ?></p></div>
<div class="flex flex-col gap-2 shrink-0">
<form method="POST" action="<?= e(url('/announcements/' . $a['id'] . '/toggle')) ?>"><?= csrf_field() ?><button class="text-xs underline"><?= (int) $a['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
<form method="POST" action="<?= e(url('/announcements/' . $a['id'] . '/delete')) ?>" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="text-xs text-red-500">Hapus</button></form>
</div>
</div>
<?php endforeach; ?>
</div>
