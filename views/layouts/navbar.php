<?php $user = currentUser(); ?>
<header class="h-16 border-b border-gray-200 bg-white flex-shrink-0">
    <div class="flex h-full items-center justify-between px-6">

        <div class="flex items-center gap-3">
            <button id="sidebar-toggle" type="button" aria-label="Toggle sidebar" class="lg:hidden rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-4">

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
                    <a href="<?= e(url('/logout')) ?>" class="block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Keluar</a>
                </div>
            </div>

        </div>

    </div>
</header>
