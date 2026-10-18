<?php
/** Presentation hooks; WooCommerce owns catalog, checkout and account behavior. */
defined('ABSPATH') || exit;
add_filter('loop_shop_columns', function() { return 4; });
add_filter('loop_shop_per_page', function() { return 12; });
add_filter('woocommerce_page_title', function($title) { if (is_search()) { return 'Resultados para: ' . get_search_query(false); } $slug = get_query_var('product_cat'); if ($slug) { $term = get_term_by('slug', $slug, 'product_cat'); if ($term && !is_wp_error($term)) { return $term->name; } } return is_shop() ? 'Todos los productos' : $title; });
add_action('woocommerce_before_shop_loop', function() {
    echo '<form class="gamilea-category-filter" method="get" action="' . esc_url(wc_get_page_permalink('shop')) . '"><label for="gamilea-category">Categoría</label><select id="gamilea-category" name="product_cat"><option value="">Todas las categorías</option>';
    foreach (tienda_categories() as $slug => $data) { echo '<option value="' . esc_attr($slug) . '" ' . selected(get_query_var('product_cat'),$slug,false) . '>' . esc_html($data[0]) . '</option>'; }
    echo '</select><button class="button" type="submit">Filtrar</button></form>';
}, 15);
add_action('wp', function() {
    if (!is_product()) { return; }
    $product = wc_get_product(get_queried_object_id());
    if (!$product || !$product->get_meta('_tienda_demo')) { return; }
    $groups = array('AUDIO'=>0,'BEAUTY'=>1,'HOME'=>2,'BABY'=>3,'HEALTH'=>4);
    $parts = explode('-', $product->get_sku());
    if (!isset($groups[$parts[1] ?? ''])) { return; }
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    add_action('woocommerce_before_single_product_summary', function() use($product,$groups,$parts) {
        echo '<div class="images gamilea-detail-photo"><span class="product-sprite sprite-' . (int)$groups[$parts[1]] . '">' . gamilea_image('imgPhoto1.png','',$product->get_name()) . '</span></div>';
    },20);
});
add_filter('woocommerce_get_catalog_ordering_args', function($args,$orderby) {
    if ('menu_order' === $orderby) { $args['orderby']='menu_order ID'; $args['order']='ASC'; }
    return $args;
},10,2);

add_filter('document_title_parts',function($parts){ $parts['site']='GA·MI·LEA'; if(is_front_page()){ $parts['title']='GA·MI·LEA'; } return $parts; });

/** Use the FAQ page's full-width shell and inset content throughout commerce. */
function gamilea_is_commerce_page() {
    return function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page());
}
add_action('wp_enqueue_scripts', function () {
    if (!gamilea_is_commerce_page()) { return; }
    wp_enqueue_style('gamilea-commerce-layout', get_template_directory_uri() . '/assets/commerce-layout.css', array('gamilea-design'), (string) filemtime(get_template_directory() . '/assets/commerce-layout.css'));
}, 30);

/** Shared content width and enhanced catalog filters. */
add_filter('body_class', function ($classes) {
    if (is_shop() || is_product_taxonomy()) { $classes[] = 'gamilea-shop-page'; }
    if (gamilea_is_commerce_page()) { $classes[] = 'gamilea-commerce-page'; }
    return $classes;
});
add_action('wp_enqueue_scripts', function () {
    if (!is_shop() && !is_product_taxonomy()) { return; }
    wp_enqueue_style('gamilea-shop', get_template_directory_uri() . '/assets/shop.css', array('gamilea-design'), (string) filemtime(get_template_directory() . '/assets/shop.css'));
    wp_enqueue_script('gamilea-shop', get_template_directory_uri() . '/assets/shop.js', array('jquery'), (string) filemtime(get_template_directory() . '/assets/shop.js'), true);
});

// Group the catalog controls explicitly instead of relying on floated forms.
add_action('woocommerce_before_shop_loop', function () {
    if (is_shop() || is_product_taxonomy()) { echo '<div class="gamilea-shop-toolbar">'; }
}, 14);
add_action('woocommerce_before_shop_loop', function () {
    if (is_shop() || is_product_taxonomy()) { echo '</div>'; }
}, 31);
