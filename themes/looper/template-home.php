<?php
/**
 * Template Name: Home
 *
 *
 * @package looper
 */

get_header(); ?>

    <div id="masonry">
    <?php 
     /**
     * Functions hooked in to looper_home_banner action.
     *
     * @hooked looper_template_blog
     */
    do_action('looper_home_blog'); ?>
    <div class="blog-sticky">
      <div class="container" >
       
        <div class="row" >
                <?php
                
                    $clear = 0;
                    $sticky = get_option( 'sticky_posts' );
                    $page_var = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    if(count($sticky) > 0 && 1 == $page_var) {
                        $query_sticky = new WP_Query( array( 'post__in' => $sticky, 'ignore_sticky_posts' => 1 ) );
                        while ( $query_sticky->have_posts() ) : $query_sticky->the_post();  ?>

                            <div class="blog-item sticky">
                                <?php get_template_part( 'contents/content', 'sticky' ); ?>
                            </div>
                        
                        <?php endwhile; wp_reset_postdata(); 
                    }else{
                        $query_one = new WP_Query( array( 'post__not_in' => $sticky, 'posts_per_page' => 1 ,'ignore_sticky_posts' => 1 ) );
                        while ( $query_one->have_posts() ) : $query_one->the_post();  ?>

                            <div class="blog-item sticky">
                                <?php get_template_part( 'contents/content', 'sticky' ); ?>
                            </div>
                        
                        <?php endwhile; wp_reset_postdata(); 
                    }
                ?>
            </div>
        </div>
        <div class="sticky-bg" >
            <span class="stick-bg"></span>
            <svg id="bigTriangleShadow" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path id="trianglePath1" d="M0 0 L50 100 L100 0 Z" />
                <path id="trianglePath2" d="M50 100 L100 40 L100 0 Z" />
            </svg>
        </div>
    </div>
     <div class="container">
        <div class="row">
            <?php if ( is_active_sidebar( 'sidebar1' ) ){
                $class = 'class="col-md-9"';
            }else{
                $class = 'class="col-md-12"';
            } ?>
            <div <?php echo $class; ?>>
                <?php
                if(is_front_page()):
                    $paginate = (get_query_var('page')) ? get_query_var('page') : 1;
                else:
                    $paginate = (get_query_var('paged')) ? get_query_var('paged') : 1;
                endif;
                $wp_query = new WP_Query( array( 'post_type' => 'post', 'post__not_in' => $sticky, 'ignore_sticky_posts' => 1, 'paged' => $paginate ) );
                ?>
                    <div class="blog-masonry">
                        <div class="grid-sizer col-xs-12 col-sm-6"></div>
                        <?php 
                        
                        while ( $wp_query -> have_posts() ) : $wp_query -> the_post(); ?>
                            
                            <div class="col-xs-12 col-sm-6 blog-item">
                                <?php get_template_part( 'contents/content', 'masonry' ); ?>
                            </div>

                        <?php 

                        endwhile;
                        
                        ?>

                    </div>
                    
                    <?php the_posts_pagination();  wp_reset_postdata(); ?>
                    <?php
             ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>