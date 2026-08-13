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

function tc_add_checkout_fields( $fields ) {
    // Prioridad 21/23: justo después de apellidos (20) para no romper el
    // emparejamiento flotante form-row-first/form-row-last de Nombre+Apellidos.
    $fields['billing']['billing_document_type'] = array(
        'type'        => 'select',
        'label'       => __( 'Tipo de documento', 'telconnect' ),
        'options'     => array(
            'boleta'  => __( 'Boleta electrónica', 'telconnect' ),
            'factura' => __( 'Factura electrónica', 'telconnect' ),
        ),
        'required'    => true,
        'class'       => array( 'form-row-wide', 'chk-field-document-type' ),
        'priority'    => 21,
    );

    $fields['billing']['billing_rut'] = array(
        'type'        => 'text',
        'label'       => __( 'RUT', 'telconnect' ),
        'placeholder' => '12.345.678-9',
        'required'    => true,
        'class'       => array( 'form-row-wide', 'chk-field-rut' ),
        'priority'    => 23,
    );

    // Razón social: reusa el campo nativo billing_company, relabeled.
    if ( isset( $fields['billing']['billing_company'] ) ) {
        $fields['billing']['billing_company']['label']    = __( 'Razón social', 'telconnect' );
        $fields['billing']['billing_company']['class']    = array( 'form-row-wide', 'chk-field-factura' );
        $fields['billing']['billing_company']['priority'] = 31;
    }

    $fields['billing']['billing_giro'] = array(
        'type'        => 'text',
        'label'       => __( 'Giro comercial', 'telconnect' ),
        'required'    => false,
        'class'       => array( 'form-row-wide', 'chk-field-factura' ),
        'priority'    => 33,
    );

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'tc_add_checkout_fields' );

// Validación server-side: RUT bien formado, y Razón social + Giro obligatorios si es factura.
function tc_validate_checkout_fields() {
    $rut = isset( $_POST['billing_rut'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_rut'] ) ) : '';

    if ( $rut && class_exists( 'WoocommercePlugin\\helpers\\RutValidator' ) ) {
        if ( ! \WoocommercePlugin\helpers\RutValidator::validate( $rut ) ) {
            wc_add_notice( __( 'El RUT ingresado no es válido. Revisa el formato (ej: 12345678-9).', 'telconnect' ), 'error' );
        }
    }

    $document_type = isset( $_POST['billing_document_type'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_document_type'] ) ) : '';

    if ( 'factura' === $document_type ) {
        $razon_social = isset( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '';
        $giro         = isset( $_POST['billing_giro'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_giro'] ) ) : '';

        if ( ! $razon_social ) {
            wc_add_notice( __( 'La Razón social es obligatoria para emitir factura.', 'telconnect' ), 'error' );
        }
        if ( ! $giro ) {
            wc_add_notice( __( 'El Giro comercial es obligatorio para emitir factura.', 'telconnect' ), 'error' );
        }
    }
}
add_action( 'woocommerce_after_checkout_validation', 'tc_validate_checkout_fields' );

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

// Sumar el RUT a la dirección de facturación formateada (aparece en emails, admin y thank-you).
function tc_append_rut_to_formatted_address( $address, $order ) {
    $rut = $order->get_meta( '_billing_rut' );
    if ( $rut ) {
        $address['rut'] = 'RUT: ' . $rut;
    }
    return $address;
}
add_filter( 'woocommerce_order_formatted_billing_address', 'tc_append_rut_to_formatted_address', 10, 2 );

function tc_add_rut_to_address_format( $formats ) {
    $formats['default'] .= "\n{rut}";
    return $formats;
}
add_filter( 'woocommerce_localisation_address_formats', 'tc_add_rut_to_address_format' );

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