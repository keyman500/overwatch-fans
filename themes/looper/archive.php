<?php
/**
 * The template for displaying archive pages.
 *
 * @package looper
 */

get_header(); ?>

    <div class="page-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-title"><?php the_archive_title(); ?></h1>
                </div>
            </div>
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
                <div id="masonry">
                    <div class="row">
                        <div class="blog-masonry">
                            <div class="grid-sizer col-xs-12 col-sm-6"></div>
                            <?php 

                            while ( have_posts() ) : the_post(); ?>

                            <div class="col-xs-12 col-sm-6 blog-item">
                            <?php get_template_part( 'contents/content', 'masonry' ); ?>
                            </div>

                            <?php endwhile; ?>
                        </div>
                        <?php the_posts_pagination(); ?>
                        <?php
                        ?>
                    </div>
                </div>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>

<?php get_footer(); ?>