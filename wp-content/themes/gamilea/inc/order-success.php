<?php
/** Order confirmation presentation; access checks remain in WooCommerce. */
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) { return; }
    wp_enqueue_style( 'gamilea-order-success', get_template_directory_uri() . '/assets/order-success.css', array( 'gamilea-design' ), (string) filemtime( get_template_directory() . '/assets/order-success.css' ) );
} );

function gamilea_order_icon( $name, $class = '' ) {
    return '<span class="order-icon ' . esc_attr( $class ) . '">' . gamilea_image( 'order-success/' . $name . '.svg' ) . '</span>';
}
