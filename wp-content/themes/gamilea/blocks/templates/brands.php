<?php defined('ABSPATH') || exit;
$brand_items = array_values(array_filter((get_field('items') ?: gamilea_default_brands()), 'is_array'));
$render_brand_logo = function ($item, $hidden = false) {
    $item = wp_parse_args($item, array('title'=>'','image'=>''));
    echo '<span class="brand-logo"' . ($hidden ? ' aria-hidden="true"' : '') . '>';
    if ($item['image']) {
        echo '<img src="' . esc_url($item['image']) . '" alt="' . esc_attr($item['title']) . '" loading="lazy">';
    } else {
        echo '<span class="brand-logo-placeholder">' . esc_html($item['title']) . '</span>';
    }
    echo '</span>';
};
?>
<section class="brands-section"><div class="section-heading"><h2><?php echo nl2br(esc_html(gamilea_field('title', 'Marcas que encuentras con nosotros'))); ?></h2><a class="text-link" href="<?php echo esc_url(gamilea_field('url', '/marcas/')); ?>"><?php echo nl2br(esc_html(gamilea_field('linkLabel', 'Ver todas las marcas'))); ?> <?php echo tienda_icon('arrow'); ?></a></div><div class="brands-carousel"><div class="brands-track">
<?php
foreach ($brand_items as $item) { $render_brand_logo($item); }
// Repeated verbatim (hidden from assistive tech) so the CSS animation can loop seamlessly.
foreach ($brand_items as $item) { $render_brand_logo($item, true); }
?>
</div></div></section>
