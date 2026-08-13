<?php
/**
 * Override de yourtheme/woocommerce/cart/cross-sells.php
 * Base: woocommerce/templates/cart/cross-sells.php (v9.6.0)
 * Mismos $cross_sells que resuelve el core, solo se cambia el
 * <ul class="products"> por el mismo grid + card (template-parts/
 * product-card.php) que ya usan PLP y relacionados del PDP —
 * mismo criterio que woocommerce/single-product/related.php.
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : ?>

    <div class="cross-sells crt-cross-sells">
        <?php
        $heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'Puede que te interese también&hellip;', 'telconnect' ) );

        if ( $heading ) :
            ?>
            <h2 class="crt-cross-sells-title"><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>

        <div class="plp-grid crt-cross-sells-grid">
            <?php
            foreach ( $cross_sells as $cross_sell ) :
                $post_object = get_post( $cross_sell->get_id() );
                setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
                global $product;
                $product = $cross_sell; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                get_template_part( 'template-parts/product-card' );
            endforeach;
            ?>
        </div>
    </div>
    <?php
endif;

wp_reset_postdata();
