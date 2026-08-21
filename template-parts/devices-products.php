<section class="devices-products">
    <div class="dp-container">
        <span class="dp-eyebrow">Elige tu máquina</span>
        <h2 class="dp-title">Todo parte por elegir el equipo correcto.</h2>
        <p class="dp-subtitle">Cualquiera que elijas te conecta al ecosistema completo desde el primer día. Si no sabes cuál te sirve, escríbenos y en dos preguntas te lo decimos.</p>

        <div class="dp-carousel">
            <div class="dp-grid" id="dp-grid">
                <?php
                $featured_ids = wc_get_featured_product_ids();

                $dp_args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'post__in'       => ! empty( $featured_ids ) ? $featured_ids : array( 0 ),
                    'orderby'        => 'post__in',
                );

                $dp_query = new WP_Query( $dp_args );

                if ( $dp_query->have_posts() ) :
                    while ( $dp_query->have_posts() ) : $dp_query->the_post();
                        global $product;
                        ?>
                        <div class="dp-card">
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
                                    <span class="btn-label-mask"><span class="btn-label">Ir a comprar</span><span class="btn-label-ghost" aria-hidden="true">Ir a comprar</span></span>
                                </a>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <p class="dp-empty">Aún no hay productos marcados como destacados. Ve a <strong>Productos &gt; Todos los productos</strong> y márcalos vía Edición rápida.</p>
                    <?php
                endif;
                ?>
            </div>

            <div class="dp-carousel-nav">
                <div class="dp-dots" id="dp-dots"></div>
                <div class="dp-arrows">
                    <button type="button" class="dp-arrow dp-arrow-prev" id="dp-prev" aria-label="Anterior">
                        <span></span>
                    </button>
                    <button type="button" class="dp-arrow dp-arrow-next" id="dp-next" aria-label="Siguiente">
                        <span></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Reassurance Strip -->
        <div class="dp-features-strip">
            <div class="dp-feature-item">
                <img class="dp-feature-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/reloj.svg' ); ?>" alt="">
                <div>
                    <strong>Entrega el mismo día</strong>
                    <p>En comunas de Santiago, comprando antes de las 12:00.</p>
                </div>
            </div>
            <div class="dp-feature-divider"></div>
            <div class="dp-feature-item">
                <img class="dp-feature-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/rayo.svg' ); ?>" alt="">
                <div>
                    <strong>Activación inmediata</strong>
                    <p>Llega lista para vender y conectada al SII.</p>
                </div>
            </div>
            <div class="dp-feature-divider"></div>
            <div class="dp-feature-item">
                <img class="dp-feature-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/camion.svg' ); ?>" alt="">
                <div>
                    <strong>Despacho a todo Chile</strong>
                    <p>A todo Chile.</p>
                </div>
            </div>
            <div class="dp-feature-divider"></div>
            <div class="dp-feature-item">
                <img class="dp-feature-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/audifono.svg' ); ?>" alt="">
                <div>
                    <strong>Soporte personalizado</strong>
                    <p>WhatsApp directo y visita presencial en Santiago.</p>
                </div>
            </div>
        </div>

        <!-- Advisory Band -->
        <div class="dp-cta-banner">
            <div>
                <strong>¿No sabes cuál elegir?</strong>
                <p>Cuéntanos qué vendes y te recomendamos la correcta en minutos.</p>
            </div>
            <a href="<?php echo tc_whatsapp_url(); ?>" target="_blank" rel="noopener" class="dp-cta-btn"><span class="btn-label-mask"><span class="btn-label">Asesórate por WhatsApp</span><span class="btn-label-ghost" aria-hidden="true">Asesórate por WhatsApp</span></span></a>
        </div>
    </div>
</section>