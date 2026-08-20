<?php
/**
 * Override de yourtheme/woocommerce/myaccount/form-login.php
 * Base: woocommerce/templates/myaccount/form-login.php (v9.9.0)
 *
 * HALLAZGO IMPORTANTE: WC_Shortcode_My_Account::output() renderiza este
 * template DIRECTO cuando el usuario no está logueado (return antes de
 * llamar a my_account()) — es decir, NUNCA pasa por el wrapper de
 * my-account.php. Sin este override quedaba flotando sin fondo/título,
 * el mismo problema ya documentado con cart-empty.php en §8.2. Se
 * envuelve acá mismo en el shell .acc-page/.acc-container.
 *
 * El core también usa ".col2-set .col-1/.col-2" (float:48%, mismo bug
 * de especificidad de §8.4) para el layout login/registro a 2 columnas
 * — se reemplaza por CSS Grid (.acc-auth-grid) igual que en
 * my-address.php. El registro está desactivado hoy
 * (woocommerce_enable_myaccount_registration = no, ver CONTEXT.md §8.6),
 * pero se deja andando por si el cliente lo activa más adelante.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$acc_registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );

do_action( 'woocommerce_before_customer_login_form' );
?>

<div class="acc-page">
    <div class="acc-container">
        <span class="acc-eyebrow"><?php esc_html_e( 'Mi cuenta', 'telconnect' ); ?></span>
        <h1 class="acc-title"><?php esc_html_e( 'Inicia sesión', 'telconnect' ); ?></h1>

        <div class="acc-auth-grid<?php echo $acc_registration_enabled ? '' : ' acc-auth-grid--single'; ?>" id="customer_login">

            <div class="acc-auth-card">
                <h2><?php esc_html_e( 'Iniciar sesión', 'telconnect' ); ?></h2>

                <form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

                    <?php do_action( 'woocommerce_login_form_start' ); ?>

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="username"><?php esc_html_e( 'Usuario o email', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="password"><?php esc_html_e( 'Contraseña', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                        <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
                    </p>

                    <?php do_action( 'woocommerce_login_form' ); ?>

                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                        <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Recordarme', 'telconnect' ); ?></span>
                    </label>

                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                    <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Iniciar sesión', 'telconnect' ); ?>"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Iniciar sesión', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Iniciar sesión', 'telconnect' ); ?></span></span></button>

                    <p class="woocommerce-LostPassword lost_password">
                        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( '¿Olvidaste tu contraseña?', 'telconnect' ); ?></a>
                    </p>

                    <?php do_action( 'woocommerce_login_form_end' ); ?>

                </form>
            </div>

            <?php if ( $acc_registration_enabled ) : ?>

                <div class="acc-auth-card">
                    <h2><?php esc_html_e( 'Crear cuenta', 'telconnect' ); ?></h2>

                    <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

                        <?php do_action( 'woocommerce_register_form_start' ); ?>

                        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                <label for="reg_username"><?php esc_html_e( 'Usuario', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
                            </p>
                        <?php endif; ?>

                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_email"><?php esc_html_e( 'Email', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                            <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
                        </p>

                        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                <label for="reg_password"><?php esc_html_e( 'Contraseña', 'telconnect' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                                <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
                            </p>
                        <?php else : ?>
                            <p><?php esc_html_e( 'Te enviaremos un link a tu email para crear una contraseña.', 'telconnect' ); ?></p>
                        <?php endif; ?>

                        <?php do_action( 'woocommerce_register_form' ); ?>

                        <p class="woocommerce-form-row form-row">
                            <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                            <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Crear cuenta', 'telconnect' ); ?>"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Crear cuenta', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Crear cuenta', 'telconnect' ); ?></span></span></button>
                        </p>

                        <?php do_action( 'woocommerce_register_form_end' ); ?>

                    </form>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
