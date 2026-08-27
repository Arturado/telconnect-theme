<?php
/**
 * Override de yourtheme/woocommerce/archive-product.php
 * Base: woocommerce/templates/archive-product.php (v8.6.0)
 * Sirve tanto para /tienda (shop) como para las categorías
 * (product_cat) — WooCommerce usa este mismo archivo para ambas.
 * Se mantienen los do_action() del core (notices, ordenamiento,
 * paginación) y se reemplaza el <ul class="products"> por un grid
 * propio que reusa .dp-card (mismo patrón que la home) vía
 * template-parts/product-card.php.
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

$plp_current_term = is_tax( 'product_cat' ) ? get_queried_object() : null;
?>

<div class="plp-page">
    <div class="plp-container tc-container">
        <span class="plp-eyebrow"><?php esc_html_e( 'Catálogo', 'telconnect' ); ?></span>
        <h1 class="plp-title"><?php echo esc_html( wp_strip_all_tags( woocommerce_page_title( false ) ) ); ?></h1>

        <?php if ( $plp_current_term && $plp_current_term->description ) : ?>
            <div class="plp-subtitle"><?php echo wp_kses_post( wpautop( $plp_current_term->description ) ); ?></div>
        <?php endif; ?>

        <?php
        $plp_categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => 0,
        ) );

        if ( ! is_wp_error( $plp_categories ) && count( $plp_categories ) > 1 ) :
            ?>
            <nav class="plp-cat-tabs" aria-label="<?php esc_attr_e( 'Categorías de producto', 'telconnect' ); ?>">
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="plp-cat-tab<?php echo ( is_shop() ) ? ' is-active' : ''; ?>">
                    <?php esc_html_e( 'Todos', 'telconnect' ); ?>
                </a>
                <?php foreach ( $plp_categories as $plp_cat ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $plp_cat ) ); ?>" class="plp-cat-tab<?php echo ( $plp_current_term && $plp_current_term->term_id === $plp_cat->term_id ) ? ' is-active' : ''; ?>">
                        <?php echo esc_html( $plp_cat->name ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <?php if ( woocommerce_product_loop() ) : ?>

            <?php do_action( 'woocommerce_before_shop_loop' ); ?>

            <?php if ( wc_get_loop_prop( 'total' ) ) : ?>
                <div class="plp-grid">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        global $product;
                        do_action( 'woocommerce_shop_loop' );
                        get_template_part( 'template-parts/product-card' );
                    endwhile;
                    ?>
                </div>
            <?php endif; ?>

            <?php do_action( 'woocommerce_after_shop_loop' ); ?>

        <?php else : ?>

            <?php do_action( 'woocommerce_no_products_found' ); ?>

        <?php endif; ?>
    </div>
</div>

<?php
do_action( 'woocommerce_after_main_content' );

do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
