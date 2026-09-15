<?php
/** Bloques de la portada GA·MI·LEA, registrados con Advanced Custom Fields PRO. */
defined('ABSPATH') || exit;

function gamilea_shop_url() {
    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
}

/**
 * A block freshly inserted without saved data has no ACF meta yet, so get_field()
 * returns empty instead of the field's default_value. Fall back explicitly so a
 * newly added block still looks right before an editor opens and saves it once.
 */
function gamilea_field($name, $default = '') {
    $value = get_field($name);
    return ($value === '' || $value === null || $value === false) ? $default : $value;
}

/**
 * Field definitions shared by block registration and by the default homepage seed below.
 * Other pages add their own blocks through the filter (see inc/contact.php), so they
 * reuse the same registration, seeding and field-key helpers as the homepage ones.
 */
function gamilea_block_defs() {
    return apply_filters('gamilea_block_defs', array(
        'hero' => array(
            'title'=>'GA·MI·LEA · Portada','icon'=>'cover-image',
            'fields'=>array(
                array('name'=>'title','label'=>'Título','type'=>'textarea','rows'=>2,'default_value'=>"Todo lo que necesitas,\n directo a tu puerta."),
                array('name'=>'description','label'=>'Descripción','type'=>'textarea','rows'=>2,'default_value'=>"Encuentra productos para ti, tu familia y tu hogar.\nEl envío ya está incluido. Tú solo compra y espera."),
                array('name'=>'mobileDescription','label'=>'Descripción en móvil','type'=>'textarea','rows'=>2,'default_value'=>"Para ti, tu familia y tu hogar.\nEl envío ya está incluido."),
                array('name'=>'button','label'=>'Texto del botón','type'=>'text','default_value'=>'Explorar productos'),
                array('name'=>'benefits','label'=>'Beneficios','type'=>'text','default_value'=>'Envío incluido　 Compra segura　 Soporte siempre'),
                array('name'=>'mobileBenefits','label'=>'Beneficios en móvil','type'=>'text','default_value'=>'Envío incluido · Compra segura'),
                array('name'=>'image','label'=>'Imagen de portada','type'=>'image','return_format'=>'url'),
                array('name'=>'url','label'=>'Enlace del botón','type'=>'url'),
            ),
        ),
        'categories' => array(
            'title'=>'GA·MI·LEA · Categorías','icon'=>'category',
            'fields'=>array(
                array('name'=>'title','label'=>'Título','type'=>'text','default_value'=>'Explora nuestras categorías'),
                array('name'=>'url','label'=>'Enlace del botón','type'=>'url'),
                array('name'=>'linkLabel','label'=>'Texto del enlace','type'=>'text','default_value'=>'Ver todas las categorías'),
                array('name'=>'cardLabel','label'=>'Texto de las tarjetas','type'=>'text','default_value'=>'Explorar'),
                array(
                    'name'=>'items','label'=>'Categorías','type'=>'repeater','layout'=>'block','button_label'=>'Añadir categoría',
                    'sub_fields'=>array(
                        array('name'=>'category','label'=>'Categoría de producto','type'=>'taxonomy','taxonomy'=>'product_cat','field_type'=>'select','allow_null'=>1,'add_term'=>0,'return_format'=>'id'),
                    ),
                ),
            ),
        ),
        'products' => array(
            'title'=>'GA·MI·LEA · Productos','icon'=>'cart',
            'fields'=>array(
                array('name'=>'title','label'=>'Título','type'=>'text','default_value'=>'Más vendidos'),
                array('name'=>'url','label'=>'Enlace del botón','type'=>'url'),
                array('name'=>'linkLabel','label'=>'Texto del enlace','type'=>'text','default_value'=>'Ver todos'),
                array('name'=>'limit','label'=>'Cantidad de productos','type'=>'range','default_value'=>5,'min'=>1,'max'=>20,'step'=>1),
            ),
        ),
        'steps' => array(
            'title'=>'GA·MI·LEA · Pasos de compra','icon'=>'editor-ol',
            'fields'=>array(
                array('name'=>'title','label'=>'Título','type'=>'text','default_value'=>'Comprar aquí es así de fácil'),
                array('name'=>'image','label'=>'Imagen lateral','type'=>'image','return_format'=>'url'),
                array(
                    'name'=>'items','label'=>'Pasos','type'=>'repeater','layout'=>'block','button_label'=>'Añadir paso',
                    'sub_fields'=>array(
                        array('name'=>'title','label'=>'Título','type'=>'text'),
                        array('name'=>'description','label'=>'Descripción','type'=>'textarea','rows'=>2),
                        array('name'=>'image','label'=>'Imagen','type'=>'image','return_format'=>'url'),
                    ),
                ),
            ),
        ),
        'brands' => array(
            'title'=>'GA·MI·LEA · Marcas','icon'=>'store',
            'fields'=>array(
                array('name'=>'title','label'=>'Título','type'=>'text','default_value'=>'Marcas que encuentras con nosotros'),
                array('name'=>'linkLabel','label'=>'Texto del enlace','type'=>'text','default_value'=>'Ver todas las marcas'),
                array('name'=>'url','label'=>'Enlace','type'=>'url','default_value'=>'/marcas/'),
                array(
                    'name'=>'items','label'=>'Marcas','type'=>'repeater','layout'=>'block','button_label'=>'Añadir marca',
                    'sub_fields'=>array(
                        array('name'=>'title','label'=>'Nombre','type'=>'text'),
                        array('name'=>'image','label'=>'Logo','type'=>'image','return_format'=>'url'),
                    ),
                ),
            ),
        ),
        'newsletter' => array(
            'title'=>'GA·MI·LEA · Suscripción','icon'=>'email',
            'fields'=>array(
                array('name'=>'title','label'=>'Título','type'=>'text','default_value'=>'Sé el primero en enterarte'),
                array('name'=>'description','label'=>'Descripción','type'=>'textarea','rows'=>2,'default_value'=>'Recibe nuestras ofertas, novedades y promociones exclusivas.'),
                array('name'=>'button','label'=>'Texto del botón','type'=>'text','default_value'=>'Suscribirme'),
                array('name'=>'privacyUrl','label'=>'Enlace de privacidad','type'=>'url','default_value'=>'/privacidad/'),
                array('name'=>'benefit0','label'=>'Beneficio 1','type'=>'text','default_value'=>'Ofertas exclusivas'),
                array('name'=>'benefit1','label'=>'Beneficio 2','type'=>'text','default_value'=>'Nuevos productos'),
                array('name'=>'benefit2','label'=>'Beneficio 3','type'=>'text','default_value'=>'Consejos y más'),
            ),
        ),
    ));
}

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) { return; }
    foreach (gamilea_block_defs() as $name => $def) {
        acf_register_block_type(array(
            'name'=>$name,'title'=>$def['title'],'category'=>'gamilea','icon'=>$def['icon'],
            'supports'=>array('multiple'=>false,'mode'=>false),
            'render_template'=>get_template_directory() . '/blocks/templates/' . $name . '.php',
            'fields'=>$def['fields'],
        ));
    }
});

