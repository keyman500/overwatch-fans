<?php

/**
 * load looper functions and definitions.
 *
 * @package looper
 */

require get_template_directory() . '/inc/functions.php'; 
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

function default_comments_on( $data ) {
    if( $data['post_type'] == 'Fan art' ) {
        $data['comment_status'] = 1;
    }

    return $data;
}
add_filter( 'wp_insert_post_data', 'default_comments_on' );




?>