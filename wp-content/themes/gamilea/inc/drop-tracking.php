<?php
/**
 * Drop shipment tracking: own tracking page (reads the Boxful tracking page) and status emails.
 * Depends on Drop Connector meta (_dwc_status, _dwc_tracking_url) and its dwc_drop_status_changed action.
 */
defined( 'ABSPATH' ) || exit;

const GAMILEA_TRACKING_HOST = 'tracking.goboxful.com';

/** Drop statuses that email the customer, with the copy used in the message. */
function gamilea_tracking_email_statuses() {
    return array(
        'recollected'     => array( 'Recolectamos tu pedido', 'Tu pedido ya fue recolectado por la paquetería y pronto saldrá hacia tu dirección.' ),
        'inroute'         => array( 'Tu pedido va en camino', 'Tu pedido salió a ruta y está en camino a tu dirección.' ),
        'in_agency'       => array( 'Tu pedido está en una agencia', 'Tu pedido llegó a una agencia de la paquetería. Pronto se coordinará la entrega.' ),
        'in_locker'       => array( 'Tu pedido está en un locker', 'Tu pedido llegó a un locker. Revisa el seguimiento para ver cómo retirarlo.' ),
        'delivered'       => array( 'Tu pedido fue entregado', 'Tu pedido fue entregado. ¡Gracias por comprar en GA·MI·LEA!' ),
        'not_delivered'   => array( 'No pudimos entregar tu pedido', 'La paquetería no pudo entregar tu pedido. Revisa el seguimiento o escríbenos para coordinar un nuevo intento.' ),
        'not_recollected' => array( 'Tuvimos un problema con tu pedido', 'La paquetería no pudo recolectar tu pedido. Estamos dando seguimiento y te avisaremos.' ),
        'issue'           => array( 'Tuvimos un problema con tu pedido', 'La paquetería reportó una incidencia con tu envío. Estamos dando seguimiento y te avisaremos.' ),
    );
}

/** Own tracking URL for an order; the order key keeps it private for guests. */
function gamilea_tracking_url( $order ) {
    return add_query_arg( array( 'gamilea_tracking' => $order->get_id(), 'key' => $order->get_order_key() ), home_url( '/' ) );
}

function gamilea_order_tracking_url_for_provider( $order ) {
    $url  = (string) $order->get_meta( '_dwc_tracking_url' );
    $host = wp_parse_url( $url, PHP_URL_HOST );
    return ( $url && $host === GAMILEA_TRACKING_HOST ) ? $url : '';
}

/**
 * Reads the shipment from the Boxful tracking page (Next.js payload) and keeps only the fields we show.
 * Returns null when anything about the page is not as expected.
 */
function gamilea_fetch_shipment( $tracking_url ) {
    $cache_key = 'gamilea_trk_' . md5( $tracking_url );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached ) { return $cached ?: null; }

    $shipment = null;
    $response = wp_remote_get( $tracking_url, array( 'timeout' => 8, 'redirection' => 2, 'user-agent' => 'GAMILEA/1.0 (+' . home_url() . ')' ) );
    if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
        $html = str_replace( array( '\\"', '\\\\' ), array( '"', '\\' ), wp_remote_retrieve_body( $response ) );
        $pos  = strpos( $html, '"shipment":{' );
        if ( false !== $pos ) {
            $json = gamilea_extract_json_object( $html, $pos + strlen( '"shipment":' ) );
            $data = $json ? json_decode( $json, true ) : null;
            if ( is_array( $data ) && isset( $data['statusHistory'] ) && is_array( $data['statusHistory'] ) ) {
                $history = array();
                foreach ( $data['statusHistory'] as $row ) {
                    if ( empty( $row['date'] ) || empty( $row['statusDescription'] ) ) { continue; }
                    $history[] = array( 'date' => (string) $row['date'], 'label' => (string) $row['statusDescription'] );
                }
                $shipment = array(
                    'number'    => isset( $data['shipmentNumber'] ) ? (string) $data['shipmentNumber'] : '',
                    'courier'   => isset( $data['courier'] ) ? (string) $data['courier'] : '',
                    'eta'       => isset( $data['estimatedDeliveryDate'] ) ? (string) $data['estimatedDeliveryDate'] : '',
                    'cod'       => ! empty( $data['cod'] ),
                    'cod_amount' => isset( $data['codAmount'] ) ? (float) $data['codAmount'] : 0.0,
                    'history'   => array_reverse( $history ),
                );
            }
        }
    }

    // Failures are cached briefly so a broken provider page is not hit on every view.
    set_transient( $cache_key, $shipment ?: 0, $shipment ? 10 * MINUTE_IN_SECONDS : MINUTE_IN_SECONDS );
    return $shipment;
}

