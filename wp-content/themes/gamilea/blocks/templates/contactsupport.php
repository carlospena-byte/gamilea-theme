<?php
/** Figma 230:13 — canales de atención y formulario de Fluent Forms. */
defined('ABSPATH') || exit;
$items = get_field('items') ?: gamilea_contact_default_channels();
/** Iconos del diseño; solo se usan mientras el canal no tenga uno propio subido. */
$fallback_icons = array('whatsapp.svg', 'mail.svg', 'package.svg');
$image = get_field('image');
$form_id = (int) get_field('formId');
?>
<section class="contact-main">
    <div class="contact-inner contact-columns">

        <div class="contact-channels">
            <?php if ($image) : ?><img class="contact-lifestyle" src="<?php echo esc_url($image); ?>" width="520" height="280" alt="" loading="lazy"><?php else : ?><img class="contact-lifestyle" src="<?php echo esc_url(gamilea_contact_asset('lifestyle.jpg')); ?>" width="520" height="280" alt="" loading="lazy"><?php endif; ?>
            <h2><?php echo nl2br(esc_html(gamilea_field('title', '¿Cómo podemos ayudarte?'))); ?></h2>
            <p class="contact-channels-intro"><?php echo nl2br(esc_html(gamilea_field('description', ''))); ?></p>
            <?php foreach (array_values($items) as $i => $item) : if (!is_array($item)) { continue; }
                $icon = !empty($item['image']) ? $item['image'] : gamilea_contact_asset($fallback_icons[$i % 3]);
                $url = gamilea_link_url($item['url'] ?? '');
            ?>
                <article class="contact-channel">
                    <span class="contact-channel-icon"><img src="<?php echo esc_url($icon); ?>" width="24" height="24" alt="" aria-hidden="true"></span>
                    <div class="contact-channel-body">
                        <h3><?php echo esc_html($item['title'] ?? ''); ?></h3>
                        <p><?php echo nl2br(esc_html($item['description'] ?? '')); ?></p>
                        <?php echo gamilea_contact_arrow_link($item['linkLabel'] ?? '', $url); ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="contact-form-card" id="contact-form">
            <h2><?php echo nl2br(esc_html(gamilea_field('formTitle', 'Envíanos un mensaje'))); ?></h2>
            <p class="contact-form-intro"><?php echo nl2br(esc_html(gamilea_field('formDescription', ''))); ?></p>
            <?php
            if ($form_id) {
                echo do_shortcode('[fluentform id="' . $form_id . '"]');
            } elseif (is_admin()) {
                // Aviso solo visible en el editor: en el frente la tarjeta se queda sin formulario.
                echo '<p class="contact-form-placeholder">Elige un formulario de Fluent Forms en los ajustes de este bloque.</p>';
            }
            ?>
        </div>

    </div>
</section>
