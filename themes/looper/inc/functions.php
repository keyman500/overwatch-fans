<?php 

/**
 * theme main functions
 *
 * @package looper
 */

/**
 * load template hooks
 */
require get_template_directory() . '/inc/template-hooks.php';

/**
 * load bootstrap navwalker
 */
if ( ! class_exists( 'wp_bootstrap_navwalker' )) {
  require get_template_directory() . '/assets/wp_bootstrap_navwalker.php'; /* Theme wp_bootstrap_navwalker display */
}
/**
 * customize4r
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Theme setup
 */
add_action( 'after_setup_theme', 'looper_theme_setup' );
function looper_theme_setup() {

    load_theme_textdomain( 'looper', get_template_directory() . '/library/translation' );

    add_action( 'wp_enqueue_scripts', 'looper_scripts_and_styles', 999 );

    add_action( 'widgets_init', 'looper_register_sidebars' );

    looper_theme_support();

    global $content_width;
    if ( ! isset( $content_width ) ) {
    $content_width = 640;
    }

    // Thumbnail sizes
    add_image_size( 'looper-600', 600, 600, true );
    add_image_size( 'looper-300', 300, 300, true );

} 

/**
 * register sidebar
 */
function looper_register_sidebars() {

  register_sidebar(array(
    'id' => 'sidebar1',
    'name' => __( 'Posts Widget Area', 'looper' ),
    'description' => __( 'The Posts Widget Area.', 'looper' ),
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget' => '</aside>',
    'before_title' => '<h3 class="widgettitle"><span>',
    'after_title' => '</span></h3>',
  ));

}

/**
 * enqueue scripts and styles
 */
function looper_scripts_and_styles() {

    global $wp_styles; 

    wp_enqueue_script( 'looper-jquery-modernizr', get_template_directory_uri() . '/assets/js/modernizr.custom.min.js', array('jquery'), '2.5.3', false );
    wp_enqueue_script( 'jquery-bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '', true );
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/fonts/font-awesome.min.css', array(), '', 'all' );
    wp_enqueue_style('looper-google-fonts-Titillium', '//fonts.googleapis.com/css?family=Titillium+Web:400,400i,900');
  
    if ( is_home() || is_front_page() || is_archive() || is_search() || is_page_template('template-home.php')) :
      wp_enqueue_script( 'jquery-masonry' );
      // Register the script
      wp_enqueue_script( 'looper-jquery-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '', true );


    endif;

    wp_enqueue_script( 'looper-jquery-menu', get_template_directory_uri() . '/assets/js/menu.js', array('jquery'), '', true );

    wp_enqueue_script( 'looper-jquery-search', get_template_directory_uri() . '/assets/js/search.js', array('jquery'), '', true );

    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
      wp_enqueue_script( 'comment-reply' );
    }

}

/**
 * theme support
 */
function looper_theme_support() {

    add_theme_support( 'post-thumbnails' );

    set_post_thumbnail_size( 600, 600 );

    add_theme_support( 'custom-background',
    array(
    'default-image' => '',    // background image default
    'default-color' => 'ffffff',    // background color default (dont add the #)
    'wp-head-callback' => '_custom_background_cb',
    'admin-head-callback' => '',
    'admin-preview-callback' => ''
    )
    );

    add_theme_support('automatic-feed-links');

    add_theme_support( 'title-tag' );

    add_theme_support( 'custom-logo' );

    register_nav_menus(
    array(
    'main-nav' => __( 'Main Nav', 'looper' ),
    'footer-nav' => __( 'Footer Nav', 'looper' ),
    )
    );
  
}

/**
 * Comment layout
 */
function looper_comments( $comment, $args, $depth ) { ?>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('comments'); ?>>

      <header class="comment-author vcard">
        <?php echo get_avatar( $comment,60 ); ?>
      </header>
      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert alert-info">
          <p><?php esc_html_e( 'Your comment is awaiting moderation.', 'looper' ) ?></p>
        </div>
      <?php endif; ?>
      <section class="comment_content cf">
        <?php /* translators: name of commenter */ ?>
        <?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'looper' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'looper' ),'  ','') ) ?>
        <a href="<?php comment_link(); ?>"><time datetime="<?php echo comment_time('Y-m-j'); ?>"><?php comment_date(); ?></time></a>
        <?php comment_text() ?>
        <p class="reply-link"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></p>
      </section>
<?php
} // don't remove this bracket!

/**
 * reoder comment form fields
 */
function looper_move_comment_field_to_bottom( $fields ) {
  $comment_field = $fields['comment'];
  unset( $fields['comment'] );
  $fields['comment'] = $comment_field;
  return $fields;
}

add_filter( 'comment_form_fields', 'looper_move_comment_field_to_bottom' );

/**
 * wp_nav_menu Fallback
 */
function looper_primary_menu_fallback() {
    ?>

    <ul id="menu-main-menu" class="nav navbar-nav navbar-right">
        <?php
        wp_list_pages(array(
            'depth'        => 1,
            'exclude' => '', //comma seperated IDs of pages you want to exclude
            'title_li' => '', //must override it to empty string so that it does not break our nav
            'sort_column' => 'post_title', //see documentation for other possibilites
            'sort_order' => 'ASC', //ASCending or DESCending
        ));
        ?>
    </ul>

    <?php
}

add_filter('excerpt_more', 'looper_new_excerpt_more');
function looper_new_excerpt_more($more) {
  if ( is_admin() ) {
     return $more;
  }
  global $post;
  return '<a class="moretag" href="'. esc_url( get_permalink($post->ID) ) . '">' . __('Read more','looper') . '</a>';
}
add_filter('excerpt_more', 'looper_new_excerpt_more');

// Filter except length to 35 words.
// tn custom excerpt length
function looper_excerpt_length( $length ) {
return 10;
}
add_filter( 'excerpt_length', 'looper_excerpt_length', 999 );

/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme Looper for publication on WordPress.org
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Plugin:
 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/inc/class-tgm.php';

add_action( 'tgmpa_register', 'looper_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function looper_register_required_plugins() {
  /*
   * Array of plugin arrays. Required keys are name and slug.
   * If the source is NOT from the .org repo, then source is also required.
   */
  $plugins = array(

    // This is an example of how to include a plugin from the WordPress Plugin Repository.
    array(
        'name'      => __('Breadcrumb NavXT','looper'),
        'slug'      => 'breadcrumb-navxt',
        'required'  => false,
    ),

  );

  /*
   * Array of configuration settings. Amend each line as needed.
   *
   * TGMPA will start providing localized text strings soon. If you already have translations of our standard
   * strings available, please help us make TGMPA even better by giving us access to these translations or by
   * sending in a pull-request with .po file(s) with the translations.
   *
   * Only uncomment the strings in the config array if you want to customize the strings.
   */
  $config = array(
    'id'           => 'looper',                 // Unique ID for hashing notices for multiple instances of TGMPA.
    'default_path' => '',                      // Default absolute path to bundled plugins.
    'menu'         => 'tgmpa-install-plugins', // Menu slug.
    'has_notices'  => true,                    // Show admin notices or not.
    'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
    'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
    'is_automatic' => false,                   // Automatically activate plugins after installation or not.
    'message'      => '',                      // Message to output right before the plugins table.

  );

  tgmpa( $plugins, $config );
}