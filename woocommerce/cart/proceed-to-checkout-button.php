<?php
/**
 * Override de yourtheme/woocommerce/cart/proceed-to-checkout-button.php
 * Base: woocommerce/templates/cart/proceed-to-checkout-button.php (v7.0.1)
 *
 * Antes NO se overrideaba este template (§8.2/§8.7 — criterio general de
 * "restylear/retextear sin tocar el template" para no arriesgarse en
 * updates de WC). Se overridea ahora, mínimamente, únicamente para poder
 * envolver el texto en el markup del roll de texto (.btn-label-mask +
 * .btn-label + .btn-label-ghost) — la duplicación de texto necesaria
 * para esa animación no se puede lograr solo con CSS. Se preserva el
 * mismo esc_html_e('Proceed to checkout', 'woocommerce') del core (no
 * la traducción ya aplicada) para que tc_translate_proceed_to_checkout_text()
 * (functions.php) lo siga interceptando igual que antes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>">
    <span class="btn-label-mask">
        <span class="btn-label"><?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?></span>
        <span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?></span>
    </span>
</a>
