<section class="ecosystem">
    <div class="eco-container">
        <span class="eco-badge">Todo incluido</span>
        <h2 class="eco-title">Compras una máquina.<br>Te llevas nueve herramientas.</h2>

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
                    'label' => 'Telconnect Parking',
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
                    <img class="eco-tab-icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" width="14" height="14">
                    <?php echo esc_html( $tool['label'] ); ?>
                </button>
                <?php
            endforeach;
            ?>
        </div>

        <div class="eco-photo-wrap">
            <img
                class="eco-main-photo"
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/ecosystem/' . $eco_tools[0]['icon'] . '.jpg' ); ?>"
                alt="<?php echo esc_attr( $eco_tools[0]['label'] ); ?>"
                loading="lazy"
            >
            <div class="eco-floating-card">
                <strong class="eco-floating-title"><?php echo esc_html( $eco_tools[0]['label'] ); ?></strong>
                <p class="eco-floating-desc"><?php echo esc_html( $eco_tools[0]['desc'] ); ?></p>
            </div>
        </div>

        <span class="eco-hashtags-label">Adaptados para cada tipo de negocio</span>
        <div class="eco-hashtags">
            <span>#estacionamientos</span>
            <span>#minimarkets</span>
            <span>#restaurantes</span>
            <span>#botillerías</span>
            <span>#servicios</span>
            <span>#delivery</span>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.eco-tab');
    var title = document.querySelector('.eco-floating-title');
    var desc = document.querySelector('.eco-floating-desc');
    var photo = document.querySelector('.eco-main-photo');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            if ( title ) title.textContent = tab.getAttribute('data-label');
            if ( desc ) desc.textContent = tab.getAttribute('data-desc');
            if ( photo ) {
                photo.src = tab.getAttribute('data-image');
                photo.alt = tab.getAttribute('data-label');
            }
        });
    });
});
</script>