/** Returns the balanced {...} starting at $start, respecting JSON strings. */
function gamilea_extract_json_object( $text, $start ) {
    $depth = 0; $in_str = false; $len = strlen( $text );
    for ( $i = $start; $i < $len; $i++ ) {
        $c = $text[ $i ];
        if ( $in_str ) {
            if ( '\\' === $c ) { $i++; } elseif ( '"' === $c ) { $in_str = false; }
            continue;
        }
        if ( '"' === $c ) { $in_str = true; }
        elseif ( '{' === $c ) { $depth++; }
        elseif ( '}' === $c && 0 === --$depth ) { return substr( $text, $start, $i - $start + 1 ); }
    }
    return null;
}

/* ---- Tracking page: /?gamilea_tracking={order id}&key={order key} ---- */

add_filter( 'query_vars', function ( $vars ) { $vars[] = 'gamilea_tracking'; return $vars; } );

function gamilea_tracking_requested_order() {
    $id = absint( get_query_var( 'gamilea_tracking' ) );
    if ( ! $id || ! function_exists( 'wc_get_order' ) ) { return null; }
    $order = wc_get_order( $id );
    $key   = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : '';
    if ( ! $order || ! $key || ! hash_equals( $order->get_order_key(), $key ) ) { return null; }
    return $order;
}

add_filter( 'template_include', function ( $template ) {
    if ( ! get_query_var( 'gamilea_tracking' ) ) { return $template; }
    return get_template_directory() . '/template-tracking.php';
} );

add_filter( 'pre_get_document_title', function ( $title ) {
    return get_query_var( 'gamilea_tracking' ) ? 'Seguimiento de tu pedido – GA·MI·LEA' : $title;
} );

add_action( 'wp_enqueue_scripts', function () {
    if ( ! get_query_var( 'gamilea_tracking' ) ) { return; }
    wp_enqueue_style( 'gamilea-tracking', get_template_directory_uri() . '/assets/tracking.css', array( 'gamilea-design' ), (string) filemtime( get_template_directory() . '/assets/tracking.css' ) );
} );

add_action( 'send_headers', function () {
    if ( get_query_var( 'gamilea_tracking' ) ) { nocache_headers(); }
} );

/** Button to the tracking page on order details (thank-you and My Account). */
add_action( 'woocommerce_order_details_after_order_table', function ( $order ) {
    if ( ! $order->get_meta( '_dwc_order_id' ) ) { return; }
    printf( '<p class="gamilea-tracking-link"><a class="button" href="%s">%s</a></p>', esc_url( gamilea_tracking_url( $order ) ), esc_html__( 'Seguir mi envío', 'gamilea' ) );
} );

/* ---- Emails ---- */

add_action( 'dwc_drop_status_changed', function ( $order, $drop_status ) {
    $statuses = gamilea_tracking_email_statuses();
    if ( ! isset( $statuses[ $drop_status ] ) || ! $order->get_billing_email() ) { return; }

    $sent_key = '_gamilea_tracking_mail_' . $drop_status;
    if ( $order->get_meta( $sent_key ) ) { return; }
    $order->update_meta_data( $sent_key, time() );
    $order->save();

    list( $heading, $message ) = $statuses[ $drop_status ];
    $mailer = WC()->mailer();
    $body   = '<p>' . sprintf( esc_html__( 'Hola %s,', 'gamilea' ), esc_html( $order->get_billing_first_name() ) ) . '</p>'
        . '<p>' . esc_html( $message ) . '</p>'
        . '<p>' . sprintf( esc_html__( 'Pedido n.º %s', 'gamilea' ), esc_html( $order->get_order_number() ) ) . '</p>'
        . '<p><a class="button" href="' . esc_url( gamilea_tracking_url( $order ) ) . '" style="display:inline-block;background:#081820;color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:600">'
        . esc_html__( 'Ver seguimiento', 'gamilea' ) . '</a></p>';

    $mailer->send( $order->get_billing_email(), $heading . ' – ' . get_bloginfo( 'name' ), $mailer->wrap_message( $heading, $body ) );
    $order->add_order_note( sprintf( 'Correo de seguimiento enviado al cliente: "%s".', $heading ) );
}, 10, 2 );

