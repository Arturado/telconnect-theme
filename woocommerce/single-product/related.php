<?php
/**
 * Override de yourtheme/woocommerce/single-product/related.php
 * Base: woocommerce/templates/single-product/related.php (v10.3.0)
 * Misma query de relacionados que arma WooCommerce ($related_products,
 * ya viene resuelto por el core), solo se cambia el <ul class="products">
 * por el mismo grid + card (.dp-card) que usa el PLP.
 */

defined( 'ABSPATH' ) || exit;

if ( $related_products ) :

    if ( function_exists( 'wp_increase_content_media_count' ) ) {
        $content_media_count = wp_increase_content_media_count( 0 );
        if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
            wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
        }
    }
    ?>
    <section class="related products pdp-related">
        <?php
        $heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'También te puede interesar', 'telconnect' ) );

        if ( $heading ) :
            ?>
            <h2 class="pdp-related-title"><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>

        <div class="plp-grid">
            <?php
            foreach ( $related_products as $related_product ) :
                $post_object = get_post( $related_product->get_id() );
                setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
                global $product;
                $product = $related_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                get_template_part( 'template-parts/product-card' );
            endforeach;
            ?>
        </div>
    </section>
    <?php
endif;

wp_reset_postdata();
