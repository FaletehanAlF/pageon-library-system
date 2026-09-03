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

    // ── Shortcut "/" untuk fokus ke pencarian buku ──────────
    document.addEventListener('keydown', function (e) {
        if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
        var tag = (document.activeElement && document.activeElement.tagName) || '';
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;
        var search = document.querySelector('input[type="search"][name="q"]');
        if (search) {
            e.preventDefault();
            search.focus();
        }
    });

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
