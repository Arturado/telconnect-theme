document.addEventListener('DOMContentLoaded', function () {
    // Steppers +/- de cantidad — mismo patrón que pdp.js, generalizado a
    // N filas (una por item del carrito).
    document.querySelectorAll('.crt-qty-stepper').forEach(function (stepper) {
        var input = stepper.querySelector('input.qty');
        var minusBtn = stepper.querySelector('.crt-qty-minus');
        var plusBtn = stepper.querySelector('.crt-qty-plus');

        if (!input || !minusBtn || !plusBtn) return;

        function getStep() {
            return parseInt(input.getAttribute('step'), 10) || 1;
        }

        function getMin() {
            var min = input.getAttribute('min');
            return min !== null ? parseInt(min, 10) : 0;
        }

        function getMax() {
            var max = input.getAttribute('max');
            return max !== null && max !== '' ? parseInt(max, 10) : Infinity;
        }

        minusBtn.addEventListener('click', function () {
            var value = parseInt(input.value, 10) || getMin();
            input.value = Math.max(getMin(), value - getStep());
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        plusBtn.addEventListener('click', function () {
            var value = parseInt(input.value, 10) || getMin();
            input.value = Math.min(getMax(), value + getStep());
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    // El Figma no tiene un botón visible "Actualizar carrito" — la cantidad
    // se actualiza sola al cambiar el input. Se reusa el botón nativo
    // name="update_cart" (oculto vía CSS) como "submitter" del form: si se
    // llamara form.submit() a secas, ese par name/value no viajaría en el
    // POST y WC_Form_Handler::update_cart_action() (que exige
    // isset($_POST['update_cart'])) ignoraría el cambio en silencio.
    var cartForm = document.querySelector('.crt-form');
    var updateCartBtn = cartForm ? cartForm.querySelector('button[name="update_cart"]') : null;

    if (cartForm && updateCartBtn) {
        cartForm.addEventListener('change', function (e) {
            if (!e.target.matches('input.qty')) return;

            // El wc-cart.js del core arranca este botón con disabled="disabled"
            // y solo lo habilita con su propio listener de cambio de cantidad —
            // si ese listener corre después que este (orden no garantizado),
            // requestSubmit(submitter) con el botón todavía disabled no
            // incluye su name/value en el POST y WC ignora el update en
            // silencio. Se fuerza a habilitado acá, justo antes de enviar.
            updateCartBtn.disabled = false;

            if (typeof cartForm.requestSubmit === 'function') {
                cartForm.requestSubmit(updateCartBtn);
            } else {
                updateCartBtn.click();
            }
        });
    }
});
