<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function acfef_can_edit_user( $user_id, $form_args ){
		$active_user = wp_get_current_user();
        $can_edit = false;
        
        if( isset( $_GET['updated'] ) ){
            $form_data = explode( '_u', $_GET['updated'] );
            if( isset( $form_data[1] ) ){
                $user_id = $form_data[1];
            }
        }
        if( isset( $_GET['user_id'] ) ){
            $user_id = $_GET['user_id'];
        }

        if( isset( $user_id ) && is_user_logged_in() ){
            if( is_array( $active_user->roles ) ){
                if ( in_array( 'administrator', $active_user->roles ) ) {
                    $can_edit = $user_id;
                }
            }
            if( $active_user->ID == $user_id || get_user_meta( $user_id, 'acfef_manager', true ) == $active_user->ID ){
                $can_edit = $user_id;
            }
		}
		return $can_edit;
	}
function acfef_can_edit_post( $post_id, $form_args ){
    $active_user = wp_get_current_user();
    $can_edit = false;

    if( isset( $_GET['updated'] ) ){
        $form_data = explode( '_', $_GET['updated'] );
        if( isset( $form_data[2] ) ){
            $post_id = $form_data[2];
        }
    }
    if( isset( $_GET['post_id'] ) ){
        $post_id == $_GET['post_id'];
    }

    if( isset( $post_id ) && is_user_logged_in() ){
        $edit_post = get_post( $post_id );

        if( is_array( $active_user->roles ) ){
            if ( in_array( 'administrator', $active_user->roles ) ) {
                $can_edit = $post_id;
            }else{
                if( $active_user->ID == $edit_post->post_author ){
                    $can_edit = $post_id;
                }
            }
        }
    }

    return $can_edit;
}
