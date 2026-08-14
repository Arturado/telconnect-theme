<?php
/**
 * Override de yourtheme/woocommerce/content-single-product.php
 * Base: woocommerce/templates/content-single-product.php (v3.6.0)
 *
 * REDISEÑO COMPLETO (v2) según Figma real — ya no delega en el hook soup
 * de woocommerce_single_product_summary para el layout de la columna
 * derecha, porque el diseño agrupa título/precio/cantidad/CTA/accordions
 * en cards con jerarquía visual que no matchea el orden default de WC.
 *
 * Lo que SÍ sigue siendo 100% WooCommerce (no reinventado):
 * - Galería (woocommerce_before_single_product_summary → wc_get_gallery_image_html etc)
 * - El <form class="cart"> real, con su nonce/hidden fields — solo se
 *   reordena visualmente el quantity input y el botón dentro del mismo form.
 * - El hook woocommerce_add_to_cart para el addon de firma electrónica.
 *
 * El checklist _dp_features (mismo campo que ya usan las cards de la
 * home) se lee acá directo, no vía el hook de functions.php — ese hook
 * quedó obsoleto para esta plantilla y no se llama más.
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    return;
}

// ---- Datos del producto ----
$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_price();
$on_sale       = $product->is_on_sale() && $regular_price > 0;
$discount_pct  = $on_sale ? round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 ) : 0;

$features_raw = get_post_meta( $product->get_id(), '_dp_features', true );
$features     = $features_raw ? array_filter( array_map( 'trim', explode( "\n", $features_raw ) ) ) : array();

$signature_addons = tc_get_signature_addon_products();

// ---- Breadcrumbs ----
$terms       = get_the_terms( $product->get_id(), 'product_cat' );
$primary_cat = ( $terms && ! is_wp_error( $terms ) ) ? reset( $terms ) : null;
?>

<div class="pdp-page">
    <div class="pdp-container">

        <nav class="pdp-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url() ); ?>">Inicio</a>
            <span class="pdp-breadcrumb-sep" aria-hidden="true"></span>
            <?php if ( $primary_cat ) : ?>
                <a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a>
                <span class="pdp-breadcrumb-sep" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="pdp-breadcrumb-current"><?php the_title(); ?></span>
        </nav>

        <div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'pdp-product', $product ); ?>>
            <div class="pdp-grid">

                <!-- Columna izquierda: Galería y detalle -->
                <div class="pdp-gallery-col">

                    <div class="pdp-photo-card">
                        <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
                    </div>

                    <?php if ( $product->get_description() ) : ?>
                        <details class="pdp-accordion" open>
                            <summary>
                                <span>Información adicional</span>
                                <span class="pdp-chevron" aria-hidden="true"></span>
                            </summary>
                            <div class="pdp-accordion-body">
                                <?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
                            </div>
                        </details>
                    <?php endif; ?>

                </div>

                <!-- Columna derecha: Compra -->
                <div class="pdp-summary-col">
                    <div class="summary entry-summary pdp-compra-card">

                        <div class="pdp-identidad">
                            <span class="pdp-eyebrow"><?php bloginfo( 'name' ); ?></span>
                            <h1 class="pdp-title"><?php the_title(); ?></h1>
                            <?php if ( $product->get_short_description() ) : ?>
                                <p class="pdp-short-desc"><?php echo wp_kses_post( $product->get_short_description() ); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="pdp-precio">
                            <?php if ( $on_sale ) : ?>
                                <span class="pdp-price-original"><?php echo wp_kses_post( wc_price( $regular_price ) ); ?></span>
                            <?php endif; ?>
                            <div class="pdp-price-row">
                                <?php if ( $on_sale ) : ?>
                                    <span class="pdp-discount-badge">-<?php echo esc_html( $discount_pct ); ?>%</span>
                                <?php endif; ?>
                                <span class="pdp-price-current"><?php echo wp_kses_post( wc_price( $sale_price ) ); ?></span>
                                <span class="pdp-price-iva">+ IVA</span>
                            </div>
                            <span class="pdp-installments">o 12 cuotas sin interés con tarjeta de crédito</span>
                        </div>

                        <?php
                        /**
                         * Form real de WooCommerce — se abre acá y se cierra después del
                         * bloque de firma electrónica, para que el addon viaje en el mismo
                         * POST que el producto principal (ver tc_maybe_add_signature_addon()).
                         */
                        do_action( 'woocommerce_before_add_to_cart_form' );
                        ?>
                        <form class="cart pdp-accion" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
                            <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

                            <div class="pdp-qty-cta-row">
                                <div class="pdp-qty-stepper">
                                    <button type="button" class="pdp-qty-btn pdp-qty-minus" aria-label="Restar">
                                        <span></span>
                                    </button>
                                    <?php
                                    woocommerce_quantity_input(
                                        array(
                                            'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                            'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                            'input_value' => 1,
                                        )
                                    );
                                    ?>
                                    <button type="button" class="pdp-qty-btn pdp-qty-plus" aria-label="Sumar">
                                        <span></span>
                                    </button>
                                </div>

                                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="pdp-add-to-cart single_add_to_cart_button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>">
                                    <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                                </button>
                            </div>

                            <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

                            <?php if ( ! empty( $features ) ) : ?>
                                <details class="pdp-accordion pdp-accordion--features" open>
                                    <summary>
                                        <span>Características principales</span>
                                        <span class="pdp-chevron" aria-hidden="true"></span>
                                    </summary>
                                    <ul class="pdp-features-list">
                                        <?php foreach ( $features as $feature ) : ?>
                                            <li><span class="pdp-feature-dot"></span><?php echo esc_html( $feature ); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            <?php endif; ?>

                            <?php if ( ! empty( $signature_addons ) ) : ?>
                                <div class="pdp-boleta">
                                    <span class="pdp-boleta-title">Emite boleta electrónica</span>
                                    <div class="pdp-signature-card">
                                        <div class="pdp-signature-head">
                                            <span class="pdp-signature-icon">
                                                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/receipt-lines.svg' ); ?>" alt="">
                                            </span>
                                            <span class="pdp-signature-label">Firma electrónica</span>
                                            <a href="#" class="pdp-signature-info">¿Qué es?</a>
                                        </div>
                                        <div class="pdp-select-wrap">
                                            <select name="tc_addon_signature" class="pdp-select">
                                                <option value="" disabled selected>
                                                    Agregar firma desde <?php echo wp_kses_post( wc_price( $signature_addons[0]['price'] ) ); ?>
                                                </option>
                                                <option value="">No, gracias</option>
                                                <?php foreach ( $signature_addons as $addon ) : ?>
                                                    <option value="<?php echo esc_attr( $addon['id'] ); ?>">
                                                        <?php echo esc_html( $addon['name'] ); ?> — <?php echo wp_kses_post( wc_price( $addon['price'] ) ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </form>
                        <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

                        <?php if ( wc_product_sku_enabled() || $product->get_category_ids() ) : ?>
                            <div class="product_meta">
                                <?php do_action( 'woocommerce_product_meta_start' ); ?>
                                <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                                    <span class="sku_wrapper">SKU: <span class="sku"><?php echo esc_html( $product->get_sku() ? $product->get_sku() : __( 'N/A', 'woocommerce' ) ); ?></span></span>
                                <?php endif; ?>
                                <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Categoría:', 'Categorías:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
                                <?php do_action( 'woocommerce_product_meta_end' ); ?>
                            </div>
                        <?php endif; ?>

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