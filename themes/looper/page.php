<?php
/**
 * The template for displaying all single page.
 *
 *
 * @package looper
 */

get_header(); ?>
     
    <div class="container post-full">
        <div class="row">
            <div class="col-md-12">
 <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Search Results</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="marc-res">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>  
<input type="text" id="searchbar" onkeydown="s.keyhandler()" />         
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" onclick="alert("hi");">
  Search
</button>
            
                <?php
                while ( have_posts() ) : the_post();

                get_template_part( 'contents/content', 'page' );

                endwhile; // End of the loop.
                ?> 
                <?php
    $page = get_the_title(get_the_ID());
    
		if($page =="Fan Art"){
		 $fanart = new WP_Query(array(
             
			 'posts_per_page' => 20,
			 'post_type' => 'Fan art',
			 'orderby' => 'title',
			 'order' => 'ASC',
		   ));


        ?>

      

        <?php
		while($fanart->have_posts()){
		$fanart->the_post();
        $image = get_field('fan_art_image');

        ?>
    

<div class="card mb-3" style="max-width: 540px; max-height:640px;">
  <div class="row no-gutters">
    <div class="col-md-4">
      <img src="<?php echo $image?>" class="card-img" alt="art pic">
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title"><?php the_title(); ?></h5>
        <p class="card-text"><?php if(has_excerpt()) the_excerpt();
        else echo wp_trim_words(get_the_content(),5); ?></p>
        <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Post</a>
      </div>
    </div>
  </div>
</div>

<hr/>

        

    
        <?php
		}?>

        <?php
     


		}

    if($page=="Fan-Fiction"){
    $fanfic = new WP_Query(array(
             
			 'posts_per_page' => 20,
			 'post_type' => 'Fan Fiction',
			 'orderby' => 'title',
			 'order' => 'ASC',
		   ));
		while($fanfic->have_posts()){
            $fanfic->the_post();
?>

<div class="card mb-3" style="max-width: 540px; max-height:640px;">
  <div class="row no-gutters">
    <div class="col-md-4">
          <?php the_post_thumbnail(); ?>
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title"><?php the_title(); ?></h5>
        <p class="card-text"><?php if(has_excerpt()) the_excerpt();
        else echo wp_trim_words(get_the_content(),5); ?></p>
        <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Post</a>
      </div>
    </div>
  </div>
</div>
<hr/>



<?php
		}



 	   }
        if($page=="Trivia"){
            $triv = new WP_Query(array(
                     
                     'posts_per_page' => 20,
                     'post_type' => 'Trivia',
                     'orderby' => 'title',
                     'order' => 'ASC',
                   ));
                while($triv->have_posts()){
                $triv->the_post();
                ?>
<div class="card mb-3" style="max-width: 540px; max-height:640px;">
  <div class="row no-gutters">
    <div class="col-md-4">
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title"><?php the_title(); ?></h5>
        <p class="card-text"><?php if(has_excerpt()) the_excerpt();
        else echo wp_trim_words(get_the_content(),5); ?></p>
        <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Post</a>
      </div>
    </div>
  </div>
</div>
<hr/>
                <?php
                }
        
        
        
                }
        
		
		?>
         
         
            <?php
                  $theParent = wp_get_post_parent_ID(get_the_ID());
                  if($theParent){ ?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                 <p><a class="metabox__blog-home-link" href="<?php echo
                get_permalink($theParent); ?>">
                <i class="fa fa-home" aria-hidden="true"></i> Back to <?php
                  echo get_the_title($theParent); ?></a>
            <span class="metabox__main"><?php echo the_title(); ?>
           </span></p>
               </div>
 <?php }

 ?>                    
                <span class="clearfix"></span> 

            </div>
        </div>
    </div>
    
	

<?php get_footer(); ?>