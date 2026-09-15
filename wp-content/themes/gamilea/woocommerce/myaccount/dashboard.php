<?php
/**
 * Gamilea account dashboard.
 * @package WooCommerce\Templates
 * @version 4.4.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="gamilea-account-welcome">
    <span class="gamilea-account-symbol"><?php echo tienda_icon( 'user' ); ?></span>
    <div>
        <span class="gamilea-account-eyebrow"><?php esc_html_e( 'TU ESPACIO EN GA·MI·LEA', 'gamilea' ); ?></span>
        <h2><?php printf( esc_html__( 'Hola, %s', 'gamilea' ), esc_html( $current_user->first_name ?: $current_user->display_name ) ); ?></h2>
        <p><?php esc_html_e( 'Todo listo para tu próxima compra. Administra tu cuenta desde aquí.', 'gamilea' ); ?></p>
    </div>
</div>
<div class="gamilea-account-shortcuts">
    <?php
    $cards = array(
        'orders' => array( 'box', __( 'Mis pedidos', 'gamilea' ), __( 'Consulta el estado y los detalles de tus compras.', 'gamilea' ) ),
        'edit-address' => array( 'truck', __( 'Mis direcciones', 'gamilea' ), __( 'Actualiza tus datos de envío y facturación.', 'gamilea' ) ),
        'edit-account' => array( 'shield', __( 'Datos de mi cuenta', 'gamilea' ), __( 'Edita tu información personal y contraseña.', 'gamilea' ) ),
    );
    foreach ( $cards as $endpoint => $card ) :
        if ( ! array_key_exists( $endpoint, wc_get_account_menu_items() ) ) { continue; }
        ?>
        <a class="gamilea-account-shortcut" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
            <?php echo tienda_icon( $card[0] ); ?>
            <h3><?php echo esc_html( $card[1] ); ?></h3>
            <p><?php echo esc_html( $card[2] ); ?></p>
            <span><?php esc_html_e( 'Ver detalles', 'gamilea' ); ?> <?php echo tienda_icon( 'arrow' ); ?></span>
        </a>
    <?php endforeach; ?>
</div>
<div class="gamilea-account-shop">
    <p><?php esc_html_e( 'Encuentra algo especial para tu día a día.', 'gamilea' ); ?></p>
    <a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Explorar la tienda', 'gamilea' ); ?> <?php echo tienda_icon( 'arrow' ); ?></a>
</div>
<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
