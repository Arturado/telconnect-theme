<?php
/**
 * Override de yourtheme/woocommerce/cart/cart-totals.php
 * Base: woocommerce/templates/cart/cart-totals.php (v2.3.6)
 *
 * v2 — reescrito completo contra Figma real (recursos/carrito/): las
 * líneas del Figma ("Productos (N)", "Firma electrónica", "Despacho",
 * "Neto", "IVA (19%)", "Total") no matchean 1:1 el loop de líneas
 * default de WooCommerce (que es genérico: subtotal/cupones/envío/fees/
 * impuestos/total sin separar "productos" de "addons"), así que se
 * abandona la tabla <table> del core (mismo criterio que cart.php) y
 * se arma a mano con los totales ya calculados por WC()->cart — nada
 * de aritmética propia de precios, todo sale de WC_Cart/WC_Tax.
 *
 * IVA: WooCommerce tenía el cálculo de impuestos desactivado
 * (woocommerce_calc_taxes=no, sin ninguna tasa configurada) — se activó
 * en esta misma sesión (decisión del usuario) y se creó la tasa
 * "IVA" 19% para Chile (Ajustes > Impuestos > Estándar). Neto/IVA/Total
 * ahora son un desglose real (Total - Impuesto = Neto), no solo texto
 * decorativo — coincide con lo que WooCommerce va a cobrar de verdad
 * en el checkout, a diferencia de antes.
 *
 * Despacho: NO se hardcodea "Gratis" — se lee el método de envío
 * real (elegido o disponible) vía WC()->shipping()->get_packages(),
 * mismo criterio pedido para no inventar el estado del envío a
 * domicilio (§8.1, sigue inactivo, solo Retiro en tienda $0 activo).
 *
 * v3 (§8.8): el cálculo se extrajo a tc_get_order_summary_breakdown()
 * en functions.php para reusarlo tal cual en review-order.php del
 * checkout — este archivo ahora solo consume el array, no recalcula.
 */

defined( 'ABSPATH' ) || exit;

$crt_summary = tc_get_order_summary_breakdown();

$crt_products_count    = $crt_summary['products_count'];
$crt_products_subtotal = $crt_summary['products_subtotal'];
$crt_addon_label       = $crt_summary['addon_label'];
$crt_addon_subtotal    = $crt_summary['addon_subtotal'];
$crt_shipping_label    = $crt_summary['shipping_label'];
$crt_shipping_html     = $crt_summary['shipping_html'];
$crt_shipping_cost     = $crt_summary['shipping_cost'];
$crt_net_total         = $crt_summary['net_total'];
$crt_total_tax         = $crt_summary['total_tax'];
$crt_tax_label         = $crt_summary['tax_label'];
$crt_total             = $crt_summary['total'];
?>
<div class="cart_totals crt-totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

    <?php do_action( 'woocommerce_before_cart_totals' ); ?>

    <h3 class="crt-summary-title"><?php esc_html_e( 'Resumen de tu compra', 'telconnect' ); ?></h3>

    <div class="crt-summary-lines">
        <div class="crt-summary-line">
            <span>
                <?php
                /* translators: %d es la cantidad de productos top-level del carrito */
                echo esc_html( sprintf( __( 'Productos (%d)', 'telconnect' ), $crt_products_count ) );
                ?>
            </span>
            <span><?php echo wp_kses_post( wc_price( $crt_products_subtotal ) ); ?></span>
        </div>

        <?php if ( $crt_addon_label ) : ?>
            <div class="crt-summary-line">
                <span><?php echo esc_html( $crt_addon_label ); ?></span>
                <span><?php echo wp_kses_post( wc_price( $crt_addon_subtotal ) ); ?></span>
            </div>
        <?php endif; ?>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <div class="crt-summary-line crt-summary-line--discount">
                <span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                <span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
            </div>
        <?php endforeach; ?>

        <?php if ( null !== $crt_shipping_html ) : ?>
            <div class="crt-summary-line">
                <span><?php esc_html_e( 'Despacho', 'telconnect' ); ?><?php echo $crt_shipping_label ? ' — ' . esc_html( $crt_shipping_label ) : ''; ?></span>
                <span class="crt-summary-shipping-value<?php echo esc_attr( 0.0 === $crt_shipping_cost ? ' crt-summary-shipping-value--free' : '' ); ?>"><?php echo wp_kses_post( $crt_shipping_html ); ?></span>
            </div>
        <?php endif; ?>

        <div class="crt-summary-line">
            <span><?php esc_html_e( 'Neto', 'telconnect' ); ?></span>
            <span><?php echo wp_kses_post( wc_price( $crt_net_total ) ); ?></span>
        </div>

        <?php if ( $crt_total_tax > 0 ) : ?>
            <div class="crt-summary-line">
                <span><?php echo esc_html( $crt_tax_label ); ?></span>
                <span><?php echo wp_kses_post( wc_price( $crt_total_tax ) ); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

    <div class="crt-summary-total">
        <span><?php esc_html_e( 'Total', 'telconnect' ); ?></span>
        <span><?php echo wp_kses_post( wc_price( $crt_total ) ); ?></span>
    </div>

    <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

    <div class="wc-proceed-to-checkout crt-proceed-to-checkout">
        <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
    </div>

    <div class="crt-trust-note">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/shield-check.svg' ); ?>" alt="">
        <span><?php esc_html_e( 'Pago seguro. Recibes la boleta electrónica al confirmar tu compra.', 'telconnect' ); ?></span>
    </div>

    <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
