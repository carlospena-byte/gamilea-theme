<?php
/** El Salvador departments and their cascading Departamento -> Ciudad checkout fields. */
defined('ABSPATH') || exit;

function gamilea_sv_locations() {
    static $data = null;
    if (null === $data) {
        $data = include get_template_directory() . '/inc/data/el-salvador-locations.php';
    }
    return $data;
}

/** Every unique town name across all departments, sorted; a handful of names repeat (e.g. two "San Lorenzo"). */
function gamilea_sv_towns() {
    static $towns = null;
    if (null === $towns) {
        $towns = array();
        foreach (gamilea_sv_locations() as $dept) {
            $towns = array_merge($towns, $dept['towns']);
        }
        $towns = array_values(array_unique($towns));
        sort($towns, SORT_STRING | SORT_FLAG_CASE);
    }
    return $towns;
}

add_filter('woocommerce_states', function ($states) {
    $states['SV'] = array();
    foreach (gamilea_sv_locations() as $code => $dept) {
        $states['SV'][$code] = $dept['name'];
    }
    return $states;
});

/** The store only sells to El Salvador, so every new checkout should start there instead of on an empty placeholder. */
add_action('init', function () {
    if ('base' !== get_option('woocommerce_default_customer_address')) {
        update_option('woocommerce_default_customer_address', 'base');
    }
    if ('SV' !== get_option('woocommerce_default_country')) {
        update_option('woocommerce_default_country', 'SV');
    }
});

/**
 * Hides the core free-text city field for SV (the select registered below replaces it) and the
 * postcode field (not part of how Salvadoran addresses are given), and reorders País > Nombre >
 * Departamento > Dirección > Teléfono to match local convention instead of WooCommerce's generic
 * default order. Ciudad itself can't be reordered this way — WooCommerce always appends custom
 * fields after every core field — so assets/checkout-locations.js moves its DOM node instead.
 */
add_filter('woocommerce_get_country_locale', function ($locale) {
    $locale['SV']['city']['hidden'] = true;
    $locale['SV']['city']['required'] = false;
    $locale['SV']['postcode']['hidden'] = true;
    $locale['SV']['postcode']['required'] = false;
    $locale['SV']['state']['priority'] = 45;
    $locale['SV']['address_1']['priority'] = 50;
    $locale['SV']['address_2']['priority'] = 60;
    $locale['SV']['phone']['priority'] = 70;
    return $locale;
});

/**
 * Product (and variation) IDs managed by the Drop Connector plugin (soydrop.com dropshipping
 * sync — see drop-woo-connector/includes/class-dwc-product-sync.php, META_MANAGED). A cart that
 * contains any of these needs Drop's own Departamento/Municipio fields: Drop's courier network
 * only accepts opaque state/city ids from its own live geography API, not free-text town names,
 * so our Ciudad field must stay out of the way rather than show a second, conflicting selector.
 * This is a plain product-meta query — safe to run at any point, unlike checking the cart itself
 * (WC_Cart isn't reliably loaded yet this early in the request, particularly for the Store API
 * checkout submission request rather than a normal page load).
 */
function gamilea_drop_managed_product_ids() {
    static $ids = null;
    if (null === $ids) {
        $ids = get_posts(array(
            'post_type' => array('product', 'product_variation'),
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_dwc_managed',
            'meta_value' => 'yes',
        ));
    }
    return $ids;
}

/**
 * Replaces the free-text city field with a Ciudad select. Every town name is registered so the
 * field still works without JS, but assets/checkout-locations.js hides all of them until a
 * Departamento is chosen, then shows only that department's towns (data localized below).
 *
 * The 'hidden' rule below is a JSON Schema condition WooCommerce evaluates itself, fresh, against
 * the real cart at render/validation time (see Automattic\WooCommerce\Blocks\...\DocumentObject) —
 * that's what makes it safe to set up this early: unlike a plain PHP `if`, it isn't evaluated now.
 * "Hidden fields cannot be required" is WooCommerce's own rule, so this also relaxes 'required'.
 */
add_action('woocommerce_init', function () {
    if (!function_exists('woocommerce_register_additional_checkout_field')) { return; }
    $options = array();
    foreach (gamilea_sv_towns() as $town) {
        $options[] = array('value' => $town, 'label' => $town);
    }
    $field = array(
        'id' => 'gamilea/city',
        'label' => 'Ciudad',
        'location' => 'address',
        'type' => 'select',
        'required' => true,
        'options' => $options,
    );
    $drop_ids = gamilea_drop_managed_product_ids();
    if ($drop_ids) {
        $field['hidden'] = array(
            'cart' => array(
                'properties' => array(
                    'items' => array('contains' => array('enum' => array_values($drop_ids))),
                ),
            ),
        );
    }
    woocommerce_register_additional_checkout_field($field);
});

add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_checkout') || !is_checkout()) { return; }
    $path = get_template_directory() . '/assets/checkout-locations.js';
    wp_enqueue_script('gamilea-checkout-locations', get_template_directory_uri() . '/assets/checkout-locations.js', array(), (string) filemtime($path), true);
    $towns_by_department = array();
    foreach (gamilea_sv_locations() as $dept) {
        $towns_by_department[$dept['name']] = $dept['towns'];
    }
    wp_localize_script('gamilea-checkout-locations', 'gamileaLocations', $towns_by_department);

    $trust_css_path = get_template_directory() . '/assets/checkout-trust.css';
    wp_enqueue_style('gamilea-checkout-trust', get_template_directory_uri() . '/assets/checkout-trust.css', array(), (string) filemtime($trust_css_path));

    $trust_js_path = get_template_directory() . '/assets/checkout-trust.js';
    wp_enqueue_script('gamilea-checkout-trust', get_template_directory_uri() . '/assets/checkout-trust.js', array(), (string) filemtime($trust_js_path), true);
});
