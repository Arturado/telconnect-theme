<?php
/**
 * Modal "Solicita tu prueba" (15 días) — Landing de Parking.
 *
 * Instancia única compartida por los 3 botones que la abren (Hero,
 * Cómo Empezar, Planes) — cada botón engancha con el atributo común
 * `data-pmodal-open` + `data-pmodal-origen` (assets/js/pmodal.js), en vez
 * de 3 modales/listeners separados. Se imprime 1 sola vez en page-parking.php,
 * justo antes de get_footer().
 */
?>
<div class="pmodal-overlay" id="pmodal-overlay" aria-hidden="true">
    <div class="pmodal-card" role="dialog" aria-modal="true" aria-labelledby="pmodal-title" id="pmodal-card">
        <button type="button" class="pmodal-close" id="pmodal-close-x" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>

        <div class="pmodal-view pmodal-view-form" id="pmodal-view-form">
            <span class="pmodal-eyebrow">Prueba de 15 días</span>
            <h2 class="pmodal-title" id="pmodal-title">Solicita tu prueba</h2>
            <p class="pmodal-desc">Déjanos tus datos y te contactamos el mismo día hábil para activarla. No pedimos tarjeta ni compromiso.</p>

            <form class="pmodal-form" id="pmodal-form" novalidate>
                <input type="hidden" name="origen" id="pmodal-origen" value="">

                <div class="pmodal-field">
                    <label for="pmodal-nombre">Nombre y apellido</label>
                    <input type="text" id="pmodal-nombre" name="nombre" required autocomplete="name">
                    <span class="pmodal-error" data-error-for="nombre"></span>
                </div>

                <div class="pmodal-field">
                    <label for="pmodal-rut">RUT</label>
                    <input type="text" id="pmodal-rut" name="rut" placeholder="12.345.678-9" required inputmode="text">
                    <span class="pmodal-error" data-error-for="rut"></span>
                </div>

                <div class="pmodal-field">
                    <label for="pmodal-telefono">Número de teléfono</label>
                    <input type="tel" id="pmodal-telefono" name="telefono" placeholder="+56 9 1234 5678" required autocomplete="tel">
                    <span class="pmodal-error" data-error-for="telefono"></span>
                </div>

                <div class="pmodal-field">
                    <label for="pmodal-email">Correo electrónico</label>
                    <input type="email" id="pmodal-email" name="email" placeholder="tu@correo.cl" required autocomplete="email">
                    <span class="pmodal-error" data-error-for="email"></span>
                </div>

                <div class="pmodal-field">
                    <label id="pmodal-maquina-label">¿Tienes una máquina de pagos TUU?</label>
                    <div class="pmodal-radio-group" role="radiogroup" aria-labelledby="pmodal-maquina-label">
                        <label class="pmodal-radio-pill is-selected">
                            <input type="radio" name="tiene_maquina" value="si" checked>
                            <span class="pmodal-radio-dot" aria-hidden="true"></span>
                            <span>Sí</span>
                        </label>
                        <label class="pmodal-radio-pill">
                            <input type="radio" name="tiene_maquina" value="no">
                            <span class="pmodal-radio-dot" aria-hidden="true"></span>
                            <span>No</span>
                        </label>
                    </div>
                    <?php
                    /**
                     * El texto de ayuda para "Sí" viene textual de la captura
                     * (recursos/modal-parking/Solicitud · Desktop.png). La
                     * captura solo muestra el estado "Sí" seleccionado — el
                     * texto para "No" no está en ninguna referencia visual,
                     * es copy razonable inventado por mí, mismo criterio que
                     * los placeholders ya documentados en funcionalidades.php
                     * / faq-parking.php. Validar con el cliente antes de
                     * publicar.
                     */
                    ?>
                    <p class="pmodal-radio-help" id="pmodal-radio-help" data-help-si="Si ya tienes una, activamos la app sobre tu equipo actual." data-help-no="Sin problema, te ayudamos a elegir el equipo correcto y coordinamos la instalación.">Si ya tienes una, activamos la app sobre tu equipo actual.</p>
                </div>

                <span class="pmodal-error pmodal-error-general" id="pmodal-error-general"></span>

                <button type="submit" class="pmodal-submit">
                    <span class="btn-label-mask">
                        <span class="btn-label" id="pmodal-submit-label">Solicitar prueba</span>
                        <span class="btn-label-ghost" aria-hidden="true" id="pmodal-submit-label-ghost">Solicitar prueba</span>
                    </span>
                </button>

                <p class="pmodal-legal">Al enviar aceptas que te contactemos por este medio. No compartimos tus datos con terceros.</p>
            </form>
        </div>

        <div class="pmodal-view pmodal-view-success" id="pmodal-view-success" hidden>
            <span class="pmodal-success-icon" aria-hidden="true"></span>
            <h2 class="pmodal-success-title">Solicitud enviada</h2>
            <p class="pmodal-success-desc">Te contactamos el mismo día hábil para coordinar la activación. Si prefieres adelantarlo, escríbenos por WhatsApp y lo vemos al tiro.</p>
            <button type="button" class="pmodal-close-btn" id="pmodal-close-btn">
                <span class="btn-label-mask">
                    <span class="btn-label">Cerrar</span>
                    <span class="btn-label-ghost" aria-hidden="true">Cerrar</span>
                </span>
            </button>
        </div>
    </div>
</div>
