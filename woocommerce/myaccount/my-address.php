<?php
/**
 * Override de yourtheme/woocommerce/myaccount/my-address.php
 * Base: woocommerce/templates/myaccount/my-address.php (v9.3.0)
 * El core envuelve las direcciones en ".col2-set .col-1/.col-2", que en
 * el CSS nativo de WooCommerce trae "float:left/right; width:48%" con
 * especificidad (0,3,0) — mismo bug de especificidad ya documentado en
 * pdp.css/§8.4 (ahí con div.images/div.summary). En vez de neutralizarlo
 * con selectores de mayor especificidad, se overridea el template
 * completo con clases propias (.acc-address-grid) y CSS Grid: es una
 * card por dirección, más simple que pelear la cascada del core acá.
 * Se preserva 'woocommerce_my_account_get_addresses' y
 * 'woocommerce_my_account_after_my_address' para no romper plugins que
 * dependan de esos hooks (hoy ninguno lo hace).
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
    $acc_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing'  => __( 'Dirección de facturación', 'telconnect' ),
            'shipping' => __( 'Dirección de envío', 'telconnect' ),
        ),
        $customer_id
    );
} else {
    $acc_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing' => __( 'Dirección de facturación', 'telconnect' ),
        ),
        $customer_id
    );
}
?>

<p class="acc-welcome-text" style="margin-bottom:24px;">
    <?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'Estas direcciones se usarán por defecto en el checkout.', 'telconnect' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</p>

<div class="acc-address-grid">
    <?php foreach ( $acc_addresses as $name => $address_title ) : ?>
        <?php $address = wc_get_account_formatted_address( $name ); ?>

        <div class="acc-address-card woocommerce-Address">
            <div class="acc-address-card-header">
                <h2 class="acc-address-card-title"><?php echo esc_html( $address_title ); ?></h2>
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="acc-address-edit-link edit">
                    <?php echo $address ? esc_html__( 'Editar', 'telconnect' ) : esc_html__( 'Agregar', 'telconnect' ); ?>
                </a>
            </div>
            <address>
                <?php if ( $address ) : ?>
                    <?php echo wp_kses_post( $address ); ?>
                <?php else : ?>
                    <span class="acc-address-empty"><?php esc_html_e( 'Aún no configuras esta dirección.', 'telconnect' ); ?></span>
                <?php endif; ?>

                <?php
                /**
                 * Usado para agregar contenido después de los campos nativos.
                 *
                 * @param string $name Tipo de dirección.
                 * @since 8.7.0
                 */
                do_action( 'woocommerce_my_account_after_my_address', $name );
                ?>
            </address>
        </div>
    <?php endforeach; ?>
</div>
