<?php
/** Small local icon set. */
function tienda_icon( $name, $class = '' ) {
    $paths = array(
        'truck' => '<path d="M3 5h11v12H3zM14 9h4l3 4v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
        'shield' => '<path d="M12 3 3 7v5c0 5 9 9 9 9s9-4 9-9V7z"/><path d="m8 12 3 3 5-6"/>',
        'box' => '<path d="m12 2 9 5v10l-9 5-9-5V7zM3 7l9 5 9-5M12 12v10M7 4.8l10 5.5"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="7.5"/><path d="m16 16 5 5"/>',
        'user' => '<circle cx="12" cy="7" r="4"/><path d="M4 22v-3a8 8 0 0 1 16 0v3"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/>',
        'cart' => '<path d="M2 3h3l3 13h11l3-10H6"/><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>',
        'arrow' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'headset' => '<path d="M4 13v-2a8 8 0 0 1 16 0v2M20 18v1a3 3 0 0 1-3 3h-3"/><rect x="2" y="11" width="5" height="8" rx="2"/><rect x="17" y="11" width="5" height="8" rx="2"/>',
        'card' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 9h20M6 15h5"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="3"/><path d="m3 6 9 7 9-7"/>',
        'gift' => '<path d="M3 10h18v4H3zM5 14v8h14v-8M12 10v12"/><path d="M12 10C1 9 6 0 12 8c6-8 11 1 0 2Z"/>',
        'lock' => '<rect x="5" y="10" width="14" height="12" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4M12 15v3"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
        'check' => '<path d="m4 12 5 5L20 6"/>',
    );
    return '<svg class="icon ' . esc_attr( $class ) . '" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ( $paths[ $name ] ?? $paths['arrow'] ) . '</svg>';
}
