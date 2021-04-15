<?php
/**
 * The template for displaying the footer.
 *
 * @package looper
 */

?>  
            
            <footer class="footer" role="contentinfo">
                <svg id="bigTriangleShadow" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100"    preserveAspectRatio="none">
                    <path id="trianglePath1" d="M0 0 L50 100 L100 0 Z" />
                    <path id="trianglePath2" d="M50 100 L100 40 L100 0 Z" />
                </svg>
                 <?php
                /**
                 * Functions hooked in to looper_footer action.
                 *
                 * @hooked looper_template_copyright -10
                 */ 
                    do_action('looper_footer'); 
                ?>
                
             

            </footer>
            
        <?php wp_footer(); ?>
    </body>

</html>