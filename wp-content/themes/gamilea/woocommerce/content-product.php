<?php
/** GA·MI·LEA card shared by home, catalog, categories and related products. */
defined('ABSPATH') || exit;
global $product;
if (!$product || !$product->is_visible()) { return; }
?>
<li <?php wc_product_class('gamilea-catalog-item', $product); ?>><?php tienda_product_card($product); ?></li>
