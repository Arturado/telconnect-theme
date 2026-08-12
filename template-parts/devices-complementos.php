<section class="devices-complementos">
    <div class="dp-container">
        <span class="cm-eyebrow">Complementos</span>
        <h2 class="dp-title">Todo parte por elegir el equipo correcto.</h2>

        <div class="dp-grid dp-grid-simple">
            <?php
            /**
             * Ajusta el slug de categoría a la real que uses en WooCommerce
             * para tus consumibles/complementos (ej: papel térmico).
             */
            $dc_args = array(
                'post_type'      => 'product',
                'posts_per_page' => 3,
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
                    <div class="dp-card dp-card-simple">
                        <div class="dp-card-image">
                            <?php echo woocommerce_get_product_thumbnail(); ?>
                        </div>
                        <div class="dp-card-body">
                            <h3><?php the_title(); ?></h3>
                            <p class="dp-card-desc"><?php echo esc_html( $product->get_short_description() ); ?></p>
                            <div class="dp-card-price">
                                <?php echo $product->get_price_html(); ?>
                                <span class="dp-price-note">+ IVA</span>
                            </div>
                            <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="btn btn-block dp-add-cart" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
                                Agregar al carro
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
    </div>
</section>