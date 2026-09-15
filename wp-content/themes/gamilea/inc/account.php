<?php
/** Account presentation. Authentication, forms and endpoints remain owned by WooCommerce. */
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) { return; }
    wp_enqueue_style( 'gamilea-account', get_template_directory_uri() . '/assets/account.css', array( 'gamilea-design', 'gamilea-commerce-layout' ), (string) filemtime( get_template_directory() . '/assets/account.css' ) );
} );

add_filter( 'the_content', function ( $content ) {
    if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || ! is_main_query() || ! in_the_loop() ) { return $content; }
    $description = is_user_logged_in()
        ? __( 'Tus pedidos, tus datos y todo lo que necesitas para tu próxima compra.', 'gamilea' )
        : ( is_wc_endpoint_url( 'lost-password' )
            ? __( 'Te ayudamos a recuperar el acceso a tu cuenta.', 'gamilea' )
            : __( 'Qué bueno tenerte aquí. Accede para consultar tus pedidos y comprar con más facilidad.', 'gamilea' ) );
    return '<p class="gamilea-account-intro">' . esc_html( $description ) . '</p>' . $content;
}, 20 );

add_action( 'woocommerce_account_content', function () {
    global $wp;
    $endpoint = WC()->query->get_current_endpoint();
    if ( ! $endpoint ) { return; }
    $title = WC()->query->get_endpoint_title( $endpoint, isset( $wp->query_vars[ $endpoint ] ) ? $wp->query_vars[ $endpoint ] : '' );
    if ( $title ) { echo '<h2 class="gamilea-account-section-title">' . esc_html( $title ) . '</h2>'; }
}, 5 );

/**
 * WooCommerce stores the registration privacy notice as an option, saved in English when the store
 * is installed, so gettext never sees it. Translating it here keeps the theme self contained.
 */
add_filter( 'option_woocommerce_registration_privacy_policy_text', function ( $text ) {
    if ( ! is_string( $text ) || false === strpos( $text, 'Your personal data will be used' ) ) { return $text; }
    return __( 'Usaremos tus datos para gestionar tu cuenta, dar seguimiento a tus pedidos y mejorar tu experiencia, como se explica en nuestra [privacy_policy].', 'gamilea' );
} );

/** The notice reads as fine print, so form-login.php renders it after the button instead. */
add_action( 'wp', function () {
    if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || is_user_logged_in() ) { return; }
    remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
} );
