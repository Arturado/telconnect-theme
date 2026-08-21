(function () {
    function initProductCarousel(gridId, dotsId, prevId, nextId) {
        var grid = document.getElementById(gridId);
        var dotsWrap = document.getElementById(dotsId);
        var prevBtn = document.getElementById(prevId);
        var nextBtn = document.getElementById(nextId);

        if (!grid) return;

        var cards = Array.prototype.slice.call(grid.querySelectorAll('.dp-card'));
        if (cards.length === 0) return;

        var carouselNav = prevBtn ? prevBtn.closest('.dp-carousel-nav') : null;
        if (cards.length <= 1) {
            if (carouselNav) carouselNav.style.display = 'none';
            return;
        }

        function getGap() {
            var style = window.getComputedStyle(grid);
            return parseInt(style.columnGap || style.gap || 24, 10);
        }

        function getCardStep() {
            return cards[0].offsetWidth + getGap();
        }

        // Los dots representan PÁGINAS visibles del carrusel, no productos
        // uno a uno — con N cards visibles por página (depende del ancho
        // real disponible, cambia por breakpoint), la cantidad de páginas
        // es Math.ceil(total / porPágina). Se recalcula en cada resize
        // porque "cuántas cards entran" cambia con el ancho de pantalla.
        //
        // getCardsPerPage(): el card N-ésimo que todavía entra en el ancho
        // visible no necesita el gap "de después" (solo hay gap ENTRE
        // cards, no al final) — por eso se suma 1 gap al ancho disponible
        // antes de dividir por el step (card+gap). Sin este +gap el
        // cálculo se queda corto en 1 card contra la realidad (ej.: 3
        // cards de 410px con 24px de gap caben perfecto en un contenedor
        // de 1280px — 3×410 + 2×24 = 1278px — pero floor(1280/434) da 2).
        function getCardsPerPage() {
            var step = getCardStep();
            var visibleWidth = grid.clientWidth;
            return Math.max(1, Math.floor((visibleWidth + getGap()) / step));
        }

        var cardsPerPage = 1;
        var totalPages = 1;
        var dots = [];

        // Cuando el total de cards no es múltiplo exacto de cardsPerPage
        // (el caso común — ej. 3 cards con 2 por página), la ÚLTIMA
        // página no necesita desplazarse un "page-step" completo: el
        // navegador clampea scrollLeft al máximo real
        // (scrollWidth - clientWidth), que queda más corto que
        // cardsPerPage * step. Dividir ese scrollLeft clampeado por el
        // page-step "redondo" subestima el índice (ej. calculaba página
        // 0 en vez de la última) — se detecta aparte: si scrollLeft ya
        // está en (o casi en) el máximo posible, es la última página,
        // sin pasar por la división.
        function getCurrentPage() {
            var maxScroll = grid.scrollWidth - grid.clientWidth;
            if (maxScroll <= 1 || grid.scrollLeft >= maxScroll - 2) {
                return totalPages - 1;
            }
            var pageStep = cardsPerPage * getCardStep();
            var index = Math.round(grid.scrollLeft / pageStep);
            return Math.min(totalPages - 1, Math.max(0, index));
        }

        function scrollToPage(pageIndex) {
            var pageStep = cardsPerPage * getCardStep();
            grid.scrollTo({
                left: pageIndex * pageStep,
                behavior: 'smooth'
            });
        }

        function updateActiveDot() {
            var current = getCurrentPage();
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === current);
            });
        }

        function buildDots() {
            dotsWrap.innerHTML = '';
            dots = [];
            for (var i = 0; i < totalPages; i++) {
                (function (pageIndex) {
                    var dot = document.createElement('span');
                    if (pageIndex === 0) dot.classList.add('is-active');
                    dot.addEventListener('click', function () {
                        scrollToPage(pageIndex);
                    });
                    dotsWrap.appendChild(dot);
                    dots.push(dot);
                })(i);
            }
        }

        function recalcLayout() {
            cardsPerPage = getCardsPerPage();
            totalPages = Math.max(1, Math.ceil(cards.length / cardsPerPage));

            // Con todas las cards visibles a la vez (ancho de pantalla
            // grande) no hay nada que paginar — misma UX que el early
            // return de arriba para cards.length<=1, pero evaluado de
            // nuevo en cada resize porque acá SÍ puede cambiar con el
            // ancho (a diferencia del total de cards, que es fijo).
            if (carouselNav) {
                carouselNav.style.display = totalPages <= 1 ? 'none' : '';
            }
            if (totalPages <= 1) {
                dotsWrap.innerHTML = '';
                dots = [];
                return;
            }

            buildDots();
            updateActiveDot();
        }

        recalcLayout();

        prevBtn.addEventListener('click', function () {
            scrollToPage(Math.max(0, getCurrentPage() - 1));
        });

        nextBtn.addEventListener('click', function () {
            scrollToPage(Math.min(totalPages - 1, getCurrentPage() + 1));
        });

        var scrollTimeout;
        grid.addEventListener('scroll', function () {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(updateActiveDot, 100);
        });

        // cardsPerPage depende del ancho visible del grid — si la ventana
        // cambia de tamaño (o rota el celular), recalcula páginas/dots en
        // vez de dejarlos pegados al valor con el que cargó la página.
        var resizeTimeout;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(recalcLayout, 150);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initProductCarousel('dp-grid', 'dp-dots', 'dp-prev', 'dp-next');
        initProductCarousel('dc-grid', 'dc-dots', 'dc-prev', 'dc-next');
    });
})();
