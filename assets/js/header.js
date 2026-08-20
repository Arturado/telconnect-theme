(function () {
    var toggle = document.getElementById('mobile-menu-toggle');
    var overlay = document.getElementById('mobile-nav-overlay');
    var closeBtn = document.getElementById('mobile-nav-close');

    if (!toggle || !overlay || !closeBtn) {
        return;
    }

    function openMenu() {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Cerrar menú');
        document.body.classList.add('mobile-nav-open');
        closeBtn.focus();
    }

    function closeMenu() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir menú');
        document.body.classList.remove('mobile-nav-open');
        toggle.focus();
    }

    toggle.addEventListener('click', function () {
        if (overlay.classList.contains('is-open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    closeBtn.addEventListener('click', closeMenu);

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeMenu();
        }
    });

    // Cerrar automáticamente si se cambia a viewport desktop con el menú abierto
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900 && overlay.classList.contains('is-open')) {
            closeMenu();
        }
    });
})();
