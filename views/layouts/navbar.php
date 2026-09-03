<?php
$user = currentUser();
$unread = 0;
$cartCount = 0;
try {
    if ($user) {
        $unread = (new Notification())->unreadCount((int) $user['id']);
        $cartCount = cart_count();
    }
} catch (Throwable) {
    $unread = 0;
    $cartCount = 0;
}
$heading = $pageTitle ?? 'Dashboard';
?>
<header class="sticky top-0 z-30 border-b border-gray-200/90 bg-white/90 backdrop-blur">
    <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:h-[72px] lg:px-8">

        <!-- Kiri: toggle + judul halaman -->
        <div class="flex min-w-0 items-center gap-3">
            <button id="sidebar-toggle" type="button" aria-label="Buka menu navigasi" class="icon-btn lg:hidden">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="min-w-0">
                <p class="hidden text-[0.72rem] font-semibold uppercase tracking-[0.08em] text-gray-400 sm:block">
                    <?= e(setting('library_name', 'Pageon')) ?>
                </p>
                <h1 class="truncate text-[1.05rem] font-bold leading-tight tracking-tight text-gray-900 sm:text-lg">
                    <?= e($heading) ?>
                </h1>
            </div>
        </div>

        <!-- Kanan: aksi cepat + profil -->
        <div class="flex shrink-0 items-center gap-1 sm:gap-2">

            <a href="<?= e(url('/books')) ?>" class="mr-1 hidden items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 transition hover:border-gray-300 hover:bg-white hover:text-gray-900 md:inline-flex" aria-label="Cari buku">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="text-gray-400">Cari buku…</span>
                <kbd class="rounded-md border border-gray-200 bg-white px-1.5 py-0.5 text-[11px] font-semibold text-gray-400">/</kbd>
            </a>

            <a href="<?= e(url('/cart')) ?>" class="icon-btn" aria-label="Keranjang pinjam<?= $cartCount > 0 ? ', ' . (int) $cartCount . ' buku' : '' ?>">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6a1 1 0 00.9 1.4H19M9 22a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                <?php if ($cartCount > 0): ?>
                    <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 text-[11px] font-bold text-white"><?= $cartCount > 9 ? '9+' : (int) $cartCount ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= e(url('/notifications')) ?>" class="icon-btn" aria-label="Notifikasi<?= $unread > 0 ? ', ' . (int) $unread . ' belum dibaca' : '' ?>">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <?php if ($unread > 0): ?>
                    <span id="notif-badge" class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 text-[11px] font-bold text-white"><?= $unread > 9 ? '9+' : (int) $unread ?></span>
                <?php endif; ?>
            </a>

            <span class="mx-1 hidden h-8 w-px bg-gray-200 sm:block" aria-hidden="true"></span>

            <div class="relative" id="user-dropdown-container">
                <button id="user-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-gray-100 sm:px-3 sm:py-2">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white">
                        <?= e(strtoupper(mb_substr($user['name'] ?? 'U', 0, 1))) ?>
                    </span>
                    <span class="hidden text-left leading-tight md:block">
                        <span class="block max-w-[140px] truncate text-sm font-semibold text-gray-900"><?= e($user['name'] ?? 'User') ?></span>
                        <span class="block text-xs capitalize text-gray-400"><?= e($user['role'] ?? 'anggota') ?></span>
                    </span>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <span class="hidden rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-700 lg:inline-flex">Admin</span>
                    <?php endif; ?>
                    <svg class="hidden h-4 w-4 text-gray-400 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="user-dropdown-menu" class="absolute right-0 top-full z-50 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-gray-200 bg-white p-1.5 shadow-xl">
                    <div class="rounded-xl bg-gray-50 px-4 py-3.5">
                        <p class="truncate text-sm font-semibold text-gray-900"><?= e($user['name'] ?? '') ?></p>
                        <p class="mt-0.5 truncate text-xs text-gray-500"><?= e($user['email'] ?? '') ?></p>
                    </div>
                    <div class="p-1.5">
                        <a href="<?= e(url('/profile')) ?>" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100">Profil Saya</a>
                        <a href="<?= e(url('/wishlist')) ?>" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100">Wishlist</a>
                        <a href="<?= e(url('/my-borrowings')) ?>" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100">Pinjaman Saya</a>
                    </div>
                    <div class="border-t border-gray-100 p-1.5">
                        <a href="<?= e(url('/logout')) ?>" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">Keluar</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</header>
