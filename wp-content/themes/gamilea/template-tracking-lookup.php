<?php
/** Order lookup page (Figma: Track Order / Desktop 82:1797) at /seguimiento/: order number + billing email -> tracking page. */
defined( 'ABSPATH' ) || exit;

$error  = gamilea_tracking_handle_lookup();
$number = isset( $_POST['order_number'] ) ? wc_clean( wp_unslash( $_POST['order_number'] ) ) : '';
$email  = isset( $_POST['order_email'] ) ? sanitize_email( wp_unslash( $_POST['order_email'] ) ) : '';

get_header(); ?>
<div class="gamilea-tracking-lookup container">
    <div class="trk-lookup">
        <h1>Sigue tu pedido</h1>
        <p class="trk-lookup-lead">Tu compra, cada vez más cerca. Consulta el estado con los datos de tu pedido.</p>
        <form method="post" action="<?php echo esc_url( gamilea_tracking_lookup_url() ); ?>" class="trk-lookup-form">
            <?php wp_nonce_field( 'gamilea_tracking_lookup', 'gamilea_lookup_nonce' ); ?>
            <?php if ( $error ) : ?><p class="trk-lookup-error" role="alert"><?php echo esc_html( $error ); ?></p><?php endif; ?>
            <label class="trk-field"><span>Número de pedido</span><input type="text" name="order_number" value="<?php echo esc_attr( $number ); ?>" placeholder="1048" inputmode="numeric" autocomplete="off" required></label>
            <label class="trk-field"><span>Correo electrónico</span><input type="email" name="order_email" value="<?php echo esc_attr( $email ); ?>" placeholder="El correo que usaste al comprar" autocomplete="email" required></label>
            <button type="submit" class="trk-lookup-submit">Consultar pedido</button>
        </form>
    </div>
    <aside class="trk-example" aria-hidden="true">
        <span class="trk-example-badge">EJEMPLO</span>
        <div class="trk-example-info">
            <p class="trk-example-order">Pedido #1048</p>
            <p class="trk-example-status">En camino</p>
            <ul><li>✓  Pedido confirmado</li><li>✓  En preparación</li><li>✓  En camino</li><li>○  Entregado</li></ul>
        </div>
        <div class="trk-example-card">
            <div><strong>Datos de envío</strong><span>En camino</span></div>
            <p>Colonia Escalón, Calle Ejemplo #123<br>San Salvador, San Salvador</p>
        </div>
    </aside>
</div>
<?php get_footer();
