<?php
/** WordPress navigation and ACF-managed supporting information. */
defined('ABSPATH') || exit;
$title = function_exists('get_field') ? get_field('gamilea_mobile_menu_title', 'option') : null;
if ($title === null || $title === false) { $title = 'GA·MI·LEA'; }
$sections = function_exists('get_field') ? get_field('gamilea_mobile_menu_info', 'option') : array();
?>
<dialog id="mobile-menu" class="mobile-menu-panel" aria-label="Menú principal">
    <div class="mobile-menu-heading">
        <span class="mobile-menu-title"><?php echo esc_html($title); ?></span>
        <button class="icon-button mobile-menu-close" type="button" aria-label="Cerrar menú" autofocus><?php echo tienda_icon('close'); ?></button>
    </div>
    <nav aria-label="Navegación móvil">
        <?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_id' => 'mobile-primary-menu', 'menu_class' => 'mobile-menu-links', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'fallback_cb' => 'gamilea_primary_menu_fallback')); ?>
    </nav>
    <?php if (is_array($sections) && $sections) : ?>
        <div class="mobile-menu-information">
            <?php foreach ($sections as $section) :
                if (empty($section['heading']) && empty($section['content'])) { continue; }
                ?>
                <section class="mobile-menu-info-block">
                    <?php if (!empty($section['heading'])) : ?><h2><?php echo esc_html($section['heading']); ?></h2><?php endif; ?>
                    <?php if (!empty($section['content'])) : ?><div><?php echo wp_kses_post($section['content']); ?></div><?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</dialog>
