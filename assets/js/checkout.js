(function () {
    'use strict';

    function validateRut(rut) {
        rut = rut.replace(/[^0-9kK]/g, '');
        if (rut.length < 8 || rut.length > 9) {
            return false;
        }

        var body = rut.slice(0, -1);
        var dv = rut.slice(-1).toUpperCase();

        var sum = 0;
        var multiplier = 2;
        for (var i = body.length - 1; i >= 0; i--) {
            sum += parseInt(body.charAt(i), 10) * multiplier;
            multiplier = multiplier === 7 ? 2 : multiplier + 1;
        }

        var remainder = 11 - (sum % 11);
        var expected = remainder === 11 ? '0' : remainder === 10 ? 'K' : String(remainder);

        return dv === expected;
    }

    // Helper genérico para marcar/limpiar un campo obligatorio vacío,
    // reusando el mismo lenguaje visual del error de RUT (borde rojo
    // .chk-input-invalid + mensaje .chk-field-rut-error debajo del campo)
    // en vez de inventar un patrón de error nuevo. Usado por
    // validateDatosStep()/validateDespachoStep() más abajo.
    function validateRequiredField(input, emptyMessage) {
        if (!input) {
            return true;
        }

        var row = input.closest('.form-row');
        var valid = !!input.value.trim();

        if (row) {
            var errorEl = row.querySelector('.chk-field-required-error');
            if (!errorEl) {
                errorEl = document.createElement('span');
                errorEl.className = 'chk-field-rut-error chk-field-required-error';
                row.appendChild(errorEl);

                // Igual que en initRutField(): limpiar el error mientras el
                // usuario escribe, no recién al volver a hacer click en
                // "Continuar a pago".
                input.addEventListener('input', function () {
                    input.classList.remove('chk-input-invalid');
                    errorEl.classList.remove('is-visible');
                });
            }
            errorEl.textContent = emptyMessage;
            errorEl.classList.toggle('is-visible', !valid);
        }

        input.classList.toggle('chk-input-invalid', !valid);

        return valid;
    }

    // Registro de validadores forzados por campo de RUT (incluye el caso
    // "vacío", que el blur por sí solo no marca como error para no
    // molestar al usuario antes de que termine de escribir). Lo usan
    // validateDatosStep()/validateDespachoStep() al intentar avanzar de
    // paso.
    var rutValidators = {};

    // Generalizado (§8.8) para poder validar tanto #billing_rut (RUT del
    // comprador) como #shipping_rut (RUT de quien recibe el despacho) con
    // la misma lógica, sin duplicar el código.
    function initRutField(fieldId) {
        var rutInput = document.getElementById(fieldId);
        if (!rutInput) {
            return;
        }

        var errorEl = document.createElement('span');
        errorEl.className = 'chk-field-rut-error';
        errorEl.textContent = 'RUT inválido. Formato: 12345678-9';
        rutInput.closest('.form-row').appendChild(errorEl);

        function applyState(valid) {
            rutInput.classList.toggle('chk-input-invalid', !valid);
            errorEl.classList.toggle('is-visible', !valid);
        }

        rutInput.addEventListener('blur', function () {
            var value = rutInput.value.trim();
            if (!value) {
                applyState(true);
                return;
            }

            applyState(validateRut(value));
        });

        rutInput.addEventListener('input', function () {
            applyState(true);
        });

        rutValidators[fieldId] = function () {
            var value = rutInput.value.trim();
            var valid = !!value && validateRut(value);
            applyState(valid);
            return valid;
        };
    }

    /**
     * ============================================================
     * Región -> Comuna (§8.8)
     * ============================================================
     * Chile no tiene un dataset de comunas nativo en WooCommerce (a
     * diferencia de las regiones, que ya vienen correctas con sus
     * códigos CL-XX vía WC()->countries->get_states('CL')). Este
     * dataset se armó a mano a partir de la división político-
     * administrativa pública de Chile (16 regiones, ~346 comunas) —
     * es un esfuerzo de buena fe por tenerlo completo, no viene de una
     * fuente oficial descargada, así que si el cliente encuentra una
     * comuna faltante o mal escrita, es un ajuste chico acá mismo (un
     * array por región). Documentado como tal en CONTEXT.md §8.8.
     */
    var CHILE_COMUNAS = {
        'CL-AP': ['Arica', 'Camarones', 'General Lagos', 'Putre'],
        'CL-TA': ['Iquique', 'Alto Hospicio', 'Camiña', 'Colchane', 'Huara', 'Pica', 'Pozo Almonte'],
        'CL-AN': ['Antofagasta', 'Mejillones', 'Sierra Gorda', 'Taltal', 'Calama', 'Ollagüe', 'San Pedro de Atacama', 'Tocopilla', 'María Elena'],
        'CL-AT': ['Copiapó', 'Caldera', 'Tierra Amarilla', 'Chañaral', 'Diego de Almagro', 'Vallenar', 'Alto del Carmen', 'Freirina', 'Huasco'],
        'CL-CO': ['La Serena', 'Coquimbo', 'Andacollo', 'La Higuera', 'Paiguano', 'Vicuña', 'Illapel', 'Canela', 'Los Vilos', 'Salamanca', 'Ovalle', 'Combarbalá', 'Monte Patria', 'Punitaqui', 'Río Hurtado'],
        'CL-VS': ['Valparaíso', 'Casablanca', 'Concón', 'Juan Fernández', 'Puchuncaví', 'Quintero', 'Viña del Mar', 'Isla de Pascua', 'Los Andes', 'Calle Larga', 'Rinconada', 'San Esteban', 'La Ligua', 'Cabildo', 'Papudo', 'Petorca', 'Zapallar', 'Quillota', 'La Calera', 'Hijuelas', 'La Cruz', 'Nogales', 'San Antonio', 'Algarrobo', 'Cartagena', 'El Quisco', 'El Tabo', 'Santo Domingo', 'San Felipe', 'Catemu', 'Llaillay', 'Panquehue', 'Putaendo', 'Santa María', 'Quilpué', 'Limache', 'Olmué', 'Villa Alemana'],
        'CL-RM': ['Santiago', 'Cerrillos', 'Cerro Navia', 'Conchalí', 'El Bosque', 'Estación Central', 'Huechuraba', 'Independencia', 'La Cisterna', 'La Florida', 'La Granja', 'La Pintana', 'La Reina', 'Las Condes', 'Lo Barnechea', 'Lo Espejo', 'Lo Prado', 'Macul', 'Maipú', 'Ñuñoa', 'Pedro Aguirre Cerda', 'Peñalolén', 'Providencia', 'Pudahuel', 'Quilicura', 'Quinta Normal', 'Recoleta', 'Renca', 'San Joaquín', 'San Miguel', 'San Ramón', 'Vitacura', 'Puente Alto', 'Pirque', 'San José de Maipo', 'Colina', 'Lampa', 'Tiltil', 'San Bernardo', 'Buin', 'Calera de Tango', 'Paine', 'Melipilla', 'Alhué', 'Curacaví', 'María Pinto', 'San Pedro', 'Talagante', 'El Monte', 'Isla de Maipo', 'Padre Hurtado', 'Peñaflor'],
        'CL-LI': ['Rancagua', 'Codegua', 'Coinco', 'Coltauco', 'Doñihue', 'Graneros', 'Las Cabras', 'Machalí', 'Malloa', 'Mostazal', 'Olivar', 'Peumo', 'Pichidegua', 'Quinta de Tilcoco', 'Rengo', 'Requínoa', 'San Vicente', 'Pichilemu', 'La Estrella', 'Litueche', 'Marchihue', 'Navidad', 'Paredones', 'San Fernando', 'Chépica', 'Chimbarongo', 'Lolol', 'Nancagua', 'Palmilla', 'Peralillo', 'Placilla', 'Pumanque', 'Santa Cruz'],
        'CL-ML': ['Talca', 'Constitución', 'Curepto', 'Empedrado', 'Maule', 'Pelarco', 'Pencahue', 'Río Claro', 'San Clemente', 'San Rafael', 'Cauquenes', 'Chanco', 'Pelluhue', 'Curicó', 'Hualañé', 'Licantén', 'Molina', 'Rauco', 'Romeral', 'Sagrada Familia', 'Teno', 'Vichuquén', 'Linares', 'Colbún', 'Longaví', 'Parral', 'Retiro', 'San Javier', 'Villa Alegre', 'Yerbas Buenas'],
        'CL-NB': ['Chillán', 'Bulnes', 'Chillán Viejo', 'El Carmen', 'Pemuco', 'Pinto', 'Quillón', 'San Ignacio', 'Yungay', 'Cobquecura', 'Coelemu', 'Ninhue', 'Portezuelo', 'Quirihue', 'Ránquil', 'Treguaco', 'San Carlos', 'Coihueco', 'Ñiquén', 'San Fabián', 'San Nicolás'],
        'CL-BI': ['Concepción', 'Coronel', 'Chiguayante', 'Florida', 'Hualqui', 'Lota', 'Penco', 'San Pedro de la Paz', 'Santa Juana', 'Talcahuano', 'Tomé', 'Hualpén', 'Lebu', 'Arauco', 'Cañete', 'Contulmo', 'Curanilahue', 'Los Álamos', 'Tirúa', 'Los Ángeles', 'Antuco', 'Cabrero', 'Laja', 'Mulchén', 'Nacimiento', 'Negrete', 'Quilaco', 'Quilleco', 'San Rosendo', 'Santa Bárbara', 'Tucapel', 'Yumbel', 'Alto Biobío'],
        'CL-AR': ['Temuco', 'Carahue', 'Cunco', 'Curarrehue', 'Freire', 'Galvarino', 'Gorbea', 'Lautaro', 'Loncoche', 'Melipeuco', 'Nueva Imperial', 'Padre las Casas', 'Perquenco', 'Pitrufquén', 'Pucón', 'Saavedra', 'Teodoro Schmidt', 'Toltén', 'Vilcún', 'Villarrica', 'Cholchol', 'Angol', 'Collipulli', 'Curacautín', 'Ercilla', 'Lonquimay', 'Los Sauces', 'Lumaco', 'Purén', 'Renaico', 'Traiguén', 'Victoria'],
        'CL-LR': ['Valdivia', 'Corral', 'Lanco', 'Los Lagos', 'Máfil', 'Mariquina', 'Paillaco', 'Panguipulli', 'La Unión', 'Futrono', 'Lago Ranco', 'Río Bueno'],
        'CL-LL': ['Puerto Montt', 'Calbuco', 'Cochamó', 'Fresia', 'Frutillar', 'Los Muermos', 'Llanquihue', 'Maullín', 'Puerto Varas', 'Castro', 'Ancud', 'Chonchi', 'Curaco de Vélez', 'Dalcahue', 'Puqueldón', 'Queilén', 'Quellón', 'Quemchi', 'Quinchao', 'Osorno', 'Puerto Octay', 'Purranque', 'Puyehue', 'Río Negro', 'San Juan de la Costa', 'San Pablo', 'Chaitén', 'Futaleufú', 'Hualaihué', 'Palena'],
        'CL-AI': ['Coyhaique', 'Lago Verde', 'Aysén', 'Cisnes', 'Guaitecas', 'Cochrane', "O'Higgins", 'Tortel', 'Chile Chico', 'Río Ibáñez'],
        'CL-MA': ['Punta Arenas', 'Laguna Blanca', 'Río Verde', 'San Gregorio', 'Cabo de Hornos', 'Antártica', 'Porvenir', 'Primavera', 'Timaukel', 'Natales', 'Torres del Paine']
    };

    function populateComunas(regionCode, selectedComuna) {
        var comunaSelect = document.getElementById('shipping_comuna');
        if (!comunaSelect) {
            return;
        }

        var comunas = CHILE_COMUNAS[regionCode] || [];
        comunaSelect.innerHTML = '';

        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = comunas.length ? 'Selecciona una comuna' : 'Selecciona una región primero';
        comunaSelect.appendChild(placeholder);

        comunas.forEach(function (name) {
            var opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            if (name === selectedComuna) {
                opt.selected = true;
            }
            comunaSelect.appendChild(opt);
        });
    }

    function initComunaCascade() {
        var regionSelect = document.getElementById('shipping_state');
        var comunaSelect = document.getElementById('shipping_comuna');
        if (!regionSelect || !comunaSelect) {
            return;
        }

        // Si el checkout vuelve a cargar con una región ya elegida
        // (ej. error de validación y el usuario vuelve atrás), repuebla
        // la comuna en vez de dejarla en el placeholder vacío.
        var initialComuna = comunaSelect.getAttribute('data-selected') || '';
        populateComunas(regionSelect.value, initialComuna);

        regionSelect.addEventListener('change', function () {
            populateComunas(regionSelect.value);
        });
    }

    /**
     * ============================================================
     * Card "Despacho": toggle de campos según Retiro/Domicilio
     * ============================================================
     * Los campos con
     * .chk-field-domicilio (RUT receptor, nombre, dirección, número,
     * región, comuna) solo se MUESTRAN cuando el método elegido es
     * "Despacho a domicilio". El refuerzo server-side está en
     * tc_validate_shipping_fields() (functions.php).
     *
     * OJO: "required" se alterna solo en el subconjunto marcado además
     * con .chk-field-domicilio-required (RUT/nombre/dirección/región/
     * comuna) — "Número/depto." (.chk-field-domicilio sin el sufijo
     * -required) es opcional en WooCommerce (no todas las direcciones
     * tienen depto) y por eso NUNCA debía volverse obligatorio. Bug
     * real encontrado con Playwright: al forzar required=true en TODOS
     * los .chk-field-domicilio por igual, "Número/depto." bloqueaba el
     * avance a Pago (form.reportValidity() fallaba) aunque el usuario
     * lo hubiera dejado vacío a propósito.
     */
    /**
     * Mantiene sincronizado el <input type="hidden" name="shipping_method_display[0]">
     * de review-order.php con el radio real recién elegido (ver docblock
     * largo en review-order.php por qué existe este espejo). Se llama
     * ANTES de que el radio termine de procesar su 'change' — como este
     * listener está atado directo al <input> (fase "target"), corre
     * antes que el listener delegado del checkout.js del core (atado al
     * <form>, fase "bubbling"), que es el que arma el payload AJAX
     * leyendo este mismo valor. El orden importa: si se sincronizara
     * después, el core alcanzaría a leer el valor viejo.
     */
    function syncShippingMethodMirror(checkedRadio) {
        var mirror = document.querySelector('.chk-shipping-method-mirror');
        if (mirror && checkedRadio) {
            mirror.value = checkedRadio.value;
        }
    }

    // Reusado también por validateDespachoStep() para saber si hay que
    // exigir los campos de dirección o no.
    function isPickupSelected() {
        var checkedRadio = document.querySelector('.chk-shipping-radio:checked');
        if (!checkedRadio) {
            return false;
        }
        var option = checkedRadio.closest('.chk-shipping-option');
        return !!(option && option.getAttribute('data-method') === 'pickup');
    }

    function toggleDeliveryFields() {
        var checkedRadio = document.querySelector('.chk-shipping-radio:checked');

        syncShippingMethodMirror(checkedRadio);

        document.querySelectorAll('.chk-shipping-option').forEach(function (opt) {
            var radio = opt.querySelector('.chk-shipping-radio');
            opt.classList.toggle('is-selected', !!(radio && radio.checked));
        });

        if (!checkedRadio) {
            return;
        }

        var isPickup = isPickupSelected();

        document.querySelectorAll('.chk-field-domicilio').forEach(function (field) {
            field.classList.toggle('chk-field-hidden', isPickup);
        });

        document.querySelectorAll('.chk-field-domicilio-required').forEach(function (field) {
            var input = field.querySelector('input, select, textarea');
            if (input) {
                input.required = !isPickup;
            }
        });
    }

    function initShippingToggle() {
        var radios = document.querySelectorAll('.chk-shipping-radio');
        if (!radios.length) {
            return;
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                toggleDeliveryFields();
                // El radio vive dentro del <form class="checkout">, así que
                // el checkout.js nativo de WC ya escucha input[name^="shipping_method"]
                // y dispara su propio update_checkout — no hace falta duplicarlo acá.
            });
        });

        toggleDeliveryFields();
    }

    /**
     * ============================================================
     * Card "Forma de pago": resaltado visual (.is-selected) del
     * gateway elegido
     * ============================================================
     * Mismo patrón que toggleDeliveryFields()/initShippingToggle()
     * arriba, pero para .chk-payment-option/.chk-payment-radio. Sin
     * esto, el radio real SÍ cambiaba de checked correctamente al
     * hacer click (la selección que se envía al submit siempre fue
     * la correcta — confirmado con pedidos reales de punta a punta
     * con ambos gateways), pero la card nunca se resaltaba (borde/
     * fondo azul) ni el dot se rellenaba al elegir un método distinto
     * al que vino "chosen" desde el render inicial del servidor,
     * porque nunca se escribió el equivalente de toggleDeliveryFields()
     * para pago — quedó documentado como pendiente en CONTEXT.md §8.20.
     * Reportado por el usuario como "el checkout no deja seleccionar
     * método de pago" — el click SÍ funcionaba, pero sin feedback
     * visual el usuario no tenía forma de saber que su click había
     * registrado, así que en la práctica bloqueaba la compra igual.
     */
    function togglePaymentSelection() {
        document.querySelectorAll('.chk-payment-option').forEach(function (opt) {
            var radio = opt.querySelector('.chk-payment-radio');
            opt.classList.toggle('is-selected', !!(radio && radio.checked));
        });
    }

    // Re-invocable a propósito (no solo una vez en DOMContentLoaded): el
    // core de WooCommerce reemplaza por completo el fragment
    // ".woocommerce-checkout-payment" (los <li> de cada gateway, generados
    // vía payment-method.php) cada vez que corre update_checkout (cambio
    // de método de envío, comuna, cupón, etc.) — eso destruye los <input>
    // radio viejos y los reemplaza por nodos nuevos sin ningún listener
    // atado. Se vuelve a llamar en el evento "updated_checkout" (mismo
    // gancho que syncProxyLabel más abajo) para re-atar los listeners a
    // los radios nuevos y resincronizar .is-selected con lo que el
    // servidor acaba de renderizar como "chosen".
    function initPaymentToggle() {
        var radios = document.querySelectorAll('.chk-payment-radio');
        if (!radios.length) {
            return;
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', togglePaymentSelection);
        });

        togglePaymentSelection();
    }

    /**
     * ============================================================
     * Wizard Datos <-> Pago (§8.8)
     * ============================================================
     * Todo el estado vive en un solo atributo: .chk-page[data-step].
     * El CSS (checkout.css) resuelve solo con eso qué título/panel/
     * paso del stepper/nota de confianza mostrar — este JS solo:
     * 1) valida el panel visible antes de avanzar (los campos del otro
     *    panel están display:none, así que quedan afuera de la
     *    validación nativa del navegador). form.reportValidity() no
     *    alcanza para esto: los campos de WooCommerce (Nombre y
     *    apellido, Teléfono, Correo, RUT, dirección de despacho) no traen el
     *    atributo HTML "required" (WC solo les pone aria-required +
     *    clase validate-required, ver woocommerce_form_field()), así
     *    que validateDatosStep()/validateDespachoStep() los chequean a
     *    mano y reusan el mismo lenguaje visual del error de RUT.
     * 2) cambia el atributo data-step,
     * 3) reenvía el click del botón "Pagar" visible al #place_order
     *    real de WooCommerce (ver form-checkout.php por qué no se
     *    reconstruye ese botón).
     */

    // Nombre y apellido, Teléfono, Correo electrónico, RUT.
    function validateDatosStep() {
        var firstNameValid = validateRequiredField(
            document.getElementById('billing_first_name'),
            'El nombre y apellido es obligatorio.'
        );
        var phoneValid = validateRequiredField(
            document.getElementById('billing_phone'),
            'El teléfono es obligatorio.'
        );
        var emailValid = validateRequiredField(
            document.getElementById('billing_email'),
            'El correo electrónico es obligatorio.'
        );

        var rutValidator = rutValidators.billing_rut;
        var rutValid = rutValidator ? rutValidator() : true;

        return firstNameValid && phoneValid && emailValid && rutValid;
    }

    // RUT receptor, Nombre de quien recibe, Dirección, Región, Comuna —
    // solo si el método elegido es "Despacho a domicilio" (con "Retiro en
    // tienda" ninguno de estos campos aplica, mismo criterio que
    // toggleDeliveryFields()/tc_validate_shipping_fields() en PHP).
    function validateDespachoStep() {
        if (isPickupSelected()) {
            return true;
        }

        var rutValidator = rutValidators.shipping_rut;
        var rutValid = rutValidator ? rutValidator() : true;

        var nameValid = validateRequiredField(
            document.getElementById('shipping_first_name'),
            'El nombre de quien recibe es obligatorio.'
        );
        var addressValid = validateRequiredField(
            document.getElementById('shipping_address_1'),
            'La dirección es obligatoria.'
        );
        var regionValid = validateRequiredField(
            document.getElementById('shipping_state'),
            'La región es obligatoria.'
        );
        var comunaValid = validateRequiredField(
            document.getElementById('shipping_comuna'),
            'La comuna es obligatoria.'
        );

        return rutValid && nameValid && addressValid && regionValid && comunaValid;
    }

    function initWizard() {
        var page = document.querySelector('.chk-page');
        var form = document.querySelector('form.checkout');
        if (!page || !form) {
            return;
        }

        var nextBtn = document.querySelector('.chk-next-step');
        var prevBtn = document.querySelector('.chk-prev-step');
        var placeOrderProxy = document.querySelector('.chk-place-order-proxy');
        var stepWarning = document.querySelector('.chk-step-warning');

        function goToStep(step) {
            page.setAttribute('data-step', step);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Además del error puntual bajo cada campo (rojo + mensaje, ver
        // validateRequiredField), sube el aviso general arriba del botón
        // y lleva la vista al primer campo inválido: si el campo con el
        // error quedó fuera de la pantalla (el usuario ya scrolleó hasta
        // el botón), el borde rojo solo no alcanza para que lo note.
        function showStepWarning() {
            if (stepWarning) {
                stepWarning.hidden = false;
            }
            var firstInvalid = document.querySelector('.chk-step-panel[data-panel="datos"] .chk-input-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus({ preventScroll: true });
            }
        }

        function hideStepWarning() {
            if (stepWarning) {
                stepWarning.hidden = true;
            }
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                // Bloquear el avance a "Pago" si faltan datos del cliente o
                // (cuando aplica) datos de despacho. Ambas se llaman siempre
                // (sin cortocircuito &&) para que se marquen TODOS los
                // campos faltantes de una vez, no solo el primero.
                var datosValid = validateDatosStep();
                var despachoValid = validateDespachoStep();

                if (!datosValid || !despachoValid) {
                    showStepWarning();
                    return;
                }

                if (!form.reportValidity()) {
                    showStepWarning();
                    return;
                }

                hideStepWarning();

                // Fuerza un recálculo de #order_review ANTES de mostrar el
                // paso Pago: si el comprador llenó la dirección y avanzó
                // rápido, el debounce nativo de WC (~1s tras el último
                // campo tocado) podía no haber alcanzado a correr todavía,
                // dejando la línea "Despacho" del resumen con el label
                // genérico de la tarifa en vez de la dirección recién
                // tecleada. jQuery(body).trigger('update_checkout') es el
                // mismo evento que WC dispara solo — no es un endpoint
                // propio, solo se adelanta el timing.
                if (window.jQuery) {
                    window.jQuery(document.body).trigger('update_checkout');
                }

                goToStep('pago');
            });
        }

        // Si el comprador corrige el campo a mano (sin volver a apretar
        // "Continuar a pago"), retirar el aviso general en cuanto no
        // quede ningún campo marcado en rojo en el panel "Datos".
        var datosPanel = document.querySelector('.chk-step-panel[data-panel="datos"]');
        if (datosPanel) {
            datosPanel.addEventListener('input', function () {
                if (!datosPanel.querySelector('.chk-input-invalid')) {
                    hideStepWarning();
                }
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                goToStep('datos');
            });
        }

        if (placeOrderProxy) {
            placeOrderProxy.addEventListener('click', function () {
                var realBtn = document.getElementById('place_order');
                if (realBtn) {
                    realBtn.click();
                }
            });

            function syncProxyLabel() {
                var totalEl = document.querySelector('.chk-summary-total span:last-child');
                if (totalEl && totalEl.textContent.trim()) {
                    // No se usa textContent sobre el botón: destruiría el markup
                    // del roll de texto (.btn-label-mask > .btn-label +
                    // .btn-label-ghost). Se actualizan los 2 spans por separado,
                    // con el mismo texto, para que el hover siga funcionando.
                    var newText = 'Pagar ' + totalEl.textContent.trim();
                    var label = placeOrderProxy.querySelector('.btn-label');
                    var ghost = placeOrderProxy.querySelector('.btn-label-ghost');
                    if (label) {
                        label.textContent = newText;
                    }
                    if (ghost) {
                        ghost.textContent = newText;
                    }
                }
            }

            // "updated_checkout" lo dispara WooCommerce vía jQuery.trigger()
            // después de cada recálculo AJAX de totales (cambio de método de
            // envío, de comuna, etc.) — con jQuery cargado como dependencia
            // (ver telconnect_enqueue_checkout_assets()) esto lo capta bien;
            // un addEventListener nativo no vería este evento.
            if (window.jQuery) {
                window.jQuery(document.body).on('updated_checkout', syncProxyLabel);
            }
        }
    }

    /**
     * ============================================================
     * Cupón del resumen (§8.8, ver docblocks en functions.php y
     * woocommerce/checkout/form-coupon.php)
     * ============================================================
     * El bloque vive dentro de review-order.php, que WC_AJAX::update_order_review()
     * reemplaza ENTERO ($(selector).replaceWith(html)) en cada recálculo
     * (cambio de método de envío, comuna, o el propio envío de este
     * cupón). Por eso el envío no puede atarse directo al nodo como
     * hace wc_checkout_coupons.init() del core (ese binding se pierde
     * apenas el nodo original se reemplaza) — se delega en document,
     * que no le importa si el nodo es el original o uno recién salido
     * del fragment nuevo. La clase es .tc-chk-coupon-form (NO
     * .checkout_coupon) justamente para que el core no le ponga
     * display:none ni se pise con este listener.
     *
     * .tc-chk-coupon-form es un <div>, NO un <form> (ver docblock de
     * form-coupon.php): este bloque vive DENTRO de <form class="checkout">,
     * y un <form> anidado ahí rompe el submit real en un click de
     * verdad — el evento 'submit' hace bubble y pasa primero por el
     * <form class="checkout"> ancestro, donde el propio wc_checkout_form
     * del core lo intercepta antes de que llegue al listener delegado
     * en document, y termina en un POST nativo de página completa (bug
     * real confirmado con Chrome: un $form.trigger('submit') sintético
     * sí llegaba al listener, pero un click real del usuario no). Por
     * eso acá se dispara a mano por click en el botón o Enter en el
     * input, nunca por el evento 'submit' del navegador.
     *
     * Se reimplementa a mano la misma llamada que wc_checkout_coupons.submit()
     * del core (wc-ajax=apply_coupon con el nonce apply_coupon_nonce ya
     * localizado por WC en wc_checkout_params), pero simplificada: acá
     * el campo se deja siempre visible (no hay panel que abrir/cerrar),
     * así que no hace falta el slideUp/slideToggle del core. El HTML
     * que devuelve el endpoint (wc_print_notices()) se inserta tal cual
     * antes del bloque — mismo notice box .woocommerce-error/-message
     * que el resto del checkout (checkout.css ya lo estiliza), tanto
     * para cupón válido como inválido/expirado.
     */
    function initCheckoutCouponForm() {
        if (!window.jQuery || typeof wc_checkout_params === 'undefined') {
            return;
        }

        var $ = window.jQuery;

        function submitCoupon($form) {
            if ($form.hasClass('processing')) {
                return;
            }

            var $couponField = $form.find('#coupon_code');
            var couponCode = $couponField.val();

            if (!couponCode) {
                return;
            }

            $form.addClass('processing').block({
                message: null,
                overlayCSS: { background: '#fff', opacity: 0.6 }
            });

            $.ajax({
                type: 'POST',
                url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
                data: {
                    security: wc_checkout_params.apply_coupon_nonce,
                    coupon_code: couponCode,
                    billing_email: $('#billing_email').val()
                },
                dataType: 'html',
                success: function (response) {
                    $('.woocommerce-error, .woocommerce-message, .is-error, .is-success').remove();
                    $form.removeClass('processing').unblock();

                    if (!response) {
                        return;
                    }

                    $form.before(response);

                    // update_checkout reemplaza TODO .woocommerce-checkout-review-order-table
                    // (ver docblock de más arriba), incluyendo el aviso que se
                    // acaba de insertar acá arriba — si se dispara también en
                    // el caso de error, el "cupón inválido/expirado" desaparece
                    // solo, antes de que el comprador alcance a leerlo. Por eso
                    // solo se recalcula el total cuando el cupón SÍ se aplicó
                    // (mismo indicador que usa el core: ausencia de
                    // woocommerce-error/is-error en la respuesta) — la línea de
                    // descuento que aparece en el resumen tras el recálculo ya
                    // es la confirmación visual que persiste.
                    var isError = response.indexOf('woocommerce-error') !== -1 || response.indexOf('is-error') !== -1;
                    if (isError) {
                        return;
                    }

                    $couponField.val('');
                    $(document.body).trigger('applied_coupon_in_checkout', [couponCode]);
                    $(document.body).trigger('update_checkout', { update_shipping_method: false });
                },
                error: function () {
                    $form.removeClass('processing').unblock();
                }
            });
        }

        $(document).on('click', '.tc-chk-coupon-submit', function (evt) {
            evt.preventDefault();
            submitCoupon($(this).closest('.tc-chk-coupon-form'));
        });

        $(document).on('keydown', '.tc-chk-coupon-form #coupon_code', function (evt) {
            if (evt.which === 13 || evt.key === 'Enter') {
                evt.preventDefault();
                submitCoupon($(this).closest('.tc-chk-coupon-form'));
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initRutField('billing_rut');
        initRutField('shipping_rut');
        initComunaCascade();
        initShippingToggle();
        initPaymentToggle();
        initWizard();
        initCheckoutCouponForm();

        // Ver docblock de initPaymentToggle(): reatar tras cada refresh de
        // fragment de WooCommerce, mismo gancho que syncProxyLabel.
        if (window.jQuery) {
            window.jQuery(document.body).on('updated_checkout', initPaymentToggle);
        }
    });
})();
