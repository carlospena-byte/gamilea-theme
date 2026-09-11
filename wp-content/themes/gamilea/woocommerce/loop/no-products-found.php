<?php
/** Overrides WooCommerce's plain "no products found" notice with a sales-focused empty state. */
defined('ABSPATH') || exit;

$gamilea_term = is_product_category() ? get_queried_object() : null;
$gamilea_category_name = $gamilea_term && !is_wp_error($gamilea_term) ? $gamilea_term->name : '';

gamilea_empty_state(array(
    'icon' => 'box',
    'title' => $gamilea_category_name ? 'Estamos reabasteciendo la categoría ' . $gamilea_category_name : 'Estamos reabasteciendo esta categoría',
    'description' => 'Pronto tendremos nuevos productos aquí. Mientras tanto, descubre otras categorías o nuestros más vendidos.',
    'primary_label' => 'Ver todos los productos',
    'primary_url' => gamilea_shop_url(),
));
gamilea_recovery_sections($gamilea_term && !is_wp_error($gamilea_term) ? $gamilea_term->term_id : 0);
