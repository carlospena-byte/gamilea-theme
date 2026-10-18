<?php
/** Product page (PDP): ACF content per product, global shipping copy, buy-now flow and assets. */
defined('ABSPATH') || exit;

function gamilea_pdp_defaults() {
    return array(
        'ship_badge'    => 'Envío gratis',
        'ship_headline' => 'Recíbelo en 24 a 72 horas hábiles',
        'ship_detail'   => 'En todo El Salvador · Sin costos adicionales de envío',
        'buy_title'     => 'Tu compra, clara desde el inicio',
        'ship_policy'   => 'Envío gratis a todo El Salvador. Entrega en 24 a 72 horas hábiles.',
    );
}

/** Global value from the "Ficha de producto" options page, falling back to the default copy. */
function gamilea_pdp_option($key) {
    $defaults = gamilea_pdp_defaults();
    $value = function_exists('get_field') ? get_field($key, 'option') : '';
    return is_string($value) && '' !== trim($value) ? $value : ($defaults[$key] ?? '');
}

/** Per-product ACF value; empty when ACF is unavailable. */
function gamilea_pdp_field($key, $id) {
    return function_exists('get_field') ? get_field($key, $id) : '';
}

add_action('acf/init', function () {
    if (!function_exists('acf_add_options_page') || !function_exists('acf_add_local_field_group')) { return; }
    $d = gamilea_pdp_defaults();

    acf_add_options_page(array(
        'page_title' => 'Ficha de producto GA·MI·LEA', 'menu_title' => 'Ficha de producto',
        'menu_slug' => 'gamilea-product-page', 'parent_slug' => 'themes.php', 'capability' => 'edit_theme_options',
    ));
    acf_add_local_field_group(array(
        'key' => 'group_gamilea_pdp_global', 'title' => 'Envío y compra (todos los productos)',
        'fields' => array(
            array('key' => 'field_gamilea_pdp_ship_badge', 'name' => 'ship_badge', 'label' => 'Etiqueta junto al precio', 'type' => 'text', 'default_value' => $d['ship_badge']),
            array('key' => 'field_gamilea_pdp_ship_headline', 'name' => 'ship_headline', 'label' => 'Caja de entrega · Título', 'type' => 'text', 'default_value' => $d['ship_headline']),
            array('key' => 'field_gamilea_pdp_ship_detail', 'name' => 'ship_detail', 'label' => 'Caja de entrega · Detalle', 'type' => 'text', 'default_value' => $d['ship_detail']),
            array('key' => 'field_gamilea_pdp_buy_title', 'name' => 'buy_title', 'label' => 'Bloque de compra · Título', 'type' => 'text', 'default_value' => $d['buy_title']),
            array('key' => 'field_gamilea_pdp_ship_policy', 'name' => 'ship_policy', 'label' => 'Entrega y costo de envío', 'type' => 'textarea', 'rows' => 2, 'default_value' => $d['ship_policy']),
        ),
        'location' => array(array(array('param' => 'options_page', 'operator' => '==', 'value' => 'gamilea-product-page'))),
    ));

    $pair = function ($prefix, $label) {
        return array(
            array('key' => "field_gamilea_pdp_{$prefix}_title", 'name' => 'title', 'label' => 'Título', 'type' => 'text'),
            array('key' => "field_gamilea_pdp_{$prefix}_text", 'name' => 'text', 'label' => 'Texto', 'type' => 'textarea', 'rows' => 3),
        );
    };
    acf_add_local_field_group(array(
        'key' => 'group_gamilea_pdp', 'title' => 'Ficha de producto', 'position' => 'normal', 'menu_order' => 0,
        'fields' => array(
            array('key' => 'field_gamilea_pdp_brand', 'name' => 'pdp_brand', 'label' => 'Marca', 'type' => 'text', 'instructions' => 'Se muestra junto a la categoría, p. ej. BIENESTAR · TOPLUX.'),
            array('key' => 'field_gamilea_pdp_subtitle', 'name' => 'pdp_subtitle', 'label' => 'Subtítulo', 'type' => 'text', 'instructions' => 'p. ej. Toplux · 90 cápsulas · 8 formas de magnesio.'),
            array(
                'key' => 'field_gamilea_pdp_features', 'name' => 'pdp_features', 'label' => 'Características principales', 'type' => 'repeater',
                'instructions' => 'Hasta 4, se muestran bajo la galería.', 'max' => 4, 'layout' => 'table', 'button_label' => 'Añadir característica',
                'sub_fields' => array(
                    array('key' => 'field_gamilea_pdp_features_value', 'name' => 'value', 'label' => 'Dato destacado', 'type' => 'text', 'instructions' => 'p. ej. 90'),
                    array('key' => 'field_gamilea_pdp_features_label', 'name' => 'label', 'label' => 'Descripción', 'type' => 'text', 'instructions' => 'p. ej. cápsulas'),
                ),
            ),
            array('key' => 'field_gamilea_pdp_promo', 'name' => 'pdp_promo', 'label' => 'Mostrar oferta semanal', 'type' => 'true_false', 'ui' => 1, 'instructions' => 'Cuenta regresiva hasta el próximo domingo a las 11:59 p. m. (hora de El Salvador).'),
            array('key' => 'field_gamilea_pdp_promo_title', 'name' => 'pdp_promo_title', 'label' => 'Título de la oferta', 'type' => 'text', 'default_value' => 'Oferta exclusiva de la semana',
                'conditional_logic' => array(array(array('field' => 'field_gamilea_pdp_promo', 'operator' => '==', 'value' => '1')))),
            array('key' => 'field_gamilea_pdp_about_title', 'name' => 'pdp_about_title', 'label' => 'Información · Título', 'type' => 'text', 'default_value' => 'Conoce tu producto', 'instructions' => 'La introducción es la Descripción del producto de WordPress.'),
            array('key' => 'field_gamilea_pdp_about', 'name' => 'pdp_about', 'label' => 'Información · Secciones', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Añadir sección', 'sub_fields' => $pair('about', 'Sección')),
            array('key' => 'field_gamilea_pdp_purchase', 'name' => 'pdp_purchase', 'label' => 'Compra · Secciones del producto', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Añadir sección',
                'instructions' => 'Se muestran después de "Entrega y costo de envío" (global), p. ej. ¿Cómo se toma?', 'sub_fields' => $pair('purchase', 'Sección')),
            array('key' => 'field_gamilea_pdp_legal', 'name' => 'pdp_legal', 'label' => 'Aviso legal', 'type' => 'textarea', 'rows' => 2),
        ),
        'location' => array(array(array('param' => 'post_type', 'operator' => '==', 'value' => 'product'))),
    ));
});

/** Collects everything the template prints; sections without content are omitted. */
function gamilea_pdp_data($product) {
    $id = $product->get_id();
    $rows = function ($key, $keys) use ($id) {
        $out = array();
        $raw = gamilea_pdp_field($key, $id);
        foreach (is_array($raw) ? $raw : array() as $row) {
            $clean = array();
            foreach ($keys as $k) { $clean[$k] = isset($row[$k]) ? trim((string) $row[$k]) : ''; }
            if (array_filter($clean)) { $out[] = $clean; }
        }
        return $out;
    };

    $category = '';
    $terms = get_the_terms($id, 'product_cat');
    foreach (is_array($terms) ? $terms : array() as $term) {
        if ('drop' === strtolower($term->slug) || 'drop' === strtolower($term->name)) { continue; }
        $category = $term->name;
        break;
    }
    $brand = trim((string) gamilea_pdp_field('pdp_brand', $id));
    $subtitle = trim((string) gamilea_pdp_field('pdp_subtitle', $id));
    if ('' === $subtitle) { $subtitle = wp_trim_words(wp_strip_all_tags($product->get_short_description()), 20); }
    $promo_title = trim((string) gamilea_pdp_field('pdp_promo_title', $id));

    return array(
        'eyebrow'     => mb_strtoupper(implode(' · ', array_filter(array($category, $brand)))),
        'subtitle'    => $subtitle,
        'features'    => $rows('pdp_features', array('value', 'label')),
        'promo'       => (bool) gamilea_pdp_field('pdp_promo', $id),
        'promo_title' => '' !== $promo_title ? $promo_title : 'Oferta exclusiva de la semana',
        'about_title' => trim((string) gamilea_pdp_field('pdp_about_title', $id)) ?: 'Conoce tu producto',
        'about_intro' => trim((string) apply_filters('the_content', $product->get_description())),
        'about'       => $rows('pdp_about', array('title', 'text')),
        'purchase'    => $rows('pdp_purchase', array('title', 'text')),
        'legal'       => trim((string) gamilea_pdp_field('pdp_legal', $id)),
    );
}

/** Plain-text price for the buy-now label; variable products get a generic label. */
function gamilea_pdp_buy_label($product) {
    if (!$product->is_type('simple') || '' === $product->get_price()) { return 'Comprar ahora'; }
    return 'Comprar ahora — ' . html_entity_decode(wp_strip_all_tags(wc_price(wc_get_price_to_display($product))), ENT_QUOTES, 'UTF-8');
}

add_action('wp', function () {
    if (!function_exists('is_product') || !is_product()) { return; }
    // Product data tabs and upsells are replaced by the ACF sections; related products stay last.
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
}, 20);

add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
});