/** Admin: Gamilea tracking link under the Drop box on the order screen. */
add_action( 'woocommerce_admin_order_data_after_shipping_address', function ( $order ) {
    if ( ! $order->get_meta( '_dwc_order_id' ) ) { return; }
    echo '<h3>GA·MI·LEA</h3>';
    printf( '<p><a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a></p>', esc_url( gamilea_tracking_url( $order ) ), esc_html__( 'Seguimiento Gamilea', 'gamilea' ) );
}, 20 );

/* ---- Own status history: the webhook gives exact times, Boxful's page does not map to our copy ---- */

add_action( 'dwc_drop_status_changed', function ( $order, $drop_status ) {
    $events = (array) $order->get_meta( '_gamilea_drop_events' );
    $last   = $events ? end( $events ) : null;
    if ( $last && isset( $last['status'] ) && $last['status'] === $drop_status ) { return; }
    $events[] = array( 'status' => $drop_status, 'time' => time() );
    $order->update_meta_data( '_gamilea_drop_events', $events );
    $order->save();
}, 5, 2 );

/** Timeline copy for every Drop status: array( title, description ). */
function gamilea_tracking_status_copy( $status ) {
    $copy = array(
        'pending_correction'    => array( 'Necesitamos revisar tu pedido', 'Estamos corrigiendo un dato de tu pedido y te contactaremos si hace falta.' ),
        'registered'            => array( 'Pedido en preparación', 'Estamos preparando tu pedido para el envío.' ),
        'fulfilled'             => array( 'Pedido preparado', 'Tu pedido está listo y será entregado a la paquetería.' ),
        'created'               => array( 'Registrado en la paquetería', 'La paquetería registró tu envío y pronto lo recolectará.' ),
        'registered_in_courier' => array( 'Registrado en la paquetería', 'La paquetería registró tu envío y pronto lo recolectará.' ),
        'recollected'           => array( 'Recibido por el transportista', 'El paquete fue recogido y registrado para su envío.' ),
        'inroute'               => array( 'Tu pedido está en camino', 'Salió a ruta hacia tu dirección.' ),
        'in_agency'             => array( 'Tu pedido está en una agencia', 'Llegó a una agencia de la paquetería. Pronto se coordinará la entrega.' ),
        'in_locker'             => array( 'Tu pedido está en un locker', 'Llegó a un locker de la paquetería.' ),
        'not_recollected'       => array( 'No pudimos recolectar tu pedido', 'La paquetería no pudo recolectarlo. Estamos dando seguimiento.' ),
        'not_delivered'         => array( 'No pudimos entregar tu pedido', 'La paquetería no pudo entregarlo. Escríbenos para coordinar un nuevo intento.' ),
        'issue'                 => array( 'Hay una incidencia con tu envío', 'La paquetería reportó una incidencia. Estamos dando seguimiento.' ),
        'delivered'             => array( 'Pedido entregado', 'Tu pedido fue entregado. ¡Gracias por comprar en GA·MI·LEA!' ),
        'cancelled'             => array( 'Pedido cancelado', 'Tu pedido fue cancelado.' ),
        'rejected'              => array( 'Pedido cancelado', 'Tu pedido fue cancelado.' ),
        'cancelled_in_boxful'   => array( 'Envío cancelado', 'La paquetería canceló tu envío.' ),
        'returned_to_warehouse' => array( 'Pedido devuelto', 'Tu pedido regresó a nuestro almacén. Escríbenos para ayudarte.' ),
    );
    return isset( $copy[ $status ] ) ? $copy[ $status ] : null;
}

