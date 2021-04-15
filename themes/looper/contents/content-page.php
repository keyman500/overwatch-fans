<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package looper
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if(function_exists('bcn_display')){ ?>
        <div class="breadcrumbs">
            <?php bcn_display(); ?>
        </div>
    <?php } ?>

    <div class="entry-title">
		<h1><?php the_title(); ?></h1>
    </div>

	<div class="entry-content">
		<?php
			the_content();
		?>

		<span class="clearfix"></span>

		<?php

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'looper' ),
				'after'  => '</div>',
			) );
		?>
		<span class="clearfix"></span>
	</div><!-- .entry-content -->


	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
				edit_post_link(
					sprintf(
						/* translators: %s: Name of current post */
						esc_html__( 'Edit %s', 'looper' ),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					),
					'<span class="edit-link">',
					'</span>'
				);

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif; ?>

		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-## -->