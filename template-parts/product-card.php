<?php
/**
 * Card de producto reutilizable — mismo look que .dp-card (usado en el
 * carrusel de la home, devices-products.php / devices-complementos.php).
 * Se le suma un link a la ficha (PDP) en imagen/título, que las cards
 * de la home no necesitan porque ahí el único CTA es "Agregar al carro".
 *
 * Asume que el loop que la incluye ya dejó $product / el post actual
 * seteados (the_post() + global $product, como en el resto del theme).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
    return;
}
?>
<div class="dp-card plp-card">
    <a href="<?php echo esc_url( get_permalink() ); ?>" class="dp-card-image">
        <?php echo woocommerce_get_product_thumbnail(); ?>
    </a>
    <div class="dp-card-body">
        <h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
        <p class="dp-card-desc"><?php echo esc_html( $product->get_short_description() ); ?></p>
        <div class="dp-card-price">
            <?php echo $product->get_price_html(); ?>
            <span class="dp-price-note">+ IVA</span>
        </div>
        <?php
        $features = get_post_meta( get_the_ID(), '_dp_features', true );
        if ( $features ) :
            $features_list = explode( "\n", trim( $features ) );
            echo '<ul class="dp-card-features">';
            foreach ( $features_list as $feature ) {
                if ( trim( $feature ) ) {
                    echo '<li>' . esc_html( trim( $feature ) ) . '</li>';
                }
            }
            echo '</ul>';
        endif;
        ?>
        <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="dp-add-cart" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
            Agregar al carro
        </a>
    </div>
</div>
