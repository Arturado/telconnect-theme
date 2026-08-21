<section class="devices-complementos">
    <div class="dc-container">
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
                            <a href="<?php echo esc_url( get_permalink() ); ?>" class="dc-card-image">
                                <?php echo woocommerce_get_product_thumbnail(); ?>
                            </a>
                            <div class="dp-card-body">
                                <h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
                                <p class="dp-card-desc"><?php echo esc_html( $product->get_short_description() ); ?></p>
                                <div class="dp-card-price">
                                    <?php echo $product->get_price_html(); ?>
                                    <span class="dp-price-note">+ IVA</span>
                                </div>
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