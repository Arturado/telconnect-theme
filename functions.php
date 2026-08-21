<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Centralización de contacto — cambiar acá, se propaga a todo el sitio
define( 'TC_WHATSAPP_NUMBER', '56900000000' ); // TODO: reemplazar con el número real del cliente

// Destino de las notificaciones por email del modal "Solicita tu prueba"
// de Parking (tc_ajax_submit_trial_request(), más abajo). Email temporal
// del usuario — TODO: reemplazar por el email real del cliente cuando lo
// confirme. El respaldo real de cada solicitud NO depende de este email,
// queda guardado en el plugin "Telconnect - Solicitudes Prueba" aunque el
// envío falle (ver wp-content/plugins/telconnect-solicitudes-prueba/).
define( 'TC_TRIAL_REQUEST_EMAIL', 'hola@arturodev.info' );


/**
 * Devuelve la URL de WhatsApp lista para usar en href.
 * Uso: <a href="<?php echo tc_whatsapp_url(); ?>">...</a>
 * Con mensaje precargado: tc_whatsapp_url('Hola, quiero cotizar una máquina')
 */
function tc_whatsapp_url( $message = '' ) {
    $url = 'https://wa.me/' . TC_WHATSAPP_NUMBER;
    if ( $message ) {
        $url .= '?text=' . rawurlencode( $message );
    }
    return esc_url( $url );
}

/**
 * Cache-busting: usa filemtime() del archivo real como versión de
 * wp_enqueue_style()/wp_enqueue_script(), en vez de un número fijo
 * ('1.0.0') que nunca cambiaba — eso hacía que los navegadores (y a
 * veces el hosting) cachearan indefinidamente la misma URL+versión
 * aunque el archivo en el servidor hubiera cambiado, generando
 * confusión real más de una vez ("¿es bug de código o es caché?").
 * La versión ahora cambia sola cada vez que se edita el archivo, sin
 * depender de acordarse de bumpear un número a mano ni de que el
 * usuario haga hard refresh.
 *
 * $relative_path va desde la raíz del theme, con "/" inicial (ej.
 * '/assets/css/main.css' o '/style.css'). Si el archivo no existe en
 * la ruta esperada, filemtime() tira un warning y devuelve false — se
 * valida con file_exists() antes y se cae a un fallback fijo, para que
 * un archivo faltante no rompa el enqueue de todo el resto de assets
 * del sitio (mismo criterio defensivo de "no romper todo por 1 asset"
 * ya usado en otras partes del theme).
 */
function tc_asset_version( $relative_path ) {
    $full_path = get_template_directory() . $relative_path;
    return file_exists( $full_path ) ? filemtime( $full_path ) : '1.0.0';
}

