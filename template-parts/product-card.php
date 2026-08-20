<?php
/**
 * Card de producto reutilizable — mismo look que .dp-card (usado en el
 * carrusel de la home, devices-products.php / devices-complementos.php).
 * Se le suma un link a la ficha (PDP) en imagen/título.
 *
 * El CTA ("Agregar al carro") ya NO agrega directo al carrito — lleva
 * a la PDP del producto. Se mantiene el texto "Agregar al carro" a
 * propósito (decisión del usuario), aunque el comportamiento real sea
 * navegación, no un add-to-cart. La única página donde el CTA agrega
 * de verdad al carrito es la PDP misma (woocommerce/content-single-product.php).
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
        <a href="<?php echo esc_url( get_permalink() ); ?>" class="dp-add-cart">
            <span class="btn-label-mask"><span class="btn-label">Agregar al carro</span><span class="btn-label-ghost" aria-hidden="true">Agregar al carro</span></span>
        </a>
    </div>
</div>