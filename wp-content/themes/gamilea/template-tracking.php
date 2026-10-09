<?php
/** Order tracking page (Figma: Seguimiento de pedido / Desktop); rendered by inc/drop-tracking.php for /?gamilea_tracking=ID&key=KEY. */
defined( 'ABSPATH' ) || exit;

$order = gamilea_tracking_requested_order();
if ( ! $order ) { status_header( 404 ); }

$provider = $order ? gamilea_order_tracking_url_for_provider( $order ) : '';
$shipment = $provider ? gamilea_fetch_shipment( $provider ) : null;
$view     = $order ? gamilea_tracking_view( $order, $shipment ) : null;
$icon     = function ( $name, $class = '' ) { return gamilea_image( 'tracking/' . $name . '.svg', $class ); };
$badge    = function ( $name, $class = '' ) use ( $icon ) { return '<span class="trk-badge ' . esc_attr( $class ) . '">' . $icon( $name ) . '</span>'; };
$day      = function ( $ts ) { return wp_date( 'j \d\e F', $ts ); };
$time     = function ( $ts ) { return wp_date( 'g:i', $ts ) . ' ' . ( 'am' === wp_date( 'a', $ts ) ? 'a. m.' : 'p. m.' ); };

get_header(); ?>
<div class="gamilea-tracking">
<?php if ( ! $order ) : ?>
    <h1>No encontramos este pedido</h1>
    <p>El enlace de seguimiento no es válido. Revisa el correo de tu pedido o <a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">escríbenos</a>.</p>
<?php else :
    $owner      = is_user_logged_in() && (int) $order->get_user_id() === get_current_user_id();
    $created    = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : 0;
    $first_name = $order->get_shipping_first_name() ?: $order->get_billing_first_name();
    $same_addr  = $order->get_formatted_shipping_address() === $order->get_formatted_billing_address() || ! $order->has_shipping_address();
    $phone      = $order->get_shipping_phone() ?: $order->get_billing_phone();
    $masked     = gamilea_mask_email( $order->get_billing_email() );
    $items      = $order->get_items();
    $count      = array_sum( array_map( function ( $i ) { return $i->get_quantity(); }, $items ) );
    $stages     = array( 'Confirmado', 'Preparado', 'En camino', 'Entregado' );
    $stage_icon = array( 'check', 'box-head', 'truck-small', 'home' );
    $progress   = $view['stage'] / 3 * 75;
