<?php
/** Theme setup and assets. */
defined( 'ABSPATH' ) || exit;

/** GA·MI·LEA requires WooCommerce and Advanced Custom Fields PRO. */
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'activate_plugins' ) ) { return; }
    $missing = array();
    if ( ! class_exists( 'WooCommerce' ) ) { $missing['woocommerce'] = __( 'WooCommerce', 'gamilea' ); }
    if ( ! class_exists( 'ACF' ) ) { $missing['advanced-custom-fields-pro/acf.php'] = __( 'Advanced Custom Fields PRO', 'gamilea' ); }
    if ( ! $missing ) { return; }
    echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'GA·MI·LEA', 'gamilea' ) . '</strong>: ' . esc_html( sprintf(
        /* translators: %s: comma separated list of missing plugin names. */
        __( 'este tema requiere %s. Instálalos y actívalos para que la portada y sus bloques funcionen correctamente.', 'gamilea' ),
        implode( ', ', $missing )
    ) ) . '</p></div>';
} );

add_action( 'after_setup_theme', function () {
    load_theme_textdomain( 'gamilea', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    register_nav_menus( array( 'primary' => __( 'Menú principal', 'gamilea' ) ) );
} );
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'gamilea-design', get_template_directory_uri() . '/assets/gamilea.css', array('gamilea'), (string) filemtime(get_template_directory() . '/assets/gamilea.css') );
    wp_enqueue_style( 'gamilea', get_stylesheet_uri(), array(), (string) filemtime( get_stylesheet_directory() . '/style.css' ) );
} );
function gamilea_navigation() {
    echo '<ul class="menu">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Inicio', 'gamilea' ) . '</a></li>';
    if ( function_exists( 'wc_get_page_permalink' ) ) {
        foreach ( array( 'shop' => __( 'Tienda', 'gamilea' ), 'cart' => __( 'Carrito', 'gamilea' ), 'myaccount' => __( 'Mi cuenta', 'gamilea' ) ) as $page => $label ) {
            echo '<li><a href="' . esc_url( wc_get_page_permalink( $page ) ) . '">' . esc_html( $label ) . '</a></li>';
        }
    }
    echo '</ul>';
}

