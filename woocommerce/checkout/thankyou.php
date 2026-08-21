<?php
/**
 * Override de yourtheme/woocommerce/checkout/thankyou.php
 * Base: woocommerce/templates/checkout/thankyou.php (v8.1.0)
 *
 * v2 — rediseño pixel-a-pixel contra recursos/thankyou/ (Copy as CSS +
 * captura). Reemplaza el v1 "sin Figma todavía" (CONTEXT.md §8.1).
 *
 * IMPORTANTE: se preservan exactamente los do_action() del core,
 * en especial 'woocommerce_thankyou' — el gateway TUU
 * (WCPluginGateway::thankyouPageCallback) está enganchado ahí para
 * confirmar el pago y vaciar el carrito al volver de la pasarela.
 * No quitar ni renombrar ese hook. Ese mismo callback también imprime un
 * <script> que redirige al home 7s después de un pago TUU completado —
 * comportamiento ya validado, no tocado por este rediseño (ver CONTEXT.md).
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;

$chk_ty_summary = ( $order && ! $order->has_status( array( 'failed', 'cancelled' ) ) ) ? tc_get_thankyou_order_summary( $order ) : null;
?>

<div class="chk-page chk-thankyou-page">
    <div class="chk-container chk-thankyou-container">

        <?php
        if ( $order ) :

            do_action( 'woocommerce_before_thankyou', $order->get_id() );
            ?>

            <?php if ( $order->has_status( 'failed' ) ) : ?>

                <div class="chk-thankyou-card chk-thankyou-card--simple">
                    <div class="chk-thankyou-icon chk-thankyou-icon-error" aria-hidden="true">✕</div>
                    <h1 class="chk-thankyou-title"><?php esc_html_e( 'No pudimos procesar tu pago', 'telconnect' ); ?></h1>
                    <p class="chk-thankyou-subtitle"><?php esc_html_e( 'El banco o medio de pago rechazó la transacción. Intenta nuevamente o escríbenos si el problema persiste.', 'telconnect' ); ?></p>

                    <p class="chk-thankyou-actions">
                        <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="chk-btn-primary"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Reintentar pago', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Reintentar pago', 'telconnect' ); ?></span></span></a>
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="chk-btn-secondary"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Ir a mi cuenta', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Ir a mi cuenta', 'telconnect' ); ?></span></span></a>
                        <?php endif; ?>
                    </p>
                </div>

            <?php elseif ( $order->has_status( 'cancelled' ) ) : ?>

                <div class="chk-thankyou-card chk-thankyou-card--simple">
                    <div class="chk-thankyou-icon chk-thankyou-icon-cancelled" aria-hidden="true">✕</div>
                    <h1 class="chk-thankyou-title"><?php esc_html_e( 'Tu pedido fue cancelado', 'telconnect' ); ?></h1>
                    <p class="chk-thankyou-subtitle"><?php esc_html_e( 'No se realizó ningún cobro. Si fue un error puedes volver a intentarlo desde tu carrito o escribirnos si necesitas ayuda.', 'telconnect' ); ?></p>

                    <div class="chk-ty-badge">
                        <?php esc_html_e( 'Pedido', 'telconnect' ); ?>
                        <strong>#<?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                    </div>

                    <p class="chk-thankyou-actions">
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="chk-btn-primary"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Volver a la tienda', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Volver a la tienda', 'telconnect' ); ?></span></span></a>
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="chk-btn-secondary"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Ver mis pedidos', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Ver mis pedidos', 'telconnect' ); ?></span></span></a>
                        <?php endif; ?>
                    </p>
                </div>

            <?php else : ?>

                <div class="chk-ty-hero">
                    <div class="chk-ty-icon chk-ty-icon--success" aria-hidden="true">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/check-01-green.svg' ); ?>" alt="">
                </div>
                    <h1 class="chk-ty-title"><?php esc_html_e( '¡Listo! Recibimos tu pedido', 'telconnect' ); ?></h1>
                    <?php if ( $order->get_billing_email() ) : ?>
                        <p class="chk-ty-subtitle">
                            <?php
                            printf(
                                /* translators: %s es el email real del comprador */
                                esc_html__( 'Te enviamos la confirmación y la boleta electrónica a %s.', 'telconnect' ),
                                '<strong>' . esc_html( $order->get_billing_email() ) . '</strong>'
                            );
                            ?>
                        </p>
                    <?php endif; ?>
                    <div class="chk-ty-badge">
                        <?php esc_html_e( 'Pedido', 'telconnect' ); ?>
                        <strong>#<?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                    </div>
                </div>

                <div class="chk-grid chk-ty-grid">

                    <div class="chk-form-col chk-ty-detail-col">
                        <div class="chk-card chk-ty-steps-card">
                            <h2 class="chk-summary-title"><?php esc_html_e( 'Qué sigue ahora', 'telconnect' ); ?></h2>

                            <ul class="chk-ty-steps">
                                <li class="chk-ty-step">
                                    <span class="chk-ty-step-icon" aria-hidden="true">
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/check-01.png' ); ?>" alt="">
                                    </span>
                                    <div class="chk-ty-step-body">
                                        <p class="chk-ty-step-title"><?php esc_html_e( 'Validamos tus datos', 'telconnect' ); ?></p>
                                        <p class="chk-ty-step-desc"><?php esc_html_e( 'Revisamos el RUT del negocio y la cuenta bancaria. Toma menos de un día hábil.', 'telconnect' ); ?></p>
                                    </div>
                                </li>
                                <li class="chk-ty-step">
                                    <span class="chk-ty-step-icon chk-ty-step-icon--truck" aria-hidden="true">
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/truck.svg' ); ?>" alt="">
                                    </span>
                                    <div class="chk-ty-step-body">
                                        <p class="chk-ty-step-title"><?php esc_html_e( 'Despachamos tu máquina', 'telconnect' ); ?></p>
                                        <p class="chk-ty-step-desc"><?php echo esc_html( $chk_ty_summary['shipping_step_text'] ); ?></p>
                                    </div>
                                </li>
                                <li class="chk-ty-step">
                                    <span class="chk-ty-step-icon chk-ty-step-icon--card" aria-hidden="true">
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/card-01.svg' ); ?>" alt="">
                                    </span>
                                    <div class="chk-ty-step-body">
                                        <p class="chk-ty-step-title"><?php esc_html_e( 'Activamos tu cuenta', 'telconnect' ); ?></p>
                                        <p class="chk-ty-step-desc"><?php esc_html_e( 'Te llega un correo con tus accesos y la app queda lista para operar.', 'telconnect' ); ?></p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="chk-ty-actions">
                            <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="chk-btn-primary"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Ir a mis pedidos', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Ir a mis pedidos', 'telconnect' ); ?></span></span></a>
                            <?php
                            /**
                             * Sin definir aún cómo se genera la boleta real (§CONTEXT) —
                             * href="#" a propósito, no conectar a nada hasta que el
                             * usuario defina el mecanismo.
                             */
                            ?>
                            <a href="#" class="chk-btn-secondary"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Descargar boleta', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Descargar boleta', 'telconnect' ); ?></span></span></a>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="chk-ty-link"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Seguir comprando', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Seguir comprando', 'telconnect' ); ?></span></span></a>
                        </div>
                    </div>

                    <div class="chk-summary-col">
                        <div class="chk-summary-card">
                            <h2 class="chk-summary-title"><?php esc_html_e( 'Resumen del pedido', 'telconnect' ); ?></h2>

                            <ul class="chk-ty-products">
                                <?php foreach ( $chk_ty_summary['items'] as $chk_ty_item ) : ?>
                                    <li class="chk-ty-product">
                                        <span class="chk-ty-product-photo"><?php echo $chk_ty_item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                                        <span class="chk-ty-product-info">
                                            <span class="chk-ty-product-name"><?php echo esc_html( $chk_ty_item['name'] ); ?></span>
                                            <span class="chk-ty-product-qty">
                                                <?php
                                                /* translators: %d es la cantidad comprada de ese producto */
                                                echo esc_html( sprintf( __( 'Cantidad %d', 'telconnect' ), $chk_ty_item['quantity'] ) );
                                                ?>
                                            </span>
                                        </span>
                                        <span class="chk-ty-product-price"><?php echo wp_kses_post( $chk_ty_item['price_html'] ); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="chk-summary-total">
                                <span><?php esc_html_e( 'Total pagado', 'telconnect' ); ?></span>
                                <span><?php echo wp_kses_post( $chk_ty_summary['total_html'] ); ?></span>
                            </div>

                            <div class="chk-summary-extra chk-ty-summary-extra">
                                <div class="chk-summary-extra-line">
                                    <span><?php esc_html_e( 'Pago', 'telconnect' ); ?></span>
                                    <span><?php echo wp_kses_post( $chk_ty_summary['payment_title'] ); ?></span>
                                </div>
                                <div class="chk-summary-extra-line">
                                    <span><?php esc_html_e( 'Despacho', 'telconnect' ); ?></span>
                                    <span><?php echo esc_html( $chk_ty_summary['shipping_value'] ); ?></span>
                                </div>
                                <div class="chk-summary-extra-line">
                                    <span><?php esc_html_e( 'Comisión', 'telconnect' ); ?></span>
                                    <span><?php esc_html_e( 'Estándar · 1,99%', 'telconnect' ); ?></span>
                                </div>
                                <div class="chk-summary-extra-line">
                                    <span><?php esc_html_e( 'Servicio', 'telconnect' ); ?></span>
                                    <span><?php esc_html_e( 'Pagos y boleta electrónica', 'telconnect' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            <?php endif; ?>

            <?php
            /**
             * woocommerce_order_details_table() (core, hook 'woocommerce_thankyou'
             * prioridad 10) y las instrucciones de gateways como BACS (hook
             * 'woocommerce_thankyou_{method}') no tienen ningún estilo propio del
             * theme — sin este wrapper, salían con el look crudo de WooCommerce
             * justo debajo de la card rediseñada. No se overridean sus templates
             * (mismo criterio del §6/§8.1: restylear vía CSS, no reemplazar
             * markup nativo sin necesidad) — solo se envuelve el output de ambos
             * do_action en un contenedor para poder darle tipografía/espaciado
             * consistentes con el resto de la página (ver .chk-thankyou-native
             * en checkout.css).
             */
            ?>
            <div class="chk-thankyou-native">
                <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
                <?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
            </div>

        <?php else : ?>

            <div class="chk-thankyou-card chk-thankyou-card--simple">
                <div class="chk-ty-icon chk-ty-icon--success" aria-hidden="true">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/check-01-green.svg' ); ?>" alt="">
                </div>
                <h1 class="chk-thankyou-title"><?php esc_html_e( '¡Gracias por tu compra!', 'telconnect' ); ?></h1>
            </div>

        <?php endif; ?>

    </div>
</div>
