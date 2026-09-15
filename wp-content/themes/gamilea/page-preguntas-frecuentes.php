<?php
/**
 * Template Name: Preguntas frecuentes
 *
 * Adaptado de Figma 210:2332. Todo el contenido vive en el grupo de campos ACF
 * "Preguntas frecuentes", anclado a esta plantilla (ver inc/faq.php).
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();
    $items      = gamilea_faq_items();
    $categories = gamilea_faq_categories();
    $cta_url    = gamilea_link_url( gamilea_field( 'faq_cta_url', '/contacto/' ) );
    /*
     * El filtro inicial se resuelve en PHP, no en JS: así los enlaces del pie que apuntan
     * a ?categoria=… llegan ya filtrados aunque el JS no haya cargado. A partir de ahí
     * las pestañas siguen filtrando en el navegador.
     */
    $active     = gamilea_faq_active_category( $categories );
    $visible    = array();
    foreach ( $items as $i => $item ) {
        if ( ! $active || in_array( $active, $item['categories'], true ) ) { $visible[] = $i; }
    }
    $first_open = $visible ? $visible[0] : -1;
    ?>
    <section class="faq-hero">
        <div class="faq-inner">
            <?php $eyebrow = gamilea_field( 'faq_eyebrow', 'Centro de Soporte' ); ?>
            <?php if ( $eyebrow ) : ?><p class="faq-eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
            <h1><?php echo esc_html( gamilea_field( 'faq_title', 'Preguntas Frecuentes' ) ); ?></h1>
            <p class="faq-lede"><?php echo nl2br( esc_html( gamilea_field( 'faq_description', '' ) ) ); ?></p>
        </div>
    </section>

    <?php if ( $categories ) : ?>
    <nav class="faq-tabs" aria-label="Filtrar preguntas por categoría">
        <div class="faq-inner faq-tabs-row">
            <button type="button" class="faq-tab" data-category="" aria-pressed="<?php echo $active ? 'false' : 'true'; ?>"><?php echo esc_html( gamilea_field( 'faq_all_label', 'Todas' ) ); ?></button>
            <?php foreach ( $categories as $category ) : ?>
                <button type="button" class="faq-tab" data-category="<?php echo esc_attr( $category->slug ); ?>" aria-pressed="<?php echo $active === $category->slug ? 'true' : 'false'; ?>"><?php echo esc_html( $category->name ); ?></button>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php endif; ?>

    <section class="faq-grid">
        <div class="faq-inner">
            <?php if ( $items ) : ?>
            <div class="faq-list">
                <?php foreach ( $items as $i => $item ) : ?>
                    <details class="faq-item" name="faq" data-categories="<?php echo esc_attr( implode( ' ', $item['categories'] ) ); ?>" <?php echo in_array( $i, $visible, true ) ? '' : 'hidden'; ?> <?php echo $i === $first_open ? 'open' : ''; ?>>
                        <summary>
                            <span class="faq-question"><?php echo esc_html( $item['question'] ); ?></span>
                            <span class="faq-glyph" aria-hidden="true"></span>
                        </summary>
                        <div class="faq-answer"><?php echo wp_kses_post( $item['answer'] ); ?></div>
                    </details>
                <?php endforeach; ?>
            </div>
            <p class="faq-empty" <?php echo $visible ? 'hidden' : ''; ?>>No hay preguntas en esta categoría.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="faq-cta-section">
        <div class="faq-inner">
            <div class="faq-cta">
                <h2><?php echo esc_html( gamilea_field( 'faq_cta_title', '¿Aún tienes dudas sin resolver?' ) ); ?></h2>
                <p><?php echo nl2br( esc_html( gamilea_field( 'faq_cta_description', '' ) ) ); ?></p>
                <?php $cta_label = gamilea_field( 'faq_cta_label', 'Contáctanos' ); ?>
                <?php if ( $cta_label && $cta_url ) : ?>
                    <a class="button faq-cta-button" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
endwhile;

get_footer();
