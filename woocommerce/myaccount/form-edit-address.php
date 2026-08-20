<?php
/**
 * Override de yourtheme/woocommerce/myaccount/form-edit-address.php
 * Base: woocommerce/templates/myaccount/form-edit-address.php (v9.3.0)
 *
 * Antes NO se overrideaba (§8.5 — "restylear vía CSS, no overridear").
 * Se overridea ahora, mínimamente (todos los campos/hooks/nonce del core
 * intactos, incluido el wc_get_template('myaccount/my-address.php') para
 * el caso sin $load_address), únicamente para envolver el texto del
 * botón "Save address" en el markup del roll de texto (.btn-label-mask +
 * .btn-label + .btn-label-ghost) — la duplicación de texto no se puede
 * lograr solo con CSS. Único cambio real respecto al core: el <button>
 * final.
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<form method="post" novalidate>

		<h2><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?></h2><?php // @codingStandardsIgnoreLine ?>

		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

			<div class="woocommerce-address-fields__field-wrapper">
				<?php
				foreach ( $address as $key => $field ) {
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				}
				?>
			</div>

			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

			<p>
				<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>">
					<span class="btn-label-mask">
						<span class="btn-label"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></span>
						<span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></span>
					</span>
				</button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</p>
		</div>

	</form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
