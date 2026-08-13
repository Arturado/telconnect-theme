<?php
/**
 * Override de yourtheme/woocommerce/myaccount/lost-password-confirmation.php
 * Base: woocommerce/templates/myaccount/lost-password-confirmation.php (v3.9.0)
 * Se muestra tras enviar el formulario de "olvidé mi contraseña". Igual
 * que los otros 2 templates de este flujo, se renderiza standalone — se
 * envuelve en el shell .acc-page/.acc-container.
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="acc-page">
    <div class="acc-container acc-simple-container">
        <span class="acc-eyebrow"><?php esc_html_e( 'Mi cuenta', 'telconnect' ); ?></span>
        <h1 class="acc-title"><?php esc_html_e( 'Revisa tu email', 'telconnect' ); ?></h1>

        <div class="acc-simple-card">
            <?php wc_print_notice( esc_html__( 'Te enviamos un email para restablecer tu contraseña.', 'telconnect' ) ); ?>

            <?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

            <p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'El email puede tardar unos minutos en llegar. Espera al menos 10 minutos antes de intentar otro restablecimiento.', 'telconnect' ) ) ); ?></p>

            <?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
        </div>
    </div>
</div>
