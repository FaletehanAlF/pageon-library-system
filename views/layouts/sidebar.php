<?php $active = $page ?? ''; ?>
<aside id="sidebar" class="w-64 border-r border-gray-200 bg-white flex-shrink-0 transition-all duration-200">

    <div class="flex h-16 items-center border-b border-gray-200 px-6">
        <a href="<?= e(url('/')) ?>" class="text-xl font-bold tracking-tight">
            📚 Pageon
        </a>
    </div>

    <nav class="p-4 space-y-1" aria-label="Navigasi utama">

        <a href="<?= e(url('/')) ?>" aria-current="<?= $active === 'dashboard' ? 'page' : 'false' ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition <?= $active === 'dashboard' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="<?= e(url('/books')) ?>" aria-current="<?= $active === 'books' ? 'page' : 'false' ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition <?= $active === 'books' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Buku
        </a>

        <a href="<?= e(url('/my-borrowings')) ?>" aria-current="<?= $active === 'my-borrowings' ? 'page' : 'false' ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition <?= $active === 'my-borrowings' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Peminjaman Saya
        </a>

        <?php if (isAdmin()): ?>

        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold uppercase text-gray-400 tracking-wider">Admin</p>
        </div>

        <a href="<?= e(url('/borrowings')) ?>" aria-current="<?= $active === 'borrowings' ? 'page' : 'false' ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition <?= $active === 'borrowings' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Kelola Peminjaman
        </a>

        <a href="<?= e(url('/categories')) ?>" aria-current="<?= $active === 'categories' ? 'page' : 'false' ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition <?= $active === 'categories' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Kategori
        </a>

        <a href="<?= e(url('/reports')) ?>" aria-current="<?= $active === 'reports' ? 'page' : 'false' ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition <?= $active === 'reports' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V7l-4-4H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Laporan
        </a>

        <?php endif; ?>

    </nav>

</aside>
