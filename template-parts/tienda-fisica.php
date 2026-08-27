<section class="tienda-fisica">
    <div class="tf-container tc-container">
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
                        <p>Lunes a viernes de 10:00 a 18:00<br>Sábados de 10:00 a 14:00</p>
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
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3326.382810779933!2d-70.605282!3d-33.5174314!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9662d1229cb6aa7d%3A0x614d94fe2456f85d!2sTELCONNECT!5e0!3m2!1ses-419!2scl!4v1787699454730!5m2!1ses-419!2scl"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
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