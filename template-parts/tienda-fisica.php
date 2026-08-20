<section class="tienda-fisica">
    <div class="tf-container">
        <span class="tf-eyebrow">Tienda física</span>
        <h2 class="tf-title">¿Prefieres ir a buscarla?</h2>
        <p class="tf-subtitle">Pasa por nuestra tienda en Santiago y sales con la máquina funcionando. La configuramos, activamos tu cuenta y te enseñamos a usarla en el mismo mostrador.</p>

        <div class="tf-grid">
            <div class="tf-info-card">
                <div class="tf-info-row">
                    <span class="tf-icon">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/Location.svg' ); ?>" alt="">
                    </span>
                    <div class="tf-info-text">
                        <span class="tf-info-label">Dirección</span>
                        <p>Vicuña Mackenna poniente 6843, oficina 805<br>La Florida.</p>
                    </div>
                </div>
                <div class="tf-info-row">
                    <span class="tf-icon">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/reloj.svg' ); ?>" alt="">
                    </span>
                    <div class="tf-info-text">
                        <span class="tf-info-label">Horario</span>
                        <p>Lunes a viernes de 9:00 a 18:30<br>Sábados de 10:00 a 14:00</p>
                    </div>
                </div>
                <div class="tf-info-row">
                    <span class="tf-icon">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/Receipt.svg' ); ?>" alt="">
                    </span>
                    <div class="tf-info-text">
                        <span class="tf-info-label">Qué llevar</span>
                        <p>Tu RUT y los datos de tu negocio.<br>Nada más, el resto lo hacemos nosotros.</p>
                    </div>
                </div>

                <a href="https://www.google.com/maps/search/?api=1&query=Vicu%C3%B1a+Mackenna+Poniente+6843,+La+Florida,+Santiago" target="_blank" rel="noopener" class="tf-btn-primary">
                    <span class="btn-label-mask"><span class="btn-label">Cómo llegar</span><span class="btn-label-ghost" aria-hidden="true">Cómo llegar</span></span>
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/icon-dere.svg' ); ?>" alt="" class="tf-btn-arrow">
                </a>
            </div>

            <div class="tf-map">
                <iframe
                    src="https://www.google.com/maps?q=Vicu%C3%B1a+Mackenna+Poniente+6843,+La+Florida,+Santiago&output=embed"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Ubicación tienda Telconnect">
                </iframe>
            </div>
        </div>

        <div class="tf-cta-banner">
            <div>
                <strong>¿Vas en camino? Avísanos y la dejamos lista antes de que llegues.</strong>
                <p>Nos mandas el RUT por WhatsApp y cuando entres solo la retiras.</p>
            </div>
            <a href="<?php echo tc_whatsapp_url(); ?>" target="_blank" rel="noopener" class="tf-btn-cta"><span class="btn-label-mask"><span class="btn-label">Escribir por WhatsApp</span><span class="btn-label-ghost" aria-hidden="true">Escribir por WhatsApp</span></span></a>
        </div>
    </div>
</section>