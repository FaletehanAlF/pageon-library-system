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
?>
<header class="h-16 border-b border-gray-200 bg-white flex-shrink-0">
    <div class="flex h-full items-center justify-between px-6">

        <div class="flex items-center gap-3">
            <button id="sidebar-toggle" type="button" aria-label="Toggle sidebar" class="lg:hidden rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-2">

            <a href="<?= e(url('/cart')) ?>" class="relative rounded-lg p-2.5 text-gray-500 hover:bg-gray-100" aria-label="Keranjang pinjam">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6a1 1 0 00.9 1.4H19M9 22a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                <?php if ($cartCount > 0): ?>
                    <span class="absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-900 px-1 text-[11px] font-bold text-white"><?= $cartCount > 9 ? '9+' : (int) $cartCount ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= e(url('/notifications')) ?>" class="relative rounded-lg p-2.5 text-gray-500 hover:bg-gray-100" aria-label="Notifikasi">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <?php if ($unread > 0): ?>
                    <span id="notif-badge" class="absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold text-white"><?= $unread > 9 ? '9+' : (int) $unread ?></span>
                <?php endif; ?>
            </a>

            <div class="relative" id="user-dropdown-container">
                <button id="user-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium hover:bg-gray-50 transition">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white">
                        <?= e(strtoupper(mb_substr($user['name'] ?? 'U', 0, 1))) ?>
                    </span>
                    <span class="hidden sm:inline"><?= e($user['name'] ?? 'User') ?></span>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <span class="hidden sm:inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">Admin</span>
                    <?php endif; ?>
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="user-dropdown-menu" class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-gray-200 bg-white py-1 shadow-lg z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium truncate"><?= e($user['name'] ?? '') ?></p>
                        <p class="text-xs text-gray-500 truncate"><?= e($user['email'] ?? '') ?></p>
                        <p class="mt-1 text-xs capitalize text-gray-400"><?= e($user['role'] ?? '') ?></p>
                    </div>
                    <a href="<?= e(url('/profile')) ?>" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Profil Saya</a>
                    <a href="<?= e(url('/wishlist')) ?>" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Wishlist</a>
                    <a href="<?= e(url('/logout')) ?>" class="block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Keluar</a>
                </div>
            </div>

        </div>

    </div>
</header>
