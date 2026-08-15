<?php
/**
 * Override de yourtheme/woocommerce/checkout/payment-method.php
 * Base: woocommerce/templates/checkout/payment-method.php (v3.5.0)
 *
 * Override SELECTIVO (§8.8) — no se toca payment.php (el wrapper #payment,
 * el botón #place_order, el nonce, el hook woocommerce_review_order_before/after_submit
 * siguen intactos, mismo criterio de "restylear/retextear sin tocar lo
 * validado" del §8.1). Solo se le da forma de radio-card grande (con
 * badge "Inmediato"/"24 h") a CADA gateway individual — se mantienen
 * el <input id="payment_method_{id}"> con el mismo id/name/value/data-
 * attribute que el core y las clases wc_payment_method/payment_method_{id}/
 * payment_box tal cual, porque el propio checkout.js de WC las usa para
 * mostrar/ocultar los campos del gateway elegido (payment_box) y para
 * togglear qué radio está "chosen" — cero JS propio para esto.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$chk_payment_badges = array(
    'wcplugingateway' => __( 'Inmediato', 'telconnect' ),
    'bacs'             => __( '24 h', 'telconnect' ),
);
$chk_badge = isset( $chk_payment_badges[ $gateway->id ] ) ? $chk_payment_badges[ $gateway->id ] : '';
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> chk-payment-option<?php echo esc_attr( $gateway->chosen ? ' is-selected' : '' ); ?>">
    <label class="chk-payment-option-label" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
        <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio chk-payment-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
        <span class="chk-payment-dot"></span>
        <span class="chk-payment-text">
            <span class="chk-payment-title"><?php echo wp_kses_post( $gateway->get_title() ); ?> <?php echo wp_kses_post( $gateway->get_icon() ); ?></span>
            <?php if ( $gateway->get_description() ) : ?>
                <span class="chk-payment-desc"><?php echo wp_kses_post( $gateway->get_description() ); ?></span>
            <?php endif; ?>
        </span>
        <?php if ( $chk_badge ) : ?>
            <span class="chk-payment-badge"><?php echo esc_html( $chk_badge ); ?></span>
        <?php endif; ?>
    </label>
    <?php if ( $gateway->has_fields() ) : ?>
        <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
            <?php $gateway->payment_fields(); ?>
        </div>
    <?php endif; ?>
</li>
