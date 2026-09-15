<?php
/** Editable supporting content for the mobile navigation. */
defined('ABSPATH') || exit;

add_action('acf/init', function () {
    if (!function_exists('acf_add_options_page') || !function_exists('acf_add_local_field_group')) { return; }
    acf_add_options_page(array(
        'page_title' => 'Menú móvil GA·MI·LEA', 'menu_title' => 'Menú móvil',
        'menu_slug' => 'gamilea-mobile-menu', 'parent_slug' => 'themes.php',
        'capability' => 'edit_theme_options',
    ));
    acf_add_local_field_group(array(
        'key' => 'group_gamilea_mobile_menu', 'title' => 'Información del menú móvil',
        'fields' => array(
            array('key' => 'field_gamilea_mobile_menu_help', 'label' => 'Enlaces de navegación', 'type' => 'message', 'message' => 'Los enlaces y submenús se administran en Apariencia → Menús, en la ubicación Menú principal. Aquí puedes editar la información adicional del panel.'),
            array('key' => 'field_gamilea_mobile_menu_title', 'name' => 'gamilea_mobile_menu_title', 'label' => 'Título del panel', 'type' => 'text', 'default_value' => 'GA·MI·LEA'),
            array(
                'key' => 'field_gamilea_mobile_menu_info', 'name' => 'gamilea_mobile_menu_info',
                'label' => 'Bloques de información', 'type' => 'repeater', 'layout' => 'block',
                'button_label' => 'Añadir bloque',
                'instructions' => 'Agrega contacto, horarios o dirección. Los bloques vacíos no se muestran.',
                'sub_fields' => array(
                    array('key' => 'field_gamilea_mobile_menu_heading', 'name' => 'heading', 'label' => 'Título', 'type' => 'text'),
                    array('key' => 'field_gamilea_mobile_menu_content', 'name' => 'content', 'label' => 'Información', 'type' => 'wysiwyg', 'tabs' => 'visual', 'toolbar' => 'basic', 'media_upload' => 0, 'instructions' => 'Puedes añadir enlaces de correo (mailto:), teléfono (tel:) o mapas.'),
                ),
            ),
        ),
        'location' => array(array(array('param' => 'options_page', 'operator' => '==', 'value' => 'gamilea-mobile-menu'))),
    ));
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('gamilea-mobile-menu', get_template_directory_uri() . '/assets/mobile-menu.css', array('gamilea-design'), (string) filemtime(get_template_directory() . '/assets/mobile-menu.css'));
});

// Both renderings use the same WordPress menu; keep their DOM IDs unique.
add_filter('nav_menu_item_id', function ($id, $item, $args) {
    return isset($args->menu_id) && $args->menu_id === 'mobile-primary-menu' ? 'mobile-menu-item-' . $item->ID : $id;
}, 10, 3);
