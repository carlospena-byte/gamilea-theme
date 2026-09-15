<?php
/**
 * Preguntas frecuentes (Figma 210:2332).
 *
 * Cada pregunta es una entrada del tipo de contenido gamilea_faq: el título es la
 * pregunta y el contenido la respuesta, sin campos propios. La categoría sale de la
 * taxonomía faq_categoria y de ahí se derivan las pestañas de la página.
 *
 * El grupo ACF que queda aquí es solo lo que pertenece a la página en sí —portada y
 * CTA—, no a las preguntas.
 */
defined( 'ABSPATH' ) || exit;

const GAMILEA_FAQ_TEMPLATE = 'page-preguntas-frecuentes.php';
const GAMILEA_FAQ_POST_TYPE = 'gamilea_faq';
const GAMILEA_FAQ_TAXONOMY = 'faq_categoria';

/**
 * No es público a propósito: las preguntas solo se muestran dentro de la página de
 * FAQ, así que no generan permalinks sueltos ni entran en el buscador del sitio.
 * show_in_rest sigue activo para poder redactar la respuesta con el editor de bloques.
 */
add_action( 'init', function () {
    register_post_type( GAMILEA_FAQ_POST_TYPE, array(
        'labels' => array(
            'name' => 'Preguntas frecuentes', 'singular_name' => 'Pregunta',
            'add_new' => 'Añadir pregunta', 'add_new_item' => 'Añadir pregunta',
            'edit_item' => 'Editar pregunta', 'new_item' => 'Nueva pregunta',
            'view_item' => 'Ver pregunta', 'search_items' => 'Buscar preguntas',
            'not_found' => 'No hay preguntas todavía', 'not_found_in_trash' => 'No hay preguntas en la papelera',
            'all_items' => 'Todas las preguntas', 'menu_name' => 'Preguntas frecuentes',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'has_archive' => false,
        'rewrite' => false,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-editor-help',
        'supports' => array( 'title', 'editor', 'page-attributes', 'revisions' ),
        'capability_type' => 'post',
    ) );

    register_taxonomy( GAMILEA_FAQ_TAXONOMY, GAMILEA_FAQ_POST_TYPE, array(
        'labels' => array(
            'name' => 'Categorías de FAQ', 'singular_name' => 'Categoría',
            'add_new_item' => 'Añadir categoría', 'edit_item' => 'Editar categoría',
            'all_items' => 'Categorías', 'menu_name' => 'Categorías',
        ),
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'publicly_queryable' => false,
        'rewrite' => false,
    ) );
} );

function gamilea_faq_default_categories() {
    return array( 'Envíos', 'Devoluciones', 'Productos', 'Pagos' );
}

add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
    acf_add_local_field_group( array(
        'key' => 'group_gamilea_faq', 'title' => 'Preguntas frecuentes',
        'menu_order' => 0, 'position' => 'normal', 'style' => 'default',
        'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => GAMILEA_FAQ_TEMPLATE ) ) ),
        'fields' => array(
            array( 'key' => 'field_gamilea_faq_eyebrow', 'name' => 'faq_eyebrow', 'label' => 'Antetítulo', 'type' => 'text', 'default_value' => 'Centro de Soporte' ),
            array( 'key' => 'field_gamilea_faq_title', 'name' => 'faq_title', 'label' => 'Título', 'type' => 'text', 'default_value' => 'Preguntas Frecuentes' ),
            array( 'key' => 'field_gamilea_faq_description', 'name' => 'faq_description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Encuentra respuestas rápidas sobre envíos, devoluciones, productos y pagos seguros.' ),
            array( 'key' => 'field_gamilea_faq_all_label', 'name' => 'faq_all_label', 'label' => 'Texto de la pestaña "todas"', 'type' => 'text', 'default_value' => 'Todas' ),
            array( 'key' => 'field_gamilea_faq_notice', 'name' => 'faq_notice', 'label' => 'Preguntas', 'type' => 'message', 'message' => 'Las preguntas se administran en <strong>Preguntas frecuentes</strong>, en el menú lateral. El orden se controla con el campo <em>Orden</em> de cada pregunta y las pestañas salen de sus categorías.' ),
            array( 'key' => 'field_gamilea_faq_cta_title', 'name' => 'faq_cta_title', 'label' => 'CTA · Título', 'type' => 'text', 'default_value' => '¿Aún tienes dudas sin resolver?' ),
            array( 'key' => 'field_gamilea_faq_cta_description', 'name' => 'faq_cta_description', 'label' => 'CTA · Descripción', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Escríbenos directamente y te ayudamos. Nuestro equipo está disponible para resolver tu caso.' ),
            array( 'key' => 'field_gamilea_faq_cta_label', 'name' => 'faq_cta_label', 'label' => 'CTA · Texto del botón', 'type' => 'text', 'default_value' => 'Contáctanos' ),
            array( 'key' => 'field_gamilea_faq_cta_url', 'name' => 'faq_cta_url', 'label' => 'CTA · Enlace del botón', 'type' => 'text', 'default_value' => '/contacto/' ),
        ),
    ) );
} );

