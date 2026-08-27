<?php
/**
 * Override de yourtheme/woocommerce/cart/cart.php
 * Base: woocommerce/templates/cart/cart.php (v11.0.0)
 *
 * v2 — rediseño completo contra Figma real (recursos/carrito/), mismo
 * criterio que la PDP v2 (§8.4b de CONTEXT.md): el layout de Figma es
 * un listado de cards, no la tabla <table> del core, así que se
 * abandona el markup <table>/<tr>/<td> y se construye a mano — pero
 * se preservan los filtros de WC (woocommerce_cart_item_*, etc.) donde
 * tiene sentido llamarlos igual, solo que envueltos en <div> en vez de
 * <td>. Se verificó (grep sobre wp-content/plugins) que ningún plugin
 * activo, incluido el gateway TUU, engancha nada a los hooks que se
 * dejan de usar acá (woocommerce_after_cart_item_name, etc.) más allá
 * de lo que ya hacía el propio WooCommerce core.
 *
 * Addon "Firma electrónica" (ver tc_maybe_add_signature_addon() en
 * functions.php): es un producto normal e independiente del carrito,
 * sin relación con el producto que lo originó — se renderiza con el
 * mismo loop que cualquier otro ítem, su propia fila.
 *
 * Nota sobre `woocommerce_cart_collaterals`: el core registra
 * `woocommerce_cross_sell_display` y `woocommerce_cart_totals` como
 * dos callbacks separados sobre ese mismo hook (wc-template-hooks.php).
 * Se verificó que ningún plugin activo engancha nada más ahí, así que
 * se llaman ambas funciones por separado — permite poner cross-sells
 * en la columna de items y el total en la columna sticky.
 *
 * Nota sobre `woocommerce_before_cart`: se llama DENTRO de
 * .crt-container (después del header) para que el aviso de "producto
 * agregado"/errores no quede fuera de la shell de la página.
 */

defined( 'ABSPATH' ) || exit;

$crt_cart_items     = WC()->cart->get_cart();
$crt_product_count  = count( $crt_cart_items );
?>

