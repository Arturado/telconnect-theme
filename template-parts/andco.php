<?php
/**
 * Sección AndCo — Home, entre Commission y Devices/Máquinas.
 * Fuente: recursos/andCO/figma-andco.css + capture.png.
 * El mockup de teléfonos (celulares.png) es una imagen plana: el
 * export de Figma para esa columna son cientos de capas vectoriales
 * de un mockup de iPhone, no se recrea en CSS.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="andco">
    <div class="andco-container tc-container">
        <div class="andco-text">
            <span class="andco-eyebrow">Más rápido que inmediato</span>

            <div class="andco-copy">
                <h2 class="andco-title">Recibe el abono de tus ventas, ¡AL INSTANTE! con AndCo.</h2>
                <p class="andco-desc">Mantén todo el dinero de tu negocio conectado con tu cuenta andCo. y tus dispositivos de pago, descarga la app y controla toda tu plata.</p>
            </div>

            <div class="andco-brands">
                <img class="andco-brand-logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/andco/Logo_AndCo.png' ); ?>" alt="AndCo" width="48" height="48">
                <span class="andco-brand-divider" aria-hidden="true"></span>
                <img class="andco-brand-haulmer" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/andco/Haulmer_author_endors.png' ); ?>" alt="Una marca Haulmer" width="132" height="42">
            </div>

            <a href="#" class="btn btn-primary andco-cta" data-amodal-open data-amodal-origen="AndCo"><span class="btn-label-mask"><span class="btn-label">Quiero la app andCo.</span><span class="btn-label-ghost" aria-hidden="true">Quiero la app andCo.</span></span></a>
        </div>

        <div class="andco-visual">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/andco/celulares.png' ); ?>" alt="App AndCo en el celular" width="639" height="712">
        </div>
    </div>
</section>
