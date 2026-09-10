<?php
/** Presentation hooks; WooCommerce owns catalog, checkout and account behavior. */
defined('ABSPATH') || exit;
add_filter('loop_shop_columns', function() { return 5; });
add_filter('loop_shop_per_page', function() { return 10; });
add_filter('woocommerce_page_title', function($title) { if (is_search()) { return 'Resultados para: ' . get_search_query(false); } $slug = get_query_var('product_cat'); if ($slug) { $term = get_term_by('slug', $slug, 'product_cat'); if ($term && !is_wp_error($term)) { return $term->name; } } return is_shop() ? 'Todos los productos' : $title; });
add_action('woocommerce_before_shop_loop', function() {
    echo '<form class="gamilea-category-filter" method="get" action="' . esc_url(wc_get_page_permalink('shop')) . '"><label for="gamilea-category">Categoría</label><select id="gamilea-category" name="product_cat"><option value="">Todas las categorías</option>';
    foreach (tienda_categories() as $slug => $data) { echo '<option value="' . esc_attr($slug) . '" ' . selected(get_query_var('product_cat'),$slug,false) . '>' . esc_html($data[0]) . '</option>'; }
    echo '</select><button class="button" type="submit">Filtrar</button></form>';
}, 15);
add_action('woocommerce_single_product_summary', function() {
    echo '<p class="gamilea-product-shipping">Envío incluido · Sin costos adicionales</p>';
}, 11);
add_action('woocommerce_single_product_summary', function() {
    global $product;
    echo '<div class="product-card gamilea-product-favorite" data-product-id="' . esc_attr($product->get_id()) . '"><div class="product-photo">' . $product->get_image() . '</div><h3><a href="' . esc_url($product->get_permalink()) . '">' . esc_html($product->get_name()) . '</a></h3><button class="favorite-toggle button" type="button" data-id="' . esc_attr($product->get_id()) . '" aria-pressed="false">Guardar en favoritos</button></div>';
}, 31);
add_filter('body_class',function($classes){ if(function_exists('is_account_page') && is_account_page() && !is_user_logged_in()) { $classes[]='gamilea-auth'; } return $classes; });
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

remove_action('woocommerce_single_product_summary','woocommerce_template_single_title',5);
add_action('woocommerce_single_product_summary',function(){ global $product; echo '<h1 class="product_title entry-title">' . gamilea_product_title($product) . '</h1>'; },5);
add_filter('document_title_parts',function($parts){ $parts['site']='GA·MI·LEA'; if(is_front_page()){ $parts['title']='GA·MI·LEA'; } return $parts; });
