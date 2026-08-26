<?php
/**
 * Override de yourtheme/woocommerce/cart/cart-empty.php
 * Base: woocommerce/templates/cart/cart-empty.php (v7.0.1)
 * El shortcode [woocommerce_cart] renderiza ESTE template en vez de
 * cart.php cuando el carrito está vacío (WC_Shortcode_Cart::output) —
 * page.php no envuelve nada, así que sin este override el estado
 * vacío se veía sin la shell .crt-page/.crt-container (título, fondo,
 * padding) que sí tiene el carrito con productos, generando el mismo
 * salto visual que se quiere evitar. Se mantiene el hook
 * woocommerce_cart_is_empty tal cual (wc_empty_cart_message) y el link
 * a la tienda.
 *
 * v2: mismo header (sin eyebrow, título "Tu carrito" Ink-900) y stepper
 * de 3 pasos decorativo que cart.php, para que la shell sea idéntica
 * entre el estado vacío y con productos — ver docblock de cart.php.
 *
 * v3: contenido del estado vacío contra diseño real (captura) — se
 * quita el hook woocommerce_cart_is_empty (wc_empty_cart_message
 * default de WC, sin ilustración) y se reemplaza por la ilustración +
 * copy + CTA del Figma. El CTA apunta a home_url(), no a la tienda.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="crt-page">
    <div class="crt-container">

        <div class="crt-header">
            <div class="crt-title-row">
                <h1 class="crt-title"><?php esc_html_e( 'Tu carrito', 'telconnect' ); ?></h1>
                <span class="crt-count"><?php esc_html_e( '0 productos', 'telconnect' ); ?></span>
            </div>

            <div class="crt-steps" aria-hidden="true">
                <div class="crt-step crt-step--active">
                    <span class="crt-step-num">1</span>
                    <span class="crt-step-label"><?php esc_html_e( 'Carrito', 'telconnect' ); ?></span>
                </div>
                <span class="crt-step-sep"></span>
                <div class="crt-step">
                    <span class="crt-step-num">2</span>
                    <span class="crt-step-label"><?php esc_html_e( 'Datos', 'telconnect' ); ?></span>
                </div>
                <span class="crt-step-sep"></span>
                <div class="crt-step">
                    <span class="crt-step-num">3</span>
                    <span class="crt-step-label"><?php esc_html_e( 'Pago', 'telconnect' ); ?></span>
                </div>
            </div>
        </div>

        <div class="crt-empty">
            <img class="crt-empty-illustration" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/carrito-vacio.png' ); ?>" alt="" width="302" height="297">

            <h2 class="crt-empty-title"><?php esc_html_e( 'Tu carrito está vacío', 'telconnect' ); ?></h2>
            <p class="crt-empty-desc"><?php esc_html_e( 'Parece que todavía no has agregado nada. Explora nuestros productos y encuentra lo que necesitas.', 'telconnect' ); ?></p>

            <a class="btn btn-primary-blue crt-empty-cta" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php esc_html_e( 'Volver al inicio', 'telconnect' ); ?>
            </a>
        </div>
    </div>
</div>
