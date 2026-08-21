<section class="faqp">
    <div class="faqp-container">
        <div class="faqp-head">
            <span class="faqp-eyebrow">Preguntas frecuentes</span>
            <h2 class="faqp-title">Aprende más sobre Telconnect Parking</h2>
        </div>

        <div class="faqp-list">
            <?php
            /**
             * Respuestas 100% inventadas por mí como placeholder — el usuario
             * confirmó que las reemplaza después con el copy real. No usar
             * en producción sin que el cliente las revise.
             */
            $faqp_items = array(
                array(
                    'q' => '¿Necesito comprar la máquina o puedo usar mi celular?',
                    'a' => 'Telconnect Parking corre sobre tu máquina TUU — si ya tienes una, solo activamos el servicio. Si no, te vendemos el equipo junto con la licencia.',
                ),
                array(
                    'q' => '¿Puedo usarlo en varios dispositivos?',
                    'a' => 'Sí, cada operador puede acceder desde su propio dispositivo con su usuario, y todo se sincroniza en tiempo real en el mismo panel.',
                ),
                array(
                    'q' => '¿Qué pasa si se corta internet?',
                    'a' => 'La máquina sigue emitiendo tickets y cobrando de forma local. Apenas vuelve la conexión, todo se sincroniza automáticamente sin perder registros.',
                ),
                array(
                    'q' => '¿Cómo controlo lo que recauda cada operador?',
                    'a' => 'Cada turno queda asociado al operador que lo abrió, con el monto declarado y la validación del administrador antes de cerrar.',
                ),
                array(
                    'q' => '¿Cuánto demora tener el sistema andando?',
                    'a' => 'La activación es el mismo día — configuramos tarifas y turnos contigo y quedas operando de inmediato con tu máquina TUU.',
                ),
            );

            foreach ( $faqp_items as $item ) :
                ?>
                <details class="faqp-item">
                    <summary>
                        <span class="faqp-question"><?php echo esc_html( $item['q'] ); ?></span>
                        <span class="faqp-toggle" aria-hidden="true">
                            <span class="faqp-toggle-sign"></span>
                        </span>
                    </summary>
                    <div class="faqp-answer">
                        <p><?php echo esc_html( $item['a'] ); ?></p>
                    </div>
                </details>
                <?php
            endforeach;
            ?>
        </div>
    </div>
</section>