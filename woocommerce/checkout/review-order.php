<?php
/**
 * Override de yourtheme/woocommerce/checkout/review-order.php
 * Base: woocommerce/templates/checkout/review-order.php (v11.0.0)
 *
 * v2 (§8.8) — mismo criterio que cart-totals.php (§8.7): se abandona la
 * <table> del core y se arma la card "Resumen de tu compra" a mano con
 * tc_get_order_summary_breakdown() (compartido con el carrito, así que
 * Productos/Firma/Despacho/Neto/IVA/Total siempre calzan idénticos
 * entre carrito y checkout).
 *
 * IMPORTANTE: el elemento raíz de este template DEBE conservar la clase
 * "woocommerce-checkout-review-order-table" — no es solo estética, es
 * el selector CSS que WC_AJAX::update_order_review() usa para
 * reemplazar este fragment en vivo ($(selector).replaceWith(html), ver
 * checkout.js línea ~760). Da igual que ya no sea un <table> real.
 *
 * Bloque extra "Datos" (Despacho real/Comisión/Servicio): solo se ve en
 * el paso Pago — el toggle es puramente CSS
 * (.chk-page[data-step="pago"] .chk-summary-extra), no JS, porque
 * .chk-page es ancestro de #order_review y sobrevive al reemplazo del
 * fragment de arriba.
 *
 * Input espejo del método de envío (bug real encontrado con Playwright,
 * §8.10): el checkout.js del CORE arma el payload AJAX de
 * "qué método de envío está elegido" buscando
 * SOLO dentro de .woocommerce-checkout-review-order-table (este mismo
 * fragment) — pero los radios reales de "Despacho" (§8.8) viven en la
 * card de la columna PRINCIPAL, no acá. Sin este espejo, el resumen en
 * vivo (mientras el comprador todavía está completando el formulario)
 * podía mostrar temporalmente el método/costo de despacho equivocado —
 * el pedido final SIEMPRE se guardaba bien (el submit real serializa
 * todo el <form>, no pasa por este mecanismo), pero la vista previa
 * podía mentir. Se replica acá como <input type="hidden"> — el
 * selector del core es `input[name^="shipping_method"][type="hidden"]`
 * (basta con que el name EMPIECE con "shipping_method"), así que se usa
 * un name DISTINTO al de los radios reales (shipping_method_display, no
 * shipping_method) para que no colisione con ellos en el $_POST del
 * submit final. checkout.js sincroniza este valor cada vez que cambia
 * el radio real, ANTES de que dispare el update_checkout.
 */

defined( 'ABSPATH' ) || exit;

$chk_summary = tc_get_order_summary_breakdown();

