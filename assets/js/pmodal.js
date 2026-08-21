(function () {
    'use strict';

    var overlay = document.getElementById('pmodal-overlay');
    if (!overlay || typeof tcPmodal === 'undefined') {
        return;
    }

    var card = document.getElementById('pmodal-card');
    var viewForm = document.getElementById('pmodal-view-form');
    var viewSuccess = document.getElementById('pmodal-view-success');
    var form = document.getElementById('pmodal-form');
    var closeX = document.getElementById('pmodal-close-x');
    var closeBtn = document.getElementById('pmodal-close-btn');
    var origenInput = document.getElementById('pmodal-origen');
    var radioHelp = document.getElementById('pmodal-radio-help');
    var errorGeneral = document.getElementById('pmodal-error-general');
    var submitBtn = form.querySelector('.pmodal-submit');
    var submitLabel = document.getElementById('pmodal-submit-label');
    var submitLabelGhost = document.getElementById('pmodal-submit-label-ghost');
    var rutInput = document.getElementById('pmodal-rut');

    var lastTrigger = null;

    // Mismo algoritmo exacto que checkout.js (validateRut) y que
    // WoocommercePlugin\helpers\RutValidator::validate() del lado servidor
    // — duplicado acá a propósito (función pura, sin dependencias del resto
    // de checkout.js) en vez de cargar todo checkout.js solo por esto.
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

    function getFocusable() {
        return Array.prototype.slice
            .call(card.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select, textarea, [tabindex]:not([tabindex="-1"])'))
            .filter(function (el) {
                return el.offsetParent !== null;
            });
    }

    function trapFocus(event) {
        if (event.key !== 'Tab') {
            return;
        }
        var focusable = getFocusable();
        if (!focusable.length) {
            return;
        }
        var first = focusable[0];
        var last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    function clearErrors() {
        form.querySelectorAll('.pmodal-error').forEach(function (el) {
            el.textContent = '';
            el.classList.remove('is-visible');
        });
        form.querySelectorAll('.pmodal-input-invalid').forEach(function (el) {
            el.classList.remove('pmodal-input-invalid');
        });
    }

    function showFieldError(field, message) {
        var input = form.querySelector('[name="' + field + '"]');
        var errorEl = form.querySelector('.pmodal-error[data-error-for="' + field + '"]');
        if (input) {
            input.classList.add('pmodal-input-invalid');
        }
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('is-visible');
        }
    }

    function setSubmitLabel(text) {
        submitLabel.textContent = text;
        submitLabelGhost.textContent = text;
    }

    function updateRadioHelp() {
        var checked = form.querySelector('input[name="tiene_maquina"]:checked');
        if (!checked || !radioHelp) {
            return;
        }
        var text = checked.value === 'no' ? radioHelp.getAttribute('data-help-no') : radioHelp.getAttribute('data-help-si');
        radioHelp.textContent = text;

        form.querySelectorAll('.pmodal-radio-pill').forEach(function (pill) {
            var input = pill.querySelector('input[name="tiene_maquina"]');
            pill.classList.toggle('is-selected', input === checked);
        });
    }

    function resetForm() {
        form.reset();
        clearErrors();
        updateRadioHelp();
        viewForm.hidden = false;
        viewSuccess.hidden = true;
        submitBtn.disabled = false;
        setSubmitLabel('Solicitar prueba');
    }

    function openModal(trigger) {
        lastTrigger = trigger;
        origenInput.value = trigger.getAttribute('data-pmodal-origen') || '';
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pmodal-open');
        document.addEventListener('keydown', onKeydown);

        var firstField = document.getElementById('pmodal-nombre');
        if (firstField) {
            firstField.focus();
        }
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('pmodal-open');
        document.removeEventListener('keydown', onKeydown);
        resetForm();
        if (lastTrigger) {
            lastTrigger.focus();
        }
    }

    function onKeydown(event) {
        if (event.key === 'Escape') {
            closeModal();
            return;
        }
        trapFocus(event);
    }

    document.querySelectorAll('[data-pmodal-open]').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            openModal(trigger);
        });
    });

    closeX.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closeModal();
        }
    });

    form.querySelectorAll('input[name="tiene_maquina"]').forEach(function (radio) {
        radio.addEventListener('change', updateRadioHelp);
    });

    rutInput.addEventListener('blur', function () {
        var value = rutInput.value.trim();
        if (!value) {
            return;
        }
        if (!validateRut(value)) {
            showFieldError('rut', 'RUT inválido. Formato: 12345678-9');
        }
    });

    rutInput.addEventListener('input', function () {
        rutInput.classList.remove('pmodal-input-invalid');
        var errorEl = form.querySelector('.pmodal-error[data-error-for="rut"]');
        if (errorEl) {
            errorEl.classList.remove('is-visible');
        }
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearErrors();
        errorGeneral.textContent = '';
        errorGeneral.classList.remove('is-visible');

        if (!form.reportValidity()) {
            return;
        }

        var rutValue = rutInput.value.trim();
        if (!validateRut(rutValue)) {
            showFieldError('rut', 'RUT inválido. Formato: 12345678-9');
            rutInput.focus();
            return;
        }

        var formData = new FormData(form);
        formData.append('action', 'tc_submit_trial_request');
        formData.append('nonce', tcPmodal.nonce);

        submitBtn.disabled = true;
        setSubmitLabel('Enviando…');

        fetch(tcPmodal.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (json) {
                if (json.success) {
                    viewForm.hidden = true;
                    viewSuccess.hidden = false;
                    closeBtn.focus();
                    return;
                }

                submitBtn.disabled = false;
                setSubmitLabel('Solicitar prueba');

                var data = json.data || {};
                if (data.errors) {
                    var firstField = null;
                    Object.keys(data.errors).forEach(function (field) {
                        showFieldError(field, data.errors[field]);
                        if (!firstField) {
                            firstField = field;
                        }
                    });
                    var el = firstField ? form.querySelector('[name="' + firstField + '"]') : null;
                    if (el) {
                        el.focus();
                    }
                } else {
                    errorGeneral.textContent = data.message || 'Ocurrió un error. Inténtalo de nuevo.';
                    errorGeneral.classList.add('is-visible');
                }
            })
            .catch(function () {
                submitBtn.disabled = false;
                setSubmitLabel('Solicitar prueba');
                errorGeneral.textContent = 'No pudimos conectar. Revisa tu conexión e inténtalo de nuevo.';
                errorGeneral.classList.add('is-visible');
            });
    });
})();
