/**
 * Dashboard sidebar enhancements — Colegio Los Angeles
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ============================================================
        // Expand active menu parent on load
        // ============================================================
        var activeLink = document.querySelector('.main-sidebar .nav-link.active');
        if (activeLink) {
            var treeviewParent = activeLink.closest('.nav-treeview');
            if (treeviewParent) {
                var parentItem = treeviewParent.closest('.has-treeview');
                if (parentItem && !parentItem.classList.contains('menu-open')) {
                    parentItem.classList.add('menu-open');
                }
            }
        }

        // ============================================================
        // Remember collapsed sidebar state
        // ============================================================
        var sidebarToggle = document.querySelector('[data-widget="pushmenu"]');
        var body = document.body;

        if (sidebarToggle) {
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                body.classList.add('sidebar-collapse');
            }

            sidebarToggle.addEventListener('click', function () {
                setTimeout(function () {
                    var collapsed = body.classList.contains('sidebar-collapse');
                    localStorage.setItem('sidebar-collapsed', collapsed);
                }, 400);
            });
        }

        // ============================================================
        // Active menu: scroll into view
        // ============================================================
        if (activeLink) {
            setTimeout(function () {
                activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);
        }
    });
})();
