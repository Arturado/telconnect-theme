<?php
/**
 * Override de yourtheme/woocommerce/checkout/order-receipt.php
 * Base: woocommerce/templates/checkout/order-receipt.php (v3.2.0)
 *
 * Pantalla intermedia real entre "hacer clic en Pagar" y salir del sitio
 * hacia Webpay — investigada antes de construir (CONTEXT.md, tarea
 * "Redirigiendo a Webpay"/"Pedido cancelado"). Se llega acá vía
 * WC_Shortcode_Checkout::order_pay() → wc_get_template('checkout/order-receipt.php')
 * cuando el pedido ya existe y necesita pago (justo después de que
 * WCPluginGateway::process_payment() redirige a get_checkout_payment_url()).
 * Sí es una página real de NUESTRO sitio (misma página "Finalizar compra",
 * con header/footer del theme) — no es HTML ajeno ni un iframe.
 *
 * IMPORTANTE: do_action('woocommerce_receipt_' . $order->get_payment_method(), ...)
 * dispara WCPluginGateway::receiptPage() (hook 'woocommerce_receipt_wcplugingateway'),
 * que hace el trabajo real: genera la URL de pago de Webpay, actualiza el
 * pedido a "cancelled" si vuelve con x_result=failed, imprime un <p>/<a>
 * de respaldo (por si el JS no corre) y el <script> que redirige de
 * verdad a los 5 segundos. NO se toca el plugin — se preserva el
 * do_action textual, solo se envuelve en la misma "shell" visual del
 * resto del checkout y se restylea su salida cruda (h1/p/a sin clases
 * propias) por selector genérico, mismo criterio que el resto del sitio
 * (§6/§8.1: overridear el wrapper, restylear el contenido nativo por CSS).
 *
 * $_GET['x_result'] se lee acá en modo SOLO LECTURA (no se muta nada),
 * únicamente para decidir qué encabezado/ícono propio mostrar ANTES del
 * do_action — es el mismo criterio que ya usa receiptPage() interno del
 * plugin, duplicado a propósito de forma inofensiva.
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;

$chk_receipt_cancelled = isset( $_GET['x_result'] ) && 'failed' === $_GET['x_result']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>

<div class="chk-page chk-thankyou-page chk-receipt-page">
    <div class="chk-container chk-thankyou-container tc-container">
        <div class="chk-thankyou-card chk-thankyou-card--simple">

            <?php if ( $chk_receipt_cancelled ) : ?>

                <div class="chk-thankyou-icon chk-thankyou-icon-cancelled" aria-hidden="true">✕</div>
                <h1 class="chk-thankyou-title"><?php esc_html_e( 'Tu pedido fue cancelado', 'telconnect' ); ?></h1>
                <p class="chk-thankyou-subtitle"><?php esc_html_e( 'El pago no se completó en Webpay y no se realizó ningún cobro. Te llevamos de vuelta a la tienda en unos segundos.', 'telconnect' ); ?></p>

            <?php else : ?>

                <div class="chk-receipt-spinner" aria-hidden="true"></div>
                <h1 class="chk-thankyou-title"><?php esc_html_e( 'Redirigiendo a Webpay', 'telconnect' ); ?></h1>
                <p class="chk-thankyou-subtitle"><?php esc_html_e( 'Estamos abriendo la pasarela de pago segura. Esto toma solo unos segundos.', 'telconnect' ); ?></p>

            <?php endif; ?>

            <ul class="chk-thankyou-overview">
                <li>
                    <?php esc_html_e( 'N° de pedido', 'telconnect' ); ?>
                    <strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
                </li>
                <li>
                    <?php esc_html_e( 'Total', 'telconnect' ); ?>
                    <strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
                </li>
            </ul>

            <div class="chk-receipt-native">
                <?php do_action( 'woocommerce_receipt_' . $order->get_payment_method(), $order->get_id() ); ?>
            </div>

        </div>
    </div>
</div>

<div class="clear"></div>