add_action('init', function () {
    $dir = get_template_directory();
    wp_register_script('gamilea-sections-block', get_template_directory_uri() . '/blocks/sections-block.js', array('wp-blocks','wp-element','wp-block-editor'), filemtime($dir . '/blocks/sections-block.js'), true);
    wp_register_style('gamilea-sections-block', get_template_directory_uri() . '/blocks/sections-block.css', array(), filemtime($dir . '/blocks/sections-block.css'));
    register_block_type('gamilea/sections', array(
        'api_version'=>3,'title'=>'GA·MI·LEA · Contenedor de inicio','category'=>'gamilea',
        'supports'=>array('html'=>false,'multiple'=>false),
        'editor_script'=>'gamilea-sections-block','editor_style'=>'gamilea-sections-block',
        'render_callback'=>function ($attributes,$content) { return '<div class="container home-sections">' . $content . '</div>'; },
    ));
    register_block_pattern('gamilea/home', array('title'=>'GA·MI·LEA · Inicio completo','categories'=>array('featured'),'content'=>gamilea_home_block_content(),'description'=>'Inicio editable con todas las secciones del tema.'));
});
add_filter('block_categories_all', function ($categories) {
    array_unshift($categories, array('slug'=>'gamilea','title'=>'GA·MI·LEA'));
    return $categories;
});

/**
 * Contenido inicial de la portada, con los bloques ACF ya "guardados" con sus
 * valores de muestra (incluidas las filas de los repetidores de categorías,
 * pasos y marcas). ACF no aplica sus valores por defecto a un bloque recién
 * insertado sin datos, así que en vez de dejarlos vacíos, se generan aquí con
 * el mismo esquema plano (campo + "_campo" con la clave) que ACF usa para
 * guardar cualquier bloque desde el editor, para que ambos —el bloque
 * insertado por este código y el editado a mano— se comporten igual.
 */
