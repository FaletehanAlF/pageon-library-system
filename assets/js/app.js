document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // ── Flash auto-dismiss ──────────────────────────────────
    ['flash-success', 'flash-error'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var timeout = id === 'flash-success' ? 4000 : 6000;
        setTimeout(function () {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 300);
        }, timeout);
    });

    // ── User dropdown ───────────────────────────────────────
    var dropdownToggle = document.getElementById('user-dropdown-toggle');
    var dropdownMenu = document.getElementById('user-dropdown-menu');

    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var isHidden = dropdownMenu.classList.contains('hidden');
            dropdownMenu.classList.toggle('hidden', !isHidden);
            dropdownToggle.setAttribute('aria-expanded', String(isHidden));
        });

        document.addEventListener('click', function () {
            if (!dropdownMenu.classList.contains('hidden')) {
                dropdownMenu.classList.add('hidden');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !dropdownMenu.classList.contains('hidden')) {
                dropdownMenu.classList.add('hidden');
                dropdownToggle.setAttribute('aria-expanded', 'false');
                dropdownToggle.focus();
            }
        });
    }

    // ── Sidebar (mobile) ────────────────────────────────────
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        var overlay = null;

        function ensureOverlay() {
            if (overlay) return overlay;
            overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-30 bg-black/30 lg:hidden';
            overlay.setAttribute('aria-hidden', 'true');
            overlay.addEventListener('click', closeSidebar);
            return overlay;
        }

        function openSidebar() {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('flex', 'fixed', 'inset-y-0', 'left-0', 'z-40', 'h-screen', 'overflow-y-auto', 'shadow-xl');
            document.body.appendChild(ensureOverlay());
            document.body.style.overflow = 'hidden';
            sidebarToggle.setAttribute('aria-expanded', 'true');
        }

        function closeSidebar() {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex', 'fixed', 'inset-y-0', 'left-0', 'z-40', 'h-screen', 'overflow-y-auto', 'shadow-xl');
            if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
            document.body.style.overflow = '';
            sidebarToggle.setAttribute('aria-expanded', 'false');
        }

        // Start hidden on small screens
        if (window.innerWidth < 1024) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
        }

        sidebarToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (sidebar.classList.contains('hidden')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-40', 'h-screen', 'overflow-y-auto', 'shadow-xl');
                if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
                document.body.style.overflow = '';
            } else {
                // Kembali ke mode drawer saat mengecil
                if (!overlay || !overlay.parentNode) {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('flex');
                }
            }
        });
    }

    // ── Pencarian navbar (ikon → panel mengembang) ──────────
    // Buka/tutup murni lewat atribut data-open; animasi di CSS
    // (tanpa timeout) sehingga klik cepat tidak menimbulkan bug.
    var searchRoot = document.getElementById('nav-search-root');
    var searchToggle = document.getElementById('nav-search-toggle');
    var searchInput = document.getElementById('nav-search-input');

    function isSearchOpen() {
        return !!searchRoot && searchRoot.getAttribute('data-open') === 'true';
    }

    function openSearch(selectText) {
        if (!searchRoot || !searchInput || !searchToggle) return;
        searchRoot.setAttribute('data-open', 'true');
        searchToggle.setAttribute('aria-expanded', 'true');
        try {
            searchInput.focus({ preventScroll: true });
        } catch (err) {
            searchInput.focus();
        }
        if (selectText && searchInput.value) {
            try { searchInput.select(); } catch (err2) { /* abaikan */ }
        }
    }

    function closeSearch(refocusToggle) {
        if (!searchRoot || !searchToggle) return;
        searchRoot.setAttribute('data-open', 'false');
        searchToggle.setAttribute('aria-expanded', 'false');
        if (refocusToggle) searchToggle.focus();
    }

    if (searchRoot && searchToggle && searchInput) {
        searchToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (isSearchOpen()) {
                closeSearch(false);
            } else {
                openSearch(true);
            }
        });

        // Klik di luar panel menutupnya (teks yang diketik tetap aman)
        document.addEventListener('click', function (e) {
            if (isSearchOpen() && !searchRoot.contains(e.target)) {
                closeSearch(false);
            }
        });

        document.addEventListener('keydown', function (e) {
            // Escape menutup panel pencarian
            if (e.key === 'Escape' && isSearchOpen()) {
                var tag = (document.activeElement && document.activeElement.tagName) || '';
                if (document.activeElement === searchInput || tag === 'INPUT') {
                    e.stopPropagation();
                }
                closeSearch(true);
                return;
            }
            // Shortcut "/" membuka pencarian (di luar kolom isian)
            if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
            var activeTag = (document.activeElement && document.activeElement.tagName) || '';
            if (activeTag === 'INPUT' || activeTag === 'TEXTAREA' || activeTag === 'SELECT') return;
            e.preventDefault();
            openSearch(true);
        });
    }

    // ── Modals ──────────────────────────────────────────────
    function closeAllModals() {
        document.querySelectorAll('[id$="-modal"]').forEach(function (modal) {
            modal.classList.add('hidden');
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAllModals();
    });

    document.querySelectorAll('[id$="-modal"]').forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });
});
