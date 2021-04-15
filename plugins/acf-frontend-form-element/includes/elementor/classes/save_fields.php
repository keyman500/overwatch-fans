<?php
namespace ACFFrontend\Classes;

use ACFFrontend\Plugin;

use ACFFrontend\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SaveFields {
	public function iterate_data( $post_id, $data_container ) {
		if ( isset( $data_container['elType'] ) ) {
			if ( ! empty( $data_container['elements'] ) ) {
				$data_container['elements'] = $this->iterate_data( $post_id, $data_container['elements'] );
			}
            if ( empty( $data_container['widgetType'] ) ) {
                return $data_container;
			}
            return $this->save_widget_fields( $data_container, $post_id, true );
		}

        if( is_array( $data_container ) ){
            foreach ( $data_container as $element_key => $element_value ) {
                $element_data = $this->iterate_data( $post_id, $data_container[ $element_key ] );

                if ( null === $element_data ) {
                    continue;
                }

                $data_container[ $element_key ] = $element_data;
            }
        }

		return $data_container;
	}
	
	public function save_widget_fields( $widget, $post_id = '', $update = false ) {
		$widget_names = acfef()->elementor->form_widgets;
		if( is_object( $widget ) ){
			if( in_array( $widget->get_name(), $widget_names ) ){
				$settings = $widget->get_settings();
				$wg_id = $widget->get_id();
				$wg_type = $widget->get_name();
			}else{
				return $widget;
			}
		}elseif( is_array( $widget ) ){
			if( in_array( $widget['widgetType'], $widget_names ) ){
				$settings = $widget['settings'];
				$wg_id = $widget['id'];
				$wg_type = $widget['widgetType'];
			}else{
				return $widget;
			}
		}else{
			return $widget;
		}

		if( ! $post_id ){
			$post_id = get_queried_object_id();
		} 
		$module = acfef()->elementor;
		
	
		if( $wg_type != 'acf_ele_form' && $wg_type != 'acf_form_fields' ){
			$settings['main_action'] = $wg_type;
		}else{
			if( ! isset( $settings['main_action'] ) ){
				$settings['main_action'] = 'edit_post';
			}
		}
		
		if( ! empty( $settings['fields_selection'] ) ){
			$exclude = ['ACF_field_groups', 'ACF_fields', 'default_fields', 'step'];
			switch( $settings['main_action'] ){
				case 'new_post':
				case 'edit_post':
				case 'duplicate_post':
					$action = acfef()->local_actions['post'];
				break;
				case 'new_user':
				case 'edit_user':
					$action = acfef()->local_actions['user'];
				break;
				case 'edit_term':
				case 'new_term':
						$action = acfef()->local_actions['term'];
				break;
				case 'edit_options':
					$action = acfef()->local_actions['options'];
				break;
				case 'new_comment':
					$action = acfef()->local_actions['comment'];
				break;
				case 'new_product':
				case 'edit_product':
					$action = acfef()->local_actions['product'];
				break;
			}

			if( isset( $action ) ){
				foreach( $settings['fields_selection'] as $form_field ){
					if( empty( $form_field['field_type'] ) || in_array( $form_field['field_type'], $exclude ) ){
						continue;
					}	
					$local_field = [];
					
					if( $form_field['field_type'] == 'taxonomy' ){
						$taxonomy = isset( $form_field['field_taxonomy'] ) ? $form_field['field_taxonomy'] : 'category';
						$field_key = 'acfef_' . $wg_id .  '_' . $taxonomy;
					}else{
						$field_key = 'acfef_' . $wg_id .  '_' . $form_field['field_type'];
						if( $form_field['field_type'] == 'recaptcha' ){
							$field_key .= '_' . $form_field['_id'];
						}
					}

					$local_field = acf_get_field( $field_key );

					if( ! empty( $local_field ) && ! $update ){
						continue;
					}
					if( empty( $local_field ) ){
						$local_field = [
							'name' => $field_key,
							'key' => $field_key,
						]; 
					}
					if( isset( $form_field['__dynamic__'] ) ) $form_field = $this->acfef_parse_tags( $form_field );
					$default_value = isset( $form_field['field_default_value'] ) ? $form_field['field_default_value'] : '';

					if( ! isset( $form_field['__dynamic__']['field_default_value'] ) ){
						if( strpos( $default_value, '[' ) !== false ){
							$data_default = acfef_get_field_names( $default_value ); 
							$default_value = acfef_get_dynamic_preview( $default_value );
						}   
					}
					
					$local_field = array_merge( $local_field, [
						'required' => isset( $form_field['field_required'] ) && $form_field['field_required'] ? 1 : 0,
						'instructions' => isset( $form_field['field_instruction'] ) ? $form_field['field_instruction'] : '',
						'wrapper' => ['class' => 'elementor-repeater-item-' . $form_field['_id'] ],
						'placeholder' => isset( $form_field['field_placeholder'] ) ? $form_field['field_placeholder'] : '',
						'default_value' => $default_value,
						'disabled' => isset( $form_field['field_disabled'] ) ? $form_field['field_disabled'] : 0,
						'readonly' => isset( $form_field['field_readonly'] ) ? $form_field['field_disabled'] : 0,
					] );
					if( isset( $data_default ) ){
						$local_field['wrapper']['data-default'] = $data_default;
						$local_field['wrapper']['data-dynamic_value'] = $default_value;
					}
					if( isset( $form_field['field_hidden'] ) && $form_field['field_hidden'] ){
						$local_field['wrapper']['class'] = 'acf-hidden';
					}
					$field_label = ucwords( str_replace( '_', ' ', $form_field['field_type'] ) );
					$local_field['label'] = isset( $form_field['field_label'] ) ? $form_field['field_label'] : $field_label;

					if( isset( $form_field['button_text'] ) && $form_field['button_text'] ){
						$local_field['button_text'] = $form_field['button_text'];
					}

					$sub_fields = false;
					if( $form_field['field_type'] == 'attributes' ){
						$sub_fields = $settings['attribute_fields'];     
					}   
	
					$local_field = $action->get_fields_display( $form_field, $local_field, $wg_id, $sub_fields );

					if( isset( $local_field['type'] ) ){ 
						if( $local_field['type'] == 'password' ){
							$local_field['password_strength'] = isset( $form_field['password_strength'] ) ? $form_field['password_strength'] : 3;
							$password_strength = true;
						}	
					}else{
						$local_field['type'] = $form_field['field_type'];
					}			
					
					if( isset( $form_field['field_label_on'] ) ){
						$local_field['field_label_hide'] = 1;
						$local_field['label'] = '';
					}else{
						$local_field['field_label_hide'] = 0;
					}

					if( ! empty( $form_field['default_terms'] ) ){
						$local_field['default_terms'] = $form_field['default_terms'];
					}
								
					acf_update_field( $local_field );
					unset( $data_default );
					unset( $default_value );
				}
	
			}

		}
		return $widget;
    }

	public function acfef_parse_tags( $settings ){
		$dynamic_tags = $settings['__dynamic__'];
		foreach( $dynamic_tags as $control_name => $tag ){
			$settings[ $control_name ] = $tag;
		}
		return $settings;
	}

	public function __construct(){
		add_action( 'elementor/editor/after_save', [ $this, 'iterate_data'], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'save_widget_fields'] );
	}
	
}

new SaveFields();