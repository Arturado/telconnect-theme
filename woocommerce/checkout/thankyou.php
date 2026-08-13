<?php
/**
 * Override de yourtheme/woocommerce/checkout/thankyou.php
 * Base: woocommerce/templates/checkout/thankyou.php (v8.1.0)
 *
 * IMPORTANTE: se preservan exactamente los do_action() del core,
 * en especial 'woocommerce_thankyou' — el gateway TUU
 * (WCPluginGateway::thankyouPageCallback) está enganchado ahí para
 * confirmar el pago y vaciar el carrito al volver de la pasarela.
 * No quitar ni renombrar ese hook.
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="chk-page">
    <div class="chk-container chk-thankyou-container">
        <div class="woocommerce-order chk-thankyou-card">

            <?php
            if ( $order ) :

                do_action( 'woocommerce_before_thankyou', $order->get_id() );
                ?>

                <?php if ( $order->has_status( 'failed' ) ) : ?>

                    <div class="chk-thankyou-icon chk-thankyou-icon-error" aria-hidden="true">✕</div>
                    <h1 class="chk-thankyou-title"><?php esc_html_e( 'No pudimos procesar tu pago', 'telconnect' ); ?></h1>
                    <p class="chk-thankyou-subtitle"><?php esc_html_e( 'El banco o medio de pago rechazó la transacción. Intenta nuevamente o escríbenos si el problema persiste.', 'telconnect' ); ?></p>

                    <p class="chk-thankyou-actions">
                        <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn-primary"><?php esc_html_e( 'Reintentar pago', 'telconnect' ); ?></a>
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-outline-dark"><?php esc_html_e( 'Ir a mi cuenta', 'telconnect' ); ?></a>
                        <?php endif; ?>
                    </p>

                <?php else : ?>

                    <div class="chk-thankyou-icon" aria-hidden="true">✓</div>
                    <h1 class="chk-thankyou-title"><?php esc_html_e( '¡Gracias por tu compra!', 'telconnect' ); ?></h1>
                    <p class="chk-thankyou-subtitle"><?php esc_html_e( 'Tu pedido fue recibido correctamente. Te enviaremos un correo con el detalle y el estado del envío.', 'telconnect' ); ?></p>

                    <ul class="chk-thankyou-overview woocommerce-order-overview woocommerce-thankyou-order-details order_details">

                        <li class="woocommerce-order-overview__order order">
                            <?php esc_html_e( 'N° de pedido', 'telconnect' ); ?>
                            <strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                        </li>

                        <li class="woocommerce-order-overview__date date">
                            <?php esc_html_e( 'Fecha', 'telconnect' ); ?>
                            <strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                        </li>

                        <?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
                            <li class="woocommerce-order-overview__email email">
                                <?php esc_html_e( 'Email', 'telconnect' ); ?>
                                <strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                            </li>
                        <?php endif; ?>

                        <li class="woocommerce-order-overview__total total">
                            <?php esc_html_e( 'Total', 'telconnect' ); ?>
                            <strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                        </li>

                        <?php if ( $order->get_payment_method_title() ) : ?>
                            <li class="woocommerce-order-overview__payment-method method">
                                <?php esc_html_e( 'Método de pago', 'telconnect' ); ?>
                                <strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
                            </li>
                        <?php endif; ?>

                    </ul>

                <?php endif; ?>

                <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
                <?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

            <?php else : ?>

                <div class="chk-thankyou-icon" aria-hidden="true">✓</div>
                <h1 class="chk-thankyou-title"><?php esc_html_e( '¡Gracias por tu compra!', 'telconnect' ); ?></h1>

            <?php endif; ?>

        </div>
    </div>
</div>
