<?php defined('ABSPATH') || exit; ?>
<section class="home-hero" aria-labelledby="hero-title">
    <img class="hero-background" src="<?php echo esc_url(get_field('image') ?: gamilea_asset('imgHero.png')); ?>" alt="Una caja de GA·MI·LEA lista para llegar a tu hogar" fetchpriority="high">
    <div class="container hero-inner"><div class="hero-copy"><h1 id="hero-title"><?php echo nl2br(esc_html(gamilea_field('title', "Todo lo que necesitas,\n directo a tu puerta."))); ?></h1><p class="desktop-intro"><?php echo nl2br(esc_html(gamilea_field('description', "Encuentra productos para ti, tu familia y tu hogar.\nEl envío ya está incluido. Tú solo compra y espera."))); ?></p><p class="mobile-intro"><?php echo nl2br(esc_html(gamilea_field('mobileDescription', "Para ti, tu familia y tu hogar.\nEl envío ya está incluido."))); ?></p><a class="button hero-cta" href="<?php echo esc_url(get_field('url') ?: gamilea_shop_url()); ?>"><?php echo nl2br(esc_html(gamilea_field('button', 'Explorar productos'))); ?> <?php echo tienda_icon('arrow'); ?></a></div>
        <p class="hero-benefits"><?php echo nl2br(esc_html(gamilea_field('benefits', 'Envío incluido　 Compra segura　 Soporte siempre'))); ?></p>
        <img class="mobile-hero-photo" src="<?php echo esc_url(get_field('image') ?: gamilea_asset('imgHero.png')); ?>" alt="Caja GA·MI·LEA en tu hogar">
        <p class="mobile-trust"><?php echo nl2br(esc_html(gamilea_field('mobileBenefits', 'Envío incluido · Compra segura'))); ?></p>
    </div>
</section>
