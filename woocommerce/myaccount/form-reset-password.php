<?php
/**
 * Override de yourtheme/woocommerce/myaccount/form-reset-password.php
 * Base: woocommerce/templates/myaccount/form-reset-password.php (v9.2.0)
 * Se llega acá desde el link del email de recuperación. Igual que
 * form-lost-password.php, se renderiza standalone (sin wrapper de
 * my-account.php) — mismo criterio de envolver en .acc-page/.acc-container.
 *
 * @var array $args {
 *     @type string $key   Clave de reset.
 *     @type string $login Login del usuario.
 * }
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="acc-page">
    <div class="acc-container acc-simple-container">
        <span class="acc-eyebrow"><?php esc_html_e( 'Mi cuenta', 'telconnect' ); ?></span>
        <h1 class="acc-title"><?php esc_html_e( 'Nueva contraseña', 'telconnect' ); ?></h1>

        <div class="acc-simple-card">
            <form method="post" class="woocommerce-ResetPassword lost_reset_password">

                <p><?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Ingresa tu nueva contraseña.', 'telconnect' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

                <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                    <label for="password_1"><?php esc_html_e( 'Nueva contraseña', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" autocomplete="new-password" required aria-required="true" />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                    <label for="password_2"><?php esc_html_e( 'Repite la contraseña', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" autocomplete="new-password" required aria-required="true" />
                </p>

                <input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
                <input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

                <div class="clear"></div>

                <?php do_action( 'woocommerce_resetpassword_form' ); ?>

                <p class="woocommerce-form-row form-row">
                    <input type="hidden" name="wc_reset_password" value="true" />
                    <button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Guardar', 'telconnect' ); ?>"><?php esc_html_e( 'Guardar', 'telconnect' ); ?></button>
                </p>

                <?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

            </form>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_reset_password_form' ); ?>
