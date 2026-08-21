<section class="ecosystem">
    <div class="eco-panel">
        <div class="eco-head">
            <span class="eco-badge">Todo incluido</span>
            <h2 class="eco-title">Compras una máquina.<br>Te llevas nueve herramientas.</h2>
        </div>

        <div class="eco-explorer">
            <div class="eco-tabs" role="tablist">
                <?php
                $eco_tools = array(
                    array(
                        'icon'  => 'card',
                        'label' => 'Pagos con tarjeta',
                        'desc'  => 'Débito, crédito, prepago y billeteras digitales. Una sola comisión para todas.',
                    ),
                    array(
                        'icon'  => 'receipt',
                        'label' => 'Boleta electrónica',
                        'desc'  => 'Conectada al SII, sin costo de emisión ni mantención. Imprime o envía por correo.',
                    ),
                    array(
                        'icon'  => 'box',
                        'label' => 'Catálogo e inventario',
                        'desc'  => 'Controla el stock en tiempo real, sincronizado con cada venta que haces.',
                    ),
                    array(
                        'icon'  => 'calendar',
                        'label' => 'Reservas',
                        'desc'  => 'Agenda, cobra y confirma citas 24/7, incluso con el local cerrado.',
                    ),
                    array(
                        'icon'  => 'ticket',
                        'label' => 'Eventos',
                        'desc'  => 'Vende entradas online y presencial, y controla los accesos en tiempo real.',
                    ),
                    array(
                        'icon'  => 'money',
                        'label' => 'Adelanto de dinero',
                        'desc'  => 'Hasta $15.000.000 de adelanto, que se paga solo con tus abonos diarios.',
                    ),
                    array(
                        'icon'  => 'bolt',
                        'label' => 'Abono Flexible',
                        'desc'  => 'Tu dinero en 15 minutos, cuando tú lo pidas, desde la misma máquina.',
                    ),
                    array(
                        'icon'  => 'wallet',
                        'label' => 'Cuotas TUU',
                        'desc'  => 'Ofrece cuotas a tus clientes y paga 0% de comisión en esa venta.',
                    ),
                    array(
                        'icon'  => 'parking',
                        'label' => 'Parking',
                        'desc'  => 'Controla tu estacionamiento desde la misma máquina. Exclusivo Telconnect.',
                    ),
                );

                foreach ( $eco_tools as $i => $tool ) :
                    $active    = 0 === $i ? ' is-active' : '';
                    $icon_url  = get_template_directory_uri() . '/assets/img/icons/eco-' . $tool['icon'] . '.svg';
                    $photo_url = get_template_directory_uri() . '/assets/img/ecosystem/' . $tool['icon'] . '.jpg';
                    ?>
                    <button
                        type="button"
                        class="eco-tab<?php echo esc_attr( $active ); ?>"
                        data-desc="<?php echo esc_attr( $tool['desc'] ); ?>"
                        data-label="<?php echo esc_attr( $tool['label'] ); ?>"
                        data-image="<?php echo esc_url( $photo_url ); ?>"
                    >
                        <img class="eco-tab-icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" width="24" height="24">
                        <?php echo esc_html( $tool['label'] ); ?>
                    </button>
                    <?php
                endforeach;
                ?>
            </div>

            <div class="eco-media">
                <img
                    class="eco-main-photo"
                    src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/ecosystem/' . $eco_tools[0]['icon'] . '.jpg' ); ?>"
                    alt="<?php echo esc_attr( $eco_tools[0]['label'] ); ?>"
                    loading="lazy"
                >
                <div class="eco-media-overlay"></div>
                <div class="eco-glass-card">
                    <span class="eco-glass-title"><?php echo esc_html( $eco_tools[0]['label'] ); ?></span>
                    <p class="eco-glass-desc"><?php echo esc_html( $eco_tools[0]['desc'] ); ?></p>
                </div>
            </div>

            <?php
            /**
             * Navegación con flechas — solo mobile (ver @media en ecosystem.css,
             * mismo breakpoint 900px que oculta .eco-tabs). Reusa el componente
             * visual .dp-arrow/.dp-arrow-prev/.dp-arrow-next del carrusel de
             * productos (devices-products.css) tal cual, sin crear uno nuevo.
             * ecosystem.js reusa la misma función que ya actualiza foto/título/
             * descripción al hacer click en una pill — no hay lógica duplicada.
             */
            ?>
            <div class="eco-mobile-nav">
                <button type="button" class="dp-arrow dp-arrow-prev" id="eco-prev" aria-label="Herramienta anterior">
                    <span></span>
                </button>
                <button type="button" class="dp-arrow dp-arrow-next" id="eco-next" aria-label="Herramienta siguiente">
                    <span></span>
                </button>
            </div>

            <div class="eco-verticals">
                <span class="eco-verticals-caption">Adaptados para cada tipo de negocio</span>
                <div class="eco-tags">
                    <span>#estacionamientos</span>
                    <span>#minimarkets</span>
                    <span>#restaurantes</span>
                    <span>#botillerías</span>
                    <span>#servicios</span>
                    <span>#delivery</span>
                </div>
            </div>
        </div>
    </div>
</section>