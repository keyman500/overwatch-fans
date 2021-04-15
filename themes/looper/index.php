<?php
/**
 * The template for displaying archive pages.
 *
 * @package looper
 */

get_header(); ?>
 
    <div class="container">
        <div class="row">
            <div class="col-md-9">
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