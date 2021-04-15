<?php 

/**
 * Customizer settings
 *
 * @package looper
 */

if ( ! function_exists( 'looper_theme_customizer' ) ) :
  function looper_theme_customizer( $wp_customize ) {

    /* color scheme option */
    $wp_customize->add_setting( 'looper_color_body', array (
      'default' => '#001d38',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'looper_color_body', array(
      'label'    => __( 'Body Text Color', 'looper' ),
      'section'  => 'colors',
      'settings' => 'looper_color_body',
    ) ) );

    $wp_customize->add_setting( 'looper_color_1', array (
      'default' => '#003871',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'looper_color_1', array(
      'label'    => __( 'Color 1', 'looper' ),
      'section'  => 'colors',
      'settings' => 'looper_color_1',
    ) ) );

    $wp_customize->add_setting( 'looper_color_2', array (
      'default' => '#001d38',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'looper_color_2', array(
      'label'    => __( 'Color 2', 'looper' ),
      'section'  => 'colors',
      'settings' => 'looper_color_2',
    ) ) );

    $wp_customize->add_setting( 'looper_color_3', array (
      'default' => '#1bbcd4',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'looper_color_3', array(
      'label'    => __( 'Color 3', 'looper' ),
      'section'  => 'colors',
      'settings' => 'looper_color_3',
    ) ) );

    $wp_customize->add_setting( 'looper_color_4', array (
      'default' => '#00101f',
      'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'looper_color_4', array(
      'label'    => __( 'Footer Copyright bg', 'looper' ),
      'section'  => 'colors',
      'settings' => 'looper_color_4',
    ) ) );


  
  }
endif;
add_action('customize_register', 'looper_theme_customizer');


/**
 * Sanitize checkbox
 */
if ( ! function_exists( 'looper_sanitize_checkbox' ) ) :
  function looper_sanitize_checkbox( $input ) {
    if ( $input == 1 ) {
      return 1;
    } else {
      return '';
    }
  }
endif;

/**
 * Sanitize text field html
 */
if ( ! function_exists( 'looper_sanitize_field_html' ) ) :
  function looper_sanitize_field_html( $str ) {
    $allowed_html = array(
    'a' => array(
    'href' => array(),
    ),
    'br' => array(),
    'span' => array(),
    );
    $str = wp_kses( $str, $allowed_html );
    return $str;
  }
endif;

if ( ! function_exists( 'looper_sanitize_dropdown_pages' ) ) :
  function looper_sanitize_dropdown_pages( $page_id, $setting ) {
    // Ensure $input is an absolute integer.
    $page_id = absint( $page_id );

    // If $page_id is an ID of a published page, return it; otherwise, return the default.
    return ( 'publish' == get_post_status( $page_id ) ? $page_id : $setting->default );
  }
endif;


/**
 * Customizer Display
 *
 * @package looper
 */

  function looper_apply_color() {

    if( get_theme_mod('looper_color_body') ){
      $body  =   esc_html( get_theme_mod('looper_color_body') );
    }else{
      $body  =  '#001d38';
    }

    if( get_theme_mod('looper_color_1') ){
      $color_1  =   esc_html( get_theme_mod('looper_color_1') );
    }else{
      $color_1  =  '#003871';
    }

    if( get_theme_mod('looper_color_2') ){
      $color_2  =   esc_html( get_theme_mod('looper_color_2') );
    }else{
      $color_2  =  '#001d38';
    }

    if( get_theme_mod('looper_color_3') ){
      $color_3  =   esc_html( get_theme_mod('looper_color_3') );
    }else{
      $color_3  =  '#1bbcd4';
    }

    if( get_theme_mod('looper_color_4') ){
      $footer_c  =   esc_html( get_theme_mod('looper_color_4') );
    }else{
      $footer_c  =  '#00101f';
    }


    $custom_css = "
        
        body,
        .blog-item .page-title-area .entry-title a, .blog-item .entry-content .entry-title a,
        .rpwwt-post-title,.entry-footer .cat-tag-links a:hover, .cat-tag-links a:hover,.author-name span,#respond .comment-reply-title{
            color: $body;
        }
        input, textarea, select{
            border-color: $body;
        }
        input[type='submit']:hover, button[type='submit']:hover, .btn:hover, .comment .comment-reply-link:hover {
          background-color: $body;
          border-color: $body;
        }
        #site-header,
        .widget .widgettitle,.blog-sticky .stick-bg{
          background-color: $color_1;
        }
        blockquote{
          border-color: $color_1;
        }
        #trianglePath1 {
            fill: $color_1;
            stroke: $color_1;
        }
        footer.footer,
        .next-prev-post{
          background-color: $color_2;
        }
        #trianglePath2 {
            fill: $color_3;
            stroke: $color_3;
        }
        .widgettitle:before{
          background: $color_3;
        }
        a,footer.footer .footer-copyright,.entry-meta span,.navbar-default .navbar-nav > li > a, .navbar-default .navbar-nav > li > a:hover, .footer-nav .navbar-nav > li > a, .footer-nav .navbar-nav > li > a:hover,.next-prev-post a,.comment .comment_content cite.fn a{
          color: $color_3;
        }
        input[type='submit'], button[type='submit'], .btn, .comment .comment-reply-link,#site-header .navbar-default .navbar-toggle,#masonry .blog-item .entry-content .moretag, .ias-trigger a{
          background-color: $color_3;
          border-color: $color_3;
        }

        footer.footer .footer-copyright{
          background-color: $footer_c;
        }


        
      ";

    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '', 'all' );
    wp_enqueue_style( 'looper-main-stylesheet', get_template_directory_uri() . '/assets/css/style.css', array(), '', 'all' );
    wp_add_inline_style( 'looper-main-stylesheet', $custom_css );
}

add_action( 'wp_enqueue_scripts', 'looper_apply_color', 999 );