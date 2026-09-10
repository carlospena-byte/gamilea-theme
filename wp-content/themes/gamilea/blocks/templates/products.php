<?php defined('ABSPATH') || exit; ?>
<?php if (!function_exists('wc_get_products')) { return; } ?>
<section id="bestsellers" class="bestsellers-section"><div class="section-heading product-heading"><h2 id="collection-heading"><?php echo nl2br(esc_html(gamilea_field('title', 'Más vendidos'))); ?></h2><div class="product-tabs" role="tablist" aria-label="Colección de productos"><button id="tab-popular" role="tab" aria-selected="true" aria-controls="panel-popular" data-collection="popular">Más vendidos</button><button id="tab-new" role="tab" aria-selected="false" aria-controls="panel-new" tabindex="-1" data-collection="new">Nuevos</button><button id="tab-sale" role="tab" aria-selected="false" aria-controls="panel-sale" tabindex="-1" data-collection="sale">En oferta</button></div><a class="text-link" href="<?php echo esc_url(get_field('url') ?: gamilea_shop_url()); ?>"><?php echo nl2br(esc_html(gamilea_field('linkLabel', 'Ver todos'))); ?> <?php echo tienda_icon('arrow'); ?></a></div>
<?php $limit = (int) (get_field('limit') ?: 5); foreach(array('popular','new','sale') as $collection) :
    $args = array('status'=>'publish','limit'=>max(1,min(20,$limit)),'orderby'=>'popularity','order'=>'DESC');
    if ('new' === $collection) { $args = array('status'=>'publish','limit'=>max(1,min(20,$limit)),'orderby'=>'date','order'=>'DESC'); }
    if ('sale' === $collection) { $args = array('status'=>'publish','limit'=>max(1,min(20,$limit)),'include'=>array_merge(array(0),wc_get_product_ids_on_sale())); }
    $products = wc_get_products($args);
?><div class="product-grid" role="tabpanel" id="panel-<?php echo esc_attr($collection); ?>" aria-labelledby="tab-<?php echo esc_attr($collection); ?>" <?php echo 'popular' !== $collection ? 'hidden' : ''; ?>><?php foreach($products as $product) { tienda_product_card($product); } ?><?php if (!$products) : ?><p>Pronto encontrarás novedades aquí.</p><?php endif; ?></div><?php endforeach; ?>
</section>
