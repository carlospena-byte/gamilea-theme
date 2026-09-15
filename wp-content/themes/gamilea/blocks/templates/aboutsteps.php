<?php
/** Figma 210:2287 — los cuatro pasos de compra. */
defined('ABSPATH') || exit;
$items = get_field('items') ?: gamilea_about_default_steps();
?>
<section class="about-steps">
    <div class="about-inner">
        <?php $eyebrow = gamilea_field('eyebrow', 'ASÍ FUNCIONA'); if ($eyebrow) : ?><p class="about-eyebrow"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
        <h2><?php echo nl2br(esc_html(gamilea_field('title', ''))); ?></h2>
        <ol class="about-step-grid">
            <?php foreach (array_values($items) as $i => $item) : if (!is_array($item) || empty($item['title'])) { continue; } ?>
                <li class="about-step">
                    <?php /* El número es decoración: el orden ya lo da la lista, así que no se lee dos veces. */ ?>
                    <span class="about-step-number" aria-hidden="true"><?php echo esc_html(str_pad($i + 1, 2, '0', STR_PAD_LEFT)); ?></span>
                    <h3><?php echo esc_html($item['title']); ?></h3>
                    <p><?php echo nl2br(esc_html($item['description'] ?? '')); ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