/** Used only while no menu is assigned to the "primary" location in Appearance > Menus. */
function gamilea_primary_menu_fallback() {
    echo '<ul class="menu">';
    foreach ( array( 'salud' => 'Salud', 'cuidado-y-belleza' => 'Cuidado y Belleza', 'tecnologia' => 'Tecnología', 'hogar' => 'Hogar', 'bebe' => 'Bebé' ) as $slug => $label ) {
        echo '<li><a href="' . esc_url( tienda_category_url( $slug ) ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '<li><a class="offer-link" href="' . esc_url( home_url( '/?collection=sale#bestsellers' ) ) . '">' . esc_html__( 'Ofertas', 'gamilea' ) . '</a></li>';
    echo '</ul>';
}

require_once get_template_directory() . '/inc/icons.php';
function tienda_asset( $name ) { return get_template_directory_uri() . '/assets/images/' . $name . '.jpg'; }
function tienda_category_url( $slug ) {
    $term = get_term_by( 'slug', $slug, 'product_cat' );
    return $term ? get_term_link( $term ) : home_url( '/shop/' );
}
function tienda_categories() {
    return array(
        'salud' => array( 'Salud', 'Bienestar para tu día a día', 'vitamins' ),
        'cuidado-y-belleza' => array( 'Cuidado y Belleza', 'Siéntete bien, siempre', 'skincare' ),
        'tecnologia' => array( 'Tecnología', 'Lo último, al alcance de tus manos', 'headphones' ),
        'hogar' => array( 'Artículos del Hogar', 'Un hogar más cómodo y práctico', 'airfryer' ),
        'bebe' => array( 'Artículos para Bebé', 'Todo para sus primeros momentos', 'stroller' ),
    );
}

/** "Drop" only tags dropshipping-sourced products internally; it must never be shown as a browsable category anywhere. */
function gamilea_hidden_category_id() {
    static $id = null;
    if (null === $id) {
        /**
         * Not get_term_by(): in this WP version it's implemented on top of get_terms(),
         * which would re-enter the get_terms_args filter below and exhaust memory.
         */
        global $wpdb;
        $id = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT t.term_id FROM {$wpdb->terms} t INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'product_cat' AND (t.slug = %s OR t.name = %s) LIMIT 1",
            'drop', 'Drop'
        ) );
    }
    return $id;
}
add_filter( 'get_terms_args', function ( $args, $taxonomies ) {
    if ( is_admin() || ! in_array( 'product_cat', (array) $taxonomies, true ) ) { return $args; }
    $hidden_id = gamilea_hidden_category_id();
    if ( $hidden_id ) { $args['exclude'] = array_unique( array_merge( (array) ( $args['exclude'] ?? array() ), array( $hidden_id ) ) ); }
    return $args;
}, 10, 2 );

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_script( 'tienda-home', get_template_directory_uri() . '/assets/home.js', array(), (string) filemtime( get_template_directory() . '/assets/home.js' ), true );
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_script( 'wc-add-to-cart' );
        wp_enqueue_script( 'wc-cart-fragments' );
    }
} );
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
    $fragments['span.cart-count'] = '<span class="cart-count">' . esc_html( WC()->cart->get_cart_contents_count() ) . '</span>';
    return $fragments;
} );
function tienda_product_card( $product ) {
    if ( ! $product ) { return; }
    $id = $product->get_id();
    $terms = get_the_terms( $id, 'product_cat' );
    $badge = $product->get_meta( '_tienda_badge' );
    ?>
    <article class="product-card" data-product-id="<?php echo esc_attr( $id ); ?>" data-product-name="<?php echo esc_attr($product->get_name()); ?>">
        <div class="product-photo">
            <?php if ( $badge ) : ?><span class="product-badge <?php echo $product->is_on_sale() ? 'sale' : ( 'Nuevo' === $badge ? 'new' : '' ); ?>"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
            <button class="favorite-toggle icon-button" type="button" data-id="<?php echo esc_attr( $id ); ?>" aria-label="<?php echo esc_attr( 'Guardar ' . $product->get_name() . ' en favoritos' ); ?>" aria-pressed="false"><?php echo tienda_icon( 'heart' ); ?></button>
            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true"><?php
$demo_images = array('DEMO-AUDIO-01'=>0,'DEMO-BEAUTY-01'=>1,'DEMO-HOME-01'=>2,'DEMO-BABY-01'=>3,'DEMO-HEALTH-01'=>4);
if (isset($demo_images[$product->get_sku()])) { echo '<span class="product-sprite sprite-' . (int)$demo_images[$product->get_sku()] . '">' . gamilea_image('imgPhoto1.png', '', $product->get_name()) . '</span>'; }
else { echo $product->get_image('woocommerce_thumbnail', array('loading'=>'lazy')); }
?></a>
        </div>
        <div class="product-info">
            <span class="product-category"><?php echo esc_html( ! is_wp_error( $terms ) && $terms ? $terms[0]->name : 'Tienda' ); ?></span>
            <h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><span class="desktop-label"><?php echo gamilea_product_title($product); ?></span><span class="mobile-label"><?php $short_names = array('DEMO-AUDIO-01'=>'Audífonos Bluetooth','DEMO-BEAUTY-01'=>'Cuidado facial','DEMO-HOME-01'=>'Freidora de aire','DEMO-BABY-01'=>'Coche para bebé'); echo esc_html($short_names[$product->get_sku()] ?? $product->get_name()); ?></span></a></h3>
            <div class="product-rating"><span aria-hidden="true"><?php echo $product->get_rating_count() ? '★★★★★' : '☆☆☆☆☆'; ?></span><small><?php echo $product->get_rating_count() ? esc_html($product->get_average_rating() . ' (' . $product->get_rating_count() . ')') : 'Sin reseñas'; ?></small></div>
            <div class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
            <p class="shipping-note"><?php echo tienda_icon( 'truck' ); ?> Envío incluido</p>
            <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="button product_type_<?php echo esc_attr($product->get_type()); ?> <?php echo $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : ''; ?> <?php echo $product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : ''; ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $id ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" aria-label="<?php echo esc_attr( 'Agregar al carrito: ' . $product->get_name() ); ?>" rel="nofollow"><span class="desktop-label"><?php echo $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock() ? 'Agregar al carrito' : esc_html($product->add_to_cart_text()); ?></span><span class="mobile-label"><?php echo $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock() ? 'Agregar' : esc_html($product->add_to_cart_text()); ?></span></a>
        </div>
    </article>
    <?php
}
// Local subscriptions are kept in WordPress; this does not send email.
add_action( 'admin_post_nopriv_tienda_subscribe', 'tienda_subscribe' );
add_action( 'admin_post_tienda_subscribe', 'tienda_subscribe' );
function tienda_subscribe() {
    if ( ! isset( $_POST['tienda_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tienda_nonce'] ) ), 'tienda_subscribe' ) ) { wp_die( 'Recarga la página y vuelve a intentarlo.', 'Solicitud no válida', array( 'response' => 403 ) ); }
    $email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    if ( ! is_email( $email ) || empty( $_POST['consent'] ) ) { wp_safe_redirect( home_url( '/?subscription=invalid#newsletter' ) ); exit; }
    $key = 'tienda_subscriber_' . hash( 'sha256', strtolower( $email ) );
    if ( ! get_option( $key ) ) { add_option( $key, array( 'email' => $email, 'date' => current_time( 'mysql' ), 'consent' => true ), '', false ); }
    wp_safe_redirect( home_url( '/?subscription=success#newsletter' ) ); exit;
}

