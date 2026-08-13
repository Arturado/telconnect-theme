<?php
/**
 * Override de yourtheme/woocommerce/myaccount/orders.php
 * Base: woocommerce/templates/myaccount/orders.php (v9.5.0)
 * Mismo árbol de hooks y columnas que el core (wc_get_account_orders_columns,
 * woocommerce_my_account_my_orders_column_{id}, paginación) — solo se
 * envuelve la tabla en .acc-orders-panel, se agrega un badge de color al
 * estado del pedido, y el estado vacío pasa de un aviso de una línea a
 * una card con CTA a la tienda (mismo criterio que crt-empty/cart-empty.php).
 *
 * @var bool $has_orders
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

    <div class="acc-orders-panel">
        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead>
                <tr>
                    <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                        <th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ( $customer_orders->orders as $customer_order ) {
                    $order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                    ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order">
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) :
                            $is_order_number = 'order-number' === $column_id;
                        ?>
                            <?php if ( $is_order_number ) : ?>
                                <th class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>" scope="row">
                            <?php else : ?>
                                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
                            <?php endif; ?>

                                <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                    <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

                                <?php elseif ( $is_order_number ) : ?>
                                    <?php /* translators: %s: el número de pedido, normalmente precedido de # */ ?>
                                    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Ver pedido número %s', 'telconnect' ), $order->get_order_number() ) ); ?>">
                                        <?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
                                    </a>

                                <?php elseif ( 'order-date' === $column_id ) : ?>
                                    <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>

                                <?php elseif ( 'order-status' === $column_id ) : ?>
                                    <span class="<?php echo esc_attr( tc_get_order_status_badge_class( $order->get_status() ) ); ?>">
                                        <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                    </span>

                                <?php elseif ( 'order-total' === $column_id ) : ?>
                                    <?php
                                    /* translators: 1: total formateado del pedido 2: cantidad de items del pedido */
                                    echo wp_kses_post( sprintf( _n( '%1$s por %2$s producto', '%1$s por %2$s productos', $item_count, 'telconnect' ), $order->get_formatted_order_total(), $item_count ) );
                                    ?>

                                <?php elseif ( 'order-actions' === $column_id ) : ?>
                                    <?php
                                    $actions = wc_get_account_orders_actions( $order );

                                    if ( ! empty( $actions ) ) {
                                        foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                            if ( empty( $action['aria-label'] ) ) {
                                                /* translators: %1$s Nombre de la acción, %2$s Número de pedido. */
                                                $action_aria_label = sprintf( __( '%1$s pedido número %2$s', 'telconnect' ), $action['name'], $order->get_order_number() );
                                            } else {
                                                $action_aria_label = $action['aria-label'];
                                            }
                                            echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button button ' . sanitize_html_class( $key ) . '" aria-label="' . esc_attr( $action_aria_label ) . '">' . esc_html( $action['name'] ) . '</a>';
                                            unset( $action_aria_label );
                                        }
                                    }
                                    ?>
                                <?php endif; ?>

                            <?php if ( $is_order_number ) : ?>
                                </th>
                            <?php else : ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

        <?php if ( 1 < $customer_orders->max_num_pages ) : ?>
            <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
                <?php if ( 1 !== $current_page ) : ?>
                    <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Anterior', 'telconnect' ); ?></a>
                <?php endif; ?>

                <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
                    <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Siguiente', 'telconnect' ); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

<?php else : ?>

    <div class="acc-empty-state">
        <h2 class="acc-empty-state-title"><?php esc_html_e( 'Todavía no tienes pedidos', 'telconnect' ); ?></h2>
        <p class="acc-empty-state-text"><?php esc_html_e( 'Cuando compres una máquina o un complemento en la tienda, vas a poder revisar el estado y el detalle de tus pedidos acá.', 'telconnect' ); ?></p>

        <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
            <a class="btn btn-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
                <?php esc_html_e( 'Ir a la tienda', 'telconnect' ); ?>
            </a>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
