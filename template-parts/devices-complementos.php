<section class="devices-complementos">
    <div class="dc-container tc-container">
        <span class="dc-eyebrow">Complementos</span>
        <h2 class="dc-title">Tu equipo es solo el comienzo.</h2>
        <p class="dc-subtitle">Accesorios, consumibles y todo lo que necesitas para sacar el máximo provecho a tu equipo. Agrégalo a tu compra y tenlo todo listo desde el primer día.</p>

        <div class="dp-carousel">
            <div class="dp-grid" id="dc-grid">
                <?php
                $dc_args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => 'complementos',
                        ),
                    ),
                );

                $dc_query = new WP_Query( $dc_args );

                if ( $dc_query->have_posts() ) :
                    while ( $dc_query->have_posts() ) : $dc_query->the_post();
                        global $product;
                        ?>
                        <div class="dp-card dc-card">
                            <div class="dc-card-image">
                                <?php if ( $product->is_on_sale() ) : ?>
                                    <span class="dp-sale-badge"><?php echo esc_html( tc_get_product_discount_percent( $product ) ); ?>% dcto.</span>
                                <?php endif; ?>
                                <?php
                                /**
                                 * 'large' en vez de 'woocommerce_thumbnail' — mismo motivo que
                                 * devices-products.php (ver comentario detallado ahí): el tamaño
                                 * cropeado cuadrado por defecto excluye del srcset cualquier
                                 * candidato más grande que 300w, sin importar la resolución del
                                 * original subido.
                                 *
                                 * sizes: .dc-card comparte .dp-card en desktop (misma fórmula que
                                 * devices-products.php), pero en mobile queda fijo en 350px (no
                                 * 100vw, ver #dc-grid .dc-card en devices-complementos.css — a
                                 * propósito deja peek del card siguiente) → imagen = 350 - 2*24
                                 * (padding) = 302px fijo, no un cálculo en vw.
                                 */
                                echo woocommerce_get_product_thumbnail(
                                    'large',
                                    array(
                                        'sizes' => '(max-width: 900px) 302px, (max-width: 1440px) calc((100vw - 272px) / 3), 390px',
                                    )
                                );
                                ?>
                            </div>
                            <div class="dp-card-body">
                                <h3><?php the_title(); ?></h3>
                                <p class="dp-card-desc"><?php echo esc_html( $product->get_short_description() ); ?></p>
                                <?php if ( $product->is_on_sale() ) : ?>
                                    <p class="dp-price-was">Precio normal: <del><?php echo wp_kses_post( wc_price( $product->get_regular_price() ) ); ?></del></p>
                                    <div class="dp-card-price">
                                        <?php echo wp_kses_post( wc_price( $product->get_sale_price() ) ); ?>
                                        <span class="dp-price-note">+ IVA</span>
                                    </div>
                                <?php else : ?>
                                    <div class="dp-card-price">
                                        <?php echo $product->get_price_html(); ?>
                                        <span class="dp-price-note">+ IVA</span>
                                    </div>
                                <?php endif; ?>
                                <a href="<?php echo esc_url( get_permalink() ); ?>" class="dp-add-cart">
                                    <span class="btn-label-mask"><span class="btn-label">Ir a comprar</span><span class="btn-label-ghost" aria-hidden="true">Ir a comprar</span></span>
                                </a>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <p class="dp-empty">Aún no hay productos en la categoría "complementos".</p>
                    <?php
                endif;
                ?>
            </div>

            <div class="dp-carousel-nav">
                <div class="dp-dots" id="dc-dots"></div>
                <div class="dp-arrows">
                    <button type="button" class="dp-arrow dp-arrow-prev" id="dc-prev" aria-label="Anterior">
                        <span></span>
                    </button>
                    <button type="button" class="dp-arrow dp-arrow-next" id="dc-next" aria-label="Siguiente">
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>