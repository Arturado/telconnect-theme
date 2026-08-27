<?php
/**
 * Modal "Solicita tu prueba" (15 días) — Home, botón "Quiero la app andCo."
 * (template-parts/andco.php).
 *
 * Componente separado del modal de Parking (template-parts/modal-prueba-parking.php)
 * a propósito: vive en otra plantilla (front-page.php), tiene su propio
 * copy/marca y su propio namespace de IDs/clases (amodal-*) para no
 * colisionar si ambos terminaran cargados en la misma página. Comparte el
 * mismo backend (tc_submit_trial_request → tc_solicitudes_prueba_guardar())
 * pasando origen="AndCo" — ver assets/js/amodal.js.
 *
 * Se imprime 1 sola vez en front-page.php, justo antes de get_footer().
 */
?>
<div class="amodal-overlay" id="amodal-overlay" aria-hidden="true">
    <div class="amodal-card" role="dialog" aria-modal="true" aria-labelledby="amodal-title" id="amodal-card">
        <button type="button" class="amodal-close" id="amodal-close-x" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>

        <div class="amodal-view amodal-view-form" id="amodal-view-form">
            <span class="amodal-eyebrow">ABONO AL INSTANTE</span>
            <h2 class="amodal-title" id="amodal-title">Solicita Andco y recibe tus abonos al instante</h2>
            <p class="amodal-desc">Completa tus datos y solicita tu cuenta digital Andco. Te contactaremos a la brevedad para ayudarte a activarla y comenzar a recibir el abono de tus ventas al instante.</p>

            <form class="amodal-form" id="amodal-form" novalidate>
                <input type="hidden" name="origen" id="amodal-origen" value="">

                <div class="amodal-field">
                    <label for="amodal-nombre">Nombre y apellido</label>
                    <input type="text" id="amodal-nombre" name="nombre" required autocomplete="name">
                    <span class="amodal-error" data-error-for="nombre"></span>
                </div>

                <div class="amodal-field">
                    <label for="amodal-rut">RUT</label>
                    <input type="text" id="amodal-rut" name="rut" placeholder="12.345.678-9" required inputmode="text">
                    <span class="amodal-error" data-error-for="rut"></span>
                </div>

                <div class="amodal-field">
                    <label for="amodal-telefono">Número de teléfono</label>
                    <input type="tel" id="amodal-telefono" name="telefono" placeholder="+56 9 1234 5678" required autocomplete="tel">
                    <span class="amodal-error" data-error-for="telefono"></span>
                </div>

                <div class="amodal-field">
                    <label for="amodal-email">Correo electrónico</label>
                    <input type="email" id="amodal-email" name="email" placeholder="tu@correo.cl" required autocomplete="email">
                    <span class="amodal-error" data-error-for="email"></span>
                </div>

                <div class="amodal-field">
                    <label id="amodal-maquina-label">¿Tienes una máquina de pagos TUU?</label>
                    <div class="amodal-radio-group" role="radiogroup" aria-labelledby="amodal-maquina-label">
                        <label class="amodal-radio-pill is-selected">
                            <input type="radio" name="tiene_maquina" value="si" checked>
                            <span class="amodal-radio-dot" aria-hidden="true"></span>
                            <span>Sí</span>
                        </label>
                        <label class="amodal-radio-pill">
                            <input type="radio" name="tiene_maquina" value="no">
                            <span class="amodal-radio-dot" aria-hidden="true"></span>
                            <span>No</span>
                        </label>
                    </div>
                    <?php
                    /**
                     * Copy base = el mismo texto del modal de Parking (placeholder
                     * a propósito, ver modal-prueba-parking.php) — falta ajustarlo
                     * al contexto de AndCo antes de publicar.
                     */
                    ?>
                    <p class="amodal-radio-help" id="amodal-radio-help" data-help-si="Si ya tienes una, activamos la app sobre tu equipo actual." data-help-no="Sin problema, te ayudamos a elegir el equipo correcto y coordinamos la instalación.">Si ya tienes una, activamos la app sobre tu equipo actual.</p>
                </div>

                <span class="amodal-error amodal-error-general" id="amodal-error-general"></span>

                <button type="submit" class="amodal-submit">
                    <span class="btn-label-mask">
                        <span class="btn-label" id="amodal-submit-label">Solicitar prueba</span>
                        <span class="btn-label-ghost" aria-hidden="true" id="amodal-submit-label-ghost">Solicitar prueba</span>
                    </span>
                </button>

                <p class="amodal-legal">Al enviar aceptas que te contactemos por este medio. No compartimos tus datos con terceros.</p>
            </form>
        </div>

        <div class="amodal-view amodal-view-success" id="amodal-view-success" hidden>
            <span class="amodal-success-icon" aria-hidden="true"></span>
            <h2 class="amodal-success-title">Solicitud enviada</h2>
            <p class="amodal-success-desc">Te contactamos el mismo día hábil para coordinar la activación. Si prefieres adelantarlo, escríbenos por WhatsApp y lo vemos al tiro.</p>
            <button type="button" class="amodal-close-btn" id="amodal-close-btn">
                <span class="btn-label-mask">
                    <span class="btn-label">Cerrar</span>
                    <span class="btn-label-ghost" aria-hidden="true">Cerrar</span>
                </span>
            </button>
        </div>
    </div>
</div>
