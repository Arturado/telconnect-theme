<?php
/**
 * Override de yourtheme/woocommerce/content-single-product.php
 * Base: woocommerce/templates/content-single-product.php (v3.6.0)
 * Mismos hooks que el core (galería, título, precio, add-to-cart,
 * meta, tabs, relacionados) — solo se envuelve en .pdp-* para el
 * layout de 2 columnas. El checklist de _dp_features se inyecta
 * vía hook en functions.php (woocommerce_single_product_summary,
 * prioridad 25), no acá.
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    return;
}
?>

<div class="pdp-page">
    <div class="pdp-container">
        <div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'pdp-product', $product ); ?>>

            <div class="pdp-grid">
                <div class="pdp-gallery-col">
                    <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
                </div>

                <div class="pdp-summary-col">
                    <div class="summary entry-summary">
                        <?php do_action( 'woocommerce_single_product_summary' ); ?>
                    </div>
                </div>
            </div>

            <div class="pdp-below">
                <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
            </div>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