?>
    <?php if ( $owner ) : ?>
    <nav class="trk-crumbs" aria-label="Ruta de navegación">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Mi cuenta</a><?php echo $icon( 'chevron' ); ?>
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">Mis pedidos</a><?php echo $icon( 'chevron' ); ?>
        <strong>Pedido #<?php echo esc_html( $order->get_order_number() ); ?></strong>
    </nav>
    <?php endif; ?>

    <section class="trk-hero">
        <div class="trk-hero-title">
            <span class="trk-badge trk-badge-xl"><?php echo $icon( 'truck-big' ); ?></span>
            <div>
                <h1>Seguimiento de tu pedido</h1>
                <p><?php echo esc_html( $first_name ? $first_name . ', ' . $view['sentence'] : ucfirst( $view['sentence'] ) ); ?> Aquí puedes ver cada paso de su recorrido.</p>
            </div>
        </div>
        <div class="trk-order-card">
            <?php echo $icon( 'box', 'trk-order-icon' ); ?>
            <div>
                <span>Número de pedido</span>
                <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
                <em><?php echo esc_html( $view['label'] ); ?></em>
            </div>
            <span class="trk-pill"><?php echo esc_html( $view['chip'] ); ?></span>
        </div>
    </section>

    <section class="trk-facts" aria-label="Datos de la compra">
        <div class="trk-fact"><?php echo $badge( 'calendar' ); ?><div><span>Fecha de pedido</span><strong><?php echo esc_html( wp_date( 'j \d\e F, Y', $created ) ); ?></strong><small><?php echo esc_html( $time( $created ) ); ?></small></div></div>
        <?php if ( $masked ) : ?><div class="trk-fact"><?php echo $badge( 'mail' ); ?><div><span>Correo electrónico</span><strong><?php echo esc_html( $masked ); ?></strong></div></div><?php endif; ?>
        <div class="trk-fact"><?php echo $badge( 'card' ); ?><div><span>Total</span><strong class="trk-fact-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></div></div>
        <div class="trk-fact"><?php echo $badge( 'wallet' ); ?><div><span>Método de pago</span><strong><?php echo esc_html( $order->get_payment_method_title() ); ?></strong><?php if ( 'cod' === $order->get_payment_method() ) : ?><small>Paga en efectivo al recibir tu pedido.</small><?php endif; ?></div></div>
    </section>

    <div class="trk-columns">
        <div class="trk-main">
            <section class="trk-card">
                <header class="trk-card-head">
                    <?php echo $badge( 'box-head' ); ?><h2>Progreso del pedido</h2><span class="trk-pill trk-pill-sage"><?php echo esc_html( $view['label'] ); ?></span>
                </header>
                <ol class="trk-stages" style="--trk-progress:<?php echo esc_attr( $progress ); ?>%">
                    <?php foreach ( $stages as $i => $name ) :
                        $state = $i < $view['stage'] || 3 === $view['stage'] ? 'done' : ( $i === $view['stage'] ? 'current' : 'pending' );
                        $ts    = $view['stage_dates'][ $i ]; ?>
                        <li class="is-<?php echo esc_attr( $state ); ?>">
                            <span class="trk-stage-dot"><?php echo $icon( 'done' === $state ? 'check' : $stage_icon[ $i ] ); ?></span>
                            <strong><?php echo esc_html( $name ); ?></strong>
                            <small><?php echo esc_html( $ts ? $day( $ts ) : ( 'pending' === $state ? 'Pendiente' : '' ) ); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <hr>
                <div class="trk-history-head"><h3>Detalle del seguimiento</h3><span>Más reciente primero</span></div>
                <ol class="trk-history">
                    <?php foreach ( $view['rows'] as $i => $row ) : ?>
                        <li<?php echo 0 === $i ? ' class="is-current"' : ''; ?>>
                            <div class="trk-when"><strong><?php echo esc_html( $day( $row['ts'] ) ); ?></strong><small><?php echo esc_html( $time( $row['ts'] ) ); ?></small></div>
                            <span class="trk-point"><?php echo $icon( 0 === $i ? 'dot-current' : 'dot' ); ?></span>
                            <div class="trk-what"><strong><?php echo esc_html( $row['title'] ); ?></strong><?php if ( $row['desc'] ) : ?><p><?php echo esc_html( $row['desc'] ); ?></p><?php endif; ?></div>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <p class="trk-updated">Última actualización: <?php echo esc_html( wp_date( 'j \d\e F \d\e Y', $view['updated'] ) . ', ' . $time( $view['updated'] ) ); ?></p>
            </section>

            <section class="trk-card">
                <header class="trk-card-head">
                    <?php echo $badge( 'bag' ); ?><h2>Productos de tu pedido</h2><span class="trk-count"><?php echo esc_html( $count . ( 1 === $count ? ' producto' : ' productos' ) ); ?></span>
                </header>
                <?php foreach ( $items as $item ) : $product = $item->get_product(); ?>
                    <div class="trk-product">
                        <div class="trk-product-img"><?php echo $product ? wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ) : ''; ?></div>
                        <div class="trk-product-info"><strong><?php echo esc_html( $item->get_name() ); ?></strong><span>Cantidad: <?php echo esc_html( $item->get_quantity() ); ?></span></div>
                        <b><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></b>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="trk-actions">
                    <?php if ( $owner ) : ?><a class="trk-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">← Volver a mis pedidos</a><?php else : ?><span></span><?php endif; ?>
                    <a class="trk-button-outline" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Seguir comprando →</a>
                </div>
            </section>
        </div>

        <aside class="trk-side">
            <section class="trk-card">
                <header class="trk-card-head"><?php echo $badge( 'truck-small' ); ?><h2>Información de entrega</h2></header>
                <?php if ( $shipment && $shipment['eta'] && strtotime( $shipment['eta'] ) && 3 !== $view['stage'] ) : $eta = strtotime( $shipment['eta'] ); ?>
                    <div class="trk-eta"><span>Entrega estimada</span><strong><?php echo esc_html( ucfirst( wp_date( 'l j \d\e F', $eta ) ) ); ?></strong></div>
                <?php endif; ?>
                <div class="trk-address">
                    <span>Dirección de envío</span>
                    <p class="trk-name"><?php echo esc_html( trim( ( $order->get_shipping_first_name() ?: $order->get_billing_first_name() ) . ' ' . ( $order->get_shipping_last_name() ?: $order->get_billing_last_name() ) ) ); ?></p>
                    <p><?php echo wp_kses_post( implode( '<br>', array_filter( array( esc_html( $order->get_shipping_address_1() ?: $order->get_billing_address_1() ), esc_html( $order->get_shipping_address_2() ?: $order->get_billing_address_2() ) ) ) ) ); ?></p>
                    <?php if ( $phone ) : ?><p><?php echo esc_html( $phone ); ?></p><?php endif; ?>
                    <small>Dirección de facturación: <?php echo $same_addr ? 'igual a la de envío.' : 'distinta a la de envío.'; ?></small>
                </div>
                <?php if ( $shipment && ( $shipment['courier'] || $shipment['number'] ) ) : ?>
                    <hr>
                    <div class="trk-ship">
                        <?php if ( $shipment['courier'] ) : ?><div><span>Servicio de entrega</span><strong><?php echo esc_html( $shipment['courier'] ); ?></strong></div><?php endif; ?>
                        <?php if ( $shipment['number'] ) : ?><div><span>Número de guía</span><strong><?php echo esc_html( $shipment['number'] ); ?></strong></div><?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="trk-card trk-summary">
                <h2>Resumen del pedido</h2>
                <p>Subtotal: <?php echo wp_kses_post( wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ) ); ?></p>
                <p class="trk-ok"><?php echo $order->get_shipping_total() > 0 ? 'Envío: ' . wp_kses_post( wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ) ) : 'Envío incluido'; ?></p>
                <p class="trk-total">Total: <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></p>
                <p class="trk-muted"><?php echo esc_html( $order->get_payment_method_title() . ( 'cod' === $order->get_payment_method() ? ' · Paga en efectivo al recibir tu pedido.' : '' ) ); ?></p>
            </section>

            <section class="trk-card">
                <div class="trk-help"><?php echo $badge( 'headset' ); ?><div><strong>¿Necesitas ayuda?</strong><p>Estamos aquí para ayudarte con tu entrega.</p></div></div>
                <a class="trk-button-dark" href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">Contactarnos</a>
                <a class="trk-link trk-center" href="<?php echo esc_url( home_url( '/ayuda/' ) ); ?>">Ver preguntas frecuentes →</a>
            </section>
        </aside>
    </div>
<?php endif; ?>
</div>
<?php get_footer();
