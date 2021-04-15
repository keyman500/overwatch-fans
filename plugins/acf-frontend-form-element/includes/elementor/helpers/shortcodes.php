<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function acfef_get_field_names( $setting ) {
	$field_names = '';
	preg_match_all( '/\[(.+?)?\]/', $setting, $matches );

	array_shift( $matches );
	$field_names = [];
	foreach( $matches[0] as $field_name ){
		if( strpos( $field_name, ':' ) !== false ){
			$field_names[] = str_replace(' ', '', explode( ':', $field_name, 2 )[1] );
		}
	}

	$field_names = json_encode( $field_names );
	return htmlentities( $field_names );
}

function acfef_get_dynamic_preview( $setting ) {
		
	return preg_replace_callback( '/\[(.+?)?\]/', function( $matches ){
		$value = '';
		$return_content = explode( ':', $matches[1] );
		return '[' . $return_content[1] . ']'; 
	}, $setting );
}
	
function acfef_get_code_value( $setting, $post_id = false ) {
	if( ! $post_id ){
		if( is_author() ){
			$post_id = 'user_' . get_queried_object_id();
		}else{
			$post_id = get_queried_object_id();
		}
	}
		
	if( ! is_string( $setting ) ) return '';
	
	$dynamic_setting = preg_replace_callback( '/\[(.+?)?\]/', function( $matches ) use( $post_id ) {
		$value = '';
		$return_content = explode( ':', $matches[1] );
	
		if( $return_content[0] == 'user' || $return_content[0] == '$user' ){
			if( is_numeric( $post_id ) ){
				$post_id = get_current_user_id();
			}
			$value = acfef_get_user_match_value( $return_content, $post_id );			
		}	
		if( $return_content[0] == 'post' || $return_content[0] == '$post' || $return_content[0] == 'product' ){
			$value = acfef_get_post_match_value( $return_content, $post_id );
		}	
		
		if( $return_content[0] == 'acf' || $return_content[0] == '$acf' ){
			$value = acfef_get_acf_match_value( $return_content, $post_id );
		}
		return $value;
	}, $setting );

	if( is_string( $dynamic_setting ) ){
		return $dynamic_setting;
	}
	return $setting;
	
}

	function acfef_get_acf_match_value( $match_params, $post_id ){
		$return = '';
		
        $field = get_field_object( $match_params[1], $post_id );
		if ( ! isset( $field ) ) return '';
		if( ! isset( $field['value'] ) || $field['value'] == '' ) return '';
 
		$value = $field['value'];

		$multiple = isset( $field['multiple'] ) ? $field['multiple'] : 0;
        switch( $field['type'] ){
            case 'image': {
                if( $field['return_format'] == 'array' ){
                    $img_id = $value['ID'];
                    $img_url = $value['url'];
                }
                if( $field['return_format'] == 'url' ){
                    $img_id = attachment_url_to_postid( $value );
                    $img_url = $value;
                }
                if( $field['return_format'] == 'id' ){
                    $img_id = $value;
                    $img_url = wp_get_attachment_url( $value );
                }
                $return_type = isset( $match_params[2] ) ? $match_params[2] : '300';
                switch( $return_type ){
                    case 'image_link':
                        $return = $img_url;
                    break;
                    case 'image_id':
                        $return = $img_id;
                    break;
                    default:
                        $return = '<img style="max-width:' .$return_type. '" src="' . $img_url . '"/>';
                }
                break;
            }
            case 'user':
                $term_field = isset( $match_params[2] ) ? $match_params[2] : 'nickname';

                if( $multiple && is_array( $value ) ){
                    $x = 1;
                    foreach( $value as $user ){
                        $return .= acfef_get_user_field( $term_field, $user );
                        if( $x < count($value) ) $return .= ', '; 
                        $x++;
                    }
                }else{
                    $return = acfef_get_user_field( $term_field, $value );
                }
                break;
            case 'post_object':
            case 'relationship':
                $term_field = isset( $match_params[2] ) ? $match_params[2] : 'post_title';

                if( $multiple && is_array( $value ) ){
                    $x = 1;
                    foreach( $value as $post ){
                        $return .= get_post_field( $term_field, $post );
                        if( $x < count($value) ) $return .= ', '; 
                        $x++;
                    }
                }else{
                    $return = get_post_field( $term_field, $value );
                }
                break;
            case 'taxonomy':
                $term_field = isset( $match_params[2] ) ? $match_params[2] : 'name';

                if( $multiple && is_array( $value ) ){
                    $x = 1;
                    foreach( $value as $term ){
                        $return .= get_term_field( $term_field, $term );
                        if( $x < count($value) ) $return .= ', '; 
                        $x++;
                    }
                }else{
                    $return = get_term_field( $term_field, $value );
                }
                break;
            default:
                $return = $value;
				if( is_array( $return ) ){
					$return = acfef_implode_recur( ', ', $return );
				}

        }
			
		return $return;
	}

	function acfef_get_user_match_value( $match_params, $post_id ){
		$value = '';
		
		if( strpos( $post_id, 'user_' ) !== false ){
			$user_id = explode( '_', $post_id )[1];
		}elseif( strpos( $post_id, 'u' ) !== false ){
			$user_id = explode( 'u', $post_id )[1];
		}else{
			$user_id = $post_id;
		}
		
		$edit_user = get_userdata( $user_id );

		if( ! $edit_user instanceof WP_User ) return $value;
		
		switch( $match_params[1] ){
			case 'id':
				$value = $user_id;
			break;
			case 'username':
			case '_username':
				$value = $edit_user->user_login;
			break;
			case 'email':
			case '_user_email':
				$value = $edit_user->user_email;
			break;
			case 'first_name':
			case '_first_name':
				$value = $edit_user->first_name;
			break;
			case 'last_name':
			case '_last_name':
				$value = $edit_user->last_name;
			break;
			case 'display_name':
				$value = $edit_user->display_name;
			break;
			case 'role':
				$role = $edit_user->roles[0];
				global $wp_roles;
				$value = $wp_roles->roles[ $role ]['name'];
			break;
			case 'bio':
				$value = $edit_user->description;
			break;
		}
		return $value;
	}
	function acfef_get_post_match_value( $match_params, $post_id = false ){
		$value = '';
		$edit_post = get_post( $post_id );
		if( ! is_wp_error( $edit_post ) ){
			switch( $match_params[1] ){
				case 'id':
					$value = $post_id;
				break;
				case 'post_title':
				case 'title':
						$value = $edit_post->post_title;
				break;
				case 'slug':
						$value = $edit_post->post_name;
				break;
				case 'post_content':
				case 'content':
				case 'desc':
						$value = $edit_post->post_content;
				break;
				case 'post_excerpt':
				case 'excerpt':
				case 'short_desc':
						$value = $edit_post->post_excerpt;
				break;
				case 'featured_image':
				case 'main_image':
					$post_thumb_id = get_post_thumbnail_id( $post_id );
					$post_thumb_url = wp_get_attachment_url( $post_thumb_id );
					$max_width = '500px';
					if( isset( $match_params[2] ) ){
						if( $match_params[2] == 'image_link' ){
							$value = $post_thumb_id;
						}elseif( $match_params[2] == 'image_id' ){
							$value = $post_thumb_url;
						}else{
							$max_width = $match_params[2];
							if( is_numeric( $max_width ) ) $max_width .= 'px';							
						}
					}
					if( ! $value ){
						$value = '<div style="max-width:' .$max_width. '"><a href="' .$post_thumb_url. '"><img style=" width: 100%;height: auto" src="' . $post_thumb_url . '"/></a></div>';
					}
				break;
				case 'post_url':
				case 'url':
						$value = get_permalink( $post_id );
				break;
			}
			return $value;
		}
		
    }
    
    function acfef_implode_recur($separator, $arrayvar) {
        $output = "";
        foreach ($arrayvar as $av)
        if (is_array ($av)) 
            $out .= acfef_implode_recur($separator, $av); // Recursive array 
        else                   
            $out .= $separator.$av;
    
        return $out . '<br>';
    }

    function acfef_get_user_field( $field, $user = null, $context = 'display' ) {
		if( is_object( $user ) ) $user = $user->ID;
		if( is_array( $user ) ) $user = $user['ID'];

		$user_data = get_userdata( $user );
     
        if ( ! $user_data ) {
            return '';
        }
     
        if ( ! isset( $user_data->$field ) ) {
            return '';
        }
     
        return sanitize_user_field( $field, $user_data->$field, $user, $context );
    }