function gamilea_block_field_key($block_name, $path) {
    $slug = sanitize_key(str_replace(array('/','-'), '_', $block_name));
    return 'field_' . $slug . '_' . $path;
}
function gamilea_seed_block_data($block_name, $fields, $rows_by_repeater = array()) {
    $data = array();
    foreach ($fields as $field) {
        $name = $field['name'];
        if ('repeater' === $field['type']) {
            $rows = isset($rows_by_repeater[$name]) ? $rows_by_repeater[$name] : array();
            $data[$name] = count($rows);
            $data['_' . $name] = gamilea_block_field_key($block_name, $name);
            foreach (array_values($rows) as $i => $row) {
                foreach ($field['sub_fields'] as $sub) {
                    $cell = $name . '_' . $i . '_' . $sub['name'];
                    $data[$cell] = isset($row[$sub['name']]) ? $row[$sub['name']] : '';
                    $data['_' . $cell] = gamilea_block_field_key($block_name, $name . '_' . $sub['name']);
                }
            }
        } else {
            $data[$name] = isset($field['default_value']) ? $field['default_value'] : '';
            $data['_' . $name] = gamilea_block_field_key($block_name, $name);
        }
    }
    return $data;
}
function gamilea_seed_block($name, $rows_by_repeater = array()) {
    $defs = gamilea_block_defs();
    $block_name = 'acf/' . $name;
    return array(
        'blockName'=>$block_name,
        'attrs'=>array(
            'id'=>'block_gamilea_' . $name,
            'name'=>$block_name,
            'data'=>gamilea_seed_block_data($block_name, $defs[$name]['fields'], $rows_by_repeater),
            'mode'=>'preview',
        ),
        'innerBlocks'=>array(),
        'innerHTML'=>'',
        'innerContent'=>array(),
    );
}
/**
 * Idempotent media import: returns the attachment ID for $source_id, creating
 * it from $bytes_callback the first time and reusing it on every later call
 * (setup can run more than once without piling up duplicate uploads).
 */
function gamilea_ensure_attachment($source_id, $filename, $mime, $bytes_callback, $title) {
    $existing = get_posts(array(
        'post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids',
        'meta_key'=>'_gamilea_asset_source','meta_value'=>$source_id,
    ));
    if ($existing) { return (int) $existing[0]; }

    $bytes = $bytes_callback();
    if (!$bytes) { return 0; }

    /**
     * wp_upload_bits() rejects file types outside wp_get_mime_types() (e.g. svg)
     * regardless of the current user's capabilities, which is the wrong gate for
     * theme-generated placeholder content that never came from an untrusted
     * upload. Write the file ourselves, the same way wp_upload_bits() would.
     */
    $upload_dir = wp_upload_dir();
    if (!empty($upload_dir['error'])) { return 0; }
    $path = $upload_dir['path'] . '/' . wp_unique_filename($upload_dir['path'], $filename);
    if (false === file_put_contents($path, $bytes)) { return 0; }

    $attachment_id = wp_insert_attachment(array(
        'post_mime_type'=>$mime,'post_title'=>$title,'post_status'=>'inherit',
        'guid'=>$upload_dir['url'] . '/' . basename($path),
    ), $path);
    if (is_wp_error($attachment_id) || !$attachment_id) { return 0; }

    update_post_meta($attachment_id, '_gamilea_asset_source', $source_id);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $metadata = wp_generate_attachment_metadata($attachment_id, $path);
    if ($metadata) { wp_update_attachment_metadata($attachment_id, $metadata); }
    return (int) $attachment_id;
}
/** No real brand assets exist yet, so a simple text logo stands in until the admin uploads real ones. */
function gamilea_placeholder_logo_svg($label) {
    return '<svg xmlns="http://www.w3.org/2000/svg" width="240" height="120" viewBox="0 0 240 120">'
        . '<rect width="240" height="120" rx="16" fill="#f3f4f4"/>'
        . '<text x="120" y="68" font-family="DM Sans, Arial, sans-serif" font-size="26" font-weight="700" fill="#081820" text-anchor="middle">' . esc_html($label) . '</text>'
        . '</svg>';
}
function gamilea_ensure_placeholder_logo_attachment($slug, $label) {
    return gamilea_ensure_attachment(
        'brand-logo:' . $slug, 'gamilea-brand-' . $slug . '.svg', 'image/svg+xml',
        function () use ($label) { return gamilea_placeholder_logo_svg($label); }, $label . ' (logo de muestra)'
    );
}

