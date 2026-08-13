<section class="testimonials">
    <div class="ts-container">
        <div class="ts-head">
            <h2 class="ts-title">Negocios como el tuyo<br>ya están creciendo con Telconnect</h2>
            <div class="ts-proof">
                <div class="ts-avatars">
                    <img class="ts-avatar" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/avatar1.jpg' ); ?>" alt="">
                    <img class="ts-avatar" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/avatar2.jpg' ); ?>" alt="">
                    <img class="ts-avatar" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/avatar3.jpg' ); ?>" alt="">
                    <span class="ts-avatar ts-avatar-count">+61</span>
                </div>
                <div class="ts-stars">★★★★★</div>
                <span class="ts-proof-caption">Clientes en Santiago y regiones</span>
            </div>
        </div>

        <div class="ts-viewport">
            <div class="ts-track" id="ts-track">
                <?php
                /**
                 * type 'stat'  -> número grande + descripción
                 * type 'quote' -> solo cita grande (estilo headline)
                 *
                 * NOTA: Camila Fuentes y Ana Torres son nombres/datos nuevos que
                 * aparecieron en el Figma real (no estaban validados antes) — y la
                 * 6ta (Valentina Rojas) la inventé por pedido explícito. TODOS los
                 * datos de result (números, negocios exactos) deben confirmarse
                 * con el cliente antes de publicar.
                 */
                $testimonials = array(
                    array(
                        'type'  => 'stat',
                        'name'  => 'Camila Fuentes',
                        'biz'   => 'Minimarket · Ñuñoa',
                        'stat'  => '21 días',
                        'quote' => 'Recuperó la inversión de la máquina.',
                    ),
                    array(
                        'type'  => 'quote',
                        'name'  => 'Rodrigo Salas',
                        'biz'   => 'Estacionamiento',
                        'quote' => 'Dejamos el cuaderno y el descuadre de caja se acabó en una semana.',
                    ),
                    array(
                        'type'  => 'stat',
                        'name'  => 'Paula Rivas',
                        'biz'   => 'Cafetería · Providencia',
                        'stat'  => '15 min',
                        'quote' => 'Desde que cobra hasta que tiene la plata.',
                    ),
                    array(
                        'type'  => 'quote',
                        'name'  => 'Jorge Medina',
                        'biz'   => 'Botillería · Maipú',
                        'quote' => 'Vinieron al local, instalaron y capacitaron a mis cajeros la misma tarde.',
                    ),
                    array(
                        'type'  => 'stat',
                        'name'  => 'Ana Torres',
                        'biz'   => 'Minimarket · La Florida',
                        'stat'  => '3x',
                        'quote' => 'Más ventas los fines de semana desde que acepta tarjetas.',
                    ),
                    array(
                        'type'  => 'quote',
                        'name'  => 'Valentina Rojas',
                        'biz'   => 'Peluquería · Providencia',
                        'quote' => 'Mis clientas pagan con QR y ya no ando pendiente del cambio.',
                    ),
                );

                // Set 1 (real) + Set 2 (duplicado, oculto a lectores de pantalla) para loop infinito sin salto
                for ( $set = 1; $set <= 2; $set++ ) :
                    foreach ( $testimonials as $t ) :
                        ?>
                        <div class="ts-card"<?php echo 2 === $set ? ' aria-hidden="true"' : ''; ?>>
                            <div class="ts-card-scrim-top"></div>
                            <div class="ts-card-scrim-bottom"></div>
                            <div class="ts-card-header">
                                <span class="ts-card-name"><?php echo esc_html( $t['name'] ); ?></span>
                                <span class="ts-card-dot">·</span>
                                <span class="ts-card-biz"><?php echo esc_html( $t['biz'] ); ?></span>
                            </div>
                            <div class="ts-card-grow"></div>
                            <div class="ts-card-result">
                                <?php if ( 'stat' === $t['type'] ) : ?>
                                    <div class="ts-card-stat"><?php echo esc_html( $t['stat'] ); ?></div>
                                    <p class="ts-card-desc"><?php echo esc_html( $t['quote'] ); ?></p>
                                <?php else : ?>
                                    <p class="ts-card-quote"><?php echo esc_html( $t['quote'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    endforeach;
                endfor;
                ?>
            </div>
        </div>
    </div>
</section>