function gamilea_is_faq_page() { return is_page_template( GAMILEA_FAQ_TEMPLATE ); }

add_filter( 'body_class', function ( $classes ) {
    if ( gamilea_is_faq_page() ) { $classes[] = 'gamilea-faq-page'; }
    return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
    if ( ! gamilea_is_faq_page() ) { return; }
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    wp_enqueue_style( 'gamilea-faq', $uri . '/assets/faq.css', array( 'gamilea-design' ), (string) filemtime( $dir . '/assets/faq.css' ) );
    wp_enqueue_script( 'gamilea-faq', $uri . '/assets/faq.js', array(), (string) filemtime( $dir . '/assets/faq.js' ), true );
} );

/**
 * Preguntas publicadas, ordenadas por el campo Orden y, a igualdad, por fecha.
 * Cada fila lleva los slugs de todas sus categorías: una pregunta puede aparecer
 * bajo más de una pestaña.
 */
function gamilea_faq_items() {
    $posts = get_posts( array(
        'post_type' => GAMILEA_FAQ_POST_TYPE,
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
    ) );
    $items = array();
    foreach ( $posts as $post ) {
        $terms = wp_get_object_terms( $post->ID, GAMILEA_FAQ_TAXONOMY, array( 'fields' => 'slugs' ) );
        $items[] = array(
            'question'   => get_the_title( $post ),
            'answer'     => apply_filters( 'the_content', $post->post_content ),
            'categories' => is_wp_error( $terms ) ? array() : $terms,
        );
    }
    return $items;
}