function gamilea_category_term_id($slug) {
    $term = get_term_by('slug', $slug, 'product_cat');
    return $term ? (int) $term->term_id : 0;
}
/** Name, description and photo live on the WooCommerce category itself; the block only picks which ones to show, and in what order. */
function gamilea_default_categories() {
    return array_map(function ($slug) { return array('category'=>gamilea_category_term_id($slug)); }, array('salud','cuidado-y-belleza','tecnologia','hogar','bebe'));
}
function gamilea_default_steps() {
    return array(
        array('title'=>'01  Elige','description'=>'Encuentra lo que necesitas.','image'=>''),
        array('title'=>'02  Compra','description'=>'Paga de forma segura.','image'=>''),
        array('title'=>'03  Recíbelo',"description"=>"El envío ya está incluido.\nSolo espera tu pedido.",'image'=>''),
    );
}
function gamilea_default_brands() {
    return array(
        array('title'=>'Samsung','image'=>''), array('title'=>'Philips','image'=>''), array('title'=>'Xiaomi','image'=>''),
        array('title'=>'L’Oréal','image'=>''), array('title'=>'Chicco','image'=>''), array('title'=>'JBL','image'=>''),
        array('title'=>'TP-Link','image'=>''), array('title'=>'Nespresso','image'=>''),
    );
}
/**
 * $with_images is only true for the one-time real page setup below: it generates
 * placeholder logo images for the brands repeater so the page opens already
 * populated. The pattern-preview call stays cheap and side-effect-free, since it
 * can run on every request via register_block_pattern().
 */
function gamilea_home_block_content($with_images = false) {
    $brands = gamilea_default_brands();
    if ($with_images) {
        foreach ($brands as &$brand) {
            $brand['image'] = gamilea_ensure_placeholder_logo_attachment(sanitize_title($brand['title']), $brand['title']);
        }
        unset($brand);
    }
    $inner = array(
        gamilea_seed_block('categories', array('items'=>gamilea_default_categories())),
        gamilea_seed_block('products'),
        gamilea_seed_block('steps', array('items'=>gamilea_default_steps())),
        gamilea_seed_block('brands', array('items'=>$brands)),
        gamilea_seed_block('newsletter'),
    );
    $inner_content = array();
    foreach ($inner as $i => $block) {
        $inner_content[] = null;
        if ($i < count($inner) - 1) { $inner_content[] = "\n"; }
    }
    $sections = array(
        'blockName'=>'gamilea/sections','attrs'=>array(),
        'innerBlocks'=>$inner,'innerHTML'=>'','innerContent'=>$inner_content,
    );
    return serialize_block(gamilea_seed_block('hero')) . "\n" . serialize_block($sections);
}

/** Explicit setup action: never overwrites an existing page. */
function gamilea_create_editable_home() {
    $front = (int)get_option('page_on_front');
    if ($front && has_block('acf/hero', $front)) { return $front; }
    /**
     * wp_insert_post() unslashes $postarr internally, assuming it came from a
     * slashed source like $_POST. Our generated content never was, so it must
     * be slashed here or its JSON escapes (e.g. "\n" inside block attrs) get
     * corrupted by that unslashing.
     */
    $id = wp_insert_post(wp_slash(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Inicio GA·MI·LEA','post_content'=>gamilea_home_block_content(true))), true);
    if (is_wp_error($id)) { return $id; }
    update_option('show_on_front','page');
    update_option('page_on_front',$id);
    return $id;
}
add_action('admin_menu', function () {
    add_theme_page('Inicio GA·MI·LEA','Inicio GA·MI·LEA','edit_theme_options','gamilea-home',function () {
        $front = (int)get_option('page_on_front');
        echo '<div class="wrap"><h1>Inicio GA·MI·LEA</h1><p>Edita las secciones con los bloques GA·MI·LEA desde el editor de páginas. Puedes moverlas, eliminarlas y añadir bloques de WordPress.</p>';
        if ($front) { echo '<p><a class="button" href="' . esc_url(get_edit_post_link($front)) . '">Editar página de inicio actual</a></p>'; }
        echo '<p>Crear un inicio editable publica una página nueva y la establece como portada. La página anterior y su contenido se conservan.</p><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="gamilea_create_home">';
        wp_nonce_field('gamilea_create_home');
        submit_button('Crear inicio con bloques GA·MI·LEA');
        echo '</form></div>';
    });
});
add_action('admin_post_gamilea_create_home', function () {
    if (!current_user_can('edit_theme_options') || !current_user_can('publish_pages')) { wp_die('No tienes permisos para crear la página.'); }
    check_admin_referer('gamilea_create_home');
    $id = gamilea_create_editable_home();
    if (is_wp_error($id)) { wp_die(esc_html($id->get_error_message())); }
    wp_safe_redirect(get_edit_post_link($id,'raw'));
    exit;
});
