<?php
/** Template Name: Información legal */
defined('ABSPATH') || exit;
get_header();
while (have_posts()) : the_post(); ?>
    <header class="legal-hero container">
        <p>INFORMACIÓN LEGAL</p>
        <h1><?php the_title(); ?></h1>
        <nav aria-label="Páginas legales"><a href="<?php echo esc_url(home_url('/terminos/')); ?>" <?php if (is_page('terminos')) echo 'aria-current="page"'; ?>>Términos y condiciones</a><a href="<?php echo esc_url(home_url('/privacidad/')); ?>" <?php if (is_page('privacidad')) echo 'aria-current="page"'; ?>>Política de privacidad</a></nav>
    </header>
    <div class="container legal-layout">
        <aside class="legal-index"><h2>En esta página</h2><nav aria-label="Secciones del documento"></nav></aside>
        <article class="legal-content"><?php the_content(); ?></article>
    </div>
<?php endwhile;
get_footer();
