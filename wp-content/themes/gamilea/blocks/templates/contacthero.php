<?php
/** Figma 230:2 — portada de la página de contacto. */
defined('ABSPATH') || exit;
$items = get_field('items') ?: gamilea_contact_default_assurances();
?>
<section class="contact-hero">
    <div class="contact-inner">
        <?php $eyebrow = gamilea_field('eyebrow', 'AYUDA Y SOPORTE'); if ($eyebrow) : ?><p class="contact-eyebrow"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
        <h1><?php echo nl2br(esc_html(gamilea_field('title', 'Estamos aquí para ayudarte'))); ?></h1>
        <p class="contact-lede"><?php echo nl2br(esc_html(gamilea_field('description', ''))); ?></p>
        <?php if ($items) : ?>
        <ul class="contact-assurance">
            <?php foreach ($items as $item) : if (empty($item['title'])) { continue; }
                // Sin enlace la garantía se queda como texto, que es el estado por defecto del diseño.
                $url = gamilea_link_url($item['url'] ?? '');
            ?>
                <li><?php if ($url) : ?><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($item['title']); ?></a><?php else : ?><span><?php echo esc_html($item['title']); ?></span><?php endif; ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
</section>
