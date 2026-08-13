<?php
/**
 * Override de yourtheme/woocommerce/myaccount/view-order.php
 * Base: woocommerce/templates/myaccount/view-order.php (v10.6.0)
 * El core imprime "Order #X was placed on Y and is currently Z" como un
 * párrafo de texto plano. Se reemplaza por un header tipo card (número +
 * fecha + badge de estado) y se agrega un link de vuelta a Pedidos.
 * Se preserva do_action('woocommerce_view_order', $order_id) intacto —
 * ahí el core engancha woocommerce_order_details_table(), que renderiza
 * order/order-details.php (tabla de items, totales y direcciones). Ese
 * template NO se overridea, se restylea vía CSS en account.css.
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
?>

<a class="acc-order-back" href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>">&larr; <?php esc_html_e( 'Volver a mis pedidos', 'telconnect' ); ?></a>

<div class="acc-panel">
    <div class="acc-order-header">
        <div class="acc-order-header-info">
            <span class="acc-order-number">
                <?php
                /* translators: %s: número de pedido */
                echo esc_html( sprintf( __( 'Pedido #%s', 'telconnect' ), $order->get_order_number() ) );
                ?>
            </span>
            <span class="acc-order-date">
                <?php
                /* translators: %s: fecha del pedido */
                echo esc_html( sprintf( __( 'Realizado el %s', 'telconnect' ), wc_format_datetime( $order->get_date_created() ) ) );
                ?>
            </span>
        </div>
        <span class="<?php echo esc_attr( tc_get_order_status_badge_class( $order->get_status() ) ); ?>">
            <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
        </span>
    </div>

    <?php if ( $notes ) : ?>
        <h2 class="acc-panel-title" style="margin-top:28px;"><?php esc_html_e( 'Novedades del pedido', 'telconnect' ); ?></h2>
        <ol class="woocommerce-OrderUpdates commentlist notes">
            <?php foreach ( $notes as $note ) : ?>
            <li class="woocommerce-OrderUpdate comment note">
                <div class="woocommerce-OrderUpdate-inner comment_container">
                    <div class="woocommerce-OrderUpdate-text comment-text">
                        <p class="woocommerce-OrderUpdate-meta meta"><?php echo esc_html( date_i18n( __( 'l jS \o\f F Y, h:ia', 'telconnect' ), strtotime( $note->comment_date ) ) ); ?></p>
                        <div class="woocommerce-OrderUpdate-description description">
                            <?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

    <?php do_action( 'woocommerce_view_order', $order_id ); ?>
</div>
