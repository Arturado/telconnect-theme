<section class="testimonials">
    <div class="ts-container">
        <div class="ts-header">
            <h2 class="ts-title">Negocios como el tuyo<br>ya están creciendo con Telconnect</h2>
            <div class="ts-social">
                <div class="ts-avatars">
                    <img class="ts-avatar" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/avatar1.jpg' ); ?>" alt="">
                    <img class="ts-avatar" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/avatar2.jpg' ); ?>" alt="">
                    <img class="ts-avatar" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/avatar3.jpg' ); ?>" alt="">
                    <span class="ts-avatar ts-avatar-count">+61</span>
                </div>
                <div class="ts-stars">★★★★★</div>
                <span class="ts-social-label">Clientes en Santiago y regiones</span>
            </div>
        </div>

        <div class="ts-track">
            <?php
            $testimonials = array(
                array(
                    'name'    => 'Rodrigo Salas',
                    'biz'     => 'Estacionamiento',
                    'quote'   => 'Dejamos el cuaderno y el descuadre de caja se acabó en una semana.',
                    'stat'    => '',
                ),
                array(
                    'name'    => 'Paula Rivas',
                    'biz'     => 'Cafetería · Providencia',
                    'quote'   => 'Desde que cobra hasta que tiene la plata',
                    'stat'    => '15 min',
                ),
                array(
                    'name'    => 'Jorge Medina',
                    'biz'     => 'Botillería · Maipú',
                    'quote'   => 'Vinieron al local, instalaron y capacitaron a mis cajeros la misma tarde.',
                    'stat'    => '',
                ),
                array(
                    'name'    => 'Antonia Fuentes',
                    'biz'     => 'Minimarket · Ñuñoa',
                    'quote'   => 'Adelanto de capital que usamos para reponer stock en temporada alta.',
                    'stat'    => '3x',
                ),
            );

            foreach ( $testimonials as $t ) :
                ?>
                <div class="ts-card">
                    <div class="ts-card-head">
                        <strong><?php echo esc_html( $t['name'] ); ?></strong>
                        <span> · <?php echo esc_html( $t['biz'] ); ?></span>
                    </div>
                    <div class="ts-card-body">
                        <?php if ( ! empty( $t['stat'] ) ) : ?>
                            <div class="ts-stat"><?php echo esc_html( $t['stat'] ); ?></div>
                        <?php endif; ?>
                        <p><?php echo esc_html( $t['quote'] ); ?></p>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
        </div>
    </div>
</section>