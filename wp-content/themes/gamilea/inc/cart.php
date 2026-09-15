<?php
/** Cart presentation. WooCommerce keeps ownership of the cart contents, totals and Store API. */
defined( 'ABSPATH' ) || exit;

function gamilea_is_cart_page() {
    return function_exists( 'is_cart' ) && is_cart() && ! is_checkout();
}

add_action( 'wp_enqueue_scripts', function () {
    if ( ! gamilea_is_cart_page() ) { return; }
    wp_enqueue_style( 'gamilea-cart', get_template_directory_uri() . '/assets/cart.css', array( 'gamilea-design', 'gamilea-commerce-layout' ), (string) filemtime( get_template_directory() . '/assets/cart.css' ) );
    wp_enqueue_script( 'gamilea-cart', get_template_directory_uri() . '/assets/cart.js', array(), (string) filemtime( get_template_directory() . '/assets/cart.js' ), true );
    wp_localize_script( 'gamilea-cart', 'gamileaCart', array(
        'singular' => __( '%d producto · El envío ya está incluido', 'gamilea' ),
        'plural'   => __( '%d productos · El envío ya está incluido', 'gamilea' ),
    ) );
} );

/** Summary line under the page title, kept in sync client-side by assets/cart.js. */
add_filter( 'the_content', function ( $content ) {
    if ( ! gamilea_is_cart_page() || ! is_main_query() || ! in_the_loop() ) { return $content; }
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) { return $content; }
    $count = (int) WC()->cart->get_cart_contents_count();
    $text  = $count
        ? sprintf( _n( '%d producto · El envío ya está incluido', '%d productos · El envío ya está incluido', $count, 'gamilea' ), $count )
        : __( 'Todavía no has añadido productos.', 'gamilea' );
    return '<p class="gamilea-cart-intro">' . esc_html( $text ) . '</p>' . $content;
}, 20 );

/**
 * The empty cart and cross-sells headings live in the page's block content, which WooCommerce
 * creates in English and gettext never sees. Rewriting the rendered headings keeps the page in
 * Spanish without editing the page every time the store is set up again.
 */
add_filter( 'the_content', function ( $content ) {
    if ( ! gamilea_is_cart_page() || ! is_main_query() || ! in_the_loop() ) { return $content; }
    $headings = array(
        'Your cart is currently empty!' => __( 'Tu carrito está vacío', 'gamilea' ),
        'New in store'                  => __( 'Novedades en la tienda', 'gamilea' ),
        'You may be interested in&hellip;' => __( 'También te puede interesar', 'gamilea' ),
        'You may be interested in…'       => __( 'También te puede interesar', 'gamilea' ),
    );
    return str_replace( array_keys( $headings ), array_values( $headings ), $content );
}, 30 );

/**
 * The cart blocks render client-side, so their wording comes from WooCommerce's script
 * translations rather than gettext. Rewriting the loaded catalogue keeps the labels in the
 * store's voice without touching the blocks themselves. Cart page only: the same strings are
 * shared with the checkout and mini cart.
 */
add_filter( 'load_script_translations', function ( $translations, $file, $handle, $domain ) {
    if ( 'woocommerce' !== $domain || ! gamilea_is_cart_page() || ! $translations ) { return $translations; }

    $labels = array(
        'Cart totals'         => 'Resumen del pedido',
        'Subtotal'            => 'Productos',
        'Estimated total'     => 'Total',
        'Proceed to Checkout' => 'Continuar al pago',
        'Add coupons'         => '¿Tienes un cupón?',
        'Enter code'          => 'Introduce tu código',
    );

    $data = json_decode( $translations, true );
    if ( ! is_array( $data ) || empty( $data['locale_data'] ) || ! is_array( $data['locale_data'] ) ) { return $translations; }

    $changed = false;
    foreach ( $data['locale_data'] as $catalogue => $messages ) {
        if ( ! is_array( $messages ) ) { continue; }
        foreach ( $labels as $source => $replacement ) {
            if ( ! isset( $messages[ $source ] ) ) { continue; }
            $data['locale_data'][ $catalogue ][ $source ] = array( $replacement );
            $changed = true;
        }
    }

    return $changed ? wp_json_encode( $data ) : $translations;
}, 10, 4 );
