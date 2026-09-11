<?php
/** Shared "keep shopping" content for empty states: 404 and empty category archives. */
defined('ABSPATH') || exit;

function gamilea_categories_with_products($limit = 6, $exclude_term_id = 0) {
    $terms = get_terms(array(
        'taxonomy' => 'product_cat', 'hide_empty' => true,
        'orderby' => 'count', 'order' => 'DESC', 'number' => $limit,
        'exclude' => $exclude_term_id ? array($exclude_term_id) : array(),
    ));
    return is_wp_error($terms) ? array() : $terms;
}

function gamilea_render_category_grid($terms, $heading) {
    if (!$terms) { return; }
    ?>
    <section class="categories-section">
        <div class="section-heading"><h2><?php echo esc_html($heading); ?></h2></div>
        <div class="category-grid category-grid--fit">
        <?php foreach ($terms as $term) :
            $link = get_term_link($term);
            if (is_wp_error($link)) { continue; }
            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            $image = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : '';
        ?>
            <a class="category-card category-<?php echo esc_attr(sanitize_html_class($term->slug)); ?>" href="<?php echo esc_url($link); ?>">
                <div class="category-photo <?php echo $image ? 'gamilea-custom-photo' : ''; ?>"><img src="<?php echo esc_url($image ?: gamilea_asset('imgPhoto.png')); ?>" alt="<?php echo esc_attr($term->name); ?>" loading="lazy"></div>
                <div class="category-copy"><h3><?php echo esc_html($term->name); ?></h3><p><?php echo esc_html($term->description); ?></p><span>Explorar <?php echo tienda_icon('arrow'); ?></span></div>
            </a>
        <?php endforeach; ?>
        </div>
    </section>
    <?php
}

function gamilea_render_bestsellers_grid($heading = 'Más vendidos', $limit = 5) {
    if (!function_exists('wc_get_products')) { return; }
    $products = wc_get_products(array('status' => 'publish', 'limit' => $limit, 'orderby' => 'popularity', 'order' => 'DESC'));
    if (!$products) { return; }
    ?>
    <section class="bestsellers-section">
        <div class="section-heading product-heading"><h2><?php echo esc_html($heading); ?></h2><a class="text-link" href="<?php echo esc_url(gamilea_shop_url()); ?>">Ver todos <?php echo tienda_icon('arrow'); ?></a></div>
        <div class="product-grid<?php echo count($products) < 5 ? ' product-grid--fit' : ''; ?>"><?php foreach ($products as $product) { tienda_product_card($product); } ?></div>
    </section>
    <?php
}

/** The "keep shopping" pair shown below every empty state: other categories, then bestsellers. */
function gamilea_recovery_sections($exclude_term_id = 0, $category_heading = 'Explora nuestras categorías') {
    ?><div class="home-sections"><?php
    gamilea_render_category_grid(gamilea_categories_with_products(6, $exclude_term_id), $category_heading);
    gamilea_render_bestsellers_grid();
    ?></div><?php
}

/**
 * Centered icon + message block used by 404.php and woocommerce/loop/no-products-found.php.
 * $args: icon, tag ('h1'|'h2'), title, description, search (bool),
 * primary_label, primary_url, secondary_label, secondary_url.
 */
function gamilea_empty_state($args) {
    $args = wp_parse_args($args, array(
        'icon' => 'box', 'tag' => 'h2', 'title' => '', 'description' => '', 'search' => false,
        'primary_label' => 'Ver todos los productos', 'primary_url' => gamilea_shop_url(),
        'secondary_label' => '', 'secondary_url' => '',
    ));
    $tag = 'h1' === $args['tag'] ? 'h1' : 'h2';
    ?>
    <section class="empty-state">
        <div class="empty-state-icon"><?php echo tienda_icon($args['icon']); ?></div>
        <<?php echo $tag; ?>><?php echo esc_html($args['title']); ?></<?php echo $tag; ?>>
        <?php if ($args['description']) : ?><p><?php echo esc_html($args['description']); ?></p><?php endif; ?>
        <?php if ($args['search']) : ?>
        <form class="empty-state-search" action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search">
            <label class="screen-reader-text" for="empty-state-search-input">Buscar productos</label>
            <input id="empty-state-search-input" type="search" name="s" placeholder="¿Qué estás buscando?" value="<?php echo esc_attr(get_search_query()); ?>">
            <input type="hidden" name="post_type" value="product">
            <button type="submit" aria-label="Buscar"><?php echo tienda_icon('search'); ?></button>
        </form>
        <?php endif; ?>
        <div class="empty-state-actions">
            <a class="button" href="<?php echo esc_url($args['primary_url']); ?>"><?php echo esc_html($args['primary_label']); ?></a>
            <?php if ($args['secondary_label']) : ?><a class="text-link" href="<?php echo esc_url($args['secondary_url']); ?>"><?php echo esc_html($args['secondary_label']); ?></a><?php endif; ?>
        </div>
    </section>
    <?php
}
