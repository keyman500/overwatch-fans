<?php

namespace ACFFrontend\Module\Classes;

use  ACFFrontend\Plugin ;
use  ACFFrontend\Module\ACFEF_Module ;
use  ACFFrontend\Module\Widgets ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

class FormSubmit
{
    public function acfef_after_submit( $form, $post_id )
    {
        
        if ( isset( $_POST['_acf_element_id'] ) ) {
            $wg_id = $_POST['_acf_element_id'];
        } elseif ( isset( $_POST['_acf_field_id'] ) ) {
            $wg_id = $_POST['_acf_field_id'];
        } elseif ( isset( $_POST['_acf_admin_page'] ) ) {
            $wg_id = $_POST['_acf_admin_page'];
        } else {
            return;
        }
        
        $module = ACFEF_Module::instance();
        
        if ( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'draft' ) {
            $form['post_id'] = $post_id;
            $form['hidden_fields']['main_action'] = 'edit_post';
            ob_start();
            acfef_render_form( $form );
            $response['clear_form'] = ob_get_clean();
            $response['replace_drafts'] = true;
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
            $wg_id
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
                $title = get_post_field( 'post_title', $post_id ) . '<a href="#" class="acf-icon -pencil small dark edit-post" data-name="edit_item"></a>';
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
                $query_args['updated'] = $wg_id . '_' . $_POST['_acf_screen_id'];
                if ( is_numeric( $post_id ) ) {
                    $query_args['updated'] .= '_' . $object_type . $post_id;
                }
                if ( isset( $form['redirect_action'] ) && $form['redirect_action'] == 'edit' ) {
                    $query_args['edit'] = 1;
                }
                if ( isset( $_POST['_acf_modal'] ) && $_POST['_acf_modal'] == 1 ) {
                    $query_args['modal'] = true;
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
                        $return = remove_query_arg( [
                            'form_id',
                            'modal',
                            'post_id',
                            'step'
                        ], $return );
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
        $message = '';
        
        if ( !isset( $_GET['step'] ) && isset( $_GET['updated'] ) && $_GET['updated'] !== 'true' ) {
            $widget = $this->get_the_widget();
            if ( !$widget ) {
                return;
            }
            $form_id = explode( '_', $_GET['updated'] );
            $widget_id = $form_id[0];
            $post_id = $widget_page = $form_id[1];
            if ( isset( $form_id[2] ) ) {
                $post_id = $form_id[2];
            }
            $settings = $widget->get_settings_for_display();
            if ( $settings['show_success_message'] == 'true' && $settings['redirect'] != 'current' ) {
                
                if ( isset( $settings['update_message'] ) ) {
                    $update_message = $settings['update_message'];
                    if ( strpos( $update_message, '[$' ) !== 'false' || strpos( $update_message, '[' ) !== 'false' ) {
                        $update_message = acfef_get_code_value( $update_message, $post_id );
                    }
                    $message = '<div class="acfef-message elementor-' . $widget_page . '">
								<div class="elementor-element elementor-element-' . $widget_id . '">
									<div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">' . $update_message . '</p><span class="acfef-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div>
								</div>
								</div>';
                }
            
            }
        }
        
        echo  $message ;
    }
    
    public function pre_save_post( $post_id, $form )
    {
        if ( !empty($_POST['acf']['_validate_email']) ) {
            return false;
        }
        return $post_id;
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
            'clear_form'     => $reload_form,
            'widget'         => $form['hidden_fields']['element_id'],
            'step'           => $form['step_index'],
            'replace_drafts' => true,
        ] );
        die;
        /* 	$query_args = [];
        		if( isset( $_POST[ '_acf_step_action' ] ) ){
        			$main_action = $_POST[ '_acf_step_action' ];
        		
        			$step_index++;
        			$query_args = [
        				'step' => $step_index,
        			];
        		
        			if( $main_action == 'new_post' ){
        				$query_args[ 'post_id' ] = $post_id;
        			}
        			if( $main_action == 'new_product' ){
        				$query_args[ 'product_id' ] = $post_id;
        			}
        			if( $main_action == 'new_user' && strpos( $post_id, 'user' ) !== false ){
        				$query_args[ 'user_id' ] = explode( '_', $post_id )[1];
        			}
        			
        			if( isset( $_POST[ '_acf_modal' ] ) && $_POST[ '_acf_modal' ] == 1 ) {
        				$query_args[ 'modal' ] = 1;
        			}	
        			if( isset( $_POST[ '_acf_element_id' ] ) ) {
        				$query_args[ 'form_id' ] = $_POST[ '_acf_element_id' ];
        			}
        			
        			// Redirect user back to the form page, with proper new $_GET parameters.
        			$redirect_url = add_query_arg( $query_args, wp_get_referer() );
        			$redirect_url = remove_query_arg( [ 'updated' ], $redirect_url );
        			
        			wp_send_json_success( [ 'redirect'=> $redirect_url ] );
        			die;
        		} */
    }
    
    public function delete_post()
    {
        if ( !acf_verify_nonce( 'acf_delete' ) || !isset( $_POST['_acf_delete_post'] ) ) {
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
        $post_id = $_POST['_acf_delete_post'];
        
        if ( isset( $form['force_delete'] ) && $form['force_delete'] == 'true' ) {
            $deleted = wp_delete_post( $post_id, true );
        } else {
            $deleted = wp_trash_post( $post_id );
        }
        
        
        if ( $deleted ) {
            $redirect_query = array(
                'trashed' => 1,
                'ids'     => $post_id,
            );
            $redirect_url = add_query_arg( $redirect_query, $form['redirect'] );
            wp_safe_redirect( $redirect_url );
            exit;
        }
    
    }
    
    protected function get_the_widget()
    {
        
        if ( isset( $_POST['_acf_element_id'] ) ) {
            $widget_id = $_POST['_acf_element_id'];
            $post_id = $_POST['_acf_screen_id'];
        } elseif ( isset( $_GET['updated'] ) ) {
            $form_id = explode( '_', $_GET['updated'] );
            $widget_id = $form_id[0];
            $post_id = $form_id[1];
        } else {
            return false;
        }
        
        
        if ( isset( $post_id ) ) {
            $elementor = Plugin::instance()->elementor();
            $document = $elementor->documents->get( $post_id );
            $module = ACFEF_Module::instance();
            if ( $document ) {
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
        add_action(
            'acf/submit_form',
            [ $this, 'acfef_after_submit' ],
            8,
            2
        );
        add_action( 'wp_footer', [ $this, 'form_message' ] );
        add_filter(
            'acf/pre_save_post',
            array( $this, 'pre_save_post' ),
            3,
            2
        );
        add_action(
            'wp',
            [ $this, 'delete_post' ],
            10,
            1
        );
    }

}
new FormSubmit();