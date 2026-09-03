<div class="page-header"><div><h1 class="page-title">Wishlist Saya</h1><p class="page-subtitle">Buku yang Anda simpan.</p></div></div>
<?php if (empty($items)): ?>
<div class="empty-state text-gray-500">Belum ada wishlist. <a class="underline text-gray-900" href="<?= e(url('/books')) ?>">Jelajahi buku</a></div>
<?php else: ?>
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
<?php foreach ($items as $b): ?>
<div class="card group overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
<a href="<?= e(url('/books/' . $b['book_id'])) ?>">
<?php $c = book_cover_url($b); ?>
<img src="<?= e($c) ?>" alt="Cover <?= e($b['title']) ?>" class="aspect-[4/3] w-full bg-gray-100 object-cover" loading="lazy">
<div class="p-5"><p class="truncate font-semibold leading-snug"><?= e($b['title']) ?></p><p class="mt-1 truncate text-[0.83rem] text-gray-500"><?= e($b['author']) ?></p></div>
</a>
<form method="POST" action="<?= e(url('/wishlist/toggle')) ?>" class="border-t border-gray-100 px-5 py-3.5"><?= csrf_field() ?><input type="hidden" name="book_id" value="<?= (int) $b['book_id'] ?>"><button class="rounded-lg px-2 py-1 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:underline">Hapus dari wishlist</button></form>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
