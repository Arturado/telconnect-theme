<?php
/**
 * Override de yourtheme/woocommerce/myaccount/dashboard.php
 * Base: woocommerce/templates/myaccount/dashboard.php (v4.4.0)
 * El core solo imprime 2 párrafos de texto plano con links inline.
 * Se reemplaza por una card de bienvenida + accesos rápidos a
 * Pedidos/Direcciones/Datos de cuenta, mismo patrón de card que el
 * resto del sitio. Se preserva 'woocommerce_account_dashboard' por si
 * algún plugin engancha algo ahí (hoy ninguno lo hace).
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="acc-panel">
    <h2 class="acc-welcome-title">
        <?php
        printf(
            /* translators: %s: nombre del usuario */
            esc_html__( 'Hola, %s', 'telconnect' ),
            '<strong>' . esc_html( $current_user->display_name ) . '</strong>'
        );
        ?>
    </h2>
    <p class="acc-welcome-text">
        <?php esc_html_e( 'Desde acá puedes revisar tus pedidos, actualizar tus direcciones de despacho y facturación, y editar los datos de tu cuenta.', 'telconnect' ); ?>
        <?php if ( is_user_logged_in() ) : ?>
            <?php
            printf(
                /* translators: %s: url de logout */
                wp_kses( __( '¿No eres tú? <a href="%s">Cerrar sesión</a>.', 'telconnect' ), array( 'a' => array( 'href' => array() ) ) ),
                esc_url( wc_logout_url() )
            );
            ?>
        <?php endif; ?>
    </p>

    <div class="acc-quicklinks">
        <a class="acc-quicklink-card" href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>">
            <h3 class="acc-quicklink-title"><?php esc_html_e( 'Mis pedidos', 'telconnect' ); ?></h3>
            <p class="acc-quicklink-desc"><?php esc_html_e( 'Revisa el estado y el historial de tus compras.', 'telconnect' ); ?></p>
            <span class="acc-quicklink-arrow"><?php esc_html_e( 'Ver pedidos →', 'telconnect' ); ?></span>
        </a>

        <a class="acc-quicklink-card" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>">
            <h3 class="acc-quicklink-title"><?php esc_html_e( 'Direcciones', 'telconnect' ); ?></h3>
            <p class="acc-quicklink-desc"><?php esc_html_e( 'Administra tu dirección de facturación y de envío.', 'telconnect' ); ?></p>
            <span class="acc-quicklink-arrow"><?php esc_html_e( 'Ver direcciones →', 'telconnect' ); ?></span>
        </a>

        <a class="acc-quicklink-card" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>">
            <h3 class="acc-quicklink-title"><?php esc_html_e( 'Datos de la cuenta', 'telconnect' ); ?></h3>
            <p class="acc-quicklink-desc"><?php esc_html_e( 'Actualiza tu nombre, email o contraseña.', 'telconnect' ); ?></p>
            <span class="acc-quicklink-arrow"><?php esc_html_e( 'Editar datos →', 'telconnect' ); ?></span>
        </a>
    </div>

    <?php
        /**
         * My Account dashboard.
         *
         * @since 2.6.0
         */
        do_action( 'woocommerce_account_dashboard' );
    ?>
</div>