// Línea "Despacho" del bloque extra (solo Pago): dirección real tecleada
// por el comprador si es domicilio, o el nombre del local si es retiro.
// address_1/address_2 SÍ se sincronizan en vivo vía AJAX (WC_AJAX::update_order_review()
// los lee de $_POST['s_address']/['s_address_2']) — comuna/nombre del
// receptor no viajan en ese payload fijo, así que esta línea solo usa
// lo que sí está garantizado actualizado.
$chk_is_pickup      = $chk_summary['shipping_method_id'] && false !== strpos( $chk_summary['shipping_method_id'], 'local_pickup' );
$chk_despacho_extra = $chk_summary['shipping_label'];
if ( ! $chk_is_pickup ) {
    $chk_addr_parts = array_filter( array(
        WC()->customer->get_shipping_address(),
        WC()->customer->get_shipping_address_2(),
    ) );
    if ( ! empty( $chk_addr_parts ) ) {
        $chk_despacho_extra = implode( ', ', $chk_addr_parts );
    }
}
?>
<div class="woocommerce-checkout-review-order-table chk-summary-lines-wrap">

    <?php if ( $chk_summary['shipping_rate_id'] ) : ?>
        <input type="hidden" class="chk-shipping-method-mirror" name="shipping_method_display[0]" data-index="0" value="<?php echo esc_attr( $chk_summary['shipping_rate_id'] ); ?>">
    <?php endif; ?>

    <?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>

    <div class="chk-summary-lines">
        <div class="chk-summary-line">
            <span>
                <?php
                /* translators: %d es la cantidad de productos top-level del carrito */
                echo esc_html( sprintf( __( 'Productos (%d)', 'telconnect' ), $chk_summary['products_count'] ) );
                ?>
            </span>
            <span><?php echo wp_kses_post( wc_price( $chk_summary['products_subtotal'] ) ); ?></span>
        </div>

        <?php if ( $chk_summary['addon_label'] ) : ?>
            <div class="chk-summary-line">
                <span><?php echo esc_html( $chk_summary['addon_label'] ); ?></span>
                <span><?php echo wp_kses_post( wc_price( $chk_summary['addon_subtotal'] ) ); ?></span>
            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <div class="chk-summary-line chk-summary-line--discount">
                <span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                <span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
            </div>
        <?php endforeach; ?>

        <?php if ( null !== $chk_summary['shipping_html'] ) : ?>
            <div class="chk-summary-line">
                <span><?php esc_html_e( 'Despacho', 'telconnect' ); ?></span>
                <span class="chk-summary-shipping-value<?php echo esc_attr( 0.0 === $chk_summary['shipping_cost'] ? ' chk-summary-shipping-value--free' : '' ); ?>">
                    <?php echo wp_kses_post( $chk_summary['shipping_html'] ); ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="chk-summary-line">
            <span><?php esc_html_e( 'Neto', 'telconnect' ); ?></span>
            <span><?php echo wp_kses_post( wc_price( $chk_summary['net_total'] ) ); ?></span>
        </div>

        <?php if ( $chk_summary['total_tax'] > 0 ) : ?>
            <div class="chk-summary-line">
                <span><?php echo esc_html( $chk_summary['tax_label'] ); ?></span>
                <span><?php echo wp_kses_post( wc_price( $chk_summary['total_tax'] ) ); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>

    <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

    <div class="chk-summary-total">
        <span><?php esc_html_e( 'Total', 'telconnect' ); ?></span>
        <span><?php echo wp_kses_post( wc_price( $chk_summary['total'] ) ); ?></span>
    </div>

    <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

    <?php
    /**
     * Bloque "Datos" del Figma de Pago (recursos/checkout-pago/):
     * Despacho (dirección real u "Retiro en tienda"), Comisión y
     * Servicio — 100% informativo, NO se suma al total ni duplica
     * ningún cargo ya calculado arriba (confirmado con el usuario:
     * es solo un recordatorio de qué incluye la compra). "1,49%" es
     * la misma tarifa "Comisión Estándar" ya publicada en la sección
     * Commission de la home (template-parts/commission.php) — no un
     * número nuevo inventado acá.
     */
    ?>
    <div class="chk-summary-extra">
        <div class="chk-summary-extra-line">
            <span><?php esc_html_e( 'Despacho', 'telconnect' ); ?></span>
            <span><?php echo esc_html( $chk_despacho_extra ); ?></span>
        </div>
        <div class="chk-summary-extra-line">
            <span><?php esc_html_e( 'Comisión', 'telconnect' ); ?></span>
            <span><?php esc_html_e( 'Estándar · 1,49%', 'telconnect' ); ?></span>
        </div>
        <div class="chk-summary-extra-line">
            <span><?php esc_html_e( 'Servicio', 'telconnect' ); ?></span>
            <span><?php esc_html_e( 'Pagos y boleta electrónica', 'telconnect' ); ?></span>
        </div>
    </div>

    <?php // Visibilidad 100% CSS vía .chk-page[data-step] — ver docblock. ?>
    <div class="chk-trust-note" data-panel="datos">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/shield-check.svg' ); ?>" alt="">
        <span><?php esc_html_e( 'Tus datos viajan cifrados y solo se usan para activar tu cuenta.', 'telconnect' ); ?></span>
    </div>
    <div class="chk-trust-note" data-panel="pago">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/shield-check.svg' ); ?>" alt="">
        <span><?php esc_html_e( 'Pago procesado en un entorno seguro. No guardamos los datos de tu tarjeta.', 'telconnect' ); ?></span>
    </div>

</div>
