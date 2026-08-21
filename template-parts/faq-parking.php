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
                    'a' => 'Puedes usar un celular o tablet Android, pero te recomendamos la máquina POS TUU: en un solo equipo cobras con tarjeta, imprimes el ticket y emites la boleta electrónica. Con celular necesitarías una impresora aparte y otro sistema para cobrar.',
                ),
                array(
                    'q' => '¿Puedo usarlo en varios dispositivos?',
                    'a' => 'Sí. En la versión de suscripción conectas múltiples dispositivos con la misma cuenta, todos sincronizados en tiempo real. Ideal si tienes varias casetas o varios operadores por turno.',
                ),
                array(
                    'q' => '¿Qué pasa si se corta internet?',
                    'a' => 'La app sigue operando en el dispositivo. Cuando vuelve la conexión, los datos se sincronizan automáticamente con la nube. No pierdes tickets ni registros.',
                ),
                array(
                    'q' => '¿Cómo controlo lo que recauda cada operador?',
                    'a' => 'Con el cierre de turno. Cada operador genera al terminar un reporte con el total recaudado, vehiculos atendidos y descuentos aplicados, y el administrador debe aprobarlo.',
                ),
                array(
                    'q' => '¿Cuánto demora tener el sistema andando?',
                    'a' => 'Si compras antes de las 12:00 en Santiago, recibes la máquina el mismo día. La configuración de tarifas y la capacitación toman menos de una hora.',
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
                        <div class="faqp-answer-inner">
                            <p><?php echo esc_html( $item['a'] ); ?></p>
                        </div>
                    </div>
                </details>
                <?php
            endforeach;
            ?>
        </div>
    </div>
</section>