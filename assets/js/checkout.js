(function () {
    'use strict';

    function toggleFacturaFields() {
        var docType = document.getElementById('billing_document_type');
        if (!docType) {
            return;
        }

        var isFactura = docType.value === 'factura';

        document.querySelectorAll('.chk-field-factura').forEach(function (field) {
            field.classList.toggle('is-visible', isFactura);
            var input = field.querySelector('input, select, textarea');
            if (input) {
                input.required = isFactura;
            }
        });
    }

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

        rutInput.addEventListener('blur', function () {
            var value = rutInput.value.trim();
            if (!value) {
                rutInput.classList.remove('chk-input-invalid');
                errorEl.classList.remove('is-visible');
                return;
            }

            var valid = validateRut(value);
            rutInput.classList.toggle('chk-input-invalid', !valid);
            errorEl.classList.toggle('is-visible', !valid);
        });

        rutInput.addEventListener('input', function () {
            rutInput.classList.remove('chk-input-invalid');
            errorEl.classList.remove('is-visible');
        });
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
     * Mismo patrón que toggleFacturaFields() — los campos con
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

        var option = checkedRadio.closest('.chk-shipping-option');
        var isPickup = option && option.getAttribute('data-method') === 'pickup';

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
     * Wizard Datos <-> Pago (§8.8)
     * ============================================================
     * Todo el estado vive en un solo atributo: .chk-page[data-step].
     * El CSS (checkout.css) resuelve solo con eso qué título/panel/
     * paso del stepper/nota de confianza mostrar — este JS solo:
     * 1) valida el panel visible antes de avanzar (los campos del otro
     *    panel están display:none, así que quedan afuera de la
     *    validación nativa del navegador),
     * 2) cambia el atributo data-step,
     * 3) reenvía el click del botón "Pagar" visible al #place_order
     *    real de WooCommerce (ver form-checkout.php por qué no se
     *    reconstruye ese botón).
     */
    function initWizard() {
        var page = document.querySelector('.chk-page');
        var form = document.querySelector('form.checkout');
        if (!page || !form) {
            return;
        }

        var nextBtn = document.querySelector('.chk-next-step');
        var prevBtn = document.querySelector('.chk-prev-step');
        var placeOrderProxy = document.querySelector('.chk-place-order-proxy');

        function goToStep(step) {
            page.setAttribute('data-step', step);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (!form.reportValidity()) {
                    return;
                }

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
                    placeOrderProxy.textContent = 'Pagar ' + totalEl.textContent.trim();
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

    document.addEventListener('DOMContentLoaded', function () {
        var docType = document.getElementById('billing_document_type');
        if (docType) {
            toggleFacturaFields();
            docType.addEventListener('change', toggleFacturaFields);
        }
        initRutField('billing_rut');
        initRutField('shipping_rut');
        initComunaCascade();
        initShippingToggle();
        initWizard();
    });
})();
