<div class="announcement-bar">
    <div class="announcement-viewport">
        <div class="announcement-track">
            <?php
            // Set 1 (real) + Set 2 (duplicado, oculto a lectores de pantalla) —
            // en mobile la marquesina anima el track; en desktop el Set 2 se
            // oculta vía CSS y se ve igual que siempre (centrado, estático).
            for ( $set = 1; $set <= 2; $set++ ) :
                ?>
                <div class="announcement-content"<?php echo 2 === $set ? ' aria-hidden="true"' : ''; ?>>
                    <img class="announcement-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icons/award.svg' ); ?>" alt="">
                    <span>Haulmer Partner Award 2026</span>
                    <span class="dot">·</span>
                    <span>Distribuidor Autorizado TUU</span>
                    <span class="dot">·</span>
                    <span>Despacho a todo Chile y entrega en Santiago el mismo día</span>
                </div>
                <?php
            endfor;
            ?>
        </div>
    </div>
</div>
