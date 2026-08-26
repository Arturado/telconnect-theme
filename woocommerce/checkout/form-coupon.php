<?php
/**
 * Override de yourtheme/woocommerce/checkout/form-coupon.php
 * Base: woocommerce/templates/checkout/form-coupon.php (v9.8.0)
 *
 * Se llama a mano desde review-order.php (después de la línea IVA),
 * no desde el hook woocommerce_before_checkout_form (ver el
 * remove_action en functions.php).
 *
 * A propósito NO lleva la clase "checkout_coupon" ni el link/toggle
 * "Have a coupon?" del template original: acá el campo va siempre
 * visible (no escondido detrás de un toggle) y el envío se maneja con
 * un listener de click/Enter propio (assets/js/checkout.js,
 * initCheckoutCouponForm()) en vez del $('form.checkout_coupon').on('submit', ...)
 * del core, que se pierde en cada recálculo AJAX porque
 * WC_AJAX::update_order_review() reemplaza TODO el contenido de
 * .woocommerce-checkout-review-order-table (ver docblock largo del
 * remove_action en functions.php).
 *
 * A propósito TAMPOCO es un <form> real: este bloque vive DENTRO de
 * <form class="checkout"> (form-checkout.php), y anidar un <form>
 * dentro de otro rompe el submit real en un click de verdad — al
 * hacer bubble el evento 'submit' pasa primero por el <form class="checkout">
 * ancestro, donde el propio wc_checkout_form del core intercepta/corta
 * la propagación antes de que llegue al listener delegado en document,
 * y termina en un POST nativo de página completa (falla real
 * comprobada con Playwright/Chrome: un $form.trigger('submit') sintético
 * sí llegaba al listener, pero un click real del usuario no). Por eso
 * es un <div> con un botón type="button" y el submit se dispara a mano
 * por click/Enter, nunca por el evento 'submit' del navegador.
 * El id="coupon_code" SÍ se conserva: el core sigue usándolo (delegado
 * en document.body) para limpiar el estado de error mientras el
 * comprador escribe.
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
    return;
}
?>
<div class="chk-summary-coupon">
    <label for="coupon_code" class="chk-summary-coupon-label"><?php esc_html_e( 'Cupón de descuento', 'telconnect' ); ?></label>
    <div class="tc-chk-coupon-form woocommerce-form-coupon chk-summary-coupon-row">
        <input type="text" name="coupon_code" class="input-text" id="coupon_code" placeholder="<?php esc_attr_e( 'Ingresa tu código', 'telconnect' ); ?>" value="" />
        <button type="button" class="button tc-chk-coupon-submit" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Aplicar', 'telconnect' ); ?></button>
    </div>
</div>
