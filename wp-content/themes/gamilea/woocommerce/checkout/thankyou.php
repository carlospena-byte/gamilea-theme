<?php
/**
 * Order confirmation, adapted from Figma 187:2234.
 * @version 8.1.0
 * @var WC_Order|false $order Validated by WooCommerce before rendering.
 */
defined( 'ABSPATH' ) || exit;

// Keep WooCommerce's recovery and generic responses for unsuccessful/unknown orders.
if ( ! $order || $order->has_status( array( 'failed', 'cancelled', 'refunded' ) ) ) {
    include WC()->plugin_path() . '/templates/checkout/thankyou.php';
    return;
}
$date = $order->get_date_created();
$contact_url = home_url( '/contacto/' );
$payment_note = 'cod' === $order->get_payment_method() ? 'Paga en efectivo al recibir tu pedido.' : '';
?>
<div class="woocommerce-order gamilea-order-success">
    <?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>
    <div class="order-success-banner">
        <div class="order-success-intro">
            <?php echo gamilea_order_icon( 'check', 'order-icon-success' ); ?>
            <div><h1>¡Gracias por tu compra!</h1><p><?php echo esc_html( $order->has_status( 'completed' ) ? 'Tu pedido ha sido completado. Gracias por elegirnos.' : ( $order->needs_payment() ? 'Tu pedido ha sido recibido. Completa el pago para que podamos prepararlo.' : 'Tu pedido ha sido recibido y ya estamos preparando todo para que llegue muy pronto.' ) ); ?></p></div>
        </div>
        <div class="order-number-card">
            <?php echo gamilea_order_icon( 'box', 'order-icon-box' ); ?>
            <div><span>Número de pedido</span><strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong><small>Te enviaremos un correo de confirmación con todos los detalles.</small></div>
            <a class="button" href="#order-details">Ver mi pedido</a>
        </div>
    </div>
    <dl class="order-metadata">
        <div><?php echo gamilea_order_icon( 'calendar' ); ?><div><dt>Fecha de pedido</dt><dd><?php echo $date ? esc_html( wc_format_datetime( $date, 'j \d\e F, Y' ) ) : '—'; ?><small><?php echo $date ? esc_html( wc_format_datetime( $date, get_option( 'time_format' ) ) ) : ''; ?></small></dd></div></div>
        <div><?php echo gamilea_order_icon( 'mail' ); ?><div><dt>Correo electrónico</dt><dd><?php echo esc_html( $order->get_billing_email() ); ?></dd></div></div>
        <div><?php echo gamilea_order_icon( 'card' ); ?><div><dt>Total</dt><dd class="order-meta-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div></div>
        <div><?php echo gamilea_order_icon( 'wallet' ); ?><div><dt>Método de pago</dt><dd><?php echo esc_html( $order->get_payment_method_title() ); ?><?php if ( $payment_note ) : ?><small><?php echo esc_html( $payment_note ); ?></small><?php endif; ?></dd></div></div>
    </dl>
    <div class="order-success-columns">
        <div class="order-success-main">
            <section class="order-panel" id="order-details" aria-labelledby="order-details-title">
                <h2 id="order-details-title"><?php echo gamilea_order_icon( 'bag' ); ?>Detalles del pedido</h2>
                <?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
                <table class="order-products">
                    <thead><tr><th scope="col">Producto</th><th scope="col">Cantidad</th><th scope="col">Total</th></tr></thead>
                    <tbody>
                    <?php do_action( 'woocommerce_order_details_before_order_table_items', $order ); ?>
                    <?php foreach ( $order->get_items() as $item_id => $item ) :
                        if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) { continue; }
                        $product = $item->get_product();
                        $refunded_qty = $order->get_qty_refunded_for_item( $item_id );
                        ?>
                        <tr><td><div class="order-product-info"><?php if ( $product ) { echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'order-product-image' ) ) ); } ?><div><?php if ( $product && $product->is_visible() ) : ?><a href="<?php echo esc_url( $product->get_permalink( $item ) ); ?>"><?php echo esc_html( $item->get_name() ); ?></a><?php else : echo esc_html( $item->get_name() ); endif; ?><?php wc_display_item_meta( $item ); ?></div></div></td><td><?php echo esc_html( $item->get_quantity() + $refunded_qty ); ?></td><td><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td></tr>
                    <?php endforeach; ?>
                    <?php do_action( 'woocommerce_order_details_after_order_table_items', $order ); ?>
                    </tbody>
                </table>
                <dl class="order-totals">
                    <?php foreach ( $order->get_order_item_totals() as $key => $total ) :
                        if ( 'payment_method' === $key ) { continue; }
                        $value = $total['value'];
                        if ( 'shipping' === $key && 0.0 === (float) $order->get_shipping_total() && 0.0 === (float) $order->get_shipping_tax() ) { $value = 'Envío incluido'; }
                        ?>
                        <div class="<?php echo 'order_total' === $key ? 'order-grand-total' : ''; ?>"><dt><?php echo esc_html( rtrim( wp_strip_all_tags( $total['label'] ), ':' ) ); ?></dt><dd><?php echo wp_kses_post( $value ); ?></dd></div>
                    <?php endforeach; ?>
                </dl>
                <?php if ( $order->get_customer_note() ) : ?><p class="order-customer-note"><strong>Nota del pedido:</strong> <?php echo esc_html( $order->get_customer_note() ); ?></p><?php endif; ?>
                <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
            </section>
            <?php if ( $order->has_downloadable_item() && $order->is_download_permitted() ) { wc_get_template( 'order/order-downloads.php', array( 'downloads' => $order->get_downloadable_items(), 'show_title' => true ) ); } ?>
            <div class="order-addresses">
                <?php foreach ( array( 'billing' => 'Dirección de facturación', 'shipping' => 'Dirección de envío' ) as $type => $label ) :
                    $address = 'billing' === $type ? $order->get_formatted_billing_address() : $order->get_formatted_shipping_address();
                    if ( ! $address ) { continue; }
                    $phone = 'billing' === $type ? $order->get_billing_phone() : $order->get_shipping_phone();
                    ?>
                    <section class="order-panel order-address"><div class="order-address-heading"><h2><?php echo gamilea_order_icon( 'billing' === $type ? 'home' : 'truck' ); ?><?php echo esc_html( $label ); ?></h2><a href="<?php echo esc_url( $contact_url ); ?>">Solicitar cambio</a></div><address><?php echo wp_kses_post( $address ); ?><?php if ( $phone ) : ?><br><?php echo esc_html( $phone ); ?><?php endif; ?><?php if ( 'billing' === $type ) : ?><br><?php echo esc_html( $order->get_billing_email() ); ?><?php endif; ?></address></section>
                <?php endforeach; ?>
            </div>
            <?php do_action( 'woocommerce_after_order_details', $order ); ?>
        </div>
        <aside class="order-success-aside" aria-label="Gracias y ayuda">
            <section class="order-panel order-appreciation"><?php echo gamilea_order_icon( 'gift', 'order-icon-gift' ); ?><div><h2>Gracias por ser parte<br>de GA·MI·LEA</h2><p>Tu compra nos ayuda a seguir creando experiencias que hacen la vida más especial.</p></div><a class="button order-button-outline" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Seguir comprando →</a></section>
            <section class="order-panel order-support"><?php echo gamilea_order_icon( 'headset' ); ?><div><h2>¿Necesitas ayuda?</h2><p>Estamos aquí para ayudarte.</p></div><a class="button order-button-outline" href="<?php echo esc_url( $contact_url ); ?>">Contactarnos</a></section>
        </aside>
    </div>
    <div class="order-integration-notices">
        <?php
        // Keep gateway instructions and extension callbacks, without duplicating the details table.
        $details_priority = has_action( 'woocommerce_thankyou', 'woocommerce_order_details_table' );
        if ( false !== $details_priority ) { remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', $details_priority ); }
        do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
        do_action( 'woocommerce_thankyou', $order->get_id() );
        if ( false !== $details_priority ) { add_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', $details_priority ); }
        ?>
    </div>
</div>
