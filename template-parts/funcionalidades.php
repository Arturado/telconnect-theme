<section class="func">
    <div class="func-container">
        <div class="func-head">
            <span class="func-eyebrow">Todo lo que hace</span>
            <h2 class="func-title">Cada función nace de un problema real de estacionamiento</h2>
        </div>

        <?php
        /**
         * Copy real cargado por el usuario (ya no son placeholders).
         *
         * 'image': cada grupo usa un único PNG ya compuesto (los 2 celulares
         * con sus sombras ya integrados en un solo archivo rasterizado), no 2
         * imágenes separadas posicionadas por CSS como en la primera versión.
         */
        $func_groups = array(
            array(
                'eyebrow'    => 'En la caseta',
                'title'      => 'La operación de todos los días',
                'image_side' => 'right',
                'gradient'   => 'linear-gradient(240deg, #2B5BFF 63.4%, #0C2CA8 136.6%)',
                'image'      => 'celulares-1.png',
                'items'      => array(
                    array(
                        'q' => 'Tickets de entrada y salida',
                        'a' => 'Se emiten automáticamente con patente, fecha y hora, y se imprimen en la impresora de tu máquina de pagos o en cualquier impresora Bluetooth Android.',
                    ),
                    array(
                        'q' => 'Configuración de tarifas',
                        'a' => 'Cobra por minuto exacto, por bloques de tiempo o con un tramo inciial gratuito. El cálculo se ajusta al tiempo real de permanencia del vehiculo.',
                    ),
                    array(
                        'q' => 'Descuentos por consumo',
                        'a' => 'Registras el monto de la boleta del cliente y el sistema rebaja el estacionamiento según las reglas que definas. Ideal para supermercados y centro comerciales.',
                    ),
                ),
            ),
            array(
                'eyebrow'    => 'Control y caja',
                'title'      => 'Que la plata cuadre siempre',
                'image_side' => 'left',
                'gradient'   => 'linear-gradient(240deg, #1E4CF0 63.4%, #0A2494 136.6%)',
                'image'      => 'celulares-2.png',
                'items'      => array(
                    array(
                        'q' => 'Cierre de turno con validación',
                        'a' => 'Cada operador genera su reporte al terminar con el total recaudado, los vehículos atendidos y los descuentos aplicados. El administrador debe aprobarlo para cerrar.',
                    ),
                    array(
                        'q' => 'Control de evasores',
                        'a' => 'Si un vehiculo sale sin pagar, su patente queda registrada y el sistema te avisa apenas ese vehiculo vuelve a ingresar.',
                    ),
                    array(
                        'q' => 'Clientes frecuentes',
                        'a' => 'Registra las patenetes habituales y asígnales acceso liberado, descuentos permanentes o una tarifa plana mensual.',
                    ),
                ),
            ),
            array(
                'eyebrow'    => 'Gestión y escala',
                'title'      => 'Cuando ya no es un solo recinto',
                'image_side' => 'right',
                'gradient'   => 'linear-gradient(240deg, #3366FF 63.4%, #12309F 136.6%)',
                'image'      => 'celulares-3.png',
                'items'      => array(
                    array(
                        'q' => 'Reportes en tiempo real',
                        'a' => 'Ingresos, salidas, pagos, descuentos, patentes liberadas y egresos sin cobro. Exporta a Excel o PDF para cuadrar con contabilidad.',
                    ),
                    array(
                        'q' => 'Múltiples sucursales',
                        'a' => 'Administra varias ubicaciones desde una sola cuenta, cada una con su configuración de precios, operadors y  reportes.',
                    ),
                    array(
                        'q' => 'Compatible con tu máquina TUU',
                        'a' => 'Funciona en celulares, tablets y equipos POS Android. Sobre la máquina TUU además cobra con tarjeta y emite la boleta electrónica.',
                    ),
                ),
            ),
        );

        foreach ( $func_groups as $group ) :
            $group_class = 'func-group func-group-image-' . $group['image_side'];
            ?>
            <div class="<?php echo esc_attr( $group_class ); ?>">

                <div class="func-text">
                    <span class="func-group-eyebrow"><?php echo esc_html( $group['eyebrow'] ); ?></span>
                    <h3><?php echo esc_html( $group['title'] ); ?></h3>

                    <div class="func-accordion-list">
                        <?php foreach ( $group['items'] as $i => $item ) : ?>
                            <details class="func-accordion"<?php echo 0 === $i ? ' open' : ''; ?>>
                                <summary>
                                    <span class="func-question"><?php echo esc_html( $item['q'] ); ?></span>
                                    <span class="func-toggle" aria-hidden="true">
                                        <span class="func-toggle-sign"></span>
                                    </span>
                                </summary>
                                <div class="func-answer">
                                    <div class="func-answer-inner">
                                        <p><?php echo esc_html( $item['a'] ); ?></p>
                                    </div>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="func-mockup" style="background: <?php echo esc_attr( $group['gradient'] ); ?>;">
                    <img
                        class="func-mockup-photo"
                        src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/parking/' . $group['image'] ); ?>"
                        alt=""
                        loading="lazy"
                    >
                </div>

            </div>
            <?php
        endforeach;
        ?>
    </div>
</section>