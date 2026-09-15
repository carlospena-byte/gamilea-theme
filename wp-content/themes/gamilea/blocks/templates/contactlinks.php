<?php
/** Figma 230:63 — accesos rápidos de autoservicio. */
defined('ABSPATH') || exit;
$items = get_field('items') ?: gamilea_contact_default_links();
?>
<section class="contact-self-service">
    <div class="contact-inner">
        <h2><?php echo nl2br(esc_html(gamilea_field('title', 'Quizá podamos resolverlo ahora'))); ?></h2>
        <p class="contact-self-service-intro"><?php echo nl2br(esc_html(gamilea_field('description', ''))); ?></p>
        <div class="contact-quick-links">
            <?php foreach ($items as $item) : if (!is_array($item)) { continue; }
                $url = gamilea_link_url($item['url'] ?? '');
            ?>
                <a class="contact-quick-link" href="<?php echo esc_url($url ?: '#'); ?>">
                    <strong><?php echo esc_html($item['title'] ?? ''); ?></strong>
                    <span class="contact-quick-link-text"><?php echo nl2br(esc_html($item['description'] ?? '')); ?></span>
                    <span class="contact-arrow-link"><?php echo esc_html($item['linkLabel'] ?? 'Ver información'); ?> <span aria-hidden="true">&rarr;</span></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
