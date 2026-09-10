<?php defined('ABSPATH') || exit; ?>
<section class="categories-section" id="categories"><div class="section-heading"><h2><?php echo nl2br(esc_html(gamilea_field('title', 'Explora nuestras categorías'))); ?></h2><a class="text-link" href="<?php echo esc_url(get_field('url') ?: gamilea_shop_url()); ?>"><?php echo nl2br(esc_html(gamilea_field('linkLabel', 'Ver todas las categorías'))); ?> <?php echo tienda_icon('arrow'); ?></a></div><div class="category-grid">
<?php $card_label = gamilea_field('cardLabel', 'Explorar'); foreach ((get_field('items') ?: gamilea_default_categories()) as $item) : if (!is_array($item) || empty($item['category'])) { continue; }
    $term = get_term((int) $item['category'], 'product_cat');
    if (!$term || is_wp_error($term)) { continue; }
    $link = get_term_link($term);
    if (is_wp_error($link)) { $link = gamilea_shop_url(); }
    $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
    $image = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : '';
?>
<a class="category-card category-<?php echo esc_attr(sanitize_html_class($term->slug)); ?>" href="<?php echo esc_url($link); ?>"><div class="category-photo <?php echo $image ? 'gamilea-custom-photo' : ''; ?>"><img src="<?php echo esc_url($image ?: gamilea_asset('imgPhoto.png')); ?>" alt="<?php echo esc_attr($term->name); ?>" loading="lazy"></div><div class="category-copy"><h3><?php echo esc_html($term->name); ?></h3><p><?php echo esc_html($term->description); ?></p><span><?php echo esc_html($card_label); ?> <?php echo tienda_icon('arrow'); ?></span></div></a>
<?php endforeach; ?>
</div></section>
