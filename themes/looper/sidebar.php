<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package looper
 */
?>
<?php if ( is_active_sidebar( 'sidebar1' ) ) : ?>

<div class="col-md-3 sidebar-area">
    <?php dynamic_sidebar( 'sidebar1' ); ?>
</div>

<?php endif; ?>