<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/announcement-bar' ); ?>

<header class="site-header">
    <div class="header-inner">
        <a href="<?php echo home_url(); ?>" class="site-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-telconnect.svg" alt="<?php bloginfo('name'); ?>">
        </a>

        <nav class="main-nav">
    <?php
    wp_nav_menu( array(
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'nav-menu',
    ) );
    ?>
    <a href="https://tu-plataforma-externa.cl/login" target="_blank" rel="noopener" class="btn-login">Iniciar sesión</a>
</nav>

        <div class="header-cart">
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link">
                    <span class="cart-icon">🛍️</span>
                    <span class="cart-label">Carrito</span>
                    <?php if ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                        <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>