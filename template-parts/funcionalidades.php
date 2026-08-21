<section class="func">
    <div class="func-container">
        <div class="func-head">
            <span class="func-eyebrow">Todo lo que hace</span>
            <h2 class="func-title">Cada función nace de un problema real de estacionamiento</h2>
        </div>

        <?php
        /**
         * Copy de las respuestas cerradas (Configuración de tarifas, Descuentos
         * por consumo, Control de evasores, Clientes frecuentes, Múltiples
         * sucursales, Compatible con tu máquina TUU): el Figma solo mostraba el
         * texto de la pregunta ABIERTA de cada grupo (la primera). Estas 6
         * respuestas son un placeholder razonable inventado, no copy real del
         * cliente — validar antes de publicar, mismo criterio que usamos en el
         * FAQ del Home.
         */
        $func_groups = array(
            array(
                'eyebrow'    => 'En la caseta',
                'title'      => 'La operación de todos los días',
                'image_side' => 'right',
                'gradient'   => 'linear-gradient(240deg, #2B5BFF 63.4%, #0C2CA8 136.6%)',
                'items'      => array(
                    array(
                        'q' => 'Tickets de entrada y salida',
                        'a' => 'Se emiten automáticamente con patente, fecha y hora, y se imprimen en la impresora de tu máquina de pagos o en cualquier impresora Bluetooth Android.',
                    ),
                    array(
                        'q' => 'Configuración de tarifas',
                        'a' => 'Define tarifas por hora, por tramo o tarifa plana, y ajústalas cuando quieras desde el panel web sin reprogramar la máquina.',
                    ),
                    array(
                        'q' => 'Descuentos por consumo',
                        'a' => 'Aplica descuentos automáticos validando el monto de la boleta, sin cálculos manuales ni criterio a ojo del operador.',
                    ),
                ),
            ),
            array(
                'eyebrow'    => 'Control y caja',
                'title'      => 'Que la plata cuadre siempre',
                'image_side' => 'left',
                'gradient'   => 'linear-gradient(240deg, #1E4CF0 63.4%, #0A2494 136.6%)',
                'items'      => array(
                    array(
                        'q' => 'Cierre de turno con validación',
                        'a' => 'Cada operador genera su reporte al terminar con el total recaudado, los vehículos atendidos y los descuentos aplicados. El administrador debe aprobarlo para cerrar.',
                    ),
                    array(
                        'q' => 'Control de evasores',
                        'a' => 'La patente queda registrada en cada ingreso — si un vehículo que no pagó intenta volver, el sistema te avisa automáticamente.',
                    ),
                    array(
                        'q' => 'Clientes frecuentes',
                        'a' => 'Identifica patentes recurrentes y aplica tarifas o beneficios especiales sin que el operador tenga que recordarlas de memoria.',
                    ),
                ),
            ),
            array(
                'eyebrow'    => 'Gestión y escala',
                'title'      => 'Cuando ya no es un solo recinto',
                'image_side' => 'right',
                'gradient'   => 'linear-gradient(240deg, #3366FF 63.4%, #12309F 136.6%)',
                'items'      => array(
                    array(
                        'q' => 'Reportes en tiempo real',
                        'a' => 'Ingresos, salidas, pagos, descuentos, patentes liberadas y egresos sin cobro. Exporta a Excel o PDF para cuadrar con contabilidad.',
                    ),
                    array(
                        'q' => 'Múltiples sucursales',
                        'a' => 'Administra todos tus recintos desde un solo panel, con ocupación y recaudación de cada uno visibles en vivo.',
                    ),
                    array(
                        'q' => 'Compatible con tu máquina TUU',
                        'a' => 'Corre directo sobre el mismo equipo que ya usas para cobrar — no necesitas comprar hardware adicional.',
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
                                    <p><?php echo esc_html( $item['a'] ); ?></p>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="func-mockup" style="background: <?php echo esc_attr( $group['gradient'] ); ?>;">
                    <img
                        class="func-mockup-phone func-mockup-phone-back"
                        src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/parking/celular-1.png' ); ?>"
                        alt=""
                        loading="lazy"
                    >
                    <img
                        class="func-mockup-phone func-mockup-phone-front"
                        src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/parking/celular-2.png' ); ?>"
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