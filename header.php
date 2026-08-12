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

        <!-- Logo Pill -->
        <a href="<?php echo home_url(); ?>" class="logo-pill">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-telconnect.svg" alt="<?php bloginfo('name'); ?>">
        </a>

        <!-- Menu Pill (nav items + Iniciar sesión, todo dentro de la misma píldora) -->
        <nav class="menu-pill">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'menu-pill-list',
                'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
            ) );
            ?>
            <a href="https://tu-plataforma-externa.cl/login" target="_blank" rel="noopener" class="menu-pill-login">Iniciar sesión</a>
        </nav>

        <!-- Cart Pill -->
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <a href="<?php echo wc_get_cart_url(); ?>" class="cart-pill">
                <span class="cart-pill-icon">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/bag-04.svg' ); ?>" alt="" class="cart-pill-icon-svg">
                    <?php if ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                        <span class="cart-pill-badge"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    <?php endif; ?>
                </span>
                <span class="cart-pill-label">Carrito</span>
            </a>
        <?php endif; ?>

    </div>
</header>