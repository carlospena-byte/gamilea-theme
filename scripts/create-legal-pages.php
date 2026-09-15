<?php
/** Run with WordPress loaded. Keeps existing editorial content intact. */
foreach (array('terminos' => 'Términos y condiciones', 'privacidad' => 'Política de privacidad') as $slug => $title) {
    $page = get_page_by_path($slug);
    $demo = $page && (strpos($page->post_content, 'Tienda de demostración') !== false || strpos($page->post_content, 'Privacidad en esta demostración') !== false);
    if ($page && trim($page->post_content) !== '' && !$demo) { echo "Conservado: {$slug}\n"; continue; }
    $content = file_get_contents(get_template_directory() . '/content/' . $slug . '.html');
    if ($page) { wp_save_post_revision($page->ID); }
    $id = wp_insert_post(array('ID' => $page ? $page->ID : 0, 'post_type' => 'page', 'post_name' => $slug, 'post_title' => $title, 'post_status' => 'publish', 'post_content' => wp_slash($content)), true);
    if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
    update_post_meta($id, '_wp_page_template', 'page-legal.php');
    if ($slug === 'privacidad') { update_option('wp_page_for_privacy_policy', $id); }
    if ($slug === 'terminos') { update_option('woocommerce_terms_page_id', $id); }
    echo "Actualizada: " . get_permalink($id) . "\n";
}
