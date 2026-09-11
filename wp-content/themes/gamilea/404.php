<?php defined('ABSPATH') || exit; get_header(); ?>
<?php gamilea_empty_state(array(
    'icon' => 'search',
    'tag' => 'h1',
    'title' => '¡Ups! Esta página no existe',
    'description' => 'Puede que el enlace esté roto o la página se haya movido. Prueba a buscar lo que necesitas o vuelve a la tienda.',
    'search' => true,
    'primary_label' => 'Ir a la tienda',
    'primary_url' => gamilea_shop_url(),
    'secondary_label' => 'Volver al inicio',
    'secondary_url' => home_url('/'),
)); ?>
<?php gamilea_recovery_sections(); ?>
<?php get_footer(); ?>
