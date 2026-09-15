<?php
/**
 * Página de contacto (Figma 210:2419) como bloques ACF editables.
 *
 * El copy, los enlaces y las imágenes viven en los campos de cada bloque, no aquí.
 * El formulario lo gestiona Fluent Forms: el bloque solo elige cuál mostrar.
 */
defined( 'ABSPATH' ) || exit;

/** Formularios publicados en Fluent Forms, para el selector del bloque. */
function gamilea_fluentform_choices() {
    static $choices = null;
    if ( null !== $choices ) { return $choices; }
    global $wpdb;
    $table = $wpdb->prefix . 'fluentform_forms';
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) { $choices = array(); return $choices; }
    $forms = $wpdb->get_results( "SELECT id, title FROM {$table} WHERE status = 'published' ORDER BY title ASC" );
    $choices = array();
    foreach ( (array) $forms as $form ) { $choices[ (string) $form->id ] = $form->title . ' (#' . $form->id . ')'; }
    return $choices;
}

function gamilea_contact_default_channels() {
    return array(
        // Sin número todavía: el editor pega aquí el enlace https://wa.me/503… y la tarjeta pasa a ser un enlace.
        array( 'title' => 'WhatsApp', 'description' => 'Ideal para consultas rápidas sobre productos, pedidos y envíos.', 'linkLabel' => 'Abrir WhatsApp', 'url' => '', 'image' => '' ),
        array( 'title' => 'Correo', 'description' => 'Si necesitas explicar tu caso con más detalle, escríbenos y te responderemos por email.', 'linkLabel' => 'Enviar correo', 'url' => 'mailto:' . get_option( 'admin_email' ), 'image' => '' ),
        array( 'title' => 'Rastrear pedido', 'description' => 'Si ya compraste, consulta el estado de tu pedido sin esperar respuesta de soporte.', 'linkLabel' => 'Seguir mi pedido', 'url' => '/my-account/orders/', 'image' => '' ),
    );
}

function gamilea_contact_default_assurances() {
    return array(
        array( 'title' => 'Compra segura', 'url' => '' ),
        array( 'title' => 'Envío incluido', 'url' => '/envios/' ),
        array( 'title' => 'Seguimiento de pedido', 'url' => '/my-account/orders/' ),
    );
}

function gamilea_contact_default_links() {
    return array(
        array( 'title' => 'Preguntas frecuentes', 'description' => 'Respuestas sobre compras, pagos, envíos y devoluciones.', 'linkLabel' => 'Ver información', 'url' => '/ayuda/' ),
        array( 'title' => 'Seguimiento de pedido', 'description' => 'Consulta el estado de tu compra en cualquier momento.', 'linkLabel' => 'Ver información', 'url' => '/my-account/orders/' ),
        array( 'title' => 'Envíos y devoluciones', 'description' => 'Revisa tiempos, cobertura, condiciones antes de comprar.', 'linkLabel' => 'Ver información', 'url' => '/envios/' ),
    );
}

/**
 * Los nombres van en minúsculas y sin guiones, igual que los bloques de la portada:
 * gamilea_block_field_key() deriva las claves ACF del nombre, y así coinciden con
 * las que genera acf_register_block_type().
 */
add_filter( 'gamilea_block_defs', function ( $defs ) {
    $defs['contacthero'] = array(
        'title' => 'GA·MI·LEA · Contacto: portada', 'icon' => 'megaphone',
        'fields' => array(
            array( 'name' => 'eyebrow', 'label' => 'Antetítulo', 'type' => 'text', 'default_value' => 'AYUDA Y SOPORTE' ),
            array( 'name' => 'title', 'label' => 'Título', 'type' => 'text', 'default_value' => 'Estamos aquí para ayudarte' ),
            array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2, 'default_value' => '¿Tienes dudas sobre tu pedido, envío, devolución o algún producto? Cuéntanos y te ayudaremos.' ),
            array(
                'name' => 'items', 'label' => 'Garantías', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Añadir garantía',
                'sub_fields' => array(
                    array( 'name' => 'title', 'label' => 'Texto', 'type' => 'text' ),
                    array( 'name' => 'url', 'label' => 'Enlace', 'type' => 'text', 'instructions' => 'Opcional. Sin enlace la garantía se muestra como texto.' ),
                ),
            ),
        ),
    );
    $defs['contactsupport'] = array(
        'title' => 'GA·MI·LEA · Contacto: canales y formulario', 'icon' => 'email-alt',
        'fields' => array(
            array( 'name' => 'image', 'label' => 'Imagen', 'type' => 'image', 'return_format' => 'url' ),
            array( 'name' => 'title', 'label' => 'Título', 'type' => 'text', 'default_value' => '¿Cómo podemos ayudarte?' ),
            array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Elige el canal que mejor se adapte a tu consulta. Para pedidos existentes, ten a mano tu número de orden.' ),
            array(
                'name' => 'items', 'label' => 'Canales de atención', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Añadir canal',
                'sub_fields' => array(
                    array( 'name' => 'image', 'label' => 'Icono', 'type' => 'image', 'return_format' => 'url' ),
                    array( 'name' => 'title', 'label' => 'Título', 'type' => 'text' ),
                    array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2 ),
                    array( 'name' => 'linkLabel', 'label' => 'Texto del enlace', 'type' => 'text' ),
                    array( 'name' => 'url', 'label' => 'Enlace', 'type' => 'text', 'instructions' => 'Admite https://wa.me/…, mailto:… o una ruta del sitio.' ),
                ),
            ),
            array( 'name' => 'formTitle', 'label' => 'Título del formulario', 'type' => 'text', 'default_value' => 'Envíanos un mensaje' ),
            array( 'name' => 'formDescription', 'label' => 'Descripción del formulario', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Completa el formulario y selecciona el motivo de tu consulta.' ),
            array( 'name' => 'formId', 'label' => 'Formulario de Fluent Forms', 'type' => 'select', 'choices' => gamilea_fluentform_choices(), 'allow_null' => 1, 'ui' => 1, 'instructions' => 'Los campos, avisos y notificaciones se editan en Fluent Forms.' ),
        ),
    );
    $defs['contactlinks'] = array(
        'title' => 'GA·MI·LEA · Contacto: accesos rápidos', 'icon' => 'admin-links',
        'fields' => array(
            array( 'name' => 'title', 'label' => 'Título', 'type' => 'text', 'default_value' => 'Quizá podamos resolverlo ahora' ),
            array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Accesos rápidos para resolver las dudas más comunes sin esperar una respuesta.' ),
            array(
                'name' => 'items', 'label' => 'Accesos', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Añadir acceso',
                'sub_fields' => array(
                    array( 'name' => 'title', 'label' => 'Título', 'type' => 'text' ),
                    array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2 ),
                    array( 'name' => 'linkLabel', 'label' => 'Texto del enlace', 'type' => 'text' ),
                    array( 'name' => 'url', 'label' => 'Enlace', 'type' => 'text' ),
                ),
            ),
        ),
    );
    return $defs;
} );