/** Most catalogue products share no category or tag, so pad the list: related products must always close the page. */
add_filter('woocommerce_related_products', function ($ids, $product_id, $args) {
    $limit = (int) ($args['posts_per_page'] ?? 4);
    $ids = array_values(array_diff((array) $ids, array($product_id)));
    if (count($ids) >= $limit) { return $ids; }
    $extra = wc_get_products(array(
        'status' => 'publish', 'visibility' => 'catalog', 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC',
        'exclude' => array_merge(array($product_id), $ids), 'return' => 'ids',
    ));
    return array_slice(array_merge($ids, $extra), 0, $limit);
}, 10, 3);

add_filter('woocommerce_breadcrumb_defaults', function ($defaults) {
    if (is_product()) { $defaults['delimiter'] = '<span class="gamilea-crumb-sep"> / </span>'; }
    return $defaults;
});

add_filter('woocommerce_product_single_add_to_cart_text', function ($text, $product) {
    return $product->is_type(array('simple', 'variable')) ? 'Añadir al carrito' : $text;
}, 10, 2);

/** "Comprar ahora" submits the normal add-to-cart form, flagged so the redirect goes to checkout. */
add_action('woocommerce_after_add_to_cart_button', function () {
    global $product;
    if (!$product || !$product->is_type(array('simple', 'variable')) || !$product->is_purchasable() || !$product->is_in_stock()) { return; }
    echo '<button type="submit" name="add-to-cart" value="' . esc_attr($product->get_id()) . '" formaction="' . esc_url(add_query_arg('gamilea_buy_now', '1', get_permalink($product->get_id()))) . '" class="single_add_to_cart_button button alt gamilea-buy-now">' . esc_html(gamilea_pdp_buy_label($product)) . '</button>';
});
add_filter('woocommerce_add_to_cart_redirect', function ($url) {
    return !empty($_REQUEST['gamilea_buy_now']) ? wc_get_checkout_url() : $url;
});

add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_product') || !is_product()) { return; }
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    wp_enqueue_style('gamilea-product', $uri . '/assets/product.css', array('gamilea-design', 'gamilea-commerce-layout'), (string) filemtime($dir . '/assets/product.css'));
    wp_enqueue_script('gamilea-product', $uri . '/assets/product.js', array(), (string) filemtime($dir . '/assets/product.js'), true);
}, 40);
