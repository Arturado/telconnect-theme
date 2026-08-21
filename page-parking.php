<?php
/**
 * Template Name: Landing Parking
 *
 * Page Template para la landing de Telconnect Parking. Se selecciona
 * desde el editor de páginas de WordPress (Atributos de página > Plantilla).
 *
 * Reusa 4 secciones tal cual del Home (mismo copy, mismo archivo, sin
 * parametrizar): Devices/Máquinas, Devices/Complementos, Commission, y
 * el Header/Navbar/Footer globales (vía get_header()/get_footer()).
 * El resto son secciones propias de Parking, con sus propios .php/.css
 * enqueueados condicionalmente en telconnect_enqueue_parking_assets()
 * (functions.php) vía is_page_template('page-parking.php').
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero-parking' ); ?>
<?php get_template_part( 'template-parts/problema' ); ?>
<?php get_template_part( 'template-parts/funcionalidades' ); ?>
<?php get_template_part( 'template-parts/admin-remota' ); ?>
<?php get_template_part( 'template-parts/devices-products' ); ?>
<?php get_template_part( 'template-parts/planes' ); ?>
<?php get_template_part( 'template-parts/devices-complementos' ); ?>
<?php get_template_part( 'template-parts/commission' ); ?>
<?php get_template_part( 'template-parts/como-empezar' ); ?>
<?php get_template_part( 'template-parts/faq-parking' ); ?>
<?php get_template_part( 'template-parts/final-cta-parking' ); ?>

<?php
get_footer();