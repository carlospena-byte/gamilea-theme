<?php
/** Order tracking page; rendered by inc/drop-tracking.php for /?gamilea_tracking=ID&key=KEY. */
defined( 'ABSPATH' ) || exit;

$order    = gamilea_tracking_requested_order();
$provider = $order ? gamilea_order_tracking_url_for_provider( $order ) : '';
$shipment = $provider ? gamilea_fetch_shipment( $provider ) : null;
$drop     = $order ? (string) $order->get_meta( '_dwc_status' ) : '';
$labels   = array_map( function ( $row ) { return $row[0]; }, gamilea_tracking_email_statuses() );
$fmt      = function ( $iso ) { $t = strtotime( $iso ); return $t ? wp_date( 'j \d\e F, g:i a', $t ) : ''; };

if ( ! $order ) { status_header( 404 ); }
get_header(); ?>
<main class="site-main container gamilea-tracking">
<?php if ( ! $order ) : ?>
    <h1>No encontramos este pedido</h1>
    <p>El enlace de seguimiento no es válido. Revisa el correo de tu pedido o <a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>">escríbenos</a>.</p>
<?php else : ?>
    <header class="tracking-hero">
        <p class="tracking-eyebrow">PEDIDO N.º <?php echo esc_html( $order->get_order_number() ); ?></p>
        <h1>Seguimiento de tu envío</h1>
        <?php if ( $drop && isset( $labels[ $drop ] ) ) : ?><p class="tracking-current"><?php echo esc_html( $labels[ $drop ] ); ?></p><?php endif; ?>
    </header>

    <div class="tracking-grid">
        <section class="tracking-card" aria-label="Historial del envío">
            <h2>Historial</h2>
            <?php if ( $shipment && $shipment['history'] ) : ?>
                <ol class="tracking-timeline">
                    <?php foreach ( $shipment['history'] as $i => $row ) : ?>
                        <li<?php echo 0 === $i ? ' class="is-current"' : ''; ?>><strong><?php echo esc_html( $row['label'] ); ?></strong><time datetime="<?php echo esc_attr( $row['date'] ); ?>"><?php echo esc_html( $fmt( $row['date'] ) ); ?></time></li>
                    <?php endforeach; ?>
                </ol>
            <?php else : ?>
                <p>Todavía no tenemos el detalle del recorrido. Vuelve a revisar en unos minutos.</p>
            <?php endif; ?>
        </section>

        <aside class="tracking-card" aria-label="Detalle del envío">
            <h2>Detalle</h2>
            <dl>
                <?php if ( $shipment && $shipment['courier'] ) : ?><dt>Paquetería</dt><dd><?php echo esc_html( $shipment['courier'] ); ?></dd><?php endif; ?>
                <?php if ( $shipment && $shipment['number'] ) : ?><dt>N.º de guía</dt><dd><?php echo esc_html( $shipment['number'] ); ?></dd><?php endif; ?>
                <?php if ( $shipment && $shipment['eta'] ) : ?><dt>Entrega estimada</dt><dd><?php echo esc_html( wp_date( 'j \d\e F', strtotime( $shipment['eta'] ) ) ); ?></dd><?php endif; ?>
                <?php if ( $shipment && $shipment['cod'] ) : ?><dt>Pago contra entrega</dt><dd><?php echo wp_kses_post( wc_price( $shipment['cod_amount'] ) ); ?></dd><?php endif; ?>
                <dt>Dirección</dt><dd><?php echo wp_kses_post( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() ); ?></dd>
            </dl>
            <?php if ( $provider ) : ?><p><a class="text-link" href="<?php echo esc_url( $provider ); ?>" target="_blank" rel="noopener noreferrer">Ver en la página de la paquetería</a></p><?php endif; ?>
        </aside>
    </div>
<?php endif; ?>
</main>
<?php get_footer();