<div class="crt-page">
    <div class="crt-container tc-container">

        <div class="crt-header">
            <div class="crt-title-row">
                <h1 class="crt-title"><?php esc_html_e( 'Tu carrito', 'telconnect' ); ?></h1>
                <span class="crt-count">
                    <?php
                    /* translators: %d es la cantidad de productos top-level (sin contar addons anidados) */
                    echo esc_html( sprintf( _n( '%d producto', '%d productos', $crt_product_count, 'telconnect' ), $crt_product_count ) );
                    ?>
                </span>
            </div>

            <?php
            /**
             * Stepper de 3 pasos — puramente decorativo por ahora (sin
             * lógica de multi-step real). El checkout sigue siendo una
             * sola página ([woocommerce_checkout], §8.1), así que "Datos"
             * y "Pago" no son rutas separadas todavía — es solo el
             * indicador visual que pide el Figma. Decisión documentada
             * en CONTEXT.md §8.6/§8.7: no repartir el checkout en 2 pasos
             * reales para esto.
             */
            ?>
            <div class="crt-steps" aria-hidden="true">
                <div class="crt-step crt-step--active">
                    <span class="crt-step-num">1</span>
                    <span class="crt-step-label"><?php esc_html_e( 'Carrito', 'telconnect' ); ?></span>
                </div>
                <span class="crt-step-sep"></span>
                <div class="crt-step">
                    <span class="crt-step-num">2</span>
                    <span class="crt-step-label"><?php esc_html_e( 'Datos', 'telconnect' ); ?></span>
                </div>
                <span class="crt-step-sep"></span>
                <div class="crt-step">
                    <span class="crt-step-num">3</span>
                    <span class="crt-step-label"><?php esc_html_e( 'Pago', 'telconnect' ); ?></span>
                </div>
            </div>
        </div>

        <?php do_action( 'woocommerce_before_cart' ); ?>

        <form class="woocommerce-cart-form crt-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <div class="crt-grid">
                <div class="crt-items-col">

                    <div class="crt-items-card">
                        <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                        <?php
                        foreach ( $crt_cart_items as $cart_item_key => $cart_item ) {
                            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                            $visible    = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

                            if ( ! ( $_product instanceof WC_Product ) || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! $visible ) {
                                continue;
                            }

                            $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            <div class="crt-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                <div class="crt-item-photo">
                                    <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                    if ( ! $product_permalink ) {
                                        echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    } else {
                                        printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    }
                                    ?>
                                </div>

                                <div class="crt-item-info">
                                    <div class="crt-item-name">
                                        <?php
                                        if ( ! $product_permalink ) {
                                            echo wp_kses_post( $product_name );
                                        } else {
                                            echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $product_name ) );
                                        }
                                        ?>
                                    </div>

                                    <?php if ( $_product->get_short_description() ) : ?>
                                        <p class="crt-item-desc"><?php echo wp_kses_post( $_product->get_short_description() ); ?></p>
                                    <?php endif; ?>

                                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

                                    <?php if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) : ?>
                                        <p class="backorder_notification"><?php esc_html_e( 'Available on backorder', 'woocommerce' ); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="crt-item-control">
                                    <div class="crt-item-price">
                                        <span class="crt-item-price-amount"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                                        <span class="crt-item-price-iva"><?php esc_html_e( '+ IVA', 'telconnect' ); ?></span>
                                    </div>

                                    <?php
                                    /**
                                     * Nota firma electrónica: NO se fuerza min=max=1 acá aunque la
                                     * regla de negocio sea "máx. 1 firma en el carrito". WC renderiza
                                     * el input como type="hidden" (sin valor visible) apenas
                                     * min_value === max_value (ver wc_get_quantity_input_args() en
                                     * woocommerce/includes/wc-template-functions.php) — eso dejaba el
                                     * stepper en blanco y bloqueaba el "+" en silencio, sin aviso. Se
                                     * deja la cantidad como un producto normal (visible, editable) y
                                     * el tope real de 1 lo aplica tc_validate_signature_addon_cart_quantity()
                                     * en functions.php: al intentar subir a 2+, el auto-submit de
                                     * cart.js dispara el update, el filtro lo rechaza y se muestra el
                                     * aviso "Solo puedes comprar una firma electrónica...".
                                     */
                                    if ( $_product->is_sold_individually() ) {
                                        $min_quantity = 1;
                                        $max_quantity = 1;
                                    } else {
                                        $min_quantity = 0;
                                        $max_quantity = $_product->get_max_purchase_quantity();
                                    }

                                    $product_quantity = woocommerce_quantity_input(
                                        array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $max_quantity,
                                            'min_value'    => $min_quantity,
                                            'product_name' => $product_name,
                                        ),
                                        $_product,
                                        false
                                    );
                                    ?>
                                    <div class="crt-qty-stepper">
                                        <button type="button" class="crt-qty-btn crt-qty-minus" aria-label="<?php esc_attr_e( 'Restar', 'telconnect' ); ?>"><span></span></button>
                                        <?php echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        <button type="button" class="crt-qty-btn crt-qty-plus" aria-label="<?php esc_attr_e( 'Sumar', 'telconnect' ); ?>"><span></span></button>
                                    </div>

                                    <?php
                                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        'woocommerce_cart_item_remove_link',
                                        sprintf(
                                            '<a role="button" href="%s" class="crt-item-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><img src="%s" alt=""><span class="btn-label-mask"><span class="btn-label">%s</span><span class="btn-label-ghost" aria-hidden="true">%s</span></span></a>',
                                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                            /* translators: %s is the product name */
                                            esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                                            esc_attr( $product_id ),
                                            esc_attr( $_product->get_sku() ),
                                            esc_url( get_template_directory_uri() . '/assets/img/icons/trash-01.svg' ),
                                            esc_html__( 'Eliminar', 'telconnect' ),
                                            esc_html__( 'Eliminar', 'telconnect' )
                                        ),
                                        $cart_item_key
                                    );
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <?php do_action( 'woocommerce_cart_contents' ); ?>
                        <?php do_action( 'woocommerce_after_cart_contents' ); ?>

                        <?php if ( wc_coupons_enabled() ) : ?>
                            <details class="crt-coupon-toggle">
                                <summary><?php esc_html_e( '¿Tienes un cupón de descuento?', 'telconnect' ); ?></summary>
                                <div class="crt-coupon-panel">
                                    <div class="crt-coupon-inner">
                                        <div class="coupon">
                                            <label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
                                            <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Código de cupón', 'telconnect' ); ?>" />
                                            <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Aplicar cupón', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Aplicar cupón', 'telconnect' ); ?></span></span></button>
                                            <?php do_action( 'woocommerce_cart_coupon' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        <?php endif; ?>

                        <?php // Oculto: solo existe como submitter para el auto-submit de cart.js (ver docblock del archivo). ?>
                        <button type="submit" class="crt-visually-hidden" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Actualizar carrito', 'telconnect' ); ?></button>

                        <?php do_action( 'woocommerce_cart_actions' ); ?>
                        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                    </div>

                    <?php do_action( 'woocommerce_after_cart_table' ); ?>

                    <?php // Ver nota en el docblock: se llama directo en vez de vía do_action('woocommerce_cart_collaterals') combinado. ?>
                    <?php woocommerce_cross_sell_display(); ?>

                    <div class="crt-help-banner">
                        <div class="crt-help-copy">
                            <strong><?php esc_html_e( '¿Tienes dudas antes de comprar?', 'telconnect' ); ?></strong>
                            <p><?php esc_html_e( 'Escríbenos y habla con nosotros y te ayudamos a elegir la máquina.', 'telconnect' ); ?></p>
                        </div>
                        <a href="<?php echo tc_whatsapp_url(); ?>" target="_blank" rel="noopener" class="crt-help-btn"><span class="btn-label-mask"><span class="btn-label"><?php esc_html_e( 'Escribir a ventas', 'telconnect' ); ?></span><span class="btn-label-ghost" aria-hidden="true"><?php esc_html_e( 'Escribir a ventas', 'telconnect' ); ?></span></span></a>
                    </div>
                </div>

                <div class="crt-summary-col">
                    <div class="crt-summary-card">
                        <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
                        <?php woocommerce_cart_totals(); ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
