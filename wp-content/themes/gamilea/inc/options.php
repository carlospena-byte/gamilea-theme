<?php
/** Ajustes globales del pie de página, editables desde ACF sin tocar código. */
defined('ABSPATH') || exit;

add_action('acf/init', function () {
    if (!function_exists('acf_add_options_page')) { return; }

    acf_add_options_page(array(
        'page_title'=>'Pie de página GA·MI·LEA','menu_title'=>'Pie de página','menu_slug'=>'gamilea-footer',
        'capability'=>'edit_theme_options','icon_url'=>'dashicons-editor-contract','position'=>61,
    ));

    acf_add_local_field_group(array(
        'key'=>'group_gamilea_footer','title'=>'Pie de página GA·MI·LEA',
        'fields'=>array(
            array('key'=>'field_gamilea_footer_tagline','name'=>'tagline','label'=>'Frase de marca','type'=>'text'),
            array(
                'key'=>'field_gamilea_footer_social','name'=>'social','label'=>'Redes sociales','type'=>'group','layout'=>'block',
                'sub_fields'=>array(
                    array('key'=>'field_gamilea_footer_social_facebook','name'=>'facebook','label'=>'Facebook','type'=>'url'),
                    array('key'=>'field_gamilea_footer_social_instagram','name'=>'instagram','label'=>'Instagram','type'=>'url'),
                    array('key'=>'field_gamilea_footer_social_tiktok','name'=>'tiktok','label'=>'TikTok','type'=>'url'),
                    array('key'=>'field_gamilea_footer_social_youtube','name'=>'youtube','label'=>'YouTube','type'=>'url'),
                ),
            ),
            array(
                'key'=>'field_gamilea_footer_columns','name'=>'columns','label'=>'Columnas de enlaces','type'=>'repeater',
                'layout'=>'block','button_label'=>'Añadir columna',
                'sub_fields'=>array(
                    array('key'=>'field_gamilea_footer_columns_title','name'=>'title','label'=>'Título','type'=>'text'),
                    array(
                        'key'=>'field_gamilea_footer_columns_links','name'=>'links','label'=>'Enlaces','type'=>'repeater',
                        'layout'=>'table','button_label'=>'Añadir enlace',
                        'sub_fields'=>array(
                            array('key'=>'field_gamilea_footer_columns_links_label','name'=>'label','label'=>'Texto','type'=>'text'),
                            array('key'=>'field_gamilea_footer_columns_links_url','name'=>'url','label'=>'Enlace','type'=>'url'),
                        ),
                    ),
                ),
            ),
            array(
                'key'=>'field_gamilea_footer_payment_methods','name'=>'payment_methods','label'=>'Medios de pago','type'=>'repeater',
                'layout'=>'table','button_label'=>'Añadir medio de pago',
                'sub_fields'=>array(
                    array('key'=>'field_gamilea_footer_payment_methods_name','name'=>'name','label'=>'Nombre','type'=>'text'),
                    array('key'=>'field_gamilea_footer_payment_methods_logo','name'=>'logo','label'=>'Logo','type'=>'image','return_format'=>'url'),
                ),
            ),
            array('key'=>'field_gamilea_footer_payment_note','name'=>'payment_note','label'=>'Nota de pagos de muestra','type'=>'text','instructions'=>'Bórrala antes de vender en producción.'),
            array('key'=>'field_gamilea_footer_rights','name'=>'rights','label'=>'Texto de derechos','type'=>'text','instructions'=>'El año se antepone automáticamente.'),
            array('key'=>'field_gamilea_footer_bottom_tagline','name'=>'bottom_tagline','label'=>'Frase final','type'=>'text'),
        ),
        'location'=>array(array(array('param'=>'options_page','operator'=>'==','value'=>'gamilea-footer'))),
    ));

    gamilea_seed_footer_options();
});

function gamilea_default_payment_methods() {
    return array_map(function ($name) { return array('name'=>$name,'logo'=>''); }, array('Visa','Mastercard','American Express','Apple Pay','Google Pay'));
}
function gamilea_footer_option($name, $default = '') {
    $value = get_field($name, 'option');
    return ($value === '' || $value === null || $value === false) ? $default : $value;
}

/**
 * Runs once (guarded by the tagline already having a value): seeds the options
 * page with the footer's current copy so it starts populated and editable,
 * instead of the admin facing an empty form the first time they open it.
 */
function gamilea_seed_footer_options() {
    if (get_field('tagline', 'option')) { return; }
    update_field('tagline', 'Buenas cosas, más cerca de ti.', 'option');
    update_field('social', array('facebook'=>'','instagram'=>'','tiktok'=>'','youtube'=>''), 'option');
    update_field('columns', array(
        array('title'=>'Comprar','links'=>array(
            array('label'=>'Todas las categorías','url'=>home_url('/shop/')),
            array('label'=>'Ofertas','url'=>home_url('/?collection=sale#bestsellers')),
            array('label'=>'Nuevos productos','url'=>home_url('/?collection=new#bestsellers')),
            array('label'=>'Más vendidos','url'=>home_url('/?collection=popular#bestsellers')),
        )),
        // Estos tres temas se responden en las FAQ: enlazan a la página ya filtrada por su categoría.
        array('title'=>'Ayuda','links'=>array(
            array('label'=>'Seguimiento de pedido','url'=>gamilea_faq_category_url('envios')),
            array('label'=>'Envíos','url'=>gamilea_faq_category_url('envios')),
            array('label'=>'Devoluciones','url'=>gamilea_faq_category_url('devoluciones')),
            array('label'=>'Preguntas frecuentes','url'=>gamilea_faq_category_url()),
            array('label'=>'Contáctanos','url'=>home_url('/contacto')),
        )),
        array('title'=>'Nosotros','links'=>array(
            array('label'=>'Sobre nosotros','url'=>home_url('/nosotros')),
            array('label'=>'Términos y condiciones','url'=>home_url('/terminos')),
            array('label'=>'Política de privacidad','url'=>home_url('/privacidad')),
        )),
    ), 'option');
    update_field('payment_methods', array_map(function ($name) {
        return array('name'=>$name, 'logo'=>gamilea_ensure_placeholder_logo_attachment(sanitize_title($name), $name));
    }, array('Visa','Mastercard','American Express','Apple Pay','Google Pay')), 'option');
    update_field('payment_note', 'Pagos de muestra · tienda en desarrollo', 'option');
    update_field('rights', 'GA·MI·LEA. Todos los derechos reservados.', 'option');
    update_field('bottom_tagline', 'Hecho con ♥ para tu día a día.', 'option');
}
