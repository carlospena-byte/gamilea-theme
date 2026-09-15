<?php
/** Figma 210:2282 — motivos para confiar en la tienda. */
defined('ABSPATH') || exit;
$items = get_field('items') ?: gamilea_about_default_values();
?>
<section class="about-values">
    <div class="about-inner">
        <?php $eyebrow = gamilea_field('eyebrow', 'POR QUÉ CONFIAR EN GAMILEA'); if ($eyebrow) : ?><p class="about-eyebrow"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
        <h2><?php echo nl2br(esc_html(gamilea_field('title', ''))); ?></h2>
        <div class="about-value-grid">
            <?php foreach ($items as $item) : if (!is_array($item) || empty($item['title'])) { continue; } ?>
                <article class="about-value">
                    <h3><span aria-hidden="true">&check;</span> <?php echo esc_html($item['title']); ?></h3>
                    <p><?php echo nl2br(esc_html($item['description'] ?? '')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
