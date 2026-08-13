(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var grid = document.getElementById('dp-grid');
        var dotsWrap = document.getElementById('dp-dots');
        var prevBtn = document.getElementById('dp-prev');
        var nextBtn = document.getElementById('dp-next');

        if (!grid) return;

        var cards = Array.prototype.slice.call(grid.querySelectorAll('.dp-card'));
        if (cards.length === 0) return;

        // Solo mostrar nav (flechas + dots) si hay más de 1 producto
        var carouselNav = document.querySelector('.dp-carousel-nav');
        if (cards.length <= 1) {
            if (carouselNav) carouselNav.style.display = 'none';
            return;
        }

        // Generar dots dinámicamente según cantidad de productos
        cards.forEach(function (card, i) {
            var dot = document.createElement('span');
            if (i === 0) dot.classList.add('is-active');
            dot.addEventListener('click', function () {
                scrollToCard(i);
            });
            dotsWrap.appendChild(dot);
        });

        var dots = Array.prototype.slice.call(dotsWrap.children);

        function getCardStep() {
            var card = cards[0];
            var style = window.getComputedStyle(grid);
            var gap = parseInt(style.columnGap || style.gap || 24, 10);
            return card.offsetWidth + gap;
        }

        function scrollToCard(index) {
            grid.scrollTo({
                left: index * getCardStep(),
                behavior: 'smooth'
            });
        }

        function updateActiveDot() {
            var step = getCardStep();
            var index = Math.round(grid.scrollLeft / step);
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === index);
            });
        }

        prevBtn.addEventListener('click', function () {
            var step = getCardStep();
            var index = Math.round(grid.scrollLeft / step);
            scrollToCard(Math.max(0, index - 1));
        });

        nextBtn.addEventListener('click', function () {
            var step = getCardStep();
            var index = Math.round(grid.scrollLeft / step);
            scrollToCard(Math.min(cards.length - 1, index + 1));
        });

        var scrollTimeout;
        grid.addEventListener('scroll', function () {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(updateActiveDot, 100);
        });
    });
})();