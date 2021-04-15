<?php 
function overwatch_post_types(){


        register_post_type('Fan art',array(
            'capability_type' => 'Fan art',
            'map_meta_cap'=> true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor','excerpt','thumbnail','comments'),
            'rewrite'=> array('slug' => 'fanart' ),
            'has_archive' => true,
            'public' => true, 
            'labels' => array(
                'name' => "Fan art",
                'add_new_item' => 'Add New Fan art',
                'edit_item' => 'Edit Fan art',
                'all_items' => 'All Fan art',
                'singular_name' => "Fan art",
                'description' => "This is posts of differnt drawings of overwatch characteras made by fans of the game overwatch" 
            ),
            'menu_icon' => 'dashicons-art',
            'taxonomies'  => array( 'category' )
        ));


         register_post_type('Fan Fiction',array(
            'capability_type' => 'Fan Fiction',
            'map_meta_cap'=> true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor','excerpt','thumbnail','comments'),
            'rewrite'=> array('slug' => 'fan_fiction' ),
            'has_archive' => true,
            'public' => true, 
            'labels' => array(
                'name' => "Fan Fiction",
                'add_new_item' => 'Add New Fan Fiction',
                'edit_item' => 'Edit Fan Fiction',
                'all_items' => 'All Fan Fictions',
                'singular_name' => "Fan Fiction",
                'description' => "This is posts of fiction stories wittren by fans of the game overwatch about overwatch"
            ),
            'menu_icon' => 'dashicons-edit-page',
            'taxonomies'  => array( 'category' )
        ));



}

function overwatch_posttypes2(){
   register_post_type('Trivia',array(
            'capability_type' => 'Trivia',
            'map_meta_cap'=> true,
            'show_in_rest' => true,
            'public' => false, 
            'show_ui' => true,
            'supports' => array('title', 'editor'),
            'rewrite'=> array('slug' => 'Trivia' ),
            'has_archive' => true,
            'public' => true, 
            'labels' => array(
                'name' => "Trivia",
                'add_new_item' => 'Add New Trivia',
                'edit_item' => 'Edit Trivia',
                'all_items' => 'All Trivia',
                'singular_name' => "Trivia",
                'description' => "This is posts of differnt Trivia on overwatch characters" 
            ),
            'menu_icon' => 'dashicons-welcome-learn-more',
            'taxonomies'  => array( 'category' )
        ));

}
add_action('init','overwatch_posttypes2');
 add_action('init', 'overwatch_post_types');
?>