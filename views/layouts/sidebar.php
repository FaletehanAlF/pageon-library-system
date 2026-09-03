<?php
$active = $page ?? '';
$cartTotal = 0;
try {
    $cartTotal = cart_count();
} catch (Throwable) {
    $cartTotal = 0;
}
?>
<aside id="sidebar" class="hidden w-[264px] shrink-0 flex-col border-r border-gray-200/90 bg-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:overflow-y-auto">

    <!-- Brand -->
    <div class="flex h-[72px] shrink-0 items-center border-b border-gray-100 px-5">
        <a href="<?= e(url('/')) ?>" class="flex items-center gap-2.5 text-[1.15rem] font-extrabold tracking-tight text-gray-900">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-900 text-white">
                <?= icon('book', 'h-5 w-5') ?>
            </span>
            <span class="truncate"><?= e(setting('library_name', 'Pageon')) ?></span>
        </a>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 pb-6 pt-3" aria-label="Navigasi utama">

        <?php
        $link = static function (string $href, string $key, string $label, string $svg) use ($active): void {
            $isActive = $active === $key;
            $cls = $isActive
                ? 'bg-gray-900 text-white shadow-sm'
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
            $aria = $isActive ? ' aria-current="page"' : '';
            echo '<a href="' . e(url($href)) . '"' . $aria . ' class="nav-link ' . $cls . '">'
                . '<span class="shrink-0 opacity-90">' . $svg . '</span>'
                . '<span class="truncate">' . e($label) . '</span>'
                . '</a>';
        };

        $icoHome = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>';
        $icoBook = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>';
        $icoBorrow = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>';
        $icoHeart = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4.3 12.5l7.7 7.7 7.7-7.7a4.5 4.5 0 00-6.4-6.4l-1.3 1.3-1.3-1.3a4.5 4.5 0 00-6.4 6.4z"/></svg>';
        $icoClock = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        $icoDoc = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
        $icoTag = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>';
        $icoChart = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V7l-4-4H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>';
        $icoUsers = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6-4a3 3 0 11-3-3"/></svg>';
        $icoBell = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
        $icoGear = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M10.3 4.3a1 1 0 011.4 0l1 1a1 1 0 001.4 0l.7-.7a1 1 0 011.4 0l1.5 1.5a1 1 0 010 1.4l-.7.7a1 1 0 000 1.4l1 1a1 1 0 010 1.4l-1 1a1 1 0 000 1.4l.7.7a1 1 0 010 1.4l-1.5 1.5a1 1 0 01-1.4 0l-.7-.7a1 1 0 00-1.4 0l-1 1a1 1 0 01-1.4 0l-1-1a1 1 0 00-1.4 0l-.7.7a1 1 0 01-1.4 0L4.7 17a1 1 0 010-1.4l.7-.7a1 1 0 000-1.4l-1-1a1 1 0 010-1.4l1-1a1 1 0 000-1.4l-.7-.7a1 1 0 010-1.4l1.5-1.5a1 1 0 011.4 0l.7.7a1 1 0 001.4 0l1-1z"/><circle cx="12" cy="12" r="3"/></svg>';
        $icoCart = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6a1 1 0 00.9 1.4H19M9 22a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>';
        $icoMoney = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8c-2.7 0-5 1.1-5 2.5S9.3 13 12 13s5 1.1 5 2.5S14.7 18 12 18m0-10V6m0 12v2m9-8a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        $icoLog = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0zM4 4l3 3m13-3l-3 3"/></svg>';
        $icoHelp = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8.3 10a3.7 3.7 0 017.3 0c0 2-2.7 2.4-2.7 4m-.3 3.5h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/></svg>';
        ?>

        <p class="nav-section">Menu Utama</p>
        <div class="space-y-1">
            <?php
            $link('/', 'dashboard', 'Dashboard', $icoHome);
            $link('/books', 'books', 'Katalog Buku', $icoBook);
            $link('/cart', 'cart', 'Keranjang' . ($cartTotal > 0 ? ' · ' . $cartTotal : ''), $icoCart);
            $link('/my-borrowings', 'my-borrowings', 'Pinjaman Saya', $icoBorrow);
            $link('/reservations', 'reservations', 'Reservasi', $icoClock);
            $link('/wishlist', 'wishlist', 'Wishlist', $icoHeart);
            $link('/fines', 'fines', isAdmin() ? 'Kas Denda' : 'Denda Saya', $icoMoney);
            $link('/notifications', 'notifications', 'Notifikasi', $icoBell);
            $link('/bantuan', 'bantuan', 'Bantuan', $icoHelp);
            ?>
        </div>

        <?php if (isAdmin()): ?>
            <p class="nav-section">Kelola Perpustakaan</p>
            <div class="space-y-1">
                <?php
                $link('/borrowings', 'borrowings', 'Kelola Peminjaman', $icoDoc);
                $link('/admin/reservations', 'reservations-manage', 'Kelola Reservasi', $icoClock);
                $link('/categories', 'categories', 'Kategori', $icoTag);
                $link('/reports', 'reports', 'Laporan', $icoChart);
                $link('/users', 'users', 'Kelola User', $icoUsers);
                $link('/announcements', 'announcements', 'Pengumuman', $icoBell);
                $link('/logs', 'logs', 'Log Aktivitas', $icoLog);
                $link('/settings', 'settings', 'Pengaturan', $icoGear);
                ?>
            </div>
        <?php endif; ?>

    </nav>

    <!-- Bantuan singkat -->
    <div class="shrink-0 p-4">
        <div class="rounded-2xl bg-gray-50 p-4 text-[0.83rem] leading-relaxed">
            <p class="font-semibold text-gray-900">Butuh bantuan?</p>
            <p class="mt-1 text-gray-500">Panduan pinjam, denda & reservasi ada di satu tempat.</p>
            <a href="<?= e(url('/bantuan')) ?>" class="mt-3 inline-flex items-center gap-1 font-semibold text-gray-900 hover:underline">
                Buka panduan <?= icon('help', 'h-4 w-4') ?>
            </a>
        </div>
    </div>

</aside>
