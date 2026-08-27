<?php
/**
 * Override de yourtheme/woocommerce/checkout/form-checkout.php
 * Base: woocommerce/templates/checkout/form-checkout.php (v9.4.0)
 *
 * v2 (§8.8) — wizard de 2 pasos (Datos/Pago) sobre el checkout ya
 * validado (§8.1), controlado 100% con CSS/JS (checkout.js) sobre UN
 * solo <form class="checkout"> — WooCommerce lo sigue procesando tal
 * cual (mismo gateway TUU, misma validación de RUT, mismo hook
 * thankyou). No hay 2 páginas ni 2 submits: "Continuar a pago" solo
 * cambia qué panel es visible después de validar los campos del panel
 * actual (form.reportValidity() — los campos ocultos vía [hidden]/
 * display:none quedan excluidos de la validación nativa del navegador).
 *
 * Reordenamiento de fragmentos de review-order/payment (importante,
 * ver CONTEXT.md §8.8 para el detalle completo): el Figma pone "Forma
 * de pago" en la columna PRINCIPAL (paso Pago) y el resumen en la
 * columna lateral SIEMPRE — pero el core engancha woocommerce_order_review
 * (tabla) y woocommerce_checkout_payment (radios+botón pagar) juntos al
 * MISMO hook combinado woocommerce_checkout_order_review. Se comprobó
 * en WC_AJAX::update_order_review() que la actualización en vivo NO usa
 * ese hook combinado — llama a woocommerce_order_review() y
 * woocommerce_checkout_payment() por separado y reemplaza fragments por
 * selector CSS (.woocommerce-checkout-review-order-table /
 * .woocommerce-checkout-payment), sin importar en qué parte del DOM
 * estén. Por eso acá también se llaman por separado, en las 2 columnas
 * que corresponden — el refresco en vivo sigue funcionando igual.
 *
 * El gateway TUU (WCPluginGateway) engancha además su propio
 * checkoutOrder() (chequeo de pedidos pendientes de pago) directo al
 * hook COMBINADO woocommerce_checkout_order_review — para no perder
 * ese chequeo se dispara igual una vez (con ob_start/ob_end_clean
 * porque no se necesita su HTML, ya que la tabla y el pago real se
 * pintan por separado más abajo).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'Debes iniciar sesión para finalizar la compra.', 'telconnect' ) ) );
    return;
}

// Rates reales de la zona de envío — nada hardcodeado (§8.1: Retiro en
// tienda $0 activo; Envío a domicilio ahora también $0, decisión final
// del cliente documentada en CONTEXT.md §8.1).
$chk_shipping_rates = array();
if ( WC()->cart->needs_shipping() ) {
    $chk_packages = WC()->shipping()->get_packages();
    if ( ! empty( $chk_packages[0]['rates'] ) ) {
        $chk_shipping_rates = $chk_packages[0]['rates'];
    }
}
$chk_chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
$chk_chosen_rate_id = isset( $chk_chosen_methods[0] ) && isset( $chk_shipping_rates[ $chk_chosen_methods[0] ] )
    ? $chk_chosen_methods[0]
    : ( ! empty( $chk_shipping_rates ) ? array_key_first( $chk_shipping_rates ) : '' );

$chk_total_now = tc_get_order_summary_breakdown()['total'];
?>

<div class="chk-page" data-step="datos">
    <div class="chk-container tc-container">

        <div class="chk-header">
            <div class="chk-title-block" data-panel="datos">
                <h1 class="chk-title"><?php esc_html_e( 'Completa tus datos', 'telconnect' ); ?></h1>
            </div>
            <div class="chk-title-block" data-panel="pago">
                <h1 class="chk-title"><?php esc_html_e( 'Elige cómo pagar', 'telconnect' ); ?></h1>
                <p class="chk-subtitle"><?php esc_html_e( 'Último paso: confirma el pago y activamos tu cuenta.', 'telconnect' ); ?></p>
            </div>

            <div class="chk-steps" aria-hidden="true">
                <div class="chk-step chk-step--done" data-name="carrito">
                    <span class="chk-step-num chk-step-check"></span>
                    <span class="chk-step-label"><?php esc_html_e( 'Carrito', 'telconnect' ); ?></span>
                </div>
                <span class="chk-step-sep"></span>
                <div class="chk-step" data-name="datos">
                    <span class="chk-step-num">2</span>
                    <span class="chk-step-label"><?php esc_html_e( 'Datos', 'telconnect' ); ?></span>
                </div>
                <span class="chk-step-sep"></span>
                <div class="chk-step" data-name="pago">
                    <span class="chk-step-num">3</span>
                    <span class="chk-step-label"><?php esc_html_e( 'Pago', 'telconnect' ); ?></span>
                </div>
            </div>
        </div>

        <?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout chk-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'telconnect' ); ?>">

            <?php
            // Ver docblock: solo para que corra WCPluginGateway::checkoutOrder()
            // (chequeo de pedidos TUU pendientes) — se descarta el HTML.
            ob_start();
            do_action( 'woocommerce_checkout_order_review' );
            ob_end_clean();
            ?>

            <div class="chk-grid">
                <div class="chk-form-col">

                    <?php if ( $checkout->get_checkout_fields() ) : ?>

                        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                        <div class="chk-step-panel" data-panel="datos">

                            <div class="chk-card">
                                <div class="chk-card-header">
                                    <span class="chk-card-icon">
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/user-profile-01.svg' ); ?>" alt="">
                                    </span>
                                    <div class="chk-card-heading">
                                        <h2><?php esc_html_e( 'Datos del cliente', 'telconnect' ); ?></h2>
                                        <p><?php esc_html_e( 'La persona de contacto para la compra.', 'telconnect' ); ?></p>
                                    </div>
                                    <span class="chk-card-step"><?php esc_html_e( 'PASO 01', 'telconnect' ); ?></span>
                                </div>
                                <div class="chk-section-billing">
                                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                                </div>
                            </div>

                            <div class="chk-card">
                                <div class="chk-card-header">
                                    <span class="chk-card-icon">
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/truck.svg' ); ?>" alt="">
                                    </span>
                                    <div class="chk-card-heading">
                                        <h2><?php esc_html_e( 'Despacho', 'telconnect' ); ?></h2>
                                        <p><?php esc_html_e( '¿Dónde te dejamos la máquina?', 'telconnect' ); ?></p>
                                    </div>
                                    <span class="chk-card-step"><?php esc_html_e( 'PASO 02', 'telconnect' ); ?></span>
                                </div>

                                <?php if ( ! empty( $chk_shipping_rates ) ) : ?>
                                    <div class="chk-shipping-methods">
                                        <span class="chk-shipping-methods-label"><?php esc_html_e( 'Tipo de entrega', 'telconnect' ); ?></span>
                                        <div class="chk-shipping-options">
                                            <?php foreach ( $chk_shipping_rates as $chk_rate_id => $chk_rate ) :
                                                $chk_is_pickup = false !== strpos( $chk_rate->get_method_id(), 'local_pickup' );
                                                $chk_cost      = (float) $chk_rate->get_cost();
                                                $chk_desc      = $chk_is_pickup
                                                    ? esc_html__( 'Av. Vicuña Mackenna Poniente 6843, La Florida.', 'telconnect' )
                                                    : esc_html__( 'Llega en 48 h hábiles a tu dirección.', 'telconnect' );
                                                ?>
                                                <label class="chk-shipping-option<?php echo esc_attr( $chk_rate_id === $chk_chosen_rate_id ? ' is-selected' : '' ); ?>" data-method="<?php echo esc_attr( $chk_is_pickup ? 'pickup' : 'delivery' ); ?>">
                                                    <input type="radio" class="chk-shipping-radio" name="shipping_method[0]" value="<?php echo esc_attr( $chk_rate_id ); ?>" <?php checked( $chk_rate_id, $chk_chosen_rate_id ); ?>>
                                                    <span class="chk-shipping-dot"></span>
                                                    <span class="chk-shipping-text">
                                                        <span class="chk-shipping-title"><?php echo esc_html( $chk_rate->get_label() ); ?></span>
                                                        <span class="chk-shipping-desc"><?php echo $chk_desc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                                                    </span>
                                                    <span class="chk-shipping-price<?php echo esc_attr( 0.0 === $chk_cost ? ' is-free' : '' ); ?>">
                                                        <?php echo $chk_cost > 0 ? wp_kses_post( wc_price( $chk_cost ) ) : esc_html__( 'Gratis', 'telconnect' ); ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="chk-section-shipping">
                                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                                </div>
                            </div>

                            <p class="chk-step-warning" role="alert" hidden><?php esc_html_e( 'Completa los campos obligatorios para continuar.', 'telconnect' ); ?></p>

                            <div class="chk-step-actions">
                                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="chk-btn-secondary"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Volver al carrito', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Volver al carrito', 'telconnect' ); ?></span></span></a>
                                <button type="button" class="chk-btn-primary chk-next-step"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Continuar a pago', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Continuar a pago', 'telconnect' ); ?></span></span></button>
                            </div>
                        </div>

                        <div class="chk-step-panel" data-panel="pago">

                            <div class="chk-card">
                                <div class="chk-card-header">
                                    <span class="chk-card-icon">
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/card-01.svg' ); ?>" alt="">
                                    </span>
                                    <div class="chk-card-heading">
                                        <h2><?php esc_html_e( 'Forma de pago', 'telconnect' ); ?></h2>
                                        <p><?php esc_html_e( 'Elige con qué medio quieres pagar tu compra.', 'telconnect' ); ?></p>
                                    </div>
                                    <span class="chk-card-step"><?php esc_html_e( 'PASO 01', 'telconnect' ); ?></span>
                                </div>

                                <?php
                                add_filter( 'woocommerce_order_button_text', function () use ( $chk_total_now ) {
                                    return sprintf(
                                        /* translators: %s es el total formateado, ej. $148.393 */
                                        esc_html__( 'Pagar %s', 'telconnect' ),
                                        wp_strip_all_tags( wc_price( $chk_total_now ) )
                                    );
                                } );
                                woocommerce_checkout_payment();
                                ?>
                            </div>

                            <div class="chk-step-actions">
                                <button type="button" class="chk-btn-secondary chk-prev-step"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Volver a datos', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Volver a datos', 'telconnect' ); ?></span></span></button>
                                <?php
                                /**
                                 * "Pagar $X" del Figma vive FUERA de la card (fila de
                                 * acciones, junto a "Volver a datos"), pero el botón
                                 * real #place_order que WooCommerce necesita (checkout.js
                                 * lo busca por ID en varios puntos: disable/enable durante
                                 * el submit AJAX, texto dinámico, etc.) queda DENTRO de
                                 * #payment más arriba (vía woocommerce_checkout_payment()).
                                 * En vez de duplicar/reconstruir ese botón acá (riesgo real
                                 * de romper el submit), se lo oculta visualmente (NO con
                                 * display:none — algunas librerías de WC filtran por
                                 * :visible) y este botón de acá solo reenvía el click al
                                 * real vía checkout.js. Cero lógica de submit propia.
                                 */
                                ?>
                                <button type="button" class="chk-btn-primary chk-place-order-proxy"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Pagar', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Pagar', 'telconnect' ); ?></span></span></button>
                            </div>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                    <?php endif; ?>

                </div>

                <div class="chk-summary-col">
                    <div class="chk-summary-card">
                        <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

                        <h3 id="order_review_heading" class="chk-summary-title"><?php esc_html_e( 'Resumen de tu compra', 'telconnect' ); ?></h3>

                        <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php woocommerce_order_review(); ?>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
