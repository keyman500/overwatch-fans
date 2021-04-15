<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}


if ( !class_exists( 'ACFFrontendForm' ) ) {
    class ACFFrontendForm
    {
        public function get_form_data( $form_args )
        {
            global  $post ;
            $active_user = wp_get_current_user();
            $object_id = false;
            
            if ( isset( $_GET['updated'] ) && isset( $_GET['edit'] ) ) {
                $object = explode( '_', $_GET['updated'] );
                if ( isset( $object[2] ) ) {
                    
                    if ( is_numeric( $object[2] ) ) {
                        $object_id = $object[2];
                    } else {
                        $object_id = substr( $object[2], 1 );
                    }
                
                }
            }
            
            if ( isset( $_GET['post_id'] ) ) {
                $object_id = $_GET['post_id'];
            }
            if ( isset( $_GET['user_id'] ) ) {
                $object_id = $_GET['user_id'];
            }
            if ( isset( $_GET['product_id'] ) ) {
                $object_id = $_GET['product_id'];
            }
            /* 		if( 'new_comment' == $form_args['main_action'] ){
            				$form_args['post_id'] = 'new_comment';
            				if( $form_args['comment_parent_post'] == 'current_post' ){
            					$comment_parent_post = $post->ID;
            				}else{
            					$comment_parent_post = $form_args['select_parent_post'];
            				}
            				$form_args['html_after_fields'] .= '<input type="hidden" value="' . $comment_parent_post . '" name="acfef_parent_post"/><input type="hidden" value="0" name="acfef_parent_comment"/>';
            			} */
            switch ( $form_args['hidden_fields']['main_action'] ) {
                case 'new_post':
                    $form_args['action'] = 'post';
                    $can_edit = acfef_can_edit_post( $object_id, $form_args );
                    
                    if ( $can_edit && isset( $_GET['edit'] ) ) {
                        $form_args['post_id'] = $can_edit;
                    } else {
                        $form_args['post_id'] = 'add_post';
                    }
                    
                    
                    if ( !empty($form_args['new_post_terms']) ) {
                        if ( $form_args['new_post_terms'] == 'select_terms' ) {
                            $form_args['post_terms'] = $form_args['new_terms_select'];
                        }
                        if ( $form_args['new_post_terms'] == 'current_term' ) {
                            $form_args['post_terms'] = get_queried_object()->term_id;
                        }
                    }
                    
                    break;
                case 'edit_post':
                case 'duplicate_post':
                    $form_args['action'] = 'post';
                    
                    if ( $form_args['post_to_edit'] == 'current_post' ) {
                        $form_args['post_id'] = $post->ID;
                    } elseif ( $form_args['post_to_edit'] == 'select_post' ) {
                        $form_args['post_id'] = $form_args['post_select'];
                    } elseif ( $form_args['post_to_edit'] == 'url_query' && isset( $_GET[$form_args['url_query_post']] ) ) {
                        $form_args['post_id'] = $_GET[$form_args['url_query_post']];
                        $form_args['url_query'] = $form_args['url_query_post'];
                    }
                    
                    break;
                case 'new_product':
                    $form_args['action'] = 'product';
                    
                    if ( $object_id ) {
                        $form_args['post_id'] = acfef_can_edit_post( $object_id, $form_args );
                    } else {
                        $status = ( $form_args['new_product_status'] != 'no_change' ? $form_args['new_product_status'] : 'publish' );
                        $form_args['post_id'] = 'add_product';
                        
                        if ( !empty($form_args['new_product_terms']) ) {
                            if ( $form_args['new_product_terms'] == 'select_terms' ) {
                                $form_args['product_terms'] = $form_args['new_product_terms_select'];
                            }
                            if ( $form_args['new_product_terms'] == 'current_term' ) {
                                $form_args['product_terms'] = get_queried_object()->term_id;
                            }
                        }
                    
                    }
                    
                    break;
                case 'edit_product':
                    $form_args['action'] = 'product';
                    $first_product = get_posts( array(
                        'posts_per_page' => 1,
                        'post_type'      => 'product',
                    ) );
                    
                    if ( $first_product ) {
                        $form_args['post_id'] = $first_product[0]->ID;
                    } else {
                        $form_args['post_id'] = 'none';
                    }
                    
                    
                    if ( $form_args['product_to_edit'] == 'select_product' && $form_args['product_select'] ) {
                        $form_args['post_id'] = $form_args['product_select'];
                    } elseif ( $form_args['product_to_edit'] == 'url_query' && isset( $_GET[$form_args['url_query_product']] ) ) {
                        $form_args['post_id'] = $_GET[$form_args['url_query_product']];
                        $form_args['url_query'] = $form_args['url_query_product'];
                    } elseif ( $post->post_type == 'product' ) {
                        $form_args['post_id'] = $post->ID;
                    }
                    
                    break;
                case 'new_user':
                    
                    if ( $object_id ) {
                        $form_args['post_id'] = 'user_' . $object_id;
                    } else {
                        $can_edit = acfef_can_edit_user( $object_id, $form_args );
                        
                        if ( !$can_edit || $form_args['redirect_action'] == 'clear' ) {
                            $form_args['post_id'] = 'user_0';
                        } else {
                            $form_args['post_id'] = 'user_' . $can_edit;
                        }
                    
                    }
                    
                    break;
                case 'edit_user':
                    
                    if ( $form_args['user_to_edit'] == 'current_user' ) {
                        $form_args['post_id'] = 'user_' . $active_user->ID;
                    } elseif ( $form_args['user_to_edit'] == 'current_author' ) {
                        
                        if ( is_author() ) {
                            $author_id = get_queried_object_id();
                        } else {
                            $author_id = get_the_author_meta( 'ID' );
                        }
                        
                        $form_args['post_id'] = 'user_' . $author_id;
                    } elseif ( $form_args['user_to_edit'] == 'select_user' ) {
                        $form_args['post_id'] = 'user_' . $form_args['user_select'];
                    } elseif ( $form_args['user_to_edit'] == 'url_query' && isset( $_GET[$form_args['url_query_user']] ) ) {
                        $form_args['post_id'] = 'user_' . $_GET[$form_args['url_query_user']];
                        $form_args['url_query'] = $form_args['url_query_user'];
                    }
                    
                    break;
                case 'new_term':
                    $form_args['post_id'] = 'add_term';
                    $form_args['hidden_fields']['taxonomy_type'] = $form_args['new_term_taxonomy'];
                    $form_args['action'] = 'term';
                    break;
                case 'edit_term':
                    
                    if ( $form_args['term_to_edit'] == 'select_term' ) {
                        $term_id = $form_args['term_select'];
                    } elseif ( $form_args['term_to_edit'] == 'url_query' && isset( $_GET[$form_args['url_query_term']] ) ) {
                        $term_id = $_GET[$form_args['url_query_term']];
                        $form_args['url_query'] = $form_args['url_query_term'];
                    } else {
                        $term_obj = get_queried_object();
                        $term_id = $term_obj->term_id;
                    }
                    
                    if ( !isset( $term_obj ) ) {
                        $term_obj = get_term( $term_id );
                    }
                    $form_args['hidden_fields']['taxonomy_type'] = $term_obj->taxonomy;
                    $form_args['post_id'] = 'term_' . $term_id;
                    $form_args['action'] = 'term';
                    break;
            }
            if ( isset( $action ) ) {
                $form_args[$action . '_fields'] = $args;
            }
            return $form_args;
        }
        
        public function validate_form( $args )
        {
            // defaults
            $args = wp_parse_args( $args, array(
                'id'                    => 'acf-form',
                'parent_form'           => '',
                'post_id'               => false,
                'new_post'              => false,
                'fields'                => false,
                'post_title'            => false,
                'post_content'          => false,
                'form'                  => true,
                'form_title'            => '',
                'form_attributes'       => array(),
                'saved_drafts'          => array(),
                'saved_revisions'       => array(),
                'save_progress'         => array(),
                'show_delete_button'    => false,
                'return'                => add_query_arg( 'updated', 'true', acf_get_current_url() ),
                'message_location'      => 'other',
                'html_before_fields'    => '',
                'hidden_fields'         => array(),
                'html_after_fields'     => '',
                'hidden_submit'         => false,
                'submit_value'          => __( "Update", 'acf' ),
                'update_message'        => __( "Post updated", 'acf' ),
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'field_el'              => 'div',
                'uploader'              => 'wp',
                'honeypot'              => true,
                'show_update_message'   => true,
                'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',
                'kses'                  => true,
                'pay_for_submission'    => false,
            ) );
            $args['form_attributes'] = wp_parse_args( $args['form_attributes'], array(
                'id'           => $args['id'],
                'class'        => 'acfef-form',
                'action'       => '',
                'method'       => 'post',
                'autocomplete' => 'disableacf',
                'novalidate'   => 'novalidate',
            ) );
            if ( !$args['post_id'] ) {
                $args = $this->get_form_data( $args );
            }
            // filter post_id
            $args['post_id'] = acf_get_valid_post_id( $args['post_id'] );
            
            if ( $args['pay_for_submission'] ) {
                $user_submissions = get_user_meta( get_current_user_id(), 'acfef_payed_submissions', true );
                $user_submitted = get_user_meta( get_current_user_id(), 'acfef_payed_submitted', true );
                if ( !$user_submitted ) {
                    $user_submitted = 0;
                }
                if ( $user_submitted >= $user_submissions ) {
                    $args['hidden_submit'] = true;
                }
            }
            
            // filter
            $args = apply_filters( 'acf/validate_form', $args );
            // return
            return $args;
        }
        
        public function multi_step_buttons( $args, $current_step )
        {
            $prev_button = $buttons_class = '';
            $form_step = $args['fields']['steps'][$current_step];
            if ( $current_step > 1 ) {
                
                if ( $form_step['prev_button_text'] ) {
                    $prev_step = $current_step - 1;
                    $prev_button = '<input type="hidden" name="prev_step_num" value="' . $prev_step . '"/>';
                    $prev_button .= '<input type="button" name="prev_step" class="acfef-prev-button acfef-submit-button acf-button button button-primary" value="' . $form_step['prev_button_text'] . '"/> ';
                    $buttons_class = 'acfef-multi-buttons-align';
                }
            
            }
            $next_button = '<input type="submit" class="acfef-submit-button acf-button button button-primary" data-state="publish" value="' . $form_step['next_button_text'] . '" />';
            $submit_button = '<div class="acf-form-submit"><div class="acfef-submit-buttons ' . $buttons_class . '">' . $prev_button . $next_button . '</div></div>';
            return $submit_button;
        }
        
        public function step_tabs( $args, $current_step )
        {
            $editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
            $current_post = get_post();
            $active_user = wp_get_current_user();
            $screens = [ 'desktop', 'tablet', 'phone' ];
            $tabs_responsive = '';
            if ( isset( $args['steps_tabs_display'] ) ) {
                foreach ( $screens as $screen ) {
                    if ( !in_array( $screen, $args['steps_tabs_display'] ) ) {
                        $tabs_responsive .= 'elementor-hidden-' . $screen . ' ';
                    }
                }
            }
            $counter_responsive = '';
            if ( isset( $args['steps_counter_display'] ) ) {
                foreach ( $screens as $screen => $label ) {
                    if ( !in_array( $screen, $args['steps_counter_display'] ) ) {
                        $counter_responsive .= 'elementor-hidden-' . $screen . ' ';
                    }
                }
            }
            $total_steps = count( $args['fields']['steps'] );
            echo  '<div class="acfef-tabs elementor-tabs"><div class="acfef-tabs-wrapper ' . $tabs_responsive . '">' ;
            $steps = $args['fields']['steps'];
            if ( in_array( 'tabs', $args['steps_display'] ) ) {
                foreach ( $steps as $step_count => $form_step ) {
                    $active = '';
                    if ( $step_count == $current_step ) {
                        $active = 'active';
                    }
                    $change_form = '';
                    if ( $editor || $args['tab_links'] ) {
                        $change_form = ' change-step';
                    }
                    $step_title = ( $form_step['form_title'] ? $form_step['form_title'] : $args['form_title'] );
                    if ( $form_step['step_tab_text'] ) {
                        $step_title = $form_step['step_tab_text'];
                    }
                    
                    if ( $step_title == '' ) {
                        $step_title = __( 'Step', 'acf-frontend-form-element' ) . ' ' . $step_count;
                    } else {
                        if ( $args['step_number'] ) {
                            $step_title = $step_count . '. ' . $step_title;
                        }
                    }
                    
                    echo  '<a class="form-tab ' . $active . $change_form . '" data-step="' . $step_count . '"><p class="step-name">' . $step_title . '</p></a>' ;
                }
            }
            echo  '</div>' ;
            echo  '<div class="form-steps elementor-tabs-content-wrapper">' ;
            if ( in_array( 'counter', $args['steps_display'] ) ) {
                echo  '<div class="' . $counter_responsive . 'step-count"><p>' . $args['counter_prefix'] . $current_step . '/' . $total_steps . $args['counter_suffix'] . '</p></div>' ;
            }
        }
        
        public function form_set_data( $data = array() )
        {
            // defaults
            $data = wp_parse_args( $data, array(
                'screen'     => 'post',
                'post_id'    => 0,
                'nonce'      => '',
                'validation' => 1,
                'changed'    => 0,
            ) );
            // crete nonce
            $data['nonce'] = wp_create_nonce( $data['screen'] );
            // return
            return $data;
        }
        
        public function form_render_data( $data = array() )
        {
            // set form data
            $data = $this->form_set_data( $data );
            ?>
			<div class="acf-form-data acf-hidden">
				<?php 
            // loop
            foreach ( $data as $name => $value ) {
                // input
                acf_hidden_input( array(
                    'name'  => '_acf_' . $name,
                    'value' => $value,
                ) );
            }
            // actions
            do_action( 'acf/form_data', $data );
            do_action( 'acf/input/form_data', $data );
            ?>
			</div>
			<?php 
        }
        
        public function render_field_wrap( $field, $element = 'div', $instruction = 'label' )
        {
            // Ensure field is complete (adds all settings).
            if ( function_exists( 'acf_validate_field' ) ) {
                $field = acf_validate_field( $field );
            }
            // Prepare field for input (modifies settings).
            if ( function_exists( 'acf_prepare_field' ) ) {
                $field = acf_prepare_field( $field );
            }
            // Allow filters to cancel render.
            if ( !$field ) {
                return;
            }
            // Determine wrapping element.
            $elements = array(
                'div' => 'div',
                'tr'  => 'td',
                'td'  => 'div',
                'ul'  => 'li',
                'ol'  => 'li',
                'dl'  => 'dt',
            );
            
            if ( isset( $elements[$element] ) ) {
                $inner_element = $elements[$element];
            } else {
                $element = $inner_element = 'div';
            }
            
            // Generate wrapper attributes.
            $wrapper = array(
                'id'        => '',
                'class'     => 'acf-field',
                'width'     => '',
                'style'     => '',
                'data-name' => $field['_name'],
                'data-type' => $field['type'],
                'data-key'  => $field['key'],
            );
            // Add field type attributes.
            $wrapper['class'] .= " acf-field-{$field['type']}";
            // add field key attributes
            if ( $field['key'] ) {
                $wrapper['class'] .= " acf-field-{$field['key']}";
            }
            // Add required attributes.
            // Todo: Remove data-required
            
            if ( $field['required'] ) {
                $wrapper['class'] .= ' is-required';
                $wrapper['data-required'] = 1;
            }
            
            // Clean up class attribute.
            $wrapper['class'] = str_replace( '_', '-', $wrapper['class'] );
            $wrapper['class'] = str_replace( 'field-field-', 'field-', $wrapper['class'] );
            // Merge in field 'wrapper' setting without destroying class and style.
            if ( $field['wrapper'] ) {
                $wrapper = acf_merge_attributes( $wrapper, $field['wrapper'] );
            }
            // Extract wrapper width and generate style.
            // Todo: Move from $wrapper out into $field.
            $width = acf_extract_var( $wrapper, 'width' );
            
            if ( $width ) {
                $width = acf_numval( $width );
                
                if ( $element !== 'tr' && $element !== 'td' ) {
                    $wrapper['data-width'] = $width;
                    $wrapper['style'] .= " width:{$width}%;";
                }
            
            }
            
            // Clean up all attributes.
            $wrapper = array_map( 'trim', $wrapper );
            $wrapper = array_filter( $wrapper );
            /**
             * Filters the $wrapper array before rendering.
             *
             * @date	21/1/19
             * @since	5.7.10
             *
             * @param	array $wrapper The wrapper attributes array.
             * @param	array $field The field array.
             */
            $wrapper = apply_filters( 'acf/field_wrapper_attributes', $wrapper, $field );
            // Append conditional logic attributes.
            if ( !empty($field['conditional_logic']) ) {
                $wrapper['data-conditions'] = $field['conditional_logic'];
            }
            if ( !empty($field['conditions']) ) {
                $wrapper['data-conditions'] = $field['conditions'];
            }
            // Vars for render.
            $attributes_html = acf_esc_attr( $wrapper );
            // Render HTML
            echo  "<{$element} {$attributes_html}>" . "\n" ;
            
            if ( $element !== 'td' && (!isset( $field['field_label_hide'] ) || !$field['field_label_hide']) ) {
                echo  "<{$inner_element} class=\"acf-label\">" . "\n" ;
                acf_render_field_label( $field );
                echo  "</{$inner_element}>" . "\n" ;
            }
            
            echo  "<{$inner_element} class=\"acf-input\">" . "\n" ;
            if ( $instruction == 'label' ) {
                acf_render_field_instructions( $field );
            }
            acf_render_field( $field );
            if ( $instruction == 'field' ) {
                acf_render_field_instructions( $field );
            }
            echo  "</{$inner_element}>" . "\n" ;
            echo  "</{$element}>" . "\n" ;
        }
        
        public function render_previous( $steps, $post_id = 0 )
        {
            if ( isset( $steps ) ) {
                foreach ( $steps as $index => $step ) {
                    ?>
					<div class="acf-step-fields step-<?php 
                    echo  $index + 1 ;
                    ?>" data-step="<?php 
                    echo  $index + 1 ;
                    ?>">
					<?php 
                    foreach ( $step['fields'] as $field_data ) {
                        
                        if ( isset( $field_data['acf'] ) ) {
                            $field = acf_maybe_get_field( $field_data['acf'], $post_id, false );
                            $field['required'] = 0;
                            
                            if ( isset( $field['wrapper']['class'] ) ) {
                                $field['wrapper']['class'] .= ' acfef-hidden';
                            } else {
                                $field['wrapper']['class'] = 'acfef-hidden';
                            }
                            
                            if ( !isset( $field['value'] ) || $field['value'] === null ) {
                                $field['value'] = acf_get_value( $post_id, $field );
                            }
                            $field['prefix'] = 'step_' . $index;
                            // Render wrap.
                            $this->render_field_wrap( $field );
                        }
                    
                    }
                    ?>
					</div>
					<?php 
                }
            }
        }
        
        public function render_fields(
            $fields,
            $post_id = 0,
            $el = 'div',
            $instruction = 'label'
        )
        {
            // Parameter order changed in ACF 5.6.9.
            
            if ( is_array( $post_id ) ) {
                $args = func_get_args();
                $fields = $args[1];
                $post_id = $args[0];
            }
            
            // Filter our false results.
            $fields = array_filter( $fields );
            /**
             * Filters the $fields array before they are rendered.
             *
             * @date	12/02/2014
             * @since	5.0.0
             *
             * @param	array $fields An array of fields.
             * @param	(int|string) $post_id The post ID to load values from.
             */
            $fields = apply_filters( 'acf/pre_render_fields', $fields, $post_id );
            // Loop over and render fields.
            
            if ( $fields ) {
                $open_columns = 0;
                foreach ( $fields as $field ) {
                    
                    if ( isset( $field['render_content'] ) ) {
                        echo  $field['render_content'] ;
                    } elseif ( isset( $field['column'] ) ) {
                        
                        if ( $field['column'] == 'endpoint' ) {
                            if ( $open_columns ) {
                                echo  '</div>' ;
                            }
                            $open_columns--;
                        } else {
                            
                            if ( isset( $field['nested'] ) ) {
                                $open_columns++;
                            } else {
                                if ( $open_columns ) {
                                    echo  '</div>' ;
                                }
                                echo  '<div class="acf-column elementor-repeater-item-' . $field['column'] . '">' ;
                                $open_columns--;
                            }
                        
                        }
                    
                    } else {
                        // Load value if not already loaded.
                        if ( !isset( $field['value'] ) || $field['value'] === null ) {
                            $field['value'] = acf_get_value( $post_id, $field );
                        }
                        // Render wrap.
                        $this->render_field_wrap( $field, $el, $instruction );
                    }
                
                }
                if ( $open_columns > 0 ) {
                    while ( $open_columns > 0 ) {
                        echo  '</div>' ;
                        $open_columns--;
                    }
                }
            }
            
            /**
             *  Fires after fields have been rendered.
             *
             *  @date	12/02/2014
             *  @since	5.0.0
             *
             * @param	array $fields An array of fields.
             * @param	(int|string) $post_id The post ID to load values from.
             */
            do_action( 'acf/render_fields', $fields, $post_id );
        }
        
        public function delete_button( $args )
        {
            $confirm_message = $args['delete_message'];
            $delete_button_icon = $args['delete_icon']['value'];
            $delete_button_text = $args['delete_text'];
            ?> 
			<form class="delete-form" action="" method="POST" >

			<?php 
            $delete_args = array(
                'screen'   => 'acf_delete',
                'form'     => acf_encrypt( json_encode( $args ) ),
                'redirect' => $args['redirect'],
            );
            if ( isset( $args['post_id'] ) ) {
                $delete_args['delete_post'] = $args['post_id'];
            }
            if ( isset( $args['term_id'] ) ) {
                $delete_args['delete_term'] = $args['term_id'];
            }
            if ( isset( $args['user_id'] ) ) {
                $delete_args['delete_user'] = $args['user_id'];
            }
            $this->form_render_data( array_merge( $delete_args, $args['hidden_fields'] ) );
            ?>
			<div class="acfef-delete-button-container">
			<button onclick="return confirm('<?php 
            echo  $confirm_message ;
            ?>')" type="submit" class="button acfef-delete-button">
			<?php 
            
            if ( $delete_button_icon ) {
                ?>
				<i class="<?php 
                echo  $delete_button_icon ;
                ?>"></i>
			<?php 
            }
            
            ?>
			<?php 
            echo  $delete_button_text ;
            ?> </button>
				</div>
			</form>
			<?php 
        }
        
        public function saved_drafts( $args )
        {
            $wg_id = $args['hidden_fields']['element_id'];
            $drafts_args = array(
                'posts_per_page' => -1,
                'post_status'    => 'draft',
                'post_type'      => $args['new_post_type'],
                'author'         => get_current_user_id(),
            );
            $form_submits = get_posts( $drafts_args );
            if ( !$form_submits ) {
                return;
            }
            ?>
			<div class="acfef-form-posts"><p class="drafts-heading"><?php 
            echo  $args['saved_drafts']['saved_drafts_label'] ;
            ?></p>
			
			<?php 
            $draft_choices = [
                'add_post' => $args['saved_drafts']['saved_drafts_new'],
            ];
            
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                for ( $x = 1 ;  $x < 4 ;  $x++ ) {
                    $draft_choices[$x] = 'Draft ' . $x . ' (' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) . ')';
                }
                $select_class = 'preview-form-drafts';
            } else {
                foreach ( $form_submits as $submit ) {
                    $post_time = get_the_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $submit->ID );
                    $draft_choices[$submit->ID] = $submit->post_title . ' (' . $post_time . ')';
                }
                $select_class = 'posts-select';
            }
            
            acf_select_input( array(
                'choices' => $draft_choices,
                'class'   => $select_class,
                'value'   => $args['post_id'],
            ) );
            ?>
			</div>
			<?php 
        }
        
        public function saved_revisions( $args )
        {
            $wg_id = $args['hidden_fields']['element_id'];
            
            if ( get_post_type( $args['post_id'] ) == 'revision' ) {
                $parent_post = wp_get_post_parent_id( $args['post_id'] );
            } else {
                $parent_post = $args['post_id'];
            }
            
            $form_submits = wp_get_post_revisions( $parent_post );
            if ( !$form_submits ) {
                return;
            }
            ?>
			<br><div class="acfef-form-posts"><p class="revisions-heading"><?php 
            echo  $args['saved_revisions']['saved_revisions_label'] ;
            ?></p>
			
			<?php 
            $revision_choices = [
                $parent_post => $args['saved_revisions']['saved_revisions_edit_main'],
            ];
            
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                for ( $x = 1 ;  $x < 4 ;  $x++ ) {
                    $revision_choices[$x] = 'Revision ' . $x . ' (' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) . ')';
                }
                $select_class = 'preview-form-revisions';
            } else {
                $first = true;
                
                if ( is_array( $form_submits ) && count( $form_submits ) > 1 ) {
                    foreach ( $form_submits as $index => $submit ) {
                        
                        if ( $first ) {
                            $first = false;
                            continue;
                        }
                        
                        $post_time = get_the_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $index );
                        $revision_choices[$index] = $submit->post_title . ' (' . $post_time . ')';
                    }
                    $select_class = 'posts-select';
                }
            
            }
            
            acf_select_input( array(
                'choices' => $revision_choices,
                'class'   => $select_class,
                'value'   => $args['post_id'],
            ) );
            ?>
			</div>
			<?php 
        }
        
        public function render_form( $args = array() )
        {
            acf_enqueue_scripts();
            acf_enqueue_uploader();
            $args = $this->validate_form( $args );
            ?>
			<form <?php 
            echo  acfef_esc_attrs( $args['form_attributes'] ) ;
            ?>> 
			<?php 
            $current_fields = $args['fields'];
            
            if ( isset( $args['fields']['steps'] ) ) {
                $current_step = 1;
                
                if ( isset( $args['step_index'] ) ) {
                    $current_step = $args['step_index'];
                } else {
                    $args['step_index'] = $current_step;
                }
                
                $args['hidden_fields']['step'] = $current_step;
                if ( $current_step == count( $args['fields']['steps'] ) ) {
                    $args['last_step'] = true;
                }
                $current_fields = $args['fields']['steps'][$current_step]['fields'];
                if ( $current_step > 1 ) {
                    $previous_steps = array_slice( $args['fields']['steps'], 0, $current_step - 1 );
                }
                $form_title = $args['fields']['steps'][$current_step]['form_title'];
                $submit_button = $this->multi_step_buttons( $args, $current_step );
                $this->step_tabs( $args, $current_step );
            } else {
                
                if ( $args['hidden_submit'] ) {
                    $hidden_submit = ' acf-hidden';
                    $disabled_submit = ' disabled ';
                } else {
                    $hidden_submit = '';
                    $disabled_submit = ' ';
                }
                
                $submit_button = '<div class="acfef-submit-buttons' . $hidden_submit . '"><input' . $disabled_submit . 'type="submit" class="acfef-submit-button acf-button button button-primary" data-state="publish" value="' . $args['submit_value'] . '" /></div>';
                $current_fields = $args['fields'];
            }
            
            if ( !isset( $form_title ) || !$form_title ) {
                $form_title = $args['form_title'];
            }
            if ( $form_title ) {
                echo  '<h2 class="acfef-form-title">' . $form_title . '</h2>' ;
            }
            $post_id = $args['post_id'];
            if ( $post_id === 'add_post' || $post_id === 'add_product' ) {
                $post_id = false;
            }
            // Set uploader type.
            acf_update_setting( 'uploader', $args['uploader'] );
            $fields = array();
            
            if ( $current_fields ) {
                foreach ( $current_fields as $field_data ) {
                    
                    if ( is_array( $field_data ) ) {
                        
                        if ( isset( $field_data['acf'] ) ) {
                            $field = acf_maybe_get_field( $field_data['acf'], $post_id, false );
                            if ( isset( $field_data['elementor'] ) ) {
                                
                                if ( isset( $field['wrapper']['class'] ) ) {
                                    $field['wrapper']['class'] .= ' elementor-repeater-item-' . $field_data['elementor'];
                                } else {
                                    $field['wrapper']['class'] = 'elementor-repeater-item-' . $field_data['elementor'];
                                }
                            
                            }
                            $fields[] = $field;
                        } else {
                            $fields[] = $field_data;
                        }
                    
                    } else {
                        $fields[] = acf_maybe_get_field( $field_data, $post_id, false );
                    }
                
                }
            } else {
                return;
            }
            
            acf_add_local_field( array(
                'prefix'  => 'acf',
                'name'    => '_validate_email',
                'key'     => '_validate_email',
                'label'   => __( 'Validate Email', 'acf' ),
                'type'    => 'text',
                'value'   => '',
                'no_save' => 1,
                'wrapper' => array(
                'style' => 'display:none !important;',
            ),
            ) );
            $anti_spam_field = acf_get_field( '_validate_email' );
            if ( $args['show_update_message'] && $args['message_location'] == 'current' ) {
                
                if ( isset( $_GET['updated'] ) && $_GET['updated'] !== 'true' ) {
                    $form_id = explode( '_', $_GET['updated'] );
                    $widget_id = $form_id[0];
                    $page_id = $widget_page = $form_id[1];
                    if ( isset( $form_id[2] ) ) {
                        $page_id = $form_id[2];
                    }
                    $update_message = $args['update_message'];
                    if ( strpos( $update_message, '[' ) !== 'false' ) {
                        $update_message = acfef_get_code_value( $update_message, $page_id );
                    }
                    printf( $args['html_updated_message'], $update_message );
                }
            
            }
            $this->form_render_data( array_merge( array(
                'screen'  => 'acfef_form',
                'status'  => '',
                'post_id' => $args['post_id'],
                'form'    => acf_encrypt( json_encode( $args ) ),
            ), $args['hidden_fields'] ) );
            ?>
			<?php 
            
            if ( isset( $args['template_id'] ) ) {
                echo  \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $args['template_id'] ) ;
            } else {
                ?>
			<div class="acf-fields acf-form-fields -<?php 
                echo  esc_attr( $args['label_placement'] ) ;
                ?>">
				<?php 
                
                if ( isset( $current_step ) ) {
                    if ( isset( $previous_steps ) ) {
                        $this->render_previous( $previous_steps, $post_id );
                    }
                    ?>
					<div class="acf-step-fields step-<?php 
                    echo  $current_step ;
                    ?>" data-step="<?php 
                    echo  $current_step ;
                    ?>">
				<?php 
                }
                
                $this->render_fields(
                    $fields,
                    $post_id,
                    $args['field_el'],
                    $args['instruction_placement']
                );
                ?>
				<?php 
                if ( isset( $current_step ) ) {
                    ?>
					</div>
				<?php 
                }
                $this->render_field_wrap( $anti_spam_field );
                echo  $args['html_after_fields'] ;
                ?>
			</div>
				<div class="acf-form-submit">
					<?php 
                echo  $submit_button ;
                ?>
				</div>
			<?php 
            }
            
            if ( isset( $args['fields']['steps'] ) ) {
                echo  '</div></div>' ;
            }
            
            if ( $args['save_progress'] ) {
                $state = ( $args['post_id'] == 'add_post' ? 'draft' : 'revision' );
                ?>
				<div class="save-progress-buttons">
				<?php 
                
                if ( $args['save_progress']['desc'] ) {
                    ?>
					<p class="description"><span class="btn-dsc"><?php 
                    echo  $args['save_progress']['desc'] ;
                    ?></span></p>
				<?php 
                }
                
                ?>
				<input formnovalidate type="submit" class="save-progress-button acf-submit-button acf-button button" value="<?php 
                echo  $args['save_progress']['text'] ;
                ?>" name="save_progress" data-state="<?php 
                echo  $state ;
                ?>" /></div>
			<?php 
            }
            
            ?>
			
			</form>
			<?php 
            if ( $args['show_delete_button'] ) {
                $this->delete_button( $args );
            }
            if ( $args['saved_drafts'] ) {
                $this->saved_drafts( $args );
            }
            if ( $args['saved_revisions'] ) {
                $this->saved_revisions( $args );
            }
        }
        
        public function change_form()
        {
            if ( !isset( $_POST['form_data'] ) ) {
                return false;
            }
            $form = json_decode( acf_decrypt( $_POST['form_data'] ), true );
            if ( !$form ) {
                return false;
            }
            
            if ( isset( $_POST['draft'] ) ) {
                $form['post_id'] = $_POST['draft'];
                
                if ( is_numeric( $_POST['draft'] ) ) {
                    $form['hidden_fields']['main_action'] = 'edit_post';
                } else {
                    $form['hidden_fields']['main_action'] = 'new_post';
                }
            
            } else {
                
                if ( isset( $_POST['step'] ) ) {
                    $form['step_index'] = $_POST['step'];
                } else {
                    $form['step_index'] = $form['step_index'] - 1;
                }
                
                
                if ( $form['step_index'] == count( $form['fields']['steps'] ) ) {
                    $form['last_step'] = true;
                } else {
                    if ( isset( $form['last_step'] ) ) {
                        unset( $form['last_step'] );
                    }
                }
            
            }
            
            $GLOBALS['acfef_form'] = $form;
            ob_start();
            acfef_render_form( $form );
            $reload_form = ob_get_contents();
            ob_end_clean();
            wp_send_json_success( [
                'reload_form' => $reload_form,
                'to_top'      => true,
            ] );
            die;
        }
        
        public function get_payment_form( $args )
        {
            
            if ( get_option( 'acfef_payments_active' ) && (get_option( 'acfef_stripe_active' ) || get_option( 'acfef_paypal_active' )) ) {
                do_action( 'acfef/credit_card_scripts', $args['payment_processor'] );
                do_action( 'acfef/credit_card_form', $args );
            }
        
        }
        
        public function check_submit_form()
        {
            // verify nonce
            if ( !acf_verify_nonce( 'acfef_form' ) ) {
                return;
            }
            // bail ealry if form not submit
            if ( empty($_POST['_acf_form']) ) {
                return;
            }
            // load form
            $form = json_decode( acf_decrypt( $_POST['_acf_form'] ), true );
            // bail ealry if form is corrupt
            if ( empty($form) ) {
                return;
            }
            // kses
            if ( $form['kses'] && isset( $_POST['acf'] ) ) {
                $_POST['acf'] = wp_kses_post_deep( $_POST['acf'] );
            }
            // validate data
            acf_validate_save_post( true );
            // submit
            $this->submit_form( $form );
        }
        
        public function submit_form( $form )
        {
            // filter
            $form = apply_filters( 'acf/pre_submit_form', $form );
            // vars
            $post_id = acf_maybe_get( $form, 'post_id', 0 );
            // add global for backwards compatibility
            $GLOBALS['acfef_form'] = $form;
            // remove validate email field before it saves an empty row in the database
            if ( isset( $_POST['acf']['_validate_email'] ) ) {
                unset( $_POST['acf']['_validate_email'] );
            }
            // allow for custom save
            $post_id = apply_filters( 'acf/pre_save_post', $post_id, $form );
            // save
            acf_save_post( $post_id );
            // restore form (potentially modified)
            $form = $GLOBALS['acfef_form'];
            // action
            do_action( 'acf/submit_form', $form, $post_id );
            $this->return_form( $form, $post_id );
        }
        
        public function return_form( $form, $post_id )
        {
            // get form id
            
            if ( isset( $_POST['_acf_element_id'] ) ) {
                $form_id = $_POST['_acf_element_id'];
            } elseif ( isset( $_POST['_acf_field_id'] ) ) {
                $form_id = $_POST['_acf_field_id'];
            } elseif ( isset( $_POST['_acf_admin_page'] ) ) {
                $form_id = $_POST['_acf_admin_page'];
            } else {
                return;
            }
            
            
            if ( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] != 'publish' ) {
                $form['post_id'] = $post_id;
                $form['hidden_fields']['main_action'] = 'edit_post';
                ob_start();
                acfef_render_form( $form );
                $response['clear_form'] = ob_get_clean();
                $response['saved_message'] = $form['saved_draft_message'];
                wp_send_json_success( $response );
                exit;
            }
            
            
            if ( isset( $_POST['_acf_step_action'] ) ) {
                $main_action = $_POST['_acf_step_action'];
            } else {
                $main_action = $_POST['_acf_main_action'];
            }
            
            
            if ( isset( $_POST['log_back_in'] ) ) {
                $user_id = $_POST['log_back_in'];
                $user_object = get_user_by( 'ID', $user_id );
                
                if ( $user_object ) {
                    wp_set_current_user( $user_id, $user_object->user_login );
                    wp_set_auth_cookie( $user_id );
                    do_action( 'wp_login', $user_object->user_login, $user_object );
                }
            
            }
            
            do_action(
                'acfef/on_submit',
                $post_id,
                $form,
                $form_id
            );
            
            if ( isset( $form['ajax_submit'] ) ) {
                $update_message = $form['update_message'];
                if ( strpos( $update_message, '[$' ) !== 'false' || strpos( $update_message, '[' ) !== 'false' ) {
                    $update_message = acfef_get_code_value( $update_message, $post_id );
                }
                $response = array(
                    'post_id'        => $post_id,
                    'update_message' => $update_message,
                    'to_top'         => true,
                );
                
                if ( isset( $form['form_attributes']['data-field'] ) ) {
                    $title = get_post_field( 'post_title', $post_id ) . '<a href="#" class="acf-icon -pencil small dark edit-rel-post" data-name="edit_item"></a>';
                    $response['append'] = [
                        'id'     => $post_id,
                        'text'   => $title,
                        'action' => ( is_numeric( $form['post_id'] ) ? 'edit' : 'add' ),
                    ];
                    $response['field_key'] = $form['form_attributes']['data-field'];
                }
                
                
                if ( isset( $form['redirect_action'] ) && $form['redirect_action'] == 'edit' ) {
                    $form['post_id'] = $post_id;
                    $main_action = $form['hidden_fields']['main_action'];
                    if ( $main_action == 'new_post' ) {
                        $form['hidden_fields']['main_action'] = 'edit_post';
                    }
                    if ( $main_action == 'new_user' ) {
                        $form['hidden_fields']['main_action'] = 'edit_user';
                    }
                }
                
                ob_start();
                acfef_render_form( $form );
                $response['clear_form'] = ob_get_clean();
                wp_send_json_success( $response );
                exit;
            } else {
                // vars
                $return = acf_maybe_get( $form, 'return', '' );
                // redirect
                
                if ( $return ) {
                    $object_type = '';
                    
                    if ( strpos( $post_id, '_' ) !== false ) {
                        $object = explode( '_', $post_id );
                        $object_type = '_' . $object[0][0];
                        $post_id = $object[1];
                    }
                    
                    // update %placeholders%
                    $return = str_replace( '%post_id%', $post_id, $return );
                    $return = str_replace( '%post_url%', get_permalink( $post_id ), $return );
                    $query_args = [];
                    $query_args['updated'] = $form_id . '_' . $_POST['_acf_screen_id'];
                    if ( is_numeric( $post_id ) ) {
                        $query_args['updated'] .= '_' . $object_type . $post_id;
                    }
                    if ( isset( $form['redirect_action'] ) && $form['redirect_action'] == 'edit' ) {
                        $query_args['edit'] = 1;
                    }
                    if ( isset( $_POST['_acf_modal'] ) && $_POST['_acf_modal'] == 1 ) {
                        $query_args['modal'] = true;
                    }
                    if ( !empty($form['url_query']) ) {
                        $query_args[$form['url_query']] = $post_id;
                    }
                    if ( isset( $form['redirect_params'] ) ) {
                        $query_args = array_merge( $query_args, $form['redirect_params'] );
                    }
                    $return = add_query_arg( $query_args, $return );
                    $return = acfef_get_code_value( $return, $post_id );
                    if ( isset( $form['last_step'] ) ) {
                        
                        if ( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'draft' ) {
                            $return = wp_get_referer();
                        } else {
                            $return = remove_query_arg( [ 'form_id', 'modal', 'step' ], $return );
                        }
                    
                    }
                    wp_send_json_success( [
                        'redirect' => $return,
                    ] );
                    // redirect
                    die;
                }
            
            }
        
        }
        
        public function form_message()
        {
            $return = '';
            
            if ( isset( $_GET['updated'] ) && $_GET['updated'] !== 'true' ) {
                $form_id = explode( '_', $_GET['updated'] );
                $type = 'update';
            }
            
            
            if ( isset( $_GET['trashed'] ) ) {
                $form_id = explode( '_', $_GET['trashed'] );
                $type = 'delete';
            }
            
            
            if ( isset( $_GET['deleted'] ) ) {
                $form_id = explode( '_', $_GET['deleted'] );
                $type = 'delete';
            }
            
            if ( !isset( $form_id ) ) {
                return;
            }
            $widget = $this->get_the_widget( $form_id );
            if ( !$widget ) {
                return;
            }
            $settings = $widget->get_settings_for_display();
            
            if ( $type == 'update' && isset( $settings['show_success_message'] ) ) {
                $show_message = $settings['show_success_message'];
                $message = $settings['update_message'];
                if ( $settings['redirect'] == 'current' ) {
                    return;
                }
            }
            
            
            if ( $type == 'delete' ) {
                
                if ( isset( $settings['show_delete_message'] ) ) {
                    $show_message = $settings['show_delete_message'];
                    $message = $settings['delete_message'];
                }
                
                
                if ( isset( $settings['show_delete_message_product'] ) ) {
                    $show_message = $settings['show_delete_message_product'];
                    $message = $settings['delete_message_product'];
                }
            
            }
            
            if ( !$show_message || empty($message) ) {
                return;
            }
            $widget_id = $form_id[0];
            $post_id = $widget_page = $form_id[1];
            if ( isset( $form_id[2] ) ) {
                $post_id = $form_id[2];
            }
            if ( strpos( $message, '[$' ) !== 'false' || strpos( $message, '[' ) !== 'false' ) {
                $message = acfef_get_code_value( $message, $post_id );
            }
            $return = '<div class="acfef-message elementor-' . $widget_page . '">
						<div class="elementor-element elementor-element-' . $widget_id . '">
							<div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">' . $message . '</p><span class="acfef-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div>
						</div>
						</div>';
            echo  $return ;
        }
        
        public function get_steps( $field )
        {
            if ( $field['field_type'] == 'step' ) {
                return true;
            }
            return false;
        }
        
        public function reload_page(
            $post_id,
            $form,
            $step,
            $step_index
        )
        {
            $form['step_index'] = $form['step_index'] + 1;
            $form['post_id'] = $post_id;
            $main_action = $form['hidden_fields']['main_action'];
            if ( $main_action == 'new_post' ) {
                $form['hidden_fields']['main_action'] = 'edit_post';
            }
            if ( $main_action == 'new_user' ) {
                $form['hidden_fields']['main_action'] = 'edit_user';
            }
            ob_start();
            acfef_render_form( $form );
            $reload_form = ob_get_contents();
            ob_end_clean();
            wp_send_json_success( [
                'clear_form' => $reload_form,
                'widget'     => $form['hidden_fields']['element_id'],
                'step'       => $form['step_index'],
            ] );
            die;
        }
        
        public function delete_object()
        {
            if ( !acf_verify_nonce( 'acf_delete' ) ) {
                return;
            }
            // bail ealry if form not submit
            if ( empty($_POST['_acf_form']) ) {
                return;
            }
            // load form
            $form = json_decode( acf_decrypt( $_POST['_acf_form'] ), true );
            // bail ealry if form is corrupt
            if ( empty($form) ) {
                return;
            }
            $page_id = $_POST['_acf_screen_id'];
            $button_id = $_POST['_acf_element_id'];
            $redirect_query = array();
            
            if ( isset( $_POST['_acf_delete_post'] ) ) {
                $post_id = intval( $_POST['_acf_delete_post'] );
                
                if ( isset( $form['force_delete'] ) && $form['force_delete'] == 'true' ) {
                    $deleted = wp_delete_post( $post_id, true );
                    $redirect_query['deleted'] = $button_id . '_' . $page_id . '_' . $post_id;
                } else {
                    $deleted = wp_trash_post( $post_id );
                    $redirect_query['trashed'] = $button_id . '_' . $post_id;
                }
            
            }
            
            
            if ( isset( $_POST['_acf_delete_term'] ) ) {
                $term_id = intval( $_POST['_acf_delete_term'] );
                $deleted = wp_delete_term( $term_id, sanitize_text_field( $_POST['_acf_taxonomy_type'] ) );
                $redirect_query['deleted'] = $button_id . '_' . $page_id . '_t' . $term_id;
            }
            
            
            if ( isset( $_POST['_acf_delete_user'] ) ) {
                $user_id = intval( $_POST['_acf_delete_user'] );
                $deleted = wp_delete_user( $user_id );
                $redirect_query['deleted'] = $button_id . '_' . $page_id . '_u' . $user_id;
            }
            
            
            if ( isset( $deleted ) ) {
                $redirect_url = add_query_arg( $redirect_query, $form['redirect'] );
                wp_safe_redirect( $redirect_url );
                exit;
            }
        
        }
        
        protected function get_the_widget( $form_id )
        {
            
            if ( is_array( $form_id ) ) {
                $widget_id = $form_id[0];
                $post_id = $form_id[1];
            } else {
                return false;
            }
            
            
            if ( isset( $post_id ) ) {
                $elementor = acfef_get_elementor_instance();
                $document = $elementor->documents->get( $post_id );
                
                if ( $document ) {
                    $module = acfef()->elementor;
                    $form = $module->find_element_recursive( $document->get_elements_data(), $widget_id );
                }
                
                
                if ( !empty($form['templateID']) ) {
                    $template = $elementor->documents->get( $form['templateID'] );
                    
                    if ( $template ) {
                        $global_meta = $template->get_elements_data();
                        $form = $global_meta[0];
                    }
                
                }
                
                if ( !$form ) {
                    return false;
                }
                $widget = $elementor->elements_manager->create_element_instance( $form );
                return $widget;
            }
        
        }
        
        public function __construct()
        {
            add_action( 'wp_footer', [ $this, 'form_message' ] );
            add_action(
                'wp',
                [ $this, 'delete_object' ],
                10,
                1
            );
        }
    
    }
    acfef()->frontend_form = new ACFFrontendForm();
}

function acfef_delete_button( $args )
{
    acfef()->frontend_form->delete_button( $args );
}

function acfef_render_form( $args )
{
    acfef()->frontend_form->render_form( $args );
}

function acfef_render_field_wrap( $field, $el = 'div', $instruction = 'label' )
{
    acfef()->frontend_form->render_field_wrap( $field, $el, $instruction );
}

add_action( 'wp_ajax_acfef/form_submit', 'acfef_form_submit' );
add_action( 'wp_ajax_nopriv_acfef/form_submit', 'acfef_form_submit' );
add_action( 'admin_post_acfef/form_submit', 'acfef_form_submit' );
add_action( 'admin_post_nopriv_acfef/form_submit', 'acfef_form_submit' );
function acfef_form_submit()
{
    acfef()->frontend_form->check_submit_form();
}

add_action( 'wp_ajax_acfef/forms/change_form', 'acfef_change_form' );
add_action( 'wp_ajax_nopriv_acfef/forms/change_form', 'acfef_change_form' );
function acfef_change_form()
{
    acfef()->frontend_form->change_form();
}
