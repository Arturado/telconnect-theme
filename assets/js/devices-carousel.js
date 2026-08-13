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
    }

    document.addEventListener('DOMContentLoaded', function () {
        initProductCarousel('dp-grid', 'dp-dots', 'dp-prev', 'dp-next');
        initProductCarousel('dc-grid', 'dc-dots', 'dc-prev', 'dc-next');
    });
})();