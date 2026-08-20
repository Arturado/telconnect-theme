<?php
/**
 * Override de yourtheme/woocommerce/myaccount/form-lost-password.php
 * Base: woocommerce/templates/myaccount/form-lost-password.php (v9.2.0)
 * Igual que form-login.php: WC_Shortcode_My_Account::lost_password()
 * renderiza este template standalone, sin pasar por my-account.php.
 * Se envuelve en el shell .acc-page/.acc-container (mismo criterio que
 * cart-empty.php, §8.2) para que no quede flotando sin fondo/título.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="acc-page">
    <div class="acc-container acc-simple-container">
        <span class="acc-eyebrow"><?php esc_html_e( 'Mi cuenta', 'telconnect' ); ?></span>
        <h1 class="acc-title"><?php esc_html_e( 'Recuperar contraseña', 'telconnect' ); ?></h1>

        <div class="acc-simple-card">
            <form method="post" class="woocommerce-ResetPassword lost_reset_password">

                <p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( '¿Olvidaste tu contraseña? Ingresa tu usuario o email y te enviaremos un link para crear una nueva.', 'telconnect' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

                <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                    <label for="user_login"><?php esc_html_e( 'Usuario o email', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
                </p>

                <div class="clear"></div>

                <?php do_action( 'woocommerce_lostpassword_form' ); ?>

                <p class="woocommerce-form-row form-row">
                    <input type="hidden" name="wc_reset_password" value="true" />
                    <button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Restablecer contraseña', 'telconnect' ); ?>"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Restablecer contraseña', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Restablecer contraseña', 'telconnect' ); ?></span></span></button>
                </p>

                <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

            </form>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_lost_password_form' ); ?>
