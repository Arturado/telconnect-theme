<?php
/**
 * Override de yourtheme/woocommerce/myaccount/my-account.php
 * Base: woocommerce/templates/myaccount/my-account.php (v3.5.0)
 * Shell de la página (mismo criterio que .chk-page/.crt-page): sidebar
 * con la navegación nativa (do_action('woocommerce_account_navigation'),
 * restyleada vía CSS en .acc-nav-card, navigation.php NO se overridea)
 * + columna de contenido (do_action('woocommerce_account_content'), que
 * WC rellena según el endpoint activo — dashboard.php, orders.php, etc.)
 *
 * El eyebrow/título se calculan a partir de wc_get_account_menu_items()
 * en vez de hardcodear un string por endpoint, para que si WooCommerce
 * agrega/quita un endpoint el título se siga resolviendo solo.
 */

defined( 'ABSPATH' ) || exit;

global $wp;

$acc_title = __( 'Mi cuenta', 'telconnect' );

foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
    if ( wc_is_current_account_menu_item( $endpoint ) ) {
        $acc_title = $label;
        break;
    }
}

// view-order no está en el menú (se navega desde la fila del pedido en
// Pedidos), así que se resuelve aparte para mostrar el número de pedido.
if ( isset( $wp->query_vars['view-order'] ) ) {
    $viewed_order = wc_get_order( absint( $wp->query_vars['view-order'] ) );
    if ( $viewed_order ) {
        /* translators: %s: número de pedido */
        $acc_title = sprintf( __( 'Pedido #%s', 'telconnect' ), $viewed_order->get_order_number() );
    }
}

// dashboard.php, orders.php, view-order.php y my-address.php ya envuelven
// su propio contenido (.acc-panel / .acc-orders-panel / .acc-address-grid)
// porque están overrideados en este theme. form-edit-account.php y
// form-edit-address.php (con un tipo de dirección específico, ej.
// /edit-address/billing/) NO están overrideados — son formularios nativos
// de WC sin ningún wrapper — así que acá se les agrega una card genérica
// para que no queden flotando sueltos sobre el fondo gris de la página.
$acc_needs_generic_panel = isset( $wp->query_vars['edit-account'] )
    || ( isset( $wp->query_vars['edit-address'] ) && '' !== $wp->query_vars['edit-address'] );
?>

<div class="acc-page">
    <div class="acc-container">
        <span class="acc-eyebrow"><?php esc_html_e( 'Mi cuenta', 'telconnect' ); ?></span>
        <h1 class="acc-title"><?php echo esc_html( $acc_title ); ?></h1>

        <div class="acc-layout">
            <div class="acc-sidebar">
                <div class="acc-nav-card">
                    <?php do_action( 'woocommerce_account_navigation' ); ?>
                </div>
            </div>

            <div class="acc-content">
                <div class="woocommerce-MyAccount-content<?php echo $acc_needs_generic_panel ? ' acc-panel' : ''; ?>">
                    <?php do_action( 'woocommerce_account_content' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
