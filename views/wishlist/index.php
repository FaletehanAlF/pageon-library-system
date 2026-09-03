<div class="mb-8"><h1 class="text-2xl font-bold">Wishlist Saya</h1><p class="text-gray-500 mt-1">Buku yang Anda simpan.</p></div>
<?php if (empty($items)): ?>
<div class="rounded-2xl border border-dashed bg-white p-12 text-center text-gray-500">Belum ada wishlist. <a class="underline text-gray-900" href="<?= e(url('/books')) ?>">Jelajahi buku</a></div>
<?php else: ?>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
<?php foreach ($items as $b): ?>
<div class="rounded-2xl border bg-white overflow-hidden">
<a href="<?= e(url('/books/' . $b['book_id'])) ?>">
<?php $c = book_cover_url($b); ?>
<img src="<?= e($c) ?>" alt="Cover <?= e($b['title']) ?>" class="h-40 w-full object-cover" loading="lazy">
<div class="p-4"><p class="font-semibold text-sm truncate"><?= e($b['title']) ?></p><p class="text-xs text-gray-500"><?= e($b['author']) ?></p></div>
</a>
<form method="POST" action="<?= e(url('/wishlist/toggle')) ?>" class="px-4 pb-4"><?= csrf_field() ?><input type="hidden" name="book_id" value="<?= (int) $b['book_id'] ?>"><button class="text-xs text-red-500 hover:underline">Hapus</button></form>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