/** Progress stage reached for a Drop status: 0 Confirmado, 1 Preparado, 2 En camino, 3 Entregado. */
function gamilea_tracking_stage( $status ) {
    if ( 'delivered' === $status ) { return 3; }
    if ( in_array( $status, array( 'recollected', 'inroute', 'in_agency', 'in_locker', 'not_recollected', 'not_delivered', 'issue' ), true ) ) { return 2; }
    if ( in_array( $status, array( 'fulfilled', 'created', 'registered_in_courier' ), true ) ) { return 1; }
    return 0;
}

/** Everything the tracking page shows, built from the order, our own events and (optionally) Boxful's page. */
function gamilea_tracking_view( $order, $shipment ) {
    $status = (string) $order->get_meta( '_dwc_status' );
    $stage  = gamilea_tracking_stage( $status );
    $events = array_values( array_filter( (array) $order->get_meta( '_gamilea_drop_events' ), 'is_array' ) );
    $boxful = $shipment && $shipment['history'] ? array_reverse( $shipment['history'] ) : array(); // oldest first
    $created = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : 0;

    $rows = array( array( 'ts' => $created, 'title' => 'Pedido confirmado', 'desc' => 'Recibimos tu compra y comenzamos a preparar tu pedido.' ) );
    if ( $events ) {
        foreach ( $events as $e ) {
            $copy = gamilea_tracking_status_copy( $e['status'] );
            if ( $copy ) { $rows[] = array( 'ts' => (int) $e['time'], 'title' => $copy[0], 'desc' => $copy[1] ); }
        }
    } else {
        foreach ( $boxful as $h ) { $rows[] = array( 'ts' => (int) strtotime( $h['date'] ), 'title' => $h['label'], 'desc' => '' ); }
    }
    usort( $rows, function ( $a, $b ) { return $b['ts'] <=> $a['ts']; } );

    // Date shown under each progress stage: first event that reached it, else Boxful's first entry for "Preparado".
    $stage_dates = array( 0 => $created, 1 => null, 2 => null, 3 => null );
    foreach ( $events as $e ) {
        $s = gamilea_tracking_stage( $e['status'] );
        if ( $s > 0 && null === $stage_dates[ $s ] ) { $stage_dates[ $s ] = (int) $e['time']; }
    }
    if ( null === $stage_dates[1] && $stage >= 1 && $boxful ) { $stage_dates[1] = (int) strtotime( $boxful[0]['date'] ); }

    $headline = array(
        0 => array( 'En preparación', 'Estamos preparando tu pedido.' ),
        1 => array( 'Preparado', 'tu pedido está preparado y pronto saldrá hacia ti.' ),
        2 => array( 'En camino', 'tu pedido ya está en camino.' ),
        3 => array( 'Entregado', 'tu pedido fue entregado.' ),
    );
    $chip = array( 0 => 'Preparando envío', 1 => 'Preparando envío', 2 => 'Envío en curso', 3 => 'Entregado' );
    if ( in_array( $status, array( 'not_delivered', 'not_recollected', 'issue' ), true ) ) { $chip[2] = 'Requiere atención'; }
    if ( in_array( $status, array( 'cancelled', 'rejected', 'cancelled_in_boxful', 'returned_to_warehouse' ), true ) ) {
        $headline[ $stage ] = array( 'Cancelado', 'tu pedido no continuará con el envío.' );
        $chip[ $stage ]     = 'Sin envío';
    }

    return array(
        'status'      => $status,
        'stage'       => $stage,
        'stage_dates' => $stage_dates,
        'rows'        => $rows,
        'label'       => $headline[ $stage ][0],
        'sentence'    => $headline[ $stage ][1],
        'chip'        => $chip[ $stage ],
        'updated'     => $rows ? $rows[0]['ts'] : 0,
    );
}

/** j***@dominio.com — the tracking link can be forwarded, so the full address is not printed. */
function gamilea_mask_email( $email ) {
    if ( ! is_email( $email ) ) { return ''; }
    list( $user, $domain ) = explode( '@', $email, 2 );
    return substr( $user, 0, 1 ) . str_repeat( '*', max( 2, strlen( $user ) - 2 ) ) . ( strlen( $user ) > 1 ? substr( $user, -1 ) : '' ) . '@' . $domain;
}
