<footer class="site-footer">
    <div class="footer-container tc-container">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="<?php echo home_url(); ?>" class="footer-logo">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-telconnect.svg' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
                </a>
                <p class="footer-address">Vicuña Mackenna poniente 6843, oficina 805 La Florida.</p>
            </div>

            <div class="footer-col">
                <h4>Sitio</h4>
                <ul>
                    <li><a href="<?php echo home_url(); ?>">Inicio</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/tienda' ) ); ?>">Máquinas</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#comisiones' ) ); ?>">Comisiones</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#faq' ) ); ?>">Preguntas frecuentes</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Telconnect Parking</h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/telconnect-parking' ) ); ?>">Qué es</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/telconnect-parking#funcionalidades' ) ); ?>">Funcionalidades</a></li>
                    <li><a href="#">Plataforma web</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/telconnect-parking#planes' ) ); ?>">Planes</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/telconnect-parking#como-empezar' ) ); ?>">Cómo empezar</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contacto</h4>
                <ul>
                    <li><a href="<?php echo tc_whatsapp_url(); ?>" target="_blank" rel="noopener">Contactar ventas</a></li>
                    <li><a href="<?php echo tc_whatsapp_url(); ?>" target="_blank" rel="noopener">Soporte técnico</a></li>
                    <li><a href="#">Trabaja con nosotros</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Accesos</h4>
                <ul>
                    <li><a href="#">Ingreso clientes</a></li>
                    <li><a href="#">Ingreso trabajadores</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-wordmark" aria-hidden="true">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-telconnect-wordmark.svg' ); ?>" alt="">
        </div>

        <div class="footer-divider"></div>

        <div class="footer-legal">
            <span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Telconnect</span>
            <span>Distribuidor autorizado de TUU, marca de Haulmer SpA</span>
            <span class="footer-legal-links">
                <a href="#">Términos y condiciones</a> · <a href="#">Política de privacidad</a>
            </span>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>