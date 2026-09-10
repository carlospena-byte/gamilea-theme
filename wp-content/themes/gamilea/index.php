<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<?php if ( have_posts() ) : ?>
    <?php if ( is_archive() ) : the_archive_title( '<h1>', '</h1>' ); endif; ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class( 'entry' ); ?>>
            <?php if ( is_singular() ) : ?>
                <h1><?php if (function_exists('is_cart') && is_cart()) { echo 'Tu carrito'; } elseif (function_exists('is_checkout') && is_checkout()) { echo is_order_received_page() ? '¡Gracias por tu compra!' : 'Finalizar compra'; } elseif (function_exists('is_account_page') && is_account_page()) { echo is_user_logged_in() ? 'Mi cuenta' : (is_wc_endpoint_url('lost-password') ? 'Recuperar contraseña' : 'Inicia sesión'); } else { the_title(); } ?></h1>
                <?php the_content(); wp_link_pages(); ?>
            <?php else : ?>
                <h2><a href="<?php the_permalink(); ?>"><?php if (function_exists('is_cart') && is_cart()) { echo 'Tu carrito'; } elseif (function_exists('is_checkout') && is_checkout()) { echo is_order_received_page() ? '¡Gracias por tu compra!' : 'Finalizar compra'; } elseif (function_exists('is_account_page') && is_account_page()) { echo is_user_logged_in() ? 'Mi cuenta' : (is_wc_endpoint_url('lost-password') ? 'Recuperar contraseña' : 'Inicia sesión'); } else { the_title(); } ?></a></h2>
                <?php the_excerpt(); ?>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
    <?php the_posts_pagination(); ?>
<?php else : ?>
    <h1><?php esc_html_e( 'No encontramos contenido', 'gamilea' ); ?></h1>
    <?php get_search_form(); ?>
<?php endif; ?>
<?php get_footer(); ?>
