<?php
/**
 * Página "Sobre nosotros" (Figma 210:2255) como bloques ACF editables.
 *
 * Misma mecánica que la página de contacto: los bloques se declaran en el filtro
 * gamilea_block_defs y todo el copy vive en sus campos, no aquí. Los valores por
 * defecto son los del diseño y sirven además para sembrar el patrón de la página.
 */
defined( 'ABSPATH' ) || exit;

function gamilea_about_default_values() {
    return array(
        array( 'title' => 'Precio claro', 'description' => 'Ves el total antes de pagar y entiendes qué estás comprando.' ),
        array( 'title' => 'Pago protegido', 'description' => 'El checkout está pensado para completar tu compra de forma segura y clara.' ),
        array( 'title' => 'Políticas visibles', 'description' => 'Envíos, devoluciones y condiciones accesibles antes y después de comprar.' ),
        array( 'title' => 'Atención cercana', 'description' => 'Si tienes una duda, sabes dónde encontrarnos y cómo contactarnos.' ),
    );
}

function gamilea_about_default_steps() {
    return array(
        array( 'title' => 'Elige lo que necesitas', 'description' => 'Explora productos para ti, tu familia y tu hogar con información clara antes de comprar.' ),
        array( 'title' => 'Compra de forma segura', 'description' => 'Completa tu pedido con un proceso simple, sin cargos sorpresa al finalizar la compra.' ),
        array( 'title' => 'Envío ya incluido', 'description' => 'El precio que ves ya contempla el envío. No necesitas calcular costos adicionales después.' ),
        array( 'title' => 'Sigue y recibe tu pedido', 'description' => 'Consulta el estado de tu compra y recibe tu pedido con mayor tranquilidad y claridad.' ),
    );
}

/**
 * Los nombres van en minúsculas y sin guiones, igual que el resto de bloques:
 * gamilea_block_field_key() deriva las claves ACF del nombre del bloque.
 */
add_filter( 'gamilea_block_defs', function ( $defs ) {
    $defs['abouthero'] = array(
        'title' => 'GA·MI·LEA · Nosotros: portada', 'icon' => 'awards',
        'fields' => array(
            array( 'name' => 'eyebrow', 'label' => 'Antetítulo', 'type' => 'text', 'default_value' => 'COMPRA CON CONFIANZA' ),
            array( 'name' => 'title', 'label' => 'Título', 'type' => 'textarea', 'rows' => 2, 'default_value' => "Compra tranquilo.\nNosotros nos encargamos del resto." ),
            array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'En GAMILEA queremos que comprar en línea se sienta simple y seguro. Ves el precio final desde el inicio, el envío ya está incluido y puedes dar seguimiento a tu pedido hasta recibirlo en tu puerta.' ),
            array( 'name' => 'image', 'label' => 'Imagen', 'type' => 'image', 'return_format' => 'url' ),
            array( 'name' => 'badgeTitle', 'label' => 'Distintivo · Título', 'type' => 'text', 'default_value' => '✓ Envío incluido en tu compra', 'instructions' => 'Tarjeta sobre la imagen. Déjalo vacío para ocultarla.' ),
            array( 'name' => 'badgeDescription', 'label' => 'Distintivo · Descripción', 'type' => 'text', 'default_value' => 'El precio que ves es el precio final.' ),
        ),
    );
    $defs['aboutvalues'] = array(
        'title' => 'GA·MI·LEA · Nosotros: por qué confiar', 'icon' => 'shield',
        'fields' => array(
            array( 'name' => 'eyebrow', 'label' => 'Antetítulo', 'type' => 'text', 'default_value' => 'POR QUÉ CONFIAR EN GAMILEA' ),
            array( 'name' => 'title', 'label' => 'Título', 'type' => 'text', 'default_value' => 'Compra con tranquilidad desde el primer clic' ),
            array(
                'name' => 'items', 'label' => 'Motivos', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Añadir motivo',
                'sub_fields' => array(
                    array( 'name' => 'title', 'label' => 'Título', 'type' => 'text' ),
                    array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2 ),
                ),
            ),
        ),
    );
    $defs['aboutsteps'] = array(
        'title' => 'GA·MI·LEA · Nosotros: así funciona', 'icon' => 'editor-ol',
        'fields' => array(
            array( 'name' => 'eyebrow', 'label' => 'Antetítulo', 'type' => 'text', 'default_value' => 'ASÍ FUNCIONA' ),
            array( 'name' => 'title', 'label' => 'Título', 'type' => 'text', 'default_value' => 'Comprar en GAMILEA es así de fácil' ),
            array(
                'name' => 'items', 'label' => 'Pasos', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Añadir paso',
                'sub_fields' => array(
                    array( 'name' => 'title', 'label' => 'Título', 'type' => 'text' ),
                    array( 'name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2 ),
                ),
            ),
        ),
    );
    return $defs;
} );

function gamilea_about_asset( $name ) { return get_template_directory_uri() . '/assets/figma/about/' . $name; }

/** La página se reconoce por su bloque de portada, así que vale para cualquier página que lo use. */
function gamilea_is_about_page() {
    if ( ! is_singular() ) { return false; }
    $id = get_queried_object_id();
    return $id && has_block( 'acf/abouthero', $id );
}

add_filter( 'body_class', function ( $classes ) {
    if ( gamilea_is_about_page() ) { $classes[] = 'gamilea-about-page'; }
    return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
    if ( ! gamilea_is_about_page() && ! is_admin() ) { return; }
    wp_enqueue_style( 'gamilea-about', get_template_directory_uri() . '/assets/about.css', array( 'gamilea-design' ), (string) filemtime( get_template_directory() . '/assets/about.css' ) );
} );

/**
 * Mismos estilos dentro del editor para que la vista previa del bloque coincida con
 * el frente; el lienzo del editor no hereda los del tema. Las hojas base ya las
 * registra inc/contact.php, así que aquí solo se añade la de esta página.
 */
add_action( 'enqueue_block_assets', function () {
    if ( ! is_admin() ) { return; }
    $dir = get_template_directory();
    wp_enqueue_style( 'gamilea-about', get_template_directory_uri() . '/assets/about.css', array( 'gamilea-editor-design' ), (string) filemtime( $dir . '/assets/about.css' ) );
} );

/** Contenido inicial de la página, con los bloques ya poblados (ver gamilea_seed_block()). */
function gamilea_about_block_content() {
    $blocks = array(
        gamilea_seed_block( 'abouthero' ),
        gamilea_seed_block( 'aboutvalues', array( 'items' => gamilea_about_default_values() ) ),
        gamilea_seed_block( 'aboutsteps', array( 'items' => gamilea_about_default_steps() ) ),
    );
    return implode( "\n", array_map( 'serialize_block', $blocks ) );
}

add_action( 'init', function () {
    if ( ! is_admin() ) { return; }
    register_block_pattern( 'gamilea/nosotros', array(
        'title' => 'GA·MI·LEA · Sobre nosotros', 'categories' => array( 'featured' ),
        'content' => gamilea_about_block_content(),
        'description' => 'Página sobre nosotros: portada con imagen, motivos para confiar y los cuatro pasos de compra.',
    ) );
}, 20 );