function gamilea_contact_asset( $name ) { return get_template_directory_uri() . '/assets/figma/contact/' . $name; }

function gamilea_contact_arrow_link( $label, $url ) {
    if ( '' === $label ) { return ''; }
    $inner = esc_html( $label ) . ' <span aria-hidden="true">&rarr;</span>';
    if ( '' === $url ) { return '<span class="contact-arrow-link">' . $inner . '</span>'; }
    $external = 0 === strpos( $url, 'http' ) && false === strpos( $url, home_url() );
    return '<a class="contact-arrow-link" href="' . esc_url( $url ) . '"' . ( $external ? ' target="_blank" rel="noopener"' : '' ) . '>' . $inner . '</a>';
}

/** La página se reconoce por sus bloques, así que sirve para cualquier página que los use. */
function gamilea_is_contact_page() {
    if ( ! is_singular() ) { return false; }
    $id = get_queried_object_id();
    return $id && has_block( 'acf/contacthero', $id );
}

add_filter( 'body_class', function ( $classes ) {
    if ( gamilea_is_contact_page() ) { $classes[] = 'gamilea-contact-page'; }
    return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
    if ( ! gamilea_is_contact_page() && ! is_admin() ) { return; }
    wp_enqueue_style( 'gamilea-contact', get_template_directory_uri() . '/assets/contact.css', array( 'gamilea-design' ), (string) filemtime( get_template_directory() . '/assets/contact.css' ) );
} );
/**
 * Mismos estilos dentro del editor para que la vista previa del bloque coincida con el frente.
 * El lienzo del editor no hereda los del tema, así que hay que cargarlos aquí también.
 */
add_action( 'enqueue_block_assets', function () {
    if ( ! is_admin() ) { return; }
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    wp_enqueue_style( 'gamilea-editor-base', get_stylesheet_uri(), array(), (string) filemtime( get_stylesheet_directory() . '/style.css' ) );
    wp_enqueue_style( 'gamilea-editor-design', $uri . '/assets/gamilea.css', array( 'gamilea-editor-base' ), (string) filemtime( $dir . '/assets/gamilea.css' ) );
    wp_enqueue_style( 'gamilea-contact', $uri . '/assets/contact.css', array( 'gamilea-editor-design' ), (string) filemtime( $dir . '/assets/contact.css' ) );
} );

/** Contenido inicial de la página, con los bloques ya poblados (ver gamilea_seed_block()). */
function gamilea_contact_block_content( $form_id = 0 ) {
    $choices = array_keys( gamilea_fluentform_choices() );
    $blocks  = array(
        gamilea_seed_block( 'contacthero', array( 'items' => gamilea_contact_default_assurances() ) ),
        gamilea_seed_block( 'contactsupport', array( 'items' => gamilea_contact_default_channels() ) ),
        gamilea_seed_block( 'contactlinks', array( 'items' => gamilea_contact_default_links() ) ),
    );
    $form_id = $form_id ?: ( $choices ? reset( $choices ) : 0 );
    if ( $form_id ) { $blocks[1]['attrs']['data']['formId'] = (string) $form_id; }
    return implode( "\n", array_map( 'serialize_block', $blocks ) );
}

add_action( 'init', function () {
    if ( ! is_admin() ) { return; }
    register_block_pattern( 'gamilea/contacto', array(
        'title' => 'GA·MI·LEA · Contáctanos', 'categories' => array( 'featured' ),
        'content' => gamilea_contact_block_content(),
        'description' => 'Página de contacto completa: portada, canales de atención con formulario de Fluent Forms y accesos rápidos.',
    ) );
}, 20 );
