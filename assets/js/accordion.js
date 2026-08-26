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
 *
 * Además, al final del archivo: acordeón exclusivo para FAQ Home, FAQ
 * Parking y Funcionalidades Parking (solo 1 pregunta abierta a la vez,
 * por grupo), reusando esta misma función de cierre animado — ver
 * comentario más abajo.
 */
document.addEventListener('DOMContentLoaded', function () {
    function closeAnimated(details) {
        var panel = details.querySelector(':scope > *:not(summary)');

        if (! panel || ! details.hasAttribute('open') || details.classList.contains('is-closing')) {
            return;
        }

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
    }

    document.querySelectorAll('details').forEach(function (details) {
        var summary = details.querySelector(':scope > summary');
        var panel = details.querySelector(':scope > *:not(summary)');

        if (!summary || !panel) return;

        summary.addEventListener('click', function (e) {
            if (! details.hasAttribute('open') || details.classList.contains('is-closing')) {
                return;
            }

            e.preventDefault();
            closeAnimated(details);
        });
    });

    /**
     * Acordeón exclusivo (FAQ Home .faq-list, FAQ Parking .faqp-list y
     * Funcionalidades Parking .func-accordion-list): al abrir una
     * pregunta, cierra con la MISMA animación la que estuviera abierta
     * en el mismo grupo. No se usa el atributo nativo `name` para esto:
     * el navegador cerraría el <details> hermano de forma síncrona
     * (mismo bug que closeAnimated existe para arreglar arriba — el
     * contenido se iría a display:none antes de que la transición
     * corra), así que la exclusividad se orquesta acá con el mismo
     * mecanismo que ya usa el cierre manual. Cada .faq-list / .faqp-list /
     * .func-accordion-list es su propio grupo independiente entre sí —
     * en Funcionalidades hay 3 (uno por sección "En la caseta"/"Control
     * y caja"/"Gestión y escala"), no se cierran entre secciones.
     */
    document.querySelectorAll('.faq-list, .faqp-list, .func-accordion-list').forEach(function (group) {
        var items = group.querySelectorAll(':scope > details');

        items.forEach(function (details) {
            details.addEventListener('toggle', function () {
                if (! details.open) return;

                items.forEach(function (other) {
                    if (other !== details && other.hasAttribute('open')) {
                        closeAnimated(other);
                    }
                });
            });
        });
    });
});
