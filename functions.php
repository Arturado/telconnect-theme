<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Centralización de contacto — cambiar acá, se propaga a todo el sitio
define( 'TC_WHATSAPP_NUMBER', '56900000000' ); // TODO: reemplazar con el número real del cliente


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

function telconnect_enqueue_fonts() {
    wp_enqueue_style( 'telconnect-fonts', 'https://fonts.googleapis.com/css2?family=Stack+Sans+Headline:wght@200..700&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_fonts' );

function telconnect_enqueue_scripts() {
    wp_enqueue_script(
        'telconnect-devices-carousel',
        get_template_directory_uri() . '/assets/js/devices-carousel.js',
        array(),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_scripts' );

function telconnect_enqueue_ecosystem_js() {
    wp_enqueue_script(
        'telconnect-ecosystem',
        get_template_directory_uri() . '/assets/js/ecosystem.js',
        array(),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_ecosystem_js' );


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
    wp_enqueue_style( 'telconnect-style', get_stylesheet_uri(), array(), '1.0.0' );
    wp_enqueue_style( 'telconnect-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0' );

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
            '1.0.0'
        );
    }
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
        '1.0.0'
    );

    wp_enqueue_script(
        'telconnect-checkout',
        get_template_directory_uri() . '/assets/js/checkout.js',
        array(),
        '1.0.0',
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
        '1.0.0'
    );

    wp_enqueue_style(
        'telconnect-cart',
        get_template_directory_uri() . '/assets/css/cart.css',
        array( 'telconnect-main', 'telconnect-plp' ),
        '1.0.0'
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
        '1.0.0'
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
        '1.0.0'
    );

    wp_enqueue_style(
        'telconnect-pdp',
        get_template_directory_uri() . '/assets/css/pdp.css',
        array( 'telconnect-main', 'telconnect-plp' ),
        '1.0.0'
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
        '1.0.0'
    );

    // Reusa checkout.js sin cambios: el formulario "Editar dirección de
    // facturación" de Mi Cuenta usa los mismos IDs de campo
    // (billing_document_type, billing_rut) que el checkout, así que el
    // toggle de Factura y la validación de RUT funcionan igual acá.
    wp_enqueue_script(
        'telconnect-checkout',
        get_template_directory_uri() . '/assets/js/checkout.js',
        array(),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'telconnect_enqueue_account_assets' );

/**
 * Header flotante SOLO en la home (ver header.css .site-header).
 * El Hero de front-page.php tiene su propia foto + overlay oscuro
 * calibrados para que el header transparente se vea bien superpuesto.
 * Cualquier página nueva sin ese tipo de fondo NO debe agregar esta
 * clase — así el header se comporta como uno normal (empuja el
 * contenido) y no hace falta compensar con padding-top a mano.
 */
function telconnect_body_classes( $classes ) {
    if ( is_front_page() ) {
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