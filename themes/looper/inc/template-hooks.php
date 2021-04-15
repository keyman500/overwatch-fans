<?php 

/**
 * theme template hooks
 *
 * @package looper
 */

/**
 * Meta Tags
 */
function looper_entry_meta(){

    $byline = sprintf(

        esc_html( '%s', 'looper' ),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a></span>'
    );

    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
        get_the_date( DATE_W3C ),
        get_the_date(),
        get_the_modified_date( DATE_W3C ),
        get_the_modified_date()
    );

    $get_category_list = get_the_category_list( __( ', ', 'looper' ) );
    $cat_list = sprintf( esc_html('%s', 'looper'),
    $get_category_list
    );

    echo '<span class="posted-on">' . $time_string . '</span><span class="cat-list">'. $cat_list .'</span>';
}


add_action( 'looper_entry_footer', 'looper_post_cat', 10 );
add_action( 'looper_entry_footer', 'looper_share', 12 );
add_action( 'looper_entry_footer', 'looper_next_prev_post', 15 );
add_action( 'looper_entry_footer', 'looper_author_bio', 20 );

function looper_post_cat(){ 

    $get_category_list = get_the_category_list( __( ', ', 'looper' ) );
    $cat_list = sprintf( esc_html('%s', 'looper'),
    $get_category_list
    );

    ?>
    <div class="cat-tag-links">
        <?php if(has_tag()): ?>
        <p><?php echo ' ' . get_the_tag_list('','',''); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

function looper_share(){
    if(shortcode_exists('ssba-buttons')){
        echo do_shortcode('[ssba-buttons]');
    }
}

function looper_author_bio(){ ?>
    <div class="author-info">
      <div class="avatar">
        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ) , 150 ); ?></a>
      </div>
      <div class="info">
          <p class="author-name"><span><?php _e('Published By ','looper'); ?></span><br><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></p>
          <?php echo get_the_author_meta('description'); ?>
      </div>
      <span class="clearfix"></span>
    </div> 
    <?php
}

function looper_next_prev_post(){
    ?>
        <div class="next-prev-post">
            <div class="npp">
            <div class="prev col-xs-6">
                
                <?php previous_post_link('<span>' . __("Previous","looper") . '</span><br> &larr; %link'); ?>
            </div>
            <div class="next col-xs-6">
                <?php next_post_link('<span>' . __('Next','looper'). '</span><br>%link &rarr;'); ?>
            </div>
        </div>
            <span class="clearfix"></span>
        </div>
    <?php
}

/**
 * site header
 */
add_action( 'looper_header', 'looper_template_header' );
function looper_template_header(){ ?>
    <header id="site-header">
        <div class="container">
            <nav class="navbar navbar-default" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">

                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navigation">
                    <span class="sr-only"><?php _e( 'Toggle navigation','looper' ); ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>

                    <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ): 
                    $looper_custom_logo_id = get_theme_mod( 'custom_logo' );
                    $image = wp_get_attachment_image_src( $looper_custom_logo_id,'full');
                    ?>
                    <h1 id="logo"><a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><img src="<?php echo esc_url( $image[0] ); ?>"></a></h1>
                    <?php else : ?>
                    <h1 id="logo"><a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><?php echo esc_html( bloginfo('name') ); ?></a></h1>
                    <?php endif; ?>

                </div>

                <div class="collapse navbar-collapse" id="main-navigation">
                    <?php 
                    if ( has_nav_menu( 'main-nav' ) ) {
                    wp_nav_menu( array(
                    'theme_location'    => 'main-nav',
                    'depth'             => 5,
                    'container'         => 'false',
                    'container_class'   => 'collapse navbar-collapse',
                    'container_id'      => 'bs-navbar-collapse-1',
                    'menu_class'        => 'nav navbar-nav navbar-right',
                    'fallback_cb'       => 'looper_primary_menu_fallback',
                    'walker'            => new wp_bootstrap_navwalker())
                    );
                    }
                    ?>
                </div><!-- /.navbar-collapse -->
            </nav>
        </div>
    </header>
    <?php if(!is_page_template('template-home.php')) { ?>
    <div class="head-sep">
        <svg id="bigTriangleShadow" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path id="trianglePath1" d="M0 0 L50 100 L100 0 Z" />
        <path id="trianglePath2" d="M50 100 L100 40 L100 0 Z" />
        </svg>
    </div>
    <?php } ?>
<?php
}


/**
 * Footer Hooks
 */
add_action( 'looper_footer', 'looper_template_copyright', 10 );


function looper_template_copyright(){ ?>
    <div class="container">
        <div class="row">
            <?php if ( has_nav_menu( 'footer-nav' ) ) { ?>
            <div class="col-md-4">
                <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ): 
                $looper_custom_logo_id = get_theme_mod( 'custom_logo' );
                $image = wp_get_attachment_image_src( $looper_custom_logo_id,'full');
                ?>
                <h1 id="logo"><a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><img src="<?php echo esc_url( $image[0] ); ?>"></a></h1>
                <?php else : ?>
                <h1 id="logo"><a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><?php echo esc_html( bloginfo('name') ); ?></a></h1>
                <?php endif; ?>
            </div>
            <div class="col-md-8 footer-nav">
                <?php 
                    
                    wp_nav_menu( array(
                    'theme_location'    => 'footer-nav',
                    'depth'             => 1,
                    'container'         => 'false',
                    'menu_class'        => 'nav navbar-nav',
                    'fallback_cb'       => 'looper_primary_menu_fallback',
                    'walker'            => new wp_bootstrap_navwalker())
                    );
              
                    ?>
            </div>
            <?php }else{ ?>
            <div class="col-md-12 full-logo">
                <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ): 
                $looper_custom_logo_id = get_theme_mod( 'custom_logo' );
                $image = wp_get_attachment_image_src( $looper_custom_logo_id,'full');
                ?>
                <h1 id="logo"><a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><img src="<?php echo esc_url( $image[0] ); ?>"></a></h1>
                <?php else : ?>
                <h1 id="logo"><a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><?php echo esc_html( bloginfo('name') ); ?></a></h1>
                <?php endif; ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="footer-copyright">
        &#169; <?php echo date_i18n(__('Y','looper')) . ' '; bloginfo( 'name' ); ?>
        <span><?php if(is_home() || is_front_page()): ?>
            - <?php echo __('Built with','looper'); ?> <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'looper' ) ); ?>" rel="nofollow" target="_blank"><?php printf( esc_html( '%s', 'looper' ), 'WordPress' ); ?></a><span><?php esc_html_e(' and ','looper'); ?></span><a href="<?php echo esc_url( __( 'https://wpdevshed.com/themes/looper/', 'looper' ) ); ?>" rel="nofollow" target="_blank"><?php printf( esc_html( '%s', 'looper' ), 'Looper' ); ?></a>
        <?php endif; ?>
        </span>
    </div>

<?php
}