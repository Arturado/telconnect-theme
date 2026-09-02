<section class="faq">
    <div class="faq-container tc-container">
        <span id="faq" class="faq-eyebrow">Preguntas frecuentes</span>
        <h2 class="faq-title">Todo lo que necesitas saber antes de comprar</h2>

        <div class="faq-list">
            <?php
            $faqs = array(
                array(
                    'q' => '¿Comprar con Telconnect cuesta más que comprar directo en TUU?',
                    'a' => 'No. Somos distribuidor autorizado: mismos precios, mismas comisiones y misma garantía. Lo que agregamos no tiene costo adicional: asesoría, instalación presencial en Santiago y soporte directo.',
                ),
                array(
                    'q' => '¿Qué incluye mi máquina?',
                    'a' => 'Pagos con tarjeta, boleta y factura electrónica conectada al SII, catálogo e inventario, sistema de reservas y acceso a Adelanto, Abono Flexible y Cuotas TUU. Todo activado desde el primer día, sin costo extra. ',
                ),
                array(
                    'q' => '¿Hay mensualidad o arriendo?',
                    'a' => 'No. Solo pagas la comisión por transacción. No hay cobro por emitir boletas, por el chip, por mantención ni por tener la máquina. El equipo es tuyo.',
                ),
                array(
                    'q' => '¿Cuál es la comisión?',
                    'a' => '1,99% por venta con bono en 1 día hábil, sin IVA. Es la misma tarifa para débito, crédito y prepago nacional. Las tarjetas internacionales tienen tarifas distintas. ',
                ),
                array(
                    'q' => '¿En cuánto tiempo llega?',
                    'a' => 'En comunas de Santiago la Recibes el mismo día si compras antes de las 12:00. En el resto de la Región Metropolitana, de 1 a 3 días hábiles: en regiones, de 3 a 5. El Desapacho es gratis en todo Chile.',
                ),
            );

            foreach ( $faqs as $i => $faq ) :
                ?>
                <details class="faq-item"<?php echo 0 === $i ? ' open' : ''; ?>>
                    <summary>
                        <span class="faq-question"><?php echo esc_html( $faq['q'] ); ?></span>
                        <span class="faq-toggle" aria-hidden="true">
                            <span class="faq-toggle-sign"></span>
                        </span>
                    </summary>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p><?php echo esc_html( $faq['a'] ); ?></p>
                        </div>
                    </div>
                </details>
                <?php
            endforeach;
            ?>
        </div>
    </div>
</section>