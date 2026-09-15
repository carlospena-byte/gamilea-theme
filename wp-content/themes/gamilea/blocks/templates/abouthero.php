<?php
/** Figma 210:2275 — portada de la página sobre nosotros. */
defined('ABSPATH') || exit;
$image = get_field('image') ?: gamilea_about_asset('hero.jpg');
$badge_title = gamilea_field('badgeTitle', '✓ Envío incluido en tu compra');
$badge_description = gamilea_field('badgeDescription', 'El precio que ves es el precio final.');
?>
<section class="about-hero">
    <div class="about-inner about-hero-grid">
        <div class="about-hero-copy">
            <?php $eyebrow = gamilea_field('eyebrow', 'COMPRA CON CONFIANZA'); if ($eyebrow) : ?><p class="about-eyebrow"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
            <h1><?php echo nl2br(esc_html(gamilea_field('title', ''))); ?></h1>
            <p class="about-lede"><?php echo nl2br(esc_html(gamilea_field('description', ''))); ?></p>
        </div>
        <figure class="about-hero-media">
            <img src="<?php echo esc_url($image); ?>" width="520" height="420" alt="" loading="lazy">
            <?php if ($badge_title || $badge_description) : ?>
            <figcaption class="about-hero-badge">
                <?php if ($badge_title) : ?><strong><?php echo esc_html($badge_title); ?></strong><?php endif; ?>
                <?php if ($badge_description) : ?><span><?php echo esc_html($badge_description); ?></span><?php endif; ?>
            </figcaption>
            <?php endif; ?>
        </figure>
    </div>
</section>
