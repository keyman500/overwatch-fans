<?php
/**
 * Template part for displaying single content in single.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package looper
 */

?>
<?php $image = get_field('fan_art_image');
$related  = get_field('related_fan-fiction');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    <?php if(function_exists('bcn_display')){ ?>
        <div class="breadcrumbs">
            <?php bcn_display(); ?>
        </div>
    <?php } ?>

    <div class="entry-title">
        <h1><?php the_title(); ?></h1>
        <?php if(empty(get_the_title())){
            echo '<h1>'.get_the_id().'</h1>';
        }?>
    </div>

    <div class="entry-meta">
        <?php looper_entry_meta(); ?>
    </div>

    <div class="text-center">
  <img src="<?php echo $image?>" class="rounded" alt="fan art dawring" style="max-height: 900px; max-width:800px;">
</div>

    <div class="entry-content">
        
        <div style="text-align:center;" class="h3"><?php

            the_content();

        ?></div>

        <?php 
         if($related){
                echo "<p>related fanfiction: ";
                foreach($related as $fiction){ //for each a post object
                ?>
                <a href="<?php echo get_the_permalink($fiction);?>">
                        <?php echo get_the_title($fiction);?>
                    </a>
                 
        <?php echo ", ";}
            echo "</p>";
            }
        
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

    
    <footer class="entry-footer">
        <?php do_action('looper_entry_footer'); 

        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif; ?>
    </footer><!-- .entry-footer -->
    
</article><!-- #post-## -->