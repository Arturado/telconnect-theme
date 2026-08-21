/**
 * Reveal animado para TODO <details>/<summary> del sitio (FAQ Home, FAQ
 * Parking, Funcionalidades Parking, accordions de la PDP, cupón del
 * carrito) — genérico, no depende del nombre de clase del panel interno
 * de cada uno.
 *
 * El CSS (grid-template-rows 0fr/1fr, ver main.css + comentario en
 * faq.css) anima la APERTURA sola perfecto sin JS. Pero <details> nativo
 * cierra de forma síncrona (el atributo "open" se saca antes de que el
 * navegador le dé una vuelta de render a la transición), así que el
 * cierre igual se veía instantáneo. Este script solo orquesta el timing
 * del cierre: intercepta el click en <summary>, mantiene "open" puesto
 * un instante más agregando la clase "is-closing" (dispara el mismo CSS
 * hacia 0fr vía `details[open].is-closing > *:not(summary)` en main.css)
 * y recién saca el atributo "open" cuando termina el transitionend real
 * — la animación la sigue haciendo el CSS, esto no la duplica ni la
 * reemplaza.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('details').forEach(function (details) {
        var summary = details.querySelector(':scope > summary');
        var panel = details.querySelector(':scope > *:not(summary)');

        if (!summary || !panel) return;

        summary.addEventListener('click', function (e) {
            if (! details.hasAttribute('open') || details.classList.contains('is-closing')) {
                return;
            }

            e.preventDefault();
            details.classList.add('is-closing');

            var onTransitionEnd = function (ev) {
                if (ev.target !== panel || ev.propertyName !== 'grid-template-rows') {
                    return;
                }
                panel.removeEventListener('transitionend', onTransitionEnd);
                details.classList.remove('is-closing');
                details.removeAttribute('open');
            };
            panel.addEventListener('transitionend', onTransitionEnd);
        });
    });
});
