<section class="plan">
    <div class="plan-container">
        <div class="plan-head">
            <span class="plan-eyebrow">Cómo se cobra el software</span>
            <h2 class="plan-title">Una licencia por operador. Ni una más.</h2>
        </div>

        <?php
        /**
         * Las 2 cards muestran el mismo plan (mismo precio, mismas 6
         * características) — no es un error de Figma, son 2 caminos de
         * conversión distintos para el mismo plan (probar gratis vs
         * comprar directo), mismo patrón que los 2 CTA del Hero.
         */
        $plan_features = array(
            'Sincronización automática entre dispositivos',
            'Panel web de administración completo',
            'Tarifas avanzadas y clientes frecuentes',
            'Respaldo automático en la nube',
            'Acceso multiusuario con validación en línea',
            'Soporte premium y actualizaciones',
        );
        ?>

        <div class="plan-grid">
            <!-- Card clara -->
            <div class="plan-card plan-card-light">
                <div class="plan-card-head">
                    <span class="plan-card-eyebrow">Telconnect Parking Premium</span>
                    <h3>Control total, cobrado por operador</h3>
                </div>
                <div class="plan-price">
                    <span class="plan-price-amount">0,8 UF</span>
                    <span class="plan-price-period">/ mes por operador + IVA</span>
                </div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <?php foreach ( $plan_features as $feature ) : ?>
                        <li>
                            <span class="plan-check plan-check-light"></span>
                            <?php echo esc_html( $feature ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="#" class="plan-cta plan-cta-light" data-pmodal-open data-pmodal-origen="Planes">
                    <span class="btn-label-mask">
                        <span class="btn-label">Solicitar prueba gratis</span>
                        <span class="btn-label-ghost" aria-hidden="true">Solicitar prueba gratis</span>
                    </span>
                </a>
            </div>

            <!-- Card oscura -->
            <div class="plan-card plan-card-dark">
                <div class="plan-card-head">
                    <span class="plan-card-eyebrow">Telconnect Parking Premium</span>
                    <h3>Control total, cobrado por operador</h3>
                </div>
                <div class="plan-price">
                    <span class="plan-price-amount">0,8 UF</span>
                    <span class="plan-price-period">/ mes por operador + IVA</span>
                </div>
                <div class="plan-divider plan-divider-dark"></div>
                <ul class="plan-features plan-features-dark">
                    <?php foreach ( $plan_features as $feature ) : ?>
                        <li>
                            <span class="plan-check plan-check-dark"></span>
                            <?php echo esc_html( $feature ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo esc_url( home_url( '/tienda' ) ); ?>" class="plan-cta plan-cta-dark">
                    <span class="btn-label-mask">
                        <span class="btn-label">Comprar mi máquina</span>
                        <span class="btn-label-ghost" aria-hidden="true">Comprar mi máquina</span>
                    </span>
                </a>
            </div>
        </div>

        <div class="plan-note">
            <div class="plan-note-text">
                <strong>El cobro es por operador, no por estacionamiento</strong>
                <p>El plan mensual no se cobra por recinto ni por máquina, sino por cada persona que atiende. Un estacionamiento con tres turnos necesita tres licencias; si operas tú solo, es una. Así no pagas por capacidad que no usas.</p>
            </div>
            <a href="<?php echo tc_whatsapp_url( 'Hola, quiero asesoría sobre los planes de Telconnect Parking' ); ?>" target="_blank" rel="noopener" class="plan-note-cta">
                <span class="btn-label-mask">
                    <span class="btn-label">Asesórate por WhatsApp</span>
                    <span class="btn-label-ghost" aria-hidden="true">Asesórate por WhatsApp</span>
                </span>
            </a>
        </div>
    </div>
</section>