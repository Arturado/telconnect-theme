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
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-telconnect.svg" alt="<?php bloginfo('name'); ?>" class="logo-pill-full">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-telconnect-mark.svg" alt="<?php bloginfo('name'); ?>" class="logo-pill-mark">
        </a>

        <!-- Menu Pill (nav items + Iniciar sesión, todo dentro de la misma píldora) — oculto <900px, reemplazado por el hamburguesa -->
        <nav class="menu-pill">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'menu-pill-list',
                'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
            ) );
            ?>
            <a href="https://tu-plataforma-externa.cl/login" target="_blank" rel="noopener" class="menu-pill-login"><span class="btn-label-mask"><span class="btn-label">Iniciar sesión</span><span class="btn-label-ghost" aria-hidden="true">Iniciar sesión</span></span></a>
        </nav>

        <!-- Grupo de acciones: en desktop solo envuelve el carrito (sin efecto visual extra),
             en mobile agrupa carrito + hamburguesa como 2 píldoras separadas pegadas -->
        <div class="header-actions">
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

            <!-- Hamburguesa: solo visible <900px -->
            <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-nav-overlay" aria-label="Abrir menú">
                <span class="mobile-menu-toggle-line"></span>
                <span class="mobile-menu-toggle-line"></span>
                <span class="mobile-menu-toggle-line mobile-menu-toggle-line--short"></span>
            </button>
        </div>

    </div>
</header>

<!-- Overlay del menú mobile: full-screen, mismos links del wp_nav_menu + Iniciar sesión -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay" role="dialog" aria-modal="true" aria-label="Menú de navegación" aria-hidden="true">
    <div class="mobile-nav-panel">
        <div class="mobile-nav-panel-header">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-telconnect-mark.svg" alt="" class="mobile-nav-logo">
            <button type="button" class="mobile-nav-close" id="mobile-nav-close" aria-label="Cerrar menú">
                <span class="mobile-nav-close-line"></span>
                <span class="mobile-nav-close-line"></span>
            </button>
        </div>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'mobile-nav-list',
            'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
        ) );
        ?>
        <a href="https://tu-plataforma-externa.cl/login" target="_blank" rel="noopener" class="mobile-nav-login"><span class="btn-label-mask"><span class="btn-label">Iniciar sesión</span><span class="btn-label-ghost" aria-hidden="true">Iniciar sesión</span></span></a>
    </div>
</div>