<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html><html <?php language_attributes(); ?>><head>
<meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?></head><body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip-link" href="#main">Saltar al contenido</a>
<header class="site-header <?php echo is_front_page() ? 'home-header' : ''; ?>">
    <div class="topbar container"><div class="topbar-benefits"><span>Envío incluido en todo el país</span><span>·</span><span>Pagos seguros</span><span>·</span><span>Seguimiento de tu pedido</span></div><div class="topbar-help"><a href="<?php echo esc_url(home_url('/ayuda/')); ?>">¿Necesitas ayuda?</a><a href="<?php echo esc_url(home_url('/contacto/')); ?>">Contáctanos</a></div></div>
    <div class="nav-shell">
        <div class="navbar-brand">
            <?php echo gamilea_logo(); ?>
        </div>
        <button class="icon-button mobile-menu-toggle" aria-controls="primary-nav" aria-expanded="false" aria-label="Abrir menú"><?php echo tienda_icon('menu'); ?></button>
        <nav id="primary-nav" aria-label="Menú principal"><ul class="menu"><?php foreach ( array('salud'=>'Salud','cuidado-y-belleza'=>'Cuidado y Belleza','tecnologia'=>'Tecnología','hogar'=>'Hogar','bebe'=>'Bebé') as $slug=>$label ) : ?><li><a href="<?php echo esc_url(tienda_category_url($slug)); ?>"><?php echo esc_html($label); ?></a></li><?php endforeach; ?><li><a class="offer-link" href="<?php echo esc_url(home_url('/?collection=sale#bestsellers')); ?>">Ofertas</a></li></ul></nav>
        <form class="header-search" action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search"><label class="screen-reader-text" for="product-search">Buscar productos</label><input id="product-search" type="search" name="s" placeholder="Buscar productos…" value="<?php echo get_search_query(); ?>"><input type="hidden" name="post_type" value="product"><button type="submit" class="icon-button" aria-label="Buscar"><?php echo tienda_icon('search'); ?></button></form>
        <div class="header-actions"><a class="icon-button" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url()); ?>" aria-label="Mi cuenta"><span class="header-glyph glyph-user"><?php echo gamilea_image('imgGroup1.svg'); ?></span></a><button class="icon-button open-favorites" aria-label="Ver favoritos" aria-controls="favorites-dialog"><span class="header-glyph glyph-heart"><?php echo gamilea_image('imgGroup1.svg'); ?></span></button><a class="icon-button cart-link" href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url()); ?>" aria-label="Ver carrito"><span class="header-glyph glyph-cart"><?php echo gamilea_image('imgGroup1.svg'); ?></span><span class="cart-count"><?php echo function_exists('WC') && WC()->cart ? esc_html(WC()->cart->get_cart_contents_count()) : '0'; ?></span></a></div>
    </div>
</header>
<main id="main" class="<?php echo is_front_page() ? 'home-main' : 'container site-main'; ?>">
