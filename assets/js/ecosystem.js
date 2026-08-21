document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.eco-tab');
    var title = document.querySelector('.eco-glass-title');
    var desc = document.querySelector('.eco-glass-desc');
    var photo = document.querySelector('.eco-main-photo');
    var prevBtn = document.getElementById('eco-prev');
    var nextBtn = document.getElementById('eco-next');

    if ( ! tabs.length ) return;

    // Única función que actualiza foto/título/descripción — reusada tanto
    // por el click en una pill (.eco-tab, desktop) como por las flechas
    // prev/next (mobile, ver .eco-mobile-nav en ecosystem.php/.css). No
    // duplicar este flujo si se agrega otro disparador en el futuro.
    function activateTab( tab ) {
        tabs.forEach(function (t) { t.classList.remove('is-active'); });
        tab.classList.add('is-active');
        if ( title ) title.textContent = tab.getAttribute('data-label');
        if ( desc ) desc.textContent = tab.getAttribute('data-desc');
        if ( photo ) {
            photo.src = tab.getAttribute('data-image');
            photo.alt = tab.getAttribute('data-label');
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab( tab );
        });
    });

    function getActiveIndex() {
        var activeIndex = 0;
        tabs.forEach(function (t, i) {
            if ( t.classList.contains('is-active') ) activeIndex = i;
        });
        return activeIndex;
    }

    // Navega a la herramienta activa + offset (-1 = anterior, 1 =
    // siguiente), con wrap-around: después de la última vuelve a la
    // primera y viceversa. Mismas 9 herramientas que las pills, mismo
    // array (data-* de cada .eco-tab) — no se duplica ninguna data.
    function goToOffset( offset ) {
        var total = tabs.length;
        var nextIndex = ( getActiveIndex() + offset + total ) % total;
        activateTab( tabs[ nextIndex ] );
    }

    if ( prevBtn ) {
        prevBtn.addEventListener('click', function () { goToOffset( -1 ); });
    }
    if ( nextBtn ) {
        nextBtn.addEventListener('click', function () { goToOffset( 1 ); });
    }
});
