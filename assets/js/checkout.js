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

    function initRutField() {
        var rutInput = document.getElementById('billing_rut');
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

    document.addEventListener('DOMContentLoaded', function () {
        var docType = document.getElementById('billing_document_type');
        if (docType) {
            toggleFacturaFields();
            docType.addEventListener('change', toggleFacturaFields);
        }
        initRutField();
    });
})();
