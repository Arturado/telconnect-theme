<?php
/**
 * Override de yourtheme/woocommerce/checkout/form-checkout.php
 * Base: woocommerce/templates/checkout/form-checkout.php (v9.4.0)
 * Mismo árbol de hooks que el core (para no romper plugins como el
 * gateway TUU, que engancha woocommerce_checkout_order_review) —
 * solo se reestructura el markup en 2 columnas con clases .chk-*.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'Debes iniciar sesión para finalizar la compra.', 'telconnect' ) ) );
    return;
}
?>

<div class="chk-page">
    <div class="chk-container">
        <span class="chk-eyebrow"><?php esc_html_e( 'Último paso', 'telconnect' ); ?></span>
        <h1 class="chk-title"><?php esc_html_e( 'Finalizar compra', 'telconnect' ); ?></h1>

        <?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout chk-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'telconnect' ); ?>">

            <div class="chk-grid">
                <div class="chk-form-col">

                    <?php if ( $checkout->get_checkout_fields() ) : ?>

                        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                        <div class="chk-section" id="customer_details">
                            <div class="chk-section-billing">
                                <?php do_action( 'woocommerce_checkout_billing' ); ?>
                            </div>
                            <div class="chk-section-shipping">
                                <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                            </div>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                    <?php endif; ?>

                </div>

                <div class="chk-summary-col">
                    <div class="chk-summary-card">
                        <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

                        <h3 id="order_review_heading" class="chk-summary-title"><?php esc_html_e( 'Tu pedido', 'telconnect' ); ?></h3>

                        <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
