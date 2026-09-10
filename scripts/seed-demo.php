<?php
/** Run with: wp eval-file /tmp/seed-demo.php */
if ( ! defined('WP_CLI') ) { exit; }
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
$assets = get_template_directory() . '/assets/images/';
$categories = array();
foreach(tienda_categories() as $slug=>$data) {
    $term = term_exists($slug, 'product_cat');
    if (!$term) { $term = wp_insert_term($data[0], 'product_cat', array('slug'=>$slug, 'description'=>$data[1])); }
    if(is_wp_error($term)) { WP_CLI::error($term->get_error_message()); }
    $categories[$slug] = (int) $term['term_id'];
}
$images=array();
foreach(array('headphones','skincare','airfryer','stroller','vitamins') as $asset) {
    $existing = get_posts(array('post_type'=>'attachment','meta_key'=>'_tienda_asset','meta_value'=>$asset,'fields'=>'ids','posts_per_page'=>1));
    if($existing) { $images[$asset]=$existing[0]; continue; }
    $path=$assets.$asset.'.jpg';
    if(!file_exists($path)) { WP_CLI::error('Missing image: '.$asset); }
    $tmp=wp_tempnam($path); copy($path,$tmp);
    $attachment=media_handle_sideload(array('name'=>$asset.'.jpg','tmp_name'=>$tmp),0);
    if(is_wp_error($attachment)) { WP_CLI::error($attachment->get_error_message()); }
    update_post_meta($attachment,'_tienda_asset',$asset);
    $images[$asset]=$attachment;
}
$items=array(
 array('DEMO-AUDIO-01','Audífonos Bluetooth Noise Cancelling','tecnologia','headphones','89.99','','Más vendido','JBL'),
 array('DEMO-BEAUTY-01','Set de Cuidado Facial Hidratante','cuidado-y-belleza','skincare','39.99','','','L’ORÉAL'),
 array('DEMO-HOME-01','Freidora de Aire 4.2L','hogar','airfryer','99.99','79.99','-20%','PHILIPS'),
 array('DEMO-BABY-01','Coche para Bebé Ultraligero','bebe','stroller','129.99','','Nuevo','chicco'),
 array('DEMO-HEALTH-01','Suplemento Multivitamínico para Adultos','salud','vitamins','24.99','','','GA·MI·LEA'),
 array('DEMO-AUDIO-02','Audífonos Inalámbricos Studio','tecnologia','headphones','119.99','95.99','-20%','SAMSUNG'),
 array('DEMO-BEAUTY-02','Rutina Facial Esencial Glow','cuidado-y-belleza','skincare','49.99','39.99','-20%','L’ORÉAL'),
 array('DEMO-HOME-02','Freidora Compacta Easy Cook','hogar','airfryer','89.99','71.99','-20%','NESPRESSO'),
 array('DEMO-BABY-02','Coche de Paseo Urban Compact','bebe','stroller','159.99','127.99','-20%','chicco'),
 array('DEMO-HEALTH-02','Complejo Diario Bienestar','salud','vitamins','29.99','','Nuevo','GA·MI·LEA'),
 array('DEMO-AUDIO-03','Audífonos Smart Everyday','tecnologia','headphones','59.99','','Nuevo','mi'),
 array('DEMO-AUDIO-04','Audífonos Wireless Home','tecnologia','headphones','69.99','','Nuevo','tp-link'),
);
foreach($items as $index=>$item) {
    list($sku,$name,$category,$asset,$regular,$sale,$badge,$brand)=$item;
    $id=wc_get_product_id_by_sku($sku);
    $product=$id ? wc_get_product($id) : new WC_Product_Simple();
    $product->set_name($name);$product->set_sku($sku);$product->set_status('publish');
    $product->set_regular_price($regular);$product->set_sale_price($sale);$product->set_price($sale ?: $regular);
    $product->set_category_ids(array($categories[$category]));$product->set_image_id($images[$asset]);
    $product->set_manage_stock(true);$product->set_stock_quantity(25);$product->set_stock_status('instock');
    $product->set_short_description('Un favorito para tu día a día. Diseño práctico, compra fácil y envío incluido.');
    $product->set_description('<h2>Buenas cosas, más cerca de ti.</h2><p>'.$name.' está pensado para acompañar tu rutina y hacerla más sencilla.</p><ul><li>Presentación cuidadosamente seleccionada.</li><li>Envío incluido, sin cargos adicionales.</li><li>Soporte para acompañarte en tu compra.</li></ul><p><strong>Producto de demostración.</strong> Imágenes, marca asociada, características, precio y disponibilidad son ficticios; no corresponde a un modelo comercial real.</p><p>Marca de muestra: '.esc_html($brand).'.</p>');
    $product->update_meta_data('_tienda_badge',$badge);$product->update_meta_data('_tienda_order',$index);
    $product->update_meta_data('_tienda_demo',1);$product->save();
    WP_CLI::log('Ready: '.$sku);
}
foreach(tienda_categories() as $slug=>$data) { update_term_meta($categories[$slug],'thumbnail_id',$images[$data[2]]); }
$pages=array(
 'ayuda'=>array('Preguntas frecuentes','<h2>Estamos para ayudarte</h2><h3>¿El envío tiene costo?</h3><p>El envío está incluido en los productos de esta tienda de demostración.</p><h3>¿Cómo consulto mi pedido?</h3><p>Entra a Mi cuenta y abre la sección Pedidos.</p><h3>¿Puedo comprar ya?</h3><p>Estamos preparando nuestra apertura. El catálogo y las condiciones son de muestra y los pagos aún no están habilitados.</p>'),
 'contacto'=>array('Contáctanos','<h2>Conversemos</h2><p>Nos encanta ayudarte a encontrar lo que necesitas. Nuestro equipo estará disponible de lunes a viernes, de 8:00 a 17:00.</p><p>Correo de muestra: hola@example.test. Los canales reales de atención se publicarán antes de la apertura.</p>'),
 'envios'=>array('Envíos','<h2>Tu pedido, directo a tu puerta</h2><p>En esta demostración, el envío está incluido. Las zonas de cobertura y los tiempos de entrega se confirmarán antes de abrir la tienda al público.</p>'),
 'devoluciones'=>array('Devoluciones','<h2>Queremos que compres con confianza</h2><p>Esta página contiene texto de muestra. Los plazos, condiciones y canales de devolución se publicarán antes de habilitar compras reales.</p>'),
 'nosotros'=>array('Sobre nosotros','<h2>Buenas cosas, más cerca de ti.</h2><p>Creemos que encontrar algo bueno debería ser fácil. Por eso reunimos productos para cuidarte, disfrutar tu hogar y acompañar a tu familia, todo en un mismo lugar.</p><p>Somos GA·MI·LEA: una forma más simple de comprar para tu día a día.</p>'),
 'terminos'=>array('Términos y condiciones','<h2>Tienda de demostración</h2><p>Este sitio está en desarrollo. Los productos, precios, marcas y condiciones comerciales son ejemplos. No se procesan pagos reales. El texto legal definitivo se incorporará antes de publicar la tienda.</p>'),
 'privacidad'=>array('Política de privacidad','<h2>Privacidad en esta demostración</h2><p>El formulario de suscripción guarda tu correo y consentimiento en la base de datos local de WordPress. No se envían campañas ni se comparten estos datos con un servicio de correo.</p><p>Los favoritos se guardan en tu navegador. WooCommerce utiliza cookies para mantener tu carrito. La política definitiva y el canal de atención de solicitudes se incorporarán antes de la apertura.</p>'),
 'marcas'=>array('Nuestras marcas','<h2>Un mundo de posibilidades</h2><p>Samsung · Philips · Xiaomi · L’Oréal · Chicco · JBL · TP-Link · Nespresso</p><p>Marcas mostradas únicamente para ilustrar el diseño. No existe una relación comercial declarada y los productos son ficticios.</p>'),
);
foreach($pages as $slug=>$page) { if(!get_page_by_path($slug)) { wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$page[0],'post_content'=>$page[1])); } }
update_option('blogname','GA·MI·LEA');update_option('blogdescription','Buenas cosas, más cerca de ti.');
// Keep storefront public locally; no payment gateway is enabled by this seed.
update_option('woocommerce_coming_soon','no');
$zone=new WC_Shipping_Zone(0);
$has_free=false;foreach($zone->get_shipping_methods() as $method) { if('free_shipping'===$method->id) $has_free=true; }
if(!$has_free) { $instance=$zone->add_shipping_method('free_shipping'); update_option('woocommerce_free_shipping_'.$instance.'_settings',array('title'=>'Envío incluido','requires'=>'')); }
wc_delete_product_transients();
WP_CLI::success('Demo catalogue ready.');
