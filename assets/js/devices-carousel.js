(function () {
    function initProductCarousel(gridId, dotsId, prevId, nextId) {
        var grid = document.getElementById(gridId);
        var dotsWrap = document.getElementById(dotsId);
        var prevBtn = document.getElementById(prevId);
        var nextBtn = document.getElementById(nextId);

        if (!grid || !prevBtn || !nextBtn) return;

        // Cards "reales" tal como las imprimió el PHP, capturadas UNA sola
        // vez antes de insertar cualquier clon de loop — si más adelante
        // se vuelve a hacer querySelectorAll('.dp-card') después de clonar,
        // trae también los clones.
        var realCards = Array.prototype.slice.call(grid.querySelectorAll('.dp-card'));
        var realCount = realCards.length;
        var carouselNav = prevBtn.closest('.dp-carousel-nav');

        if (realCount <= 1) {
            if (carouselNav) carouselNav.style.display = 'none';
            return;
        }

        var insertedClones = [];
        var dots = [];
        var loopEnabled = false;
        var animating = false;
        var settleTimeout;

        function getGap() {
            var style = window.getComputedStyle(grid);
            return parseInt(style.columnGap || style.gap || 24, 10);
        }

        // Con gap entre flex items (no después del último), el borde
        // izquierdo del ítem N (0-based, contando también los clones,
        // todos del mismo ancho) cae siempre en N * step — mismo cálculo
        // que ya usaba la versión anterior para las páginas.
        // getBoundingClientRect().width (sub-pixel, ej. 437.33) en vez de
        // offsetWidth (entero, redondea a 437) — el ancho del card es un
        // % calculado del grid (ver .dp-card en devices-products.css), así
        // que casi nunca cae en un entero exacto. Con offsetWidth el error
        // de redondeo (~0.3px) se arrastra y multiplica en cada salto
        // (realLeft() = N * step), y a los pocos clicks ya se nota como un
        // pedacito del card vecino asomando — justo lo que este carrusel
        // no puede mostrar.
        function getStep() {
            return realCards[0].getBoundingClientRect().width + getGap();
        }

        // Posición scrollLeft del card REAL #index (0..realCount-1) dentro
        // del track ya clonado: el bloque real arranca justo después del
        // set completo de clones "before" (largo realCount).
        function realLeft(index) {
            return ( realCount + index ) * getStep();
        }

        function jumpTo( left ) {
            grid.scrollTo( { left: left, behavior: 'auto' } );
        }

        function smoothTo( left ) {
            grid.scrollTo( { left: left, behavior: 'smooth' } );
        }

        function removeClones() {
            insertedClones.forEach( function ( el ) {
                if ( el.parentNode ) el.parentNode.removeChild( el );
            } );
            insertedClones = [];
        }

        // Clones invisibles para lectores de pantalla y no alcanzables por
        // teclado — son puramente decorativos, el contenido real vive en
        // realCards (mismo patrón que usan Slick/Swiper para sus slides
        // clonados).
        function markAsClone( clone ) {
            clone.setAttribute( 'aria-hidden', 'true' );
            clone.querySelectorAll( 'a, button, input, [tabindex]' ).forEach( function ( el ) {
                el.setAttribute( 'tabindex', '-1' );
            } );
        }

        function buildDots() {
            dotsWrap.innerHTML = '';
            dots = [];
            for ( var i = 0; i < realCount; i++ ) {
                (function ( index ) {
                    var dot = document.createElement( 'span' );
                    if ( index === 0 ) dot.classList.add( 'is-active' );
                    dot.addEventListener( 'click', function () {
                        goTo( index );
                    } );
                    dotsWrap.appendChild( dot );
                    dots.push( dot );
                })( i );
            }
        }

        function updateActiveDot( realIndex ) {
            dots.forEach( function ( dot, i ) {
                dot.classList.toggle( 'is-active', i === realIndex );
            } );
        }

        function goTo( index ) {
            if ( animating || ! loopEnabled ) return;
            animating = true;
            smoothTo( realLeft( index ) );
        }

        function step( direction ) {
            if ( animating || ! loopEnabled ) return;
            animating = true;
            var currentFlat = Math.round( grid.scrollLeft / getStep() );
            smoothTo( ( currentFlat + direction ) * getStep() );
        }

        // Corre en cada "asentamiento" del scroll (click de flecha, click
        // de dot, o swipe/drag manual del visitante — el listener de
        // scroll de más abajo no distingue el origen). Si el scroll quedó
        // en la zona de clones "before" o "after", lo reposiciona AL TOQUE
        // (behavior:'auto', sin animación) al card real equivalente — como
        // el clon es una copia idéntica, el salto no se percibe. Esto es
        // lo que da el efecto de loop infinito sin una animación de vuelta
        // visible hasta el principio.
        function onSettle() {
            animating = false;

            var flat = Math.round( grid.scrollLeft / getStep() );
            var realIndex;

            if ( flat < realCount ) {
                realIndex = flat;
                jumpTo( realLeft( realIndex ) );
            } else if ( flat >= 2 * realCount ) {
                realIndex = flat - 2 * realCount;
                jumpTo( realLeft( realIndex ) );
            } else {
                realIndex = flat - realCount;
            }

            updateActiveDot( realIndex );
        }

        function setup() {
            removeClones();
            animating = false;

            var gap = getGap();
            var stepPx = getStep();
            // Ancho real del track sin clones (sin el gap "de después" del
            // último card, que no existe).
            var contentWidth = realCount * stepPx - gap;
            var hasOverflow = contentWidth > grid.clientWidth + 1;

            if ( ! hasOverflow ) {
                // Entran todos los cards a la vez — no hay nada que
                // recorrer ni loopear (mismo criterio que el early-return
                // de arriba para realCount<=1, pero evaluado de nuevo acá
                // porque esto SÍ puede cambiar con el ancho de pantalla).
                loopEnabled = false;
                if ( carouselNav ) carouselNav.style.display = 'none';
                dotsWrap.innerHTML = '';
                dots = [];
                jumpTo( 0 );
                return;
            }

            loopEnabled = true;
            if ( carouselNav ) carouselNav.style.display = '';

            // 1 set completo de clones a cada lado. Alcanza siempre: solo
            // puede haber 1 salto "en vuelo" por vez (nav bloqueado
            // mientras animating=true), así que la posición nunca se aleja
            // más de 1 card del bloque real.
            var fragBefore = document.createDocumentFragment();
            realCards.forEach( function ( card ) {
                var clone = card.cloneNode( true );
                markAsClone( clone );
                fragBefore.appendChild( clone );
                insertedClones.push( clone );
            } );
            grid.insertBefore( fragBefore, grid.firstChild );

            var fragAfter = document.createDocumentFragment();
            realCards.forEach( function ( card ) {
                var clone = card.cloneNode( true );
                markAsClone( clone );
                fragAfter.appendChild( clone );
                insertedClones.push( clone );
            } );
            grid.appendChild( fragAfter );

            // Arranca posicionado en el card real #0 (no en el clon
            // "before" con el que arrancaría un scrollLeft:0 crudo) — salto
            // instantáneo antes del primer paint, no se alcanza a ver.
            jumpTo( realLeft( 0 ) );
            buildDots();
            updateActiveDot( 0 );
        }

        prevBtn.addEventListener( 'click', function () { step( -1 ); } );
        nextBtn.addEventListener( 'click', function () { step( 1 ); } );

        grid.addEventListener( 'scroll', function () {
            clearTimeout( settleTimeout );
            settleTimeout = setTimeout( onSettle, 100 );
        } );

        setup();

        // El ancho de cada card (step) depende del ancho visible del grid
        // (ver .dp-card, flex-basis en %) — si la ventana cambia de tamaño
        // hay que reconstruir el carrusel: puede cambiar tanto el step en
        // px como si hace falta o no loopear (a cierto ancho podrían entrar
        // todos los cards a la vez).
        var resizeTimeout;
        window.addEventListener( 'resize', function () {
            clearTimeout( resizeTimeout );
            resizeTimeout = setTimeout( setup, 150 );
        } );
    }

    document.addEventListener( 'DOMContentLoaded', function () {
        initProductCarousel( 'dp-grid', 'dp-dots', 'dp-prev', 'dp-next' );
        initProductCarousel( 'dc-grid', 'dc-dots', 'dc-prev', 'dc-next' );
    } );
})();