function telconnect_enqueue_fonts() {
    wp_enqueue_style( 'telconnect-fonts', 'https://fonts.googleapis.com/css2?family=Stack+Sans+Headline:wght@200..700&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_fonts' );

function telconnect_enqueue_scripts() {
    wp_enqueue_script(
        'telconnect-devices-carousel',
        get_template_directory_uri() . '/assets/js/devices-carousel.js',
        array(),
        tc_asset_version( '/assets/js/devices-carousel.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_scripts' );

function telconnect_enqueue_ecosystem_js() {
    wp_enqueue_script(
        'telconnect-ecosystem',
        get_template_directory_uri() . '/assets/js/ecosystem.js',
        array(),
        tc_asset_version( '/assets/js/ecosystem.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_ecosystem_js' );

/**
 * ============================================================
 * Addon "Firma electrónica" en la PDP
 * ============================================================
 * Lista los productos de la categoría 'firma-electronica' para
 * mostrarlos como opción adicional en la ficha de producto.
 * Crear esa categoría en Productos > Categorías y asignarle los
 * productos correspondientes — aparecen acá automáticamente,
 * sin tocar código.
 */
function tc_get_signature_addon_products() {
    $products = wc_get_products( array(
        'category' => array( 'firma-electronica' ),
        'status'   => 'publish',
        'limit'    => -1,
        'orderby'  => 'price',
        'order'    => 'ASC',
    ) );

    $addons = array();
    foreach ( $products as $product ) {
        $addons[] = array(
            'id'    => $product->get_id(),
            'name'  => $product->get_name(),
            'price' => (float) $product->get_price(),
        );
    }

    return $addons;
}

/**
 * Si ESTE producto específico debe mostrar el bloque "Emite boleta
 * electrónica" en su propia PDP — ver metabox "Firma electrónica" más
 * abajo. Antes el bloque aparecía en la PDP de todos los productos con
 * solo confirmar que existieran productos reales en la categoría
 * 'firma-electronica'; el cliente pidió controlarlo producto por
 * producto (no todas las máquinas la ofrecen, y no se asume que los
 * complementos nunca la van a ofrecer tampoco).
 *
 * Default para productos que nunca configuraron este meta: NO se
 * muestra (opt-in explícito) — más seguro que mostrarlo por default en
 * todo lo ya publicado y obligar al cliente a desactivarlo uno por uno
 * donde no corresponde.
 */
function tc_product_offers_signature_addon( $product_id ) {
    return 'yes' === get_post_meta( $product_id, '_tc_offers_signature_addon', true );
}

/**
 * ============================================================
 * Metabox "Firma electrónica" en productos
 * ============================================================
 * Checkbox simple, mismo patrón sin dependencias que el metabox de
 * _dp_features más abajo. Guarda '_tc_offers_signature_addon' ('yes'/'no').
 */
function tc_add_signature_addon_metabox() {
    add_meta_box(
        'tc_signature_addon',
        'Firma electrónica',
        'tc_render_signature_addon_metabox',
        'product',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'tc_add_signature_addon_metabox' );

function tc_render_signature_addon_metabox( $post ) {
    wp_nonce_field( 'tc_save_signature_addon', 'tc_signature_addon_nonce' );

    $enabled = tc_product_offers_signature_addon( $post->ID );
    ?>
    <p>
        <label>
            <input type="checkbox" name="tc_offers_signature_addon" value="yes" <?php checked( $enabled ); ?>>
            ¿Este producto ofrece la opción de agregar firma electrónica?
        </label>
    </p>
    <p class="description">
        Si está marcado, la ficha de este producto muestra el bloque
        "Emite boleta electrónica" (siempre que además existan productos
        reales publicados en la categoría "firma-electronica").
    </p>
    <?php
}

function tc_save_signature_addon_metabox( $post_id ) {
    if ( ! isset( $_POST['tc_signature_addon_nonce'] ) || ! wp_verify_nonce( $_POST['tc_signature_addon_nonce'], 'tc_save_signature_addon' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_product', $post_id ) ) {
        return;
    }

    $enabled = isset( $_POST['tc_offers_signature_addon'] ) && 'yes' === $_POST['tc_offers_signature_addon'];
    update_post_meta( $post_id, '_tc_offers_signature_addon', $enabled ? 'yes' : 'no' );
}
add_action( 'save_post_product', 'tc_save_signature_addon_metabox' );

/**
 * Regla de negocio: máximo 1 producto de la categoría 'firma-electronica'
 * por carrito, sin importar cuál (no pueden coexistir "Firma 1 año" y
 * "Firma 3 años"). Si el comprador ya tiene una y quiere otra distinta,
 * se bloquea el agregado — debe eliminar la que tiene primero, no se
 * reemplaza automáticamente.
 *
 * $product_id === el mismo producto ya en el carrito NO cuenta como
 * conflicto (permite que WooCommerce siga sumando cantidad de la misma
 * firma con su comportamiento nativo de merge por cart_id — la regla
 * es sobre no mezclar 2 firmas DISTINTAS, no sobre limitar cantidad).
 */
function tc_product_is_signature_addon( $product_id ) {
    return has_term( 'firma-electronica', 'product_cat', $product_id );
}

function tc_cart_has_conflicting_signature_addon( $product_id ) {
    if ( ! WC()->cart ) {
        return false;
    }

    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( (int) $cart_item['product_id'] === (int) $product_id ) {
            continue;
        }

        if ( tc_product_is_signature_addon( $cart_item['product_id'] ) ) {
            return true;
        }
    }

    return false;
}

function tc_signature_addon_conflict_message() {
    return __( 'Ya tienes una Firma electrónica distinta en tu carrito. Elimínala antes de agregar otra.', 'telconnect' );
}

/**
 * Camino 1: acceso directo a la PDP del propio producto de firma
 * electrónica (es un producto real de WooCommerce, navegable como
 * cualquier otro) y envío del form nativo de agregar al carrito. Ese
 * submit pasa por WC_Form_Handler::add_to_cart_action(), que sí aplica
 * este filtro — a diferencia del camino 2 (ver más abajo), que llama a
 * WC()->cart->add_to_cart() directo y por eso NO pasa por acá.
 */
function tc_validate_signature_addon_cart_limit( $passed, $product_id ) {
    if ( ! $passed ) {
        return $passed;
    }

    if ( tc_product_is_signature_addon( $product_id ) && tc_cart_has_conflicting_signature_addon( $product_id ) ) {
        wc_add_notice( tc_signature_addon_conflict_message(), 'error' );
        return false;
    }

    return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'tc_validate_signature_addon_cart_limit', 10, 2 );

/**
 * Camino 2 (el flujo normal): si el comprador eligió un addon de firma
 * electrónica en el <select> de la PDP de una máquina, lo agrega como
 * un segundo ítem al carrito en el mismo request que agrega el
 * producto principal.
 *
 * Este método llama a WC()->cart->add_to_cart() directo (no pasa por
 * WC_Form_Handler ni WC_AJAX), así que NO dispara el filtro
 * 'woocommerce_add_to_cart_validation' — WC_Cart::add_to_cart() no lo
 * aplica internamente, solo lo aplican esos 2 callers. Por eso acá hay
 * que repetir el mismo chequeo de tc_validate_signature_addon_cart_limit()
 * a mano, antes de la llamada directa.
 *
 * El addon se taguea con el cart item data 'tc_parent_cart_item_key'
 * (la key del producto principal recién agregado) para que el template
 * del carrito lo pueda detectar y renderizarlo anidado bajo su producto
 * padre en vez de como fila independiente — ver tc_get_cart_children_map()
 * y woocommerce/cart/cart.php. La key no se muestra en el carrito
 * (wc_get_formatted_cart_item_data() solo imprime lo que el filtro
 * woocommerce_get_item_data agregue explícitamente, así que un cart_item_data
 * custom sin ese filtro queda invisible por defecto).
 */
function tc_maybe_add_signature_addon( $cart_item_key, $product_id, $quantity ) {
    if ( empty( $_POST['tc_addon_signature'] ) ) {
        return;
    }

    $addon_id = absint( $_POST['tc_addon_signature'] );

    if ( ! $addon_id || $addon_id === $product_id ) {
        return;
    }

    $addon_product = wc_get_product( $addon_id );
    if ( ! $addon_product || ! $addon_product->is_purchasable() ) {
        return;
    }

    if ( tc_cart_has_conflicting_signature_addon( $addon_id ) ) {
        wc_add_notice( tc_signature_addon_conflict_message(), 'error' );
        return;
    }

    // Evita loop infinito: este hook no debe reaccionar a la propia
    // llamada de abajo que agrega el addon.
    remove_action( 'woocommerce_add_to_cart', 'tc_maybe_add_signature_addon', 10 );
    WC()->cart->add_to_cart(
        $addon_id,
        1,
        0,
        array(),
        array( 'tc_parent_cart_item_key' => $cart_item_key )
    );
    add_action( 'woocommerce_add_to_cart', 'tc_maybe_add_signature_addon', 10, 3 );
}
add_action( 'woocommerce_add_to_cart', 'tc_maybe_add_signature_addon', 10, 3 );

/**
 * Mapa parent_key => array de child cart_item_keys, para los addons de
 * firma electrónica anidados. Un item se considera "hijo" solo si su
 * 'tc_parent_cart_item_key' apunta a otro item que sigue existiendo en
 * el carrito — si el padre fue eliminado, el addon queda huérfano y se
 * trata como top-level (fallback razonable: WooCommerce no borra en
 * cascada los cart items relacionados, así que igual debe poder verse
 * y eliminarse por su cuenta).
 */
function tc_get_cart_children_map() {
    $cart = WC()->cart->get_cart();
    $map  = array();

    foreach ( $cart as $key => $item ) {
        if ( empty( $item['tc_parent_cart_item_key'] ) ) {
            continue;
        }
        $parent_key = $item['tc_parent_cart_item_key'];
        if ( ! isset( $cart[ $parent_key ] ) ) {
            continue; // padre eliminado: el addon se trata como top-level.
        }
        $map[ $parent_key ][] = $key;
    }

    return $map;
}

/**
 * Eliminación en cascada: si se elimina del carrito un cart item que
 * tiene un addon de firma electrónica vinculado (via
 * 'tc_parent_cart_item_key'), el addon se elimina en el mismo momento
 * — relación unidireccional, eliminar el hijo (el botón propio de
 * .crt-item-addon-remove en cart.php) nunca dispara esto para el padre.
 *
 * Se lee $cart->get_cart() (no tc_get_cart_children_map(), que ya
 * asume que el padre sigue en el carrito) porque en este momento el
 * padre YA fue eliminado de $cart->cart_contents (WC_Cart::remove_cart_item()
 * hace unset() antes de disparar este hook) — se busca directo cualquier
 * item cuyo tc_parent_cart_item_key sea la key que se acaba de eliminar.
 */
function tc_remove_signature_addon_children( $cart_item_key, $cart ) {
    foreach ( $cart->get_cart() as $key => $item ) {
        if ( ! empty( $item['tc_parent_cart_item_key'] ) && $item['tc_parent_cart_item_key'] === $cart_item_key ) {
            $cart->remove_cart_item( $key );
        }
    }
}
add_action( 'woocommerce_cart_item_removed', 'tc_remove_signature_addon_children', 10, 2 );

/**
 * Cart items "top-level" — excluye los addons de firma electrónica que
 * tienen un padre válido todavía presente en el carrito (esos se
 * renderizan anidados, no como fila propia). Es el mismo criterio que
 * define "N productos" en el header y "Productos (N)" del resumen.
 */
function tc_get_top_level_cart_items() {
    $cart       = WC()->cart->get_cart();
    $children   = tc_get_cart_children_map();
    $child_keys = array();
    foreach ( $children as $keys ) {
        $child_keys = array_merge( $child_keys, $keys );
    }

    $top_level = array();
    foreach ( $cart as $key => $item ) {
        if ( in_array( $key, $child_keys, true ) ) {
            continue;
        }
        $top_level[ $key ] = $item;
    }

    return $top_level;
}

/**
 * Desglose fiscal del pedido (Productos/Firma electrónica/Despacho/Neto/
 * IVA/Total) — extraído de cart-totals.php (v2, §8.7) para reusarlo tal
 * cual en review-order.php del checkout (wizard de 2 pasos, §8.8). Nada
 * de aritmética propia de precios: todo sale de WC()->cart / WC_Tax, así
 * que carrito y checkout siempre muestran el mismo cálculo real (con IVA
 * activado, ver §8.7) sin duplicar lógica entre los 2 templates.
 *
 * @return array {
 *     @type int         $products_count
 *     @type float        $products_subtotal
 *     @type string        $addon_label      Vacío si no hay addons en el carrito.
 *     @type float        $addon_subtotal
 *     @type string|null  $shipping_label    Null si no aplica envío.
 *     @type string|null  $shipping_html     HTML ya formateado ("Gratis" o wc_price()).
 *     @type float|null   $shipping_cost
 *     @type float        $net_total
 *     @type float        $total_tax
 *     @type string       $tax_label         Nombre + tasa real de WC_Tax::get_rates().
 *     @type float        $total
 * }
 */
function tc_get_order_summary_breakdown() {
    $top_level_items = tc_get_top_level_cart_items();
    $children_map     = tc_get_cart_children_map();
    $cart             = WC()->cart->get_cart();

    // ----- Productos (top-level, excluye addons anidados) -----
    $products_count    = count( $top_level_items );
    $products_subtotal = 0;
    foreach ( $top_level_items as $item ) {
        $products_subtotal += (float) $item['line_subtotal'];
    }

    // ----- Addons de firma electrónica (líneas hijas) -----
    $addon_keys = array();
    foreach ( $children_map as $keys ) {
        $addon_keys = array_merge( $addon_keys, $keys );
    }

    $addon_subtotal = 0;
    $addon_label    = '';
    if ( ! empty( $addon_keys ) ) {
        $addon_names = array();
        foreach ( $addon_keys as $addon_key ) {
            if ( ! isset( $cart[ $addon_key ] ) ) {
                continue;
            }
            $addon_subtotal += (float) $cart[ $addon_key ]['line_subtotal'];
            $addon_names[]   = $cart[ $addon_key ]['data']->get_name();
        }
        $addon_names = array_unique( $addon_names );
        // Un solo tipo de addon en el carrito (el caso normal): se usa su
        // nombre real de producto. Si hubiera más de un tipo, se agrupan
        // bajo una etiqueta genérica con el conteo, mismo criterio que
        // "Productos (N)".
        $addon_label = ( 1 === count( $addon_names ) )
            ? $addon_names[0]
            : sprintf(
                /* translators: %d es la cantidad de addons de firma electrónica distintos en el carrito */
                esc_html__( 'Firma electrónica (%d)', 'telconnect' ),
                count( $addon_names )
            );
    }

    // ----- Despacho: método real elegido/disponible, sin hardcodear -----
    $shipping_label     = null;
    $shipping_html      = null;
    $shipping_cost      = null;
    $shipping_method_id = null;
    $shipping_rate_id   = null; // ej. "free_shipping:3" — la key completa, no solo el method_id. Ver mirror en review-order.php/checkout.js.
    if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        foreach ( WC()->shipping()->get_packages() as $package_index => $package ) {
            if ( empty( $package['rates'] ) ) {
                continue;
            }
            $rate = ( isset( $chosen_methods[ $package_index ], $package['rates'][ $chosen_methods[ $package_index ] ] ) )
                ? $package['rates'][ $chosen_methods[ $package_index ] ]
                : reset( $package['rates'] );

            $shipping_label     = $rate->get_label();
            $shipping_cost      = (float) $rate->get_cost();
            $shipping_html      = $shipping_cost > 0 ? wc_price( $shipping_cost ) : esc_html__( 'Gratis', 'telconnect' );
            $shipping_method_id = $rate->get_method_id(); // ej. "local_pickup" o "flat_rate" — usado para saber si mostrar la dirección real o el local.
            $shipping_rate_id   = $rate->get_id();
            break; // Un solo package en este negocio (sin envíos a múltiples direcciones).
        }
    }

    // ----- Neto / IVA / Total: 100% de WC_Cart, nada calculado a mano -----
    $total     = (float) WC()->cart->get_total( 'edit' );
    $total_tax = (float) WC()->cart->get_total_tax();
    $net_total = $total - $total_tax;

    $tax_label = esc_html__( 'IVA', 'telconnect' );
    if ( $total_tax > 0 ) {
        $rates = WC_Tax::get_rates();
        $rate  = reset( $rates );
        if ( $rate ) {
            $tax_label = sprintf( '%s (%s%%)', $rate['label'], rtrim( rtrim( number_format( (float) $rate['rate'], 2 ), '0' ), '.' ) );
        }
    }

    return array(
        'products_count'     => $products_count,
        'products_subtotal'  => $products_subtotal,
        'addon_label'        => $addon_label,
        'addon_subtotal'     => $addon_subtotal,
        'shipping_label'     => $shipping_label,
        'shipping_html'      => $shipping_html,
        'shipping_cost'      => $shipping_cost,
        'shipping_method_id' => $shipping_method_id,
        'shipping_rate_id'   => $shipping_rate_id,
        'net_total'          => $net_total,
        'total_tax'          => $total_tax,
        'tax_label'          => $tax_label,
        'total'              => $total,
    );
}

/**
 * Datos reales del PEDIDO para la página de gracias (thankyou.php v2,
 * rediseño pixel-a-pixel contra recursos/thankyou/). A diferencia de
 * tc_get_order_summary_breakdown() (que lee WC()->cart — sirve para
 * carrito/checkout ANTES de pagar), acá el carrito ya está vacío en el
 * momento en que thankyou.php se renderiza: todo sale del $order ya
 * creado, no de la sesión.
 *
 * @param WC_Order $order
 * @return array {
 *     @type array  $items               Lista de líneas del pedido: name/quantity/price_html/image_html.
 *     @type string $total_html          wc_price() del total pagado.
 *     @type string $payment_title       Título del método de pago usado.
 *     @type bool   $is_pickup           true si el método de envío es Local pickup.
 *     @type string $shipping_value      Texto para la línea "Despacho" del resumen (dirección real o nombre del método).
 *     @type string $shipping_step_text  Texto para el paso "Despachamos tu máquina" de "Qué sigue ahora".
 * }
 */
function tc_get_thankyou_order_summary( $order ) {
    $items = array();
    foreach ( $order->get_items() as $item ) {
        $product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : false;
        $items[] = array(
            'name'       => $item->get_name(),
            'quantity'   => $item->get_quantity(),
            'price_html' => wc_price( $order->get_line_total( $item, false ) ),
            'image_html' => $product ? $product->get_image( 'thumbnail' ) : wc_placeholder_img( 'thumbnail' ),
        );
    }

    // Mismo criterio que review-order.php (§8.8/§8.10): "local_pickup" en
    // el method_id decide si se muestra la dirección real o el nombre del
    // método de retiro.
    $is_pickup = false;
    foreach ( $order->get_shipping_methods() as $shipping_item ) {
        $is_pickup = false !== strpos( $shipping_item->get_method_id(), 'local_pickup' );
        break; // Un solo package en este negocio, ver review-order.php.
    }

    // _shipping_comuna: post meta custom del checkout (§8.8), no una prop
    // nativa de WC_Order — no se inyecta en get_shipping_address_1/2().
    $comuna       = get_post_meta( $order->get_id(), '_shipping_comuna', true );
    $addr_parts   = array_filter( array( $comuna, $order->get_shipping_address_1(), $order->get_shipping_address_2() ) );
    $full_address = implode( ', ', $addr_parts );

    if ( $is_pickup ) {
        $shipping_value     = $order->get_shipping_method() ? $order->get_shipping_method() : __( 'Retiro en tienda', 'telconnect' );
        // Dirección real de la tienda, la misma de template-parts/tienda-fisica.php — no inventada.
        $shipping_step_text = __( 'Retira tu equipo en Vicuña Mackenna poniente 6843, oficina 805, La Florida, cuando te avisemos por correo que está listo.', 'telconnect' );
    } else {
        $shipping_value     = $full_address ? $full_address : $order->get_shipping_method();
        $shipping_step_text = $full_address
            /* translators: %s es la dirección de despacho (comuna, calle y número) */
            ? sprintf( __( 'Llega en 48 h hábiles a %s.', 'telconnect' ), $full_address )
            : __( 'Llega en 48 h hábiles a la dirección de despacho indicada.', 'telconnect' );
    }

    return array(
        'items'              => $items,
        'total_html'         => $order->get_formatted_order_total(),
        'payment_title'      => $order->get_payment_method_title() ? $order->get_payment_method_title() : __( 'No especificado', 'telconnect' ),
        'is_pickup'          => $is_pickup,
        'shipping_value'     => $shipping_value,
        'shipping_step_text' => $shipping_step_text,
    );
}

/**
 * "Proceed to checkout" -> "Ir a pagar" (texto exacto del Figma del
 * carrito). No se overridea cart/proceed-to-checkout-button.php (mismo
 * criterio del §8.2: restylear/retextear sin tocar el template) — el
 * string original en inglés es estable entre locales, a diferencia de
 * comparar contra la traducción ya aplicada.
 */
function tc_translate_proceed_to_checkout_text( $translated, $original, $domain ) {
    if ( 'woocommerce' === $domain && 'Proceed to checkout' === $original ) {
        return __( 'Ir a pagar', 'telconnect' );
    }
    return $translated;
}
add_filter( 'gettext', 'tc_translate_proceed_to_checkout_text', 10, 3 );

/**
 * Checkout wizard (§8.8): la card "Despacho" reemplaza el concepto de
 * "¿Enviar a una dirección distinta?" del core — acá SIEMPRE se
 * recolecta una dirección de envío propia (shipping_*), sea para
 * despacho a domicilio o, aunque no la use, para retiro en tienda.
 * Se fuerza el checkbox nativo a "marcado" (así el core renderiza
 * .shipping_address con los campos) y se lo oculta vía CSS
 * (#ship-to-different-address) — no tiene sentido mostrarlo, no hay
 * ninguna "dirección de facturación" que ofrecer como alternativa (la
 * card de billing ya no pide dirección, ver
 * tc_reorder_billing_fields_for_checkout_wizard()).
 */
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );

/**
 * Retextea la descripción del gateway TUU para que calce con el Figma
 * del paso Pago ("Pago inmediato. Puedes pagar en cuotas.") — el string
 * original ("Paga con tarjetas de débito, crédito y prepago.") está
 * hardcodeado en el constructor de WCPluginGateway (plugin de terceros,
 * no se toca su código). $gateway->description es una property pública
 * de WC_Payment_Gateway pensada para poder overridearse así.
 */
function tc_retext_tuu_gateway_description( $gateways ) {
    if ( isset( $gateways['wcplugingateway'] ) ) {
        $gateways['wcplugingateway']->description = __( 'Pago inmediato. Puedes pagar en cuotas.', 'telconnect' );
    }
    return $gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'tc_retext_tuu_gateway_description' );

// Soporte básico del theme
function telconnect_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    register_nav_menus( array(
        'primary' => __( 'Menú Principal', 'telconnect' ),
        'footer'  => __( 'Menú Footer', 'telconnect' ),
    ) );
}
add_action( 'after_setup_theme', 'telconnect_setup' );

// Estilos y scripts
function telconnect_enqueue_assets() {
    wp_enqueue_style( 'telconnect-style', get_stylesheet_uri(), array(), tc_asset_version( '/style.css' ) );
    wp_enqueue_style( 'telconnect-main', get_template_directory_uri() . '/assets/css/main.css', array(), tc_asset_version( '/assets/css/main.css' ) );

    $tc_sections = array(
        'header', 'hero', 'partner-difference', 'devices-products',
        'commission', 'devices-complementos', 'ecosystem', 'parking-teaser',
        'social-proof', 'testimonials', 'venta-soporte', 'tienda-fisica',
        'faq', 'final-cta', 'footer',
    );

    foreach ( $tc_sections as $section ) {
        wp_enqueue_style(
            'telconnect-' . $section,
            get_template_directory_uri() . '/assets/css/' . $section . '.css',
            array( 'telconnect-main' ),
            tc_asset_version( '/assets/css/' . $section . '.css' )
        );
    }

    // Toggle del menú hamburguesa mobile — el header (con su overlay) se
    // imprime en TODAS las páginas vía header.php, no solo la home.
    wp_enqueue_script(
        'telconnect-header',
        get_template_directory_uri() . '/assets/js/header.js',
        array(),
        tc_asset_version( '/assets/js/header.js' ),
        true
    );

    // Orquesta el timing del cierre animado de TODO <details> del sitio
    // (FAQ Home/Parking, Funcionalidades, PDP, cupón del carrito) — ver
    // docblock de accordion.js. Genérico y global por la misma razón que
    // telconnect-header: no hay forma barata de saber de antemano en qué
    // páginas hay un <details>.
    wp_enqueue_script(
        'telconnect-accordion',
        get_template_directory_uri() . '/assets/js/accordion.js',
        array(),
        tc_asset_version( '/assets/js/accordion.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_assets' );

// --- Soporte explícito para Elementor ---
function telconnect_elementor_support() {
    add_theme_support( 'elementor' );
    // Elimina el margen/padding default del theme en el contenido para que Elementor tenga control full-width
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'telconnect_elementor_support' );

// Registrar ubicaciones de "Elementor Theme Locations" (header/footer vía Elementor si usan Elementor Pro más adelante, y deja el hook listo)
function telconnect_register_elementor_locations( $elementor_theme_manager ) {
    $elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'telconnect_register_elementor_locations' );

// Content width para embeds/imágenes
if ( ! isset( $content_width ) ) {
    $content_width = 1200;
}

// Assets del checkout (solo se cargan en esa página, a diferencia de las secciones de la landing)
function telconnect_enqueue_checkout_assets() {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
        return;
    }

    wp_enqueue_style(
        'telconnect-checkout',
        get_template_directory_uri() . '/assets/css/checkout.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/checkout.css' )
    );

    // Depende de jquery (§8.8): el wizard escucha el evento
    // "updated_checkout" que WooCommerce dispara vía jQuery.trigger()
    // tras cada actualización AJAX de totales — un addEventListener()
    // nativo no lo captura, hace falta jQuery(...).on(...).
    wp_enqueue_script(
        'telconnect-checkout',
        get_template_directory_uri() . '/assets/js/checkout.js',
        array( 'jquery' ),
        tc_asset_version( '/assets/js/checkout.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_checkout_assets' );

// Assets del carrito
function telconnect_enqueue_cart_assets() {
    if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
        return;
    }

    // Los cross-sells reusan .dp-card/.plp-grid (mismo criterio que el PDP con relacionados).
    wp_enqueue_style(
        'telconnect-plp',
        get_template_directory_uri() . '/assets/css/plp.css',
        array( 'telconnect-main', 'telconnect-devices-products' ),
        tc_asset_version( '/assets/css/plp.css' )
    );

    wp_enqueue_style(
        'telconnect-cart',
        get_template_directory_uri() . '/assets/css/cart.css',
        array( 'telconnect-main', 'telconnect-plp' ),
        tc_asset_version( '/assets/css/cart.css' )
    );

    wp_enqueue_script(
        'telconnect-cart',
        get_template_directory_uri() . '/assets/js/cart.js',
        array(),
        tc_asset_version( '/assets/js/cart.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_cart_assets' );

// Assets del PLP (tienda / categorías de producto)
function telconnect_enqueue_plp_assets() {
    if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() ) ) {
        return;
    }

    // Depende de telconnect-devices-products porque reusa .dp-card (definida ahí) como base.
    wp_enqueue_style(
        'telconnect-plp',
        get_template_directory_uri() . '/assets/css/plp.css',
        array( 'telconnect-main', 'telconnect-devices-products' ),
        tc_asset_version( '/assets/css/plp.css' )
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_plp_assets' );

// Assets del PDP (ficha de producto individual)
function telconnect_enqueue_pdp_assets() {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) {
        return;
    }

    // El PDP también usa .dp-card / .plp-card (relacionados) y .plp-grid.
    wp_enqueue_style(
        'telconnect-plp',
        get_template_directory_uri() . '/assets/css/plp.css',
        array( 'telconnect-main', 'telconnect-devices-products' ),
        tc_asset_version( '/assets/css/plp.css' )
    );

    wp_enqueue_style(
        'telconnect-pdp',
        get_template_directory_uri() . '/assets/css/pdp.css',
        array( 'telconnect-main', 'telconnect-plp' ),
        tc_asset_version( '/assets/css/pdp.css' )
    );

    // Bug preexistente (no de esta sesión): pdp.js (stepper +/- de cantidad)
    // nunca se enqueueaba — el archivo existía en assets/js/ pero ningún
    // wp_enqueue_script lo cargaba, así que los botones +/- quedaban sin
    // listener alguno pese a que el markup/CSS estaban correctos.
    wp_enqueue_script(
        'telconnect-pdp',
        get_template_directory_uri() . '/assets/js/pdp.js',
        array(),
        tc_asset_version( '/assets/js/pdp.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_pdp_assets' );

// Assets de Mi Cuenta (dashboard, pedidos, direcciones, login/registro, etc.)
function telconnect_enqueue_account_assets() {
    if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }

    wp_enqueue_style(
        'telconnect-account',
        get_template_directory_uri() . '/assets/css/account.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/account.css' )
    );

    // Reusa checkout.js sin cambios: el formulario "Editar dirección de
    // facturación" de Mi Cuenta usa los mismos IDs de campo
    // (billing_document_type, billing_rut) que el checkout, así que el
    // toggle de Factura y la validación de RUT funcionan igual acá.
    // Depende de jquery (§8.8): el wizard escucha el evento
    // "updated_checkout" que WooCommerce dispara vía jQuery.trigger()
    // tras cada actualización AJAX de totales — un addEventListener()
    // nativo no lo captura, hace falta jQuery(...).on(...).
    wp_enqueue_script(
        'telconnect-checkout',
        get_template_directory_uri() . '/assets/js/checkout.js',
        array( 'jquery' ),
        tc_asset_version( '/assets/js/checkout.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_account_assets' );


// Assets de la landing de Telconnect Parking (Hero propio, distinto al de la home)
function telconnect_enqueue_parking_assets() {
    if ( ! is_page_template( 'page-parking.php' ) ) {
        return;
    }

    wp_enqueue_style(
        'telconnect-hero-parking',
        get_template_directory_uri() . '/assets/css/hero-parking.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/hero-parking.css' )
    );

    wp_enqueue_style(
        'telconnect-problema',
        get_template_directory_uri() . '/assets/css/problema.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/problema.css' )
    );
    wp_enqueue_style(
        'telconnect-funcionalidades',
        get_template_directory_uri() . '/assets/css/funcionalidades.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/funcionalidades.css' )
    );

    wp_enqueue_style(
        'telconnect-admin-remota',
        get_template_directory_uri() . '/assets/css/admin-remota.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/admin-remota.css' )
    );

    wp_enqueue_style(
        'telconnect-planes',
        get_template_directory_uri() . '/assets/css/planes.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/planes.css' )
    );

    wp_enqueue_style(
        'telconnect-como-empezar',
        get_template_directory_uri() . '/assets/css/como-empezar.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/como-empezar.css' )
    );

    wp_enqueue_style(
        'telconnect-faq-parking',
        get_template_directory_uri() . '/assets/css/faq-parking.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/faq-parking.css' )
    );

    wp_enqueue_style(
        'telconnect-final-cta-parking',
        get_template_directory_uri() . '/assets/css/final-cta-parking.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/final-cta-parking.css' )
    );

    // Modal "Solicita tu prueba" — compartido por los 3 botones de la
    // landing (Hero, Cómo Empezar, Planes). Ver template-parts/modal-prueba-parking.php.
    wp_enqueue_style(
        'telconnect-pmodal',
        get_template_directory_uri() . '/assets/css/pmodal.css',
        array( 'telconnect-main' ),
        tc_asset_version( '/assets/css/pmodal.css' )
    );

    wp_enqueue_script(
        'telconnect-pmodal',
        get_template_directory_uri() . '/assets/js/pmodal.js',
        array(),
        tc_asset_version( '/assets/js/pmodal.js' ),
        true
    );

    wp_localize_script(
        'telconnect-pmodal',
        'tcPmodal',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'tc_trial_request' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_parking_assets' );

/**
 * ============================================================
 * AJAX — Modal "Solicita tu prueba" (Parking)
 * ============================================================
 * 1. Valida (requeridos + RUT vía la misma WoocommercePlugin\helpers\RutValidator
 *    que ya usa el checkout, §8.1 — no se reinventa el algoritmo).
 * 2. Persiste vía tc_solicitudes_prueba_guardar() (plugin standalone,
 *    wp-content/plugins/telconnect-solicitudes-prueba/) — ES el respaldo
 *    real, no depende de que el email se entregue.
 * 3. Envía notificación por wp_mail() a TC_TRIAL_REQUEST_EMAIL (arriba).
 *    Si el email falla (típico en local sin SMTP configurado) no se
 *    bloquea la respuesta de éxito al usuario, porque el dato ya quedó
 *    guardado en el paso 2 — se deja constancia en el log de PHP.
 */
function tc_ajax_submit_trial_request() {
    if ( ! check_ajax_referer( 'tc_trial_request', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Tu sesión expiró. Recarga la página e inténtalo de nuevo.' ), 400 );
    }

    $nombre        = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
    $rut           = isset( $_POST['rut'] ) ? sanitize_text_field( wp_unslash( $_POST['rut'] ) ) : '';
    $telefono      = isset( $_POST['telefono'] ) ? sanitize_text_field( wp_unslash( $_POST['telefono'] ) ) : '';
    $email         = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $tiene_maquina = ( isset( $_POST['tiene_maquina'] ) && 'no' === $_POST['tiene_maquina'] ) ? 'no' : 'si';
    $origen        = isset( $_POST['origen'] ) ? sanitize_text_field( wp_unslash( $_POST['origen'] ) ) : '';

    $errors = array();

    if ( '' === $nombre ) {
        $errors['nombre'] = 'Ingresa tu nombre y apellido.';
    }

    if ( '' === $rut ) {
        $errors['rut'] = 'Ingresa tu RUT.';
    } elseif ( class_exists( 'WoocommercePlugin\\helpers\\RutValidator' ) && ! \WoocommercePlugin\helpers\RutValidator::validate( $rut ) ) {
        $errors['rut'] = 'RUT inválido. Revisa el formato (ej: 12345678-9).';
    }

    if ( '' === $telefono ) {
        $errors['telefono'] = 'Ingresa tu número de teléfono.';
    }

    if ( '' === $email || ! is_email( $email ) ) {
        $errors['email'] = 'Ingresa un correo electrónico válido.';
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( array( 'errors' => $errors ), 422 );
    }

    $datos = array(
        'nombre'        => $nombre,
        'rut'           => $rut,
        'telefono'      => $telefono,
        'email'         => $email,
        'tiene_maquina' => $tiene_maquina,
        'origen'        => $origen,
    );

    if ( function_exists( 'tc_solicitudes_prueba_guardar' ) ) {
        $resultado = tc_solicitudes_prueba_guardar( $datos );
        if ( is_wp_error( $resultado ) ) {
            error_log( '[Telconnect Parking] tc_solicitudes_prueba_guardar() devolvió error: ' . $resultado->get_error_message() );
            wp_send_json_error( array( 'message' => 'No pudimos guardar tu solicitud. Escríbenos por WhatsApp mientras lo revisamos.' ), 500 );
        }
    } else {
        // El plugin de respaldo no está activo — no bloqueamos el envío del
        // correo por esto (el usuario igual debe poder solicitar la prueba),
        // pero queda constancia en el log para no perder el rastro del bug.
        error_log( '[Telconnect Parking] tc_solicitudes_prueba_guardar() no existe — activa el plugin "Telconnect - Solicitudes Prueba".' );
    }

    $subject = 'Nueva solicitud de prueba — Telconnect Parking';
    $body    = "Nombre: {$nombre}\n"
        . "RUT: {$rut}\n"
        . "Teléfono: {$telefono}\n"
        . "Email: {$email}\n"
        . '¿Tiene máquina TUU?: ' . ( 'si' === $tiene_maquina ? 'Sí' : 'No' ) . "\n"
        . 'Origen: ' . ( $origen ? $origen : '(no especificado)' ) . "\n";

    $mail_sent = wp_mail( TC_TRIAL_REQUEST_EMAIL, $subject, $body );

    // Log de diagnóstico — confirma qué se intentó enviar y si wp_mail() lo
    // aceptó. Útil en local sin SMTP configurado, donde wp_mail() puede
    // devolver false aunque los datos estén bien armados (no hay MTA).
    error_log(
        sprintf(
            '[Telconnect Parking] Solicitud de prueba de "%s" (%s) — wp_mail() a %s: %s',
            $nombre,
            $email,
            TC_TRIAL_REQUEST_EMAIL,
            $mail_sent ? 'OK' : 'FALLÓ'
        )
    );

    wp_send_json_success( array( 'message' => 'Solicitud enviada.' ) );
}
add_action( 'wp_ajax_tc_submit_trial_request', 'tc_ajax_submit_trial_request' );
add_action( 'wp_ajax_nopriv_tc_submit_trial_request', 'tc_ajax_submit_trial_request' );

/**
 * Header flotante en la home Y en la landing de Parking (ver header.css
 * .site-header). Ambos Heroes (front-page.php y hero-parking.php) tienen
 * su propia foto + overlay oscuro calibrados para que el header
 * transparente se vea bien superpuesto. Cualquier página nueva sin ese
 * tipo de fondo NO debe agregar esta clase — así el header se comporta
 * como uno normal (empuja el contenido) y no hace falta compensar con
 * padding-top a mano.
 */
function telconnect_body_classes( $classes ) {
    if ( is_front_page() || is_page_template( 'page-parking.php' ) ) {
        $classes[] = 'has-floating-header';
    }
    return $classes;
}
add_filter( 'body_class', 'telconnect_body_classes' );

// Ancho del wrapper de WooCommerce (evita que quede pegado a los bordes)
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
add_action( 'woocommerce_before_main_content', 'telconnect_wc_wrapper_start', 10 );
function telconnect_wc_wrapper_start() {
    echo '<div class="woocommerce-wrapper">';
}
add_action( 'woocommerce_after_main_content', 'telconnect_wc_wrapper_end', 10 );
function telconnect_wc_wrapper_end() {
    echo '</div>';
}
/**
 * ============================================================
 * Fixes PDP v2 — agregar a functions.php
 * ============================================================
 */

// Fix bug 1: quita el breadcrumb default de WooCommerce (duplicaba
// el breadcrumb custom .pdp-breadcrumb de content-single-product.php).
// Aplica en todo el sitio, no solo PDP — no queremos el breadcrumb
// nativo sin estilo en ningún lado ya que tenemos el nuestro donde
// hace falta.
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// Fix bug 2: quita la pestaña "Descripción" de los tabs nativos —
// esa descripción ya se muestra en el accordion "Información adicional"
// del lado izquierdo de la PDP. Deja "Valoraciones" y cualquier otra
// pestaña (atributos, etc) intacta.
function tc_remove_description_tab( $tabs ) {
    unset( $tabs['description'] );
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'tc_remove_description_tab', 98 );

/**
 * ============================================================
 * Metabox "Características (checklist landing)" en productos
 * ============================================================
 * Permite cargar desde el editor de WooCommerce el listado de
 * checks que se muestra en las cards de la home (Devices/Products).
 * Se guarda en el custom field '_dp_features', un ítem por línea.
 */

function tc_add_features_metabox() {
    add_meta_box(
        'tc_dp_features',
        'Características (checklist landing)',
        'tc_render_features_metabox',
        'product',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'tc_add_features_metabox' );

function tc_render_features_metabox( $post ) {
    wp_nonce_field( 'tc_save_features', 'tc_features_nonce' );

    $features = get_post_meta( $post->ID, '_dp_features', true );
    $rows     = $features ? explode( "\n", trim( $features ) ) : array();

    if ( empty( $rows ) ) {
        $rows = array( '' ); // al menos 1 fila vacía para partir
    }
    ?>
    <p class="description" style="margin-bottom:12px;">
        Estos ítems aparecen con un check (✓) en la card del producto dentro de la landing.
        Ejemplo: "Lector de código de barras integrado".
    </p>

    <div id="tc-features-rows">
        <?php foreach ( $rows as $row ) : ?>
            <div class="tc-feature-row" style="display:flex;gap:8px;margin-bottom:8px;">
                <input
                    type="text"
                    name="tc_dp_features[]"
                    value="<?php echo esc_attr( $row ); ?>"
                    class="widefat"
                    placeholder="Ej: Boleta y factura electrónica al SII"
                >
                <button type="button" class="button tc-remove-row">Quitar</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" class="button button-secondary" id="tc-add-row">+ Agregar característica</button>

    <script>
    (function () {
        var container = document.getElementById('tc-features-rows');
        var addBtn = document.getElementById('tc-add-row');

        function bindRemove(row) {
            row.querySelector('.tc-remove-row').addEventListener('click', function () {
                if (container.children.length > 1) {
                    row.remove();
                } else {
                    row.querySelector('input').value = '';
                }
            });
        }

        addBtn.addEventListener('click', function () {
            var row = document.createElement('div');
            row.className = 'tc-feature-row';
            row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;';
            row.innerHTML = '<input type="text" name="tc_dp_features[]" class="widefat" placeholder="Ej: Cuotas TUU con 0% de comisión"> <button type="button" class="button tc-remove-row">Quitar</button>';
            container.appendChild(row);
            bindRemove(row);
        });

        Array.prototype.forEach.call(container.querySelectorAll('.tc-feature-row'), bindRemove);
    })();
    </script>
    <?php
}

function tc_save_features_metabox( $post_id ) {
    if ( ! isset( $_POST['tc_features_nonce'] ) || ! wp_verify_nonce( $_POST['tc_features_nonce'], 'tc_save_features' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_product', $post_id ) ) {
        return;
    }

    $items = isset( $_POST['tc_dp_features'] ) ? (array) $_POST['tc_dp_features'] : array();
    $items = array_map( 'sanitize_text_field', $items );
    $items = array_filter( $items, function ( $v ) {
        return trim( $v ) !== '';
    } );

    update_post_meta( $post_id, '_dp_features', implode( "\n", $items ) );
}
add_action( 'save_post_product', 'tc_save_features_metabox' );

/**
 * ============================================================
 * Checkout — RUT y documento tributario (boleta / factura)
 * ============================================================
 * Chile exige RUT para emitir boleta o factura electrónica al SII.
 * Se agregan como campos de billing custom; "Razón social" reusa el
 * campo nativo billing_company (solo se pide/exige cuando es factura),
 * y se suma "Giro comercial" como campo nuevo.
 */

/**
 * Definición compartida de los 3 campos custom de billing — reusada en
 * el checkout (woocommerce_checkout_fields) Y en Mi Cuenta > Direcciones >
 * Editar dirección de facturación (woocommerce_billing_fields, un filtro
 * DISTINTO — WC_Countries::get_address_fields() no pasa por
 * woocommerce_checkout_fields). Antes solo estaban en checkout, así que
 * el cliente no podía corregir su RUT/tipo de documento desde Mi Cuenta
 * sin volver a pasar por una compra. Los IDs (billing_document_type,
 * billing_rut) son los mismos en los 2 formularios, así que checkout.js
 * (toggle Factura + validación de RUT) funciona en ambos sin duplicar JS.
 */
function tc_get_billing_extra_fields() {
    // Prioridad 21/23: justo después de apellidos (20) para no romper el
    // emparejamiento flotante form-row-first/form-row-last de Nombre+Apellidos.
    return array(
        'billing_document_type' => array(
            'type'     => 'select',
            'label'    => __( 'Tipo de documento', 'telconnect' ),
            'options'  => array(
                'boleta'  => __( 'Boleta electrónica', 'telconnect' ),
                'factura' => __( 'Factura electrónica', 'telconnect' ),
            ),
            'required' => true,
            'class'    => array( 'form-row-wide', 'chk-field-document-type' ),
            'priority' => 21,
        ),
        'billing_rut'            => array(
            'type'        => 'text',
            'label'       => __( 'RUT', 'telconnect' ),
            'placeholder' => '12.345.678-9',
            'required'    => true,
            'class'       => array( 'form-row-wide', 'chk-field-rut' ),
            'priority'    => 23,
        ),
        'billing_giro'           => array(
            'type'     => 'text',
            'label'    => __( 'Giro comercial', 'telconnect' ),
            'required' => false,
            'class'    => array( 'form-row-wide', 'chk-field-factura' ),
            'priority' => 33,
        ),
    );
}

// Razón social: reusa el campo nativo billing_company, relabeled — solo
// visible cuando se elige Factura. Misma relabel en checkout y en Mi Cuenta.
function tc_relabel_billing_company( array $fields ) {
    if ( isset( $fields['billing_company'] ) ) {
        $fields['billing_company']['label']    = __( 'Razón social', 'telconnect' );
        $fields['billing_company']['class']    = array( 'form-row-wide', 'chk-field-factura' );
        $fields['billing_company']['priority'] = 31;
    }
    return $fields;
}

function tc_add_checkout_fields( $fields ) {
    $fields['billing'] = array_merge( $fields['billing'], tc_get_billing_extra_fields() );
    $fields['billing'] = tc_relabel_billing_company( $fields['billing'] );
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'tc_add_checkout_fields' );

/**
 * ============================================================
 * Checkout wizard (§8.8) — reordenar/ocultar campos SOLO en checkout
 * ============================================================
 * No toca tc_get_billing_extra_fields()/tc_relabel_billing_company()
 * (esas siguen alimentando también el formulario de Mi Cuenta >
 * Direcciones, §8.5, que debe mantener su layout original) — este
 * filtro corre DESPUÉS de tc_add_checkout_fields() (prioridad 20 > 10)
 * y solo reacomoda lo que ya existe para el checkout.
 *
 * La card "Datos del cliente" del Figma (recursos/checkout-datos/) solo
 * muestra 4 campos: "Nombre y apellido" (un input, no 2), Teléfono,
 * Correo, RUT. Decisiones tomadas donde el Figma no alcanza a cubrir
 * el caso (documentadas en detalle en CONTEXT.md §8.8):
 * - "Nombre y apellido" reusa billing_first_name tal cual (recibe el
 *   nombre completo) — billing_last_name se oculta y deja de ser
 *   requerido. No se hace un split automático por espacio: los
 *   apellidos compuestos son la norma en Chile (ej. "Guerra Vásquez")
 *   y un split ingenuo los partiría mal.
 * - Dirección de facturación (address_1/2/city/state/postcode) no
 *   aparece en el Figma — se ocultan y dejan de ser obligatorias. La
 *   dirección real de envío se recolecta en la card "Despacho" vía
 *   shipping_* (ver tc_add_shipping_fields_for_checkout_wizard()).
 *   billing_country se mantiene oculto pero con su valor default (CL)
 *   intacto — no rompe nada porque el IVA se calcula según la
 *   dirección de SHIPPING (woocommerce_tax_based_on = shipping), no
 *   billing.
 * - Boleta/Factura (billing_document_type + Razón social/Giro) NO
 *   aparece en el Figma provisto — pero es una capacidad de negocio ya
 *   validada (§8.1, factura para empresas) que no hay motivo para
 *   eliminar solo porque el mockup no la capturó. Se mantiene, solo se
 *   le baja la prioridad visual (después del RUT). Anotado para
 *   confirmar con el cliente si el Figma la omitió a propósito.
 */
function tc_reorder_billing_fields_for_checkout_wizard( $fields ) {
    $overrides = array(
        'billing_first_name'    => array(
            'label'    => __( 'Nombre y apellido', 'telconnect' ),
            'priority' => 10,
            'class'    => array( 'form-row-first' ),
        ),
        'billing_last_name'     => array(
            'required' => false,
            'priority' => 11,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'billing_phone'         => array(
            'required' => true,
            'priority' => 20,
            'class'    => array( 'form-row-last' ),
        ),
        'billing_email'         => array(
            'priority' => 30,
            'class'    => array( 'form-row-first' ),
        ),
        'billing_rut'           => array(
            'priority' => 35,
            'class'    => array( 'form-row-last', 'chk-field-rut' ),
        ),
        'billing_country'       => array(
            'required' => false,
            'priority' => 40,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'billing_address_1'     => array(
            'required' => false,
            'priority' => 41,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'billing_address_2'     => array(
            'required' => false,
            'priority' => 42,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'billing_city'          => array(
            'required' => false,
            'priority' => 43,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'billing_state'         => array(
            'required' => false,
            'priority' => 44,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'billing_postcode'      => array(
            'required' => false,
            'priority' => 45,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'billing_document_type' => array(
            'priority' => 50,
            'class'    => array( 'form-row-wide', 'chk-field-document-type' ),
        ),
        'billing_company'       => array(
            'priority' => 51,
            'class'    => array( 'form-row-wide', 'chk-field-factura' ),
        ),
        'billing_giro'          => array(
            'priority' => 52,
            'class'    => array( 'form-row-wide', 'chk-field-factura' ),
        ),
    );

    foreach ( $overrides as $key => $override ) {
        if ( isset( $fields['billing'][ $key ] ) ) {
            $fields['billing'][ $key ] = array_merge( $fields['billing'][ $key ], $override );
        }
    }

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'tc_reorder_billing_fields_for_checkout_wizard', 20 );

/**
 * Card "Despacho": agrega shipping_rut y shipping_comuna (nuevos, sin
 * equivalente nativo en WooCommerce) y reordena/relabela los campos de
 * shipping nativos para que calcen con el Figma. shipping_state SÍ es
 * nativo y ya trae las 16 regiones de Chile con los códigos correctos
 * (CL-RM, CL-VS, etc. — ver WC()->countries->get_states('CL')), así
 * que "Región" lo reusa tal cual, sin inventar un dataset propio.
 *
 * "Comuna" no existe en WooCommerce para Chile — es un <select> nuevo
 * con un placeholder server-side; las opciones reales las puebla
 * checkout.js según la Región elegida (cascading select, dataset de
 * comunas ahí mismo). Ver CONTEXT.md §8.8 por el alcance del dataset.
 *
 * Ambos campos nuevos solo son obligatorios cuando el método de envío
 * elegido es "Despacho a domicilio" (flat_rate) — el toggle visual/
 * required lo maneja checkout.js (mismo patrón que chk-field-factura),
 * y tc_validate_shipping_fields() abajo lo refuerza server-side.
 */
function tc_add_shipping_fields_for_checkout_wizard( $fields ) {
    $fields['shipping']['shipping_rut'] = array(
        'type'        => 'text',
        'label'       => __( 'RUT receptor', 'telconnect' ),
        'placeholder' => '12.345.678-9',
        // required=false ACÁ a propósito, aunque en la práctica casi
        // siempre haga falta (domicilio es el método por defecto): la
        // validación "required" de WooCommerce (WC_Checkout::validate_posted_data())
        // es INCONDICIONAL — si se pone true acá, WC exige este campo
        // SIEMPRE, incluso cuando el comprador elige "Retiro en tienda"
        // (donde el campo ni se muestra). Lo condicional (solo exigir
        // si el método elegido es a domicilio) vive en
        // tc_validate_shipping_fields() más abajo, enganchado a
        // woocommerce_after_checkout_validation — ese sí lee qué método
        // se eligió antes de exigir el campo. El "(opcional)" que
        // aparece en el label mientras tanto es un costo cosmético
        // aceptado: se oculta por CSS (.chk-field-domicilio .optional)
        // en vez de reventar la validación real por prolijidad visual.
        'required'    => false,
        'class'       => array( 'form-row-first', 'chk-field-rut', 'chk-field-domicilio', 'chk-field-domicilio-required' ),
        'priority'    => 5,
    );

    $fields['shipping']['shipping_comuna'] = array(
        'type'              => 'select',
        'label'             => __( 'Comuna', 'telconnect' ),
        'options'           => array( '' => __( 'Selecciona una región primero', 'telconnect' ) ),
        'required'          => false, // ver comentario extenso en shipping_rut arriba — mismo criterio.
        'class'             => array( 'form-row-last', 'chk-field-domicilio', 'chk-field-domicilio-required' ),
        // checkout.js puebla las opciones reales según la Región elegida
        // (cascading select) — este data-attribute le pasa el valor ya
        // guardado (si el checkout se recarga tras un error de validación)
        // para que la repueble seleccionada en vez de perderla.
        'custom_attributes' => array(
            'data-selected' => (string) WC()->checkout()->get_value( 'shipping_comuna' ),
        ),
        'priority'          => 31,
    );

    $shipping_overrides = array(
        'shipping_first_name' => array(
            'label'    => __( 'Nombre de quien recibe', 'telconnect' ),
            'required' => false, // idem shipping_rut — condicional server-side, no acá.
            'priority' => 10,
            'class'    => array( 'form-row-last', 'chk-field-domicilio', 'chk-field-domicilio-required' ),
        ),
        'shipping_last_name'  => array(
            'required' => false,
            'priority' => 11,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'shipping_address_1'  => array(
            'label'    => __( 'Dirección', 'telconnect' ),
            'required' => false, // idem shipping_rut — condicional server-side, no acá.
            'priority' => 20,
            'class'    => array( 'form-row-first', 'chk-field-domicilio', 'chk-field-domicilio-required' ),
        ),
        'shipping_address_2'  => array(
            'label'    => __( 'Número / depto. u oficina', 'telconnect' ),
            'priority' => 21,
            'class'    => array( 'form-row-last', 'chk-field-domicilio' ),
        ),
        'shipping_state'      => array(
            'label'    => __( 'Región', 'telconnect' ),
            'required' => false, // idem shipping_rut — condicional server-side, no acá.
            'priority' => 30,
            'class'    => array( 'form-row-first', 'chk-field-domicilio', 'chk-field-domicilio-required', 'chk-field-region' ),
        ),
        'shipping_country'    => array(
            'required' => false,
            'priority' => 40,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'shipping_city'       => array(
            'required' => false,
            'priority' => 41,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'shipping_postcode'   => array(
            'required' => false,
            'priority' => 42,
            'class'    => array( 'chk-field-hidden' ),
        ),
        // Nativos que el Figma no muestra en absoluto — mismo criterio
        // que billing_company/address_1/etc en la card "Datos del
        // cliente" (tc_reorder_billing_fields_for_checkout_wizard()).
        'shipping_company'    => array(
            'required' => false,
            'priority' => 43,
            'class'    => array( 'chk-field-hidden' ),
        ),
        'shipping_phone'      => array(
            'required' => false,
            'priority' => 44,
            'class'    => array( 'chk-field-hidden' ),
        ),
    );

    foreach ( $shipping_overrides as $key => $override ) {
        if ( isset( $fields['shipping'][ $key ] ) ) {
            $fields['shipping'][ $key ] = array_merge( $fields['shipping'][ $key ], $override );
        }
    }

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'tc_add_shipping_fields_for_checkout_wizard', 20 );

/**
 * Refuerzo server-side: si el método de envío elegido es "Despacho a
 * domicilio" (flat_rate), exige RUT receptor válido, nombre, dirección,
 * región y comuna. Si es "Retiro en tienda" (local_pickup), no exige
 * nada de esto — mismo criterio condicional que ya usa checkout.js en
 * el cliente (defensa en profundidad: el toggle visual no es la única
 * validación).
 */
function tc_validate_shipping_fields() {
    $chosen_method = isset( $_POST['shipping_method'][0] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_method'][0] ) ) : '';
    $is_delivery   = 0 === strpos( $chosen_method, 'flat_rate' );

    if ( ! $is_delivery ) {
        return;
    }

    $shipping_rut = isset( $_POST['shipping_rut'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_rut'] ) ) : '';
    if ( ! $shipping_rut ) {
        wc_add_notice( __( 'El RUT de quien recibe es obligatorio para el despacho a domicilio.', 'telconnect' ), 'error', array( 'id' => 'shipping_rut' ) );
    } elseif ( class_exists( 'WoocommercePlugin\\helpers\\RutValidator' ) && ! \WoocommercePlugin\helpers\RutValidator::validate( $shipping_rut ) ) {
        wc_add_notice( __( 'El RUT de quien recibe no es válido. Revisa el formato (ej: 12345678-9).', 'telconnect' ), 'error', array( 'id' => 'shipping_rut' ) );
    }

    if ( empty( $_POST['shipping_first_name'] ) ) {
        wc_add_notice( __( 'El nombre de quien recibe es obligatorio para el despacho a domicilio.', 'telconnect' ), 'error', array( 'id' => 'shipping_first_name' ) );
    }
    if ( empty( $_POST['shipping_address_1'] ) ) {
        wc_add_notice( __( 'La dirección es obligatoria para el despacho a domicilio.', 'telconnect' ), 'error', array( 'id' => 'shipping_address_1' ) );
    }
    if ( empty( $_POST['shipping_state'] ) ) {
        wc_add_notice( __( 'La región es obligatoria para el despacho a domicilio.', 'telconnect' ), 'error', array( 'id' => 'shipping_state' ) );
    }
    if ( empty( $_POST['shipping_comuna'] ) ) {
        wc_add_notice( __( 'La comuna es obligatoria para el despacho a domicilio.', 'telconnect' ), 'error', array( 'id' => 'shipping_comuna' ) );
    }
}
add_action( 'woocommerce_after_checkout_validation', 'tc_validate_shipping_fields' );

// shipping_rut/shipping_comuna no son props nativas de WC_Order — se guardan como meta.
function tc_save_shipping_fields_to_order( $order_id ) {
    if ( isset( $_POST['shipping_rut'] ) ) {
        update_post_meta( $order_id, '_shipping_rut', sanitize_text_field( wp_unslash( $_POST['shipping_rut'] ) ) );
    }
    if ( isset( $_POST['shipping_comuna'] ) ) {
        update_post_meta( $order_id, '_shipping_comuna', sanitize_text_field( wp_unslash( $_POST['shipping_comuna'] ) ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'tc_save_shipping_fields_to_order' );

// Mismos campos en Mi Cuenta > Direcciones > Editar dirección de facturación.
function tc_add_account_billing_address_fields( $address_fields ) {
    $address_fields = array_merge( $address_fields, tc_get_billing_extra_fields() );
    $address_fields = tc_relabel_billing_company( $address_fields );
    return $address_fields;
}
add_filter( 'woocommerce_billing_fields', 'tc_add_account_billing_address_fields' );

// Validación server-side compartida: RUT bien formado, y Razón social +
// Giro obligatorios si es factura. Se usa tanto en el checkout como al
// guardar la dirección de facturación desde Mi Cuenta (mismos $_POST keys
// en los 2 formularios).
function tc_validate_rut_and_document_fields() {
    $rut = isset( $_POST['billing_rut'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_rut'] ) ) : '';

    if ( $rut && class_exists( 'WoocommercePlugin\\helpers\\RutValidator' ) ) {
        if ( ! \WoocommercePlugin\helpers\RutValidator::validate( $rut ) ) {
            wc_add_notice( __( 'El RUT ingresado no es válido. Revisa el formato (ej: 12345678-9).', 'telconnect' ), 'error', array( 'id' => 'billing_rut' ) );
        }
    }

    $document_type = isset( $_POST['billing_document_type'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_document_type'] ) ) : '';

    if ( 'factura' === $document_type ) {
        $razon_social = isset( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '';
        $giro         = isset( $_POST['billing_giro'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_giro'] ) ) : '';

        if ( ! $razon_social ) {
            wc_add_notice( __( 'La Razón social es obligatoria para emitir factura.', 'telconnect' ), 'error', array( 'id' => 'billing_company' ) );
        }
        if ( ! $giro ) {
            wc_add_notice( __( 'El Giro comercial es obligatorio para emitir factura.', 'telconnect' ), 'error', array( 'id' => 'billing_giro' ) );
        }
    }
}

function tc_validate_checkout_fields() {
    tc_validate_rut_and_document_fields();
}
add_action( 'woocommerce_after_checkout_validation', 'tc_validate_checkout_fields' );

function tc_validate_account_billing_address_fields( $user_id, $address_type ) {
    if ( 'billing' === $address_type ) {
        tc_validate_rut_and_document_fields();
    }
}
add_action( 'woocommerce_after_save_address_validation', 'tc_validate_account_billing_address_fields', 10, 2 );

// Guardar RUT, tipo de documento y giro en la orden (no son props nativas de WC_Order).
function tc_save_checkout_fields_to_order( $order_id ) {
    if ( isset( $_POST['billing_rut'] ) ) {
        update_post_meta( $order_id, '_billing_rut', sanitize_text_field( wp_unslash( $_POST['billing_rut'] ) ) );
    }
    if ( isset( $_POST['billing_document_type'] ) ) {
        update_post_meta( $order_id, '_billing_document_type', sanitize_text_field( wp_unslash( $_POST['billing_document_type'] ) ) );
    }
    if ( isset( $_POST['billing_giro'] ) ) {
        update_post_meta( $order_id, '_billing_giro', sanitize_text_field( wp_unslash( $_POST['billing_giro'] ) ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'tc_save_checkout_fields_to_order' );

/**
 * Sumar el RUT a la dirección de facturación formateada del pedido
 * (aparece en emails, admin y en el detalle de pedido de Mi Cuenta).
 *
 * BUG ENCONTRADO Y CORREGIDO (el enfoque anterior no funcionaba): se
 * intentaba inyectar `$address['rut']` ANTES de formatear y confiar en
 * que `{rut}` en el template de formato lo reemplazara. Pero
 * WC_Countries::get_formatted_address() solo reemplaza un whitelist fijo
 * de tokens ({name}, {company}, {address_1}, etc — ver class-wc-countries.php)
 * e ignora cualquier key desconocida como 'rut' en silencio. Encima, el
 * `{rut}` se había agregado solo al formato 'default', pero Chile tiene su
 * propio formato explícito en $formats['CL'] (línea ~604 de
 * class-wc-countries.php) que NO pasa por 'default' — o sea que para
 * cualquier pedido con country=CL (100% de los pedidos de esta tienda) el
 * RUT nunca se mostraba, Y para un pedido con country vacío (bug de datos,
 * no de template) el texto "{rut}" quedaba impreso tal cual sin reemplazar.
 * Se cambia a un filtro que corre DESPUÉS del formateo (recibe el string ya
 * armado), evitando pelear con el sistema de tokens/formato por país.
 */
function tc_append_rut_to_formatted_address( $address, $raw_address, $order ) {
    $rut = $order->get_meta( '_billing_rut' );
    if ( $rut && $address ) {
        $address .= '<br/>' . esc_html__( 'RUT:', 'telconnect' ) . ' ' . esc_html( $rut );
    }
    return $address;
}
add_filter( 'woocommerce_order_get_formatted_billing_address', 'tc_append_rut_to_formatted_address', 10, 3 );

// Mostrar RUT / tipo de documento / giro en el panel de admin del pedido.
function tc_display_rut_admin_order_meta( $order ) {
    $rut           = $order->get_meta( '_billing_rut' );
    $document_type = $order->get_meta( '_billing_document_type' );
    $giro          = $order->get_meta( '_billing_giro' );

    if ( ! $rut && ! $document_type && ! $giro ) {
        return;
    }

    echo '<p><strong>' . esc_html__( 'RUT:', 'telconnect' ) . '</strong> ' . esc_html( $rut ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Documento:', 'telconnect' ) . '</strong> ' . esc_html( 'factura' === $document_type ? 'Factura electrónica' : 'Boleta electrónica' ) . '</p>';
    if ( $giro ) {
        echo '<p><strong>' . esc_html__( 'Giro:', 'telconnect' ) . '</strong> ' . esc_html( $giro ) . '</p>';
    }
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'tc_display_rut_admin_order_meta' );

/**
 * ============================================================
 * Mi Cuenta
 * ============================================================
 */

// Modificador de color del badge de estado (.acc-badge--*, ver account.css)
// para un status interno de WC ('pending', 'processing', etc, sin el
// prefijo 'wc-'). Reusado en myaccount/orders.php y myaccount/view-order.php.
// Si algún plugin agrega un estado custom no listado acá, el badge cae al
// estilo base .acc-badge sin color (no rompe, solo queda neutro).
function tc_get_order_status_badge_class( $status ) {
    $status = str_replace( 'wc-', '', $status );
    $known  = array( 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed' );

    return in_array( $status, $known, true ) ? 'acc-badge acc-badge--' . $status : 'acc-badge';
}

// Sin productos descargables en este negocio (venta de POS físico) — se
// oculta la pestaña "Descargas" del menú de Mi Cuenta para no mostrar un
// tab siempre vacío. El endpoint /mi-cuenta/downloads/ sigue existiendo
// (no es un problema de seguridad visitarlo a mano), solo se quita del
// menú. Si en el futuro se venden productos descargables (manuales,
// software), quitar este unset().
function tc_remove_account_downloads_tab( $items ) {
    unset( $items['downloads'] );
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'tc_remove_account_downloads_tab' );

/**
 * ============================================================
 * PDP — checklist de características (_dp_features) en la ficha
 * ============================================================
 * Reusa el mismo custom field que ya alimenta las cards de la home
 * (ver metabox más arriba). Se muestra entre el precio y el botón
 * de agregar al carro, con el mismo look que .dp-card-features.
 */
function tc_pdp_features_checklist() {
    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $features = get_post_meta( $product->get_id(), '_dp_features', true );
    if ( ! $features ) {
        return;
    }

    $features_list = explode( "\n", trim( $features ) );
    echo '<ul class="dp-card-features pdp-features">';
    foreach ( $features_list as $feature ) {
        if ( trim( $feature ) ) {
            echo '<li>' . esc_html( trim( $feature ) ) . '</li>';
        }
    }
    echo '</ul>';
}
add_action( 'woocommerce_single_product_summary', 'tc_pdp_features_checklist', 25 );

// Nota "+ IVA" bajo el precio del PDP (mismo texto que ya se usa en .dp-price-note de las cards).
function tc_pdp_price_note() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }
    echo '<span class="pdp-price-note">' . esc_html__( 'Precio + IVA', 'telconnect' ) . '</span>';
}
add_action( 'woocommerce_single_product_summary', 'tc_pdp_price_note', 11 );