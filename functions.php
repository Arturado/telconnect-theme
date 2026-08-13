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