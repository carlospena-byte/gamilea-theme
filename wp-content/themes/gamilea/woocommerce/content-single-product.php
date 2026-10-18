<?php
/** GA·MI·LEA product page, laid out after the Figma PDP; related products always close the page. */
defined('ABSPATH') || exit;
global $product;

do_action('woocommerce_before_single_product');
if (post_password_required()) { echo get_the_password_form(); return; }

$pdp = gamilea_pdp_data($product);
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('gamilea-pdp', $product); ?>>
    <div class="gamilea-pdp__top">
        <div class="gamilea-pdp__media">
            <div class="gamilea-pdp__photo"><?php do_action('woocommerce_before_single_product_summary'); ?></div>
            <?php if ($pdp['features']) : ?>
                <ul class="gamilea-pdp__features">
                    <?php foreach ($pdp['features'] as $feature) : ?>
                        <li><strong><?php echo esc_html($feature['value']); ?></strong><span><?php echo esc_html($feature['label']); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="gamilea-pdp__buy">
            <?php if ($pdp['eyebrow']) : ?><p class="gamilea-pdp__eyebrow"><?php echo esc_html($pdp['eyebrow']); ?></p><?php endif; ?>
            <h1 class="product_title entry-title"><?php echo gamilea_product_title($product); ?></h1>
            <?php if ($pdp['subtitle']) : ?><p class="gamilea-pdp__subtitle"><?php echo esc_html($pdp['subtitle']); ?></p><?php endif; ?>

            <div class="gamilea-pdp__price-row">
                <p class="price"><?php echo wp_kses_post($product->get_price_html()); ?></p>
                <span class="gamilea-pdp__badge"><?php echo esc_html(gamilea_pdp_option('ship_badge')); ?></span>
            </div>

            <div class="gamilea-pdp__delivery">
                <strong><?php echo esc_html(gamilea_pdp_option('ship_headline')); ?></strong>
                <span><?php echo esc_html(gamilea_pdp_option('ship_detail')); ?></span>
            </div>

            <?php if ($pdp['promo']) : ?>
                <div class="gamilea-pdp__promo">
                    <strong><?php echo esc_html($pdp['promo_title']); ?></strong>
                    <div class="gamilea-pdp__promo-row">
                        <span class="gamilea-pdp__countdown" data-gamilea-countdown role="timer" aria-label="Tiempo restante de la oferta">00d : 00h : 00m : 00s</span>
                        <span class="gamilea-pdp__promo-end">Termina el domingo<br>11:59 p. m. · El Salvador</span>
                    </div>
                </div>
            <?php endif; ?>

            <p class="gamilea-pdp__stock <?php echo $product->is_in_stock() ? 'is-in' : 'is-out'; ?>"><?php echo $product->is_in_stock() ? 'Disponible para envío' : 'Agotado'; ?></p>

            <div class="gamilea-pdp__cart"><?php woocommerce_template_single_add_to_cart(); ?></div>
        </div>
    </div>

    <?php if ($pdp['about_intro'] || $pdp['about'] || $pdp['purchase'] || gamilea_pdp_option('ship_policy')) : ?>
        <div class="gamilea-pdp__details">
            <section class="gamilea-pdp__info">
                <?php if ($pdp['about_intro'] || $pdp['about']) : ?>
                    <h2><?php echo esc_html($pdp['about_title']); ?></h2>
                    <?php if ($pdp['about_intro']) : ?><div class="gamilea-pdp__text"><?php echo wp_kses_post($pdp['about_intro']); ?></div><?php endif; ?>
                    <?php foreach ($pdp['about'] as $row) : ?>
                        <?php if ($row['title']) : ?><h3><?php echo esc_html($row['title']); ?></h3><?php endif; ?>
                        <?php if ($row['text']) : ?><div class="gamilea-pdp__text"><?php echo wp_kses_post(wpautop(esc_html($row['text']))); ?></div><?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
            <section class="gamilea-pdp__info">
                <h2><?php echo esc_html(gamilea_pdp_option('buy_title')); ?></h2>
                <h3>Entrega y costo de envío</h3>
                <div class="gamilea-pdp__text"><?php echo wp_kses_post(wpautop(esc_html(gamilea_pdp_option('ship_policy')))); ?></div>
                <?php foreach ($pdp['purchase'] as $row) : ?>
                    <?php if ($row['title']) : ?><h3><?php echo esc_html($row['title']); ?></h3><?php endif; ?>
                    <?php if ($row['text']) : ?><div class="gamilea-pdp__text"><?php echo wp_kses_post(wpautop(esc_html($row['text']))); ?></div><?php endif; ?>
                <?php endforeach; ?>
            </section>
        </div>
    <?php endif; ?>

    <?php if ($pdp['legal']) : ?><p class="gamilea-pdp__legal"><?php echo esc_html($pdp['legal']); ?></p><?php endif; ?>

    <?php do_action('woocommerce_after_single_product_summary'); // Related products only (tabs/upsells are removed). ?>
</div>
<?php do_action('woocommerce_after_single_product'); ?>
