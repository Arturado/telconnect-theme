<section class="parking-teaser">
    <div class="pt-container">
        <span class="pt-eyebrow">Nuestra solución exclusiva</span>
        <h2 class="pt-title">Gestiona tu estacionamiento con confianza.</h2>
        <p class="pt-subtitle">Telconnect Parking corre en la misma máquina para aceptar pagos. Un solo sistema para controlar accesos, cobrar e imprimir el ticket.</p>

        <div class="pt-grid">
            <!-- Card grande: foto con overlay -->
            <div class="pt-card pt-card-photo">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/parking-teaser-photo.jpg' ); ?>" alt="Tickets, turnos y reportes desde tu POS" loading="lazy">
                <div class="pt-card-overlay">
                    <h3>Tickets, turnos y reportes desde tu POS</h3>
                    <p>Tus operadores registran patentes, cobran e imprimen el ticket desde cualquiera de nuestras máquinas. El cierre de turno queda validado por el administrador y todo se respalda en la nube.</p>
                    <a href="<?php echo esc_url( home_url( '/telconnect-parking' ) ); ?>" class="btn btn-primary">Ver más detalles</a>
                </div>
            </div>

            <!-- Card chica: mockup teléfono -->
            <div class="pt-card pt-card-mockup">
                <div class="pt-mockup-visual">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/parking-teaser-mockup.jpg' ); ?>" alt="Tu estacionamiento en una sola máquina" loading="lazy">
                </div>
                <div class="pt-card-text">
                    <h3>Tu estacionamiento en una sola máquina.</h3>
                    <p>El mismo equipo que cobra con tarjeta imprime el ticket y emite la boleta.</p>
                    <a href="<?php echo esc_url( home_url( '/tienda' ) ); ?>" class="btn btn-outline-dark">Comprar una máquina</a>
                </div>
            </div>
        </div>
    </div>
</section>