/** Normaliza un enlace escrito en un campo ACF: acepta rutas del sitio, mailto:, tel: y URLs completas. */
function gamilea_link_url($url, $fallback = '') {
    $url = trim((string) $url);
    if ('' === $url) { $url = $fallback; }
    if ('' === $url) { return ''; }
    if (preg_match('#^(https?:|mailto:|tel:|\#)#i', $url)) { return $url; }
    return home_url($url);
}
function gamilea_asset($name) { return get_template_directory_uri() . '/assets/figma/' . $name; }
function gamilea_image($name, $class = '', $alt = '') { return '<img class="' . esc_attr($class) . '" src="' . esc_url(gamilea_asset($name)) . '" alt="' . esc_attr($alt) . '">'; }
function gamilea_crop($class, $alt = '') { return '<span class="figma-crop ' . esc_attr($class) . '">' . gamilea_image('imgPhoto.png', '', $alt) . '</span>'; }
function gamilea_logo() { return '<a class="gamilea-logo" href="' . esc_url(home_url('/')) . '" aria-label="GA·MI·LEA — Inicio">' . gamilea_image('imgGroup.svg','logo-monogram') . gamilea_image('imgVector.svg','logo-divider') . '<span>GA·MI·LEA</span></a>'; }
require_once get_template_directory() . '/inc/gamilea-commerce.php';

function gamilea_product_title($product) {
    $titles = array('DEMO-AUDIO-01'=>array('Audífonos Bluetooth','Noise Cancelling'),'DEMO-BEAUTY-01'=>array('Set de Cuidado Facial','Hidratante'),'DEMO-BABY-01'=>array('Coche para Bebé','Ultraligero'),'DEMO-HEALTH-01'=>array('Suplemento Multivitamínico','para Adultos'));
    return isset($titles[$product->get_sku()]) ? implode('<br>',array_map('esc_html',$titles[$product->get_sku()])) : esc_html($product->get_name());
}

require_once get_template_directory() . '/inc/blocks.php';
require_once get_template_directory() . '/inc/options.php';
require_once get_template_directory() . '/inc/mobile-menu.php';
require_once get_template_directory() . '/inc/recovery.php';
require_once get_template_directory() . '/inc/checkout-locations.php';
require_once get_template_directory() . '/inc/order-success.php';
require_once get_template_directory() . '/inc/contact.php';
require_once get_template_directory() . '/inc/faq.php';
require_once get_template_directory() . '/inc/about.php';

add_filter('body_class', function ($classes) {
    if (is_page_template('page-legal.php')) { $classes[] = 'gamilea-legal-page'; }
    return $classes;
});
add_action('wp_enqueue_scripts', function () {
    if (!is_page_template('page-legal.php')) { return; }
    wp_enqueue_style('gamilea-legal', get_template_directory_uri() . '/assets/legal.css', array('gamilea-design'), (string) filemtime(get_template_directory() . '/assets/legal.css'));
    wp_enqueue_script('gamilea-legal', get_template_directory_uri() . '/assets/legal.js', array(), (string) filemtime(get_template_directory() . '/assets/legal.js'), true);
});