/** Categorías con preguntas, en orden de creación para que las sembradas salgan como en el diseño. */
function gamilea_faq_categories() {
    $terms = get_terms( array(
        'taxonomy' => GAMILEA_FAQ_TAXONOMY,
        'hide_empty' => true,
        'orderby' => 'term_id',
        'order' => 'ASC',
    ) );
    return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Alta inicial: crea las cuatro categorías del diseño y, si la página de FAQ todavía
 * guarda el repetidor antiguo, convierte sus filas en preguntas. Corre una sola vez y
 * nunca pisa contenido existente: si ya hay preguntas creadas, no hace nada.
 */
add_action( 'admin_init', function () {
    if ( get_option( 'gamilea_faq_migrated' ) ) { return; }
    if ( ! current_user_can( 'edit_posts' ) ) { return; }

    foreach ( gamilea_faq_default_categories() as $name ) {
        if ( ! term_exists( $name, GAMILEA_FAQ_TAXONOMY ) ) { wp_insert_term( $name, GAMILEA_FAQ_TAXONOMY ); }
    }

    $existing = get_posts( array( 'post_type' => GAMILEA_FAQ_POST_TYPE, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
    if ( $existing ) { update_option( 'gamilea_faq_migrated', 1, false ); return; }

    $pages = get_posts( array(
        'post_type' => 'page', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids',
        'meta_key' => '_wp_page_template', 'meta_value' => GAMILEA_FAQ_TEMPLATE,
    ) );
    /**
     * Se leen los metadatos en crudo y no con get_field(): el campo del repetidor ya
     * no está registrado en el grupo, así que ACF no sabría resolverlo. ACF guarda las
     * filas como faq_items = nº de filas más faq_items_<i>_<campo> por celda.
     */
    $page_id = $pages ? (int) $pages[0] : 0;
    $total = $page_id ? (int) get_post_meta( $page_id, 'faq_items', true ) : 0;
    for ( $i = 0; $i < $total; $i++ ) {
        $question = (string) get_post_meta( $page_id, 'faq_items_' . $i . '_question', true );
        if ( '' === trim( $question ) ) { continue; }
        $id = wp_insert_post( array(
            'post_type' => GAMILEA_FAQ_POST_TYPE, 'post_status' => 'publish',
            'post_title' => $question,
            'post_content' => (string) get_post_meta( $page_id, 'faq_items_' . $i . '_answer', true ),
            'menu_order' => ( $i + 1 ) * 10,
        ) );
        if ( ! $id || is_wp_error( $id ) ) { continue; }
        $category = trim( (string) get_post_meta( $page_id, 'faq_items_' . $i . '_category', true ) );
        if ( '' === $category ) { continue; }
        $term = term_exists( $category, GAMILEA_FAQ_TAXONOMY ) ?: wp_insert_term( $category, GAMILEA_FAQ_TAXONOMY );
        if ( ! is_wp_error( $term ) ) { wp_set_object_terms( $id, (int) $term['term_id'], GAMILEA_FAQ_TAXONOMY ); }
    }
    update_option( 'gamilea_faq_migrated', 1, false );
} );

/**
 * La lista de wp-admin sale por título y el campo Orden no se ve, así que no hay forma
 * de comprobar en qué orden quedarán las preguntas. Se añade la columna y se ordena por
 * ella salvo que el usuario pida otra ordenación pulsando una cabecera.
 */
add_filter( 'manage_' . GAMILEA_FAQ_POST_TYPE . '_posts_columns', function ( $columns ) {
    $columns['menu_order'] = 'Orden';
    return $columns;
} );
add_action( 'manage_' . GAMILEA_FAQ_POST_TYPE . '_posts_custom_column', function ( $column, $post_id ) {
    if ( 'menu_order' === $column ) { echo (int) get_post_field( 'menu_order', $post_id ); }
}, 10, 2 );
add_filter( 'manage_edit-' . GAMILEA_FAQ_POST_TYPE . '_sortable_columns', function ( $columns ) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
} );
add_action( 'pre_get_posts', function ( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) { return; }
    if ( GAMILEA_FAQ_POST_TYPE !== $query->get( 'post_type' ) ) { return; }
    if ( $query->get( 'orderby' ) ) { return; }
    $query->set( 'orderby', 'menu_order' );
    $query->set( 'order', 'ASC' );
} );

/** URL de la página que usa la plantilla de FAQ, para enlazar hacia ella desde otras partes del tema. */
function gamilea_faq_page_url() {
    static $url = null;
    if ( null !== $url ) { return $url; }
    $pages = get_posts( array(
        'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1, 'fields' => 'ids',
        'meta_key' => '_wp_page_template', 'meta_value' => GAMILEA_FAQ_TEMPLATE,
    ) );
    $url = $pages ? get_permalink( $pages[0] ) : home_url( '/ayuda/' );
    return $url;
}

/** Enlace a la página de FAQ ya filtrada por una categoría. Sin slug devuelve la página sin filtrar. */
function gamilea_faq_category_url( $slug = '' ) {
    $url = gamilea_faq_page_url();
    return $slug ? add_query_arg( 'categoria', $slug, $url ) : $url;
}

/** Categoría pedida en la URL, validada contra las que existen. Cadena vacía = todas. */
function gamilea_faq_active_category( $categories ) {
    $slug = isset( $_GET['categoria'] ) ? sanitize_title( wp_unslash( $_GET['categoria'] ) ) : '';
    return ( $slug && in_array( $slug, wp_list_pluck( $categories, 'slug' ), true ) ) ? $slug : '';
}
