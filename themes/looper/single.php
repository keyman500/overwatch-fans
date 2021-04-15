<?php
/**
 * The template for displaying all single posts.
 *
 *
 * @package looper
 */

get_header(); ?>

    <div class="container post-full">
        <div class="row">
            <div class="col-md-12">
                <?php
                while ( have_posts() ) : the_post();

                get_template_part( 'contents/content', 'single' );

                endwhile; // End of the loop.
                ?> 
                <span class="clearfix"></span> 
            </div>
        </div>
    </div>

<?php get_footer(); ?>