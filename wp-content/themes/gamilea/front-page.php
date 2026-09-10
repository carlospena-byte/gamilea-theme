<?php
defined('ABSPATH') || exit;
get_header();
if ('page' === get_option('show_on_front') && get_queried_object_id()) {
    while (have_posts()) {
        the_post();
        the_content();
    }
} else {
    echo do_blocks(gamilea_home_block_content());
}
get_footer();
