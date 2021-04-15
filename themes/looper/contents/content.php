<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package looper
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="page-title-area">
        <?php $thumb_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_id() ) ); 
        if( has_post_thumbnail() ): ?>
        <a href="<?php the_permalink(); ?>"><span class="featured-image" style="<?php if( $thumb_url ) { ?> background-image: url( <?php echo esc_url( $thumb_url ); ?> ); <?php } ?>"></span></a>
        <?php endif; ?>
        <?php if ( is_single() ) :
        the_title( '<h1 class="entry-title">', '</h1>' );
        else :
        the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif; ?>

    </div>
	<div class="entry-content">
		<?php
			the_excerpt();

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'looper' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
    <div class="entry-meta">
        <?php looper_entry_meta(); ?>
    </div>

</article><!-- #post-## -->