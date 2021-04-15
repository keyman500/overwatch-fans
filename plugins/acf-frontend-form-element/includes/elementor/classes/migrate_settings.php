<?php
namespace ACFFrontend\Classes;


use ACFFrontend\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class MigrateSettings{

    public function get_form_fields( $settings, $widget_type ){
        if( $widget_type != 'acf_ele_form' ){
            $settings['main_action'] = $widget_type;
        }else{
            if( ! isset( $settings['main_action'] ) ){
                $settings['main_action'] = 'edit_post';
            }
        }
        switch( $settings['main_action'] ){
            case 'new_post': 
            case 'edit_post':
                $fields = ['title', 'content', 'featured_image', 'excerpt', 'categories', 'tags'];
            break;              
            case 'new_user': 
            case 'edit_user':
                $fields = ['username', 'password', 'confirm_password', 'email', 'first_name', 'last_name', 'bio', 'role'];
            break;
            case 'edit_term': 
                $fields = ['term_name'];
            break;
            case 'edit_options': 
                $fields = ['site_title', 'site_tagline', 'site_logo'];
            break;
            case 'new_comment': 
                $fields = ['comment'];
            break;
            case 'new_product': 
            case 'edit_product':
                $fields = ['product_title', 'product_content', 'product_featured_image', 'product_images', 'product_excerpt', 'product_categories', 'product_tags', 'product_price', 'sale_price', 'sku', 'stock_status', 'sold_ind'];
            break;
        }
        if( isset( $fields ) ){
            foreach( $fields as $field ){
                $default_fields = [
                    'title', 'username', 'password', 'email', 'term_name', 'product_title', 'site_title', 'site_tagline', 'site_logo', 'product_price', 'sale_price', 'sku', 'stock_status', 'sold_ind',
                ];
                if( in_array( $field, $default_fields ) && ! isset( $settings['show_' . $field ] ) ){
                    $settings['show_' . $field ] = 'default';
                }
                if( isset( $settings['show_' . $field ] ) && $settings['show_' . $field ] != false ){
                    if( $settings['main_action'] == 'edit_product' || $settings['main_action'] == 'new_product' ){
                        $field = $this->change_control_names( $field );
                    }                     
                    $settings['fields_selection'][] = [
                        'field_type' => $field,
                        'field_width' => isset( $settings[ $field . '_width'] ) ? $settings[ $field . '_width']: 100,
                        'field_width_tablet' => isset( $settings[ $field . '_width_tablet'] ) ? $settings[ $field . '_width_tablet']: 100,
                        'field_width_mobile' => isset( $settings[ $field . '_width_mobile'] ) ? $settings[ $field . '_width_mobile']: 100,
                        'field_required' => isset( $settings[ $field . '_required'] ) ? $settings[ $field . '_required'] : '',
                        'field_instruction' => isset( $settings[ $field . '_instruction'] ) ? $settings[ $field . '_instruction'] : '',
                        'field_label' => isset( $settings[ $field . '_text'] ) ? $settings[ $field . '_text'] : '',
                        'field_label_on' => 'true',
                        '_id' => $this->generateRandomString(),
                    ];
                }
            }
        } 
        if( ! isset( $settings['form_fields'] ) ){
            $settings['form_fields'] = 'field_groups';
        }
        switch( $settings['form_fields'] ){
            case 'field_groups':
                $field_type = 'ACF_field_groups';
                if( isset( $settings['field_groups_select'] ) ){
                    $acf_fields = $settings['field_groups_select'];
                }
            break;
            case 'fields':
                $field_type = 'ACF_fields';
                if( isset( $settings['fields_select'] ) ){
                    $acf_fields = $settings['fields_select'];
                }
            break; 
        }
        if( isset(  $field_type ) ){
            $settings['fields_selection'][] = [
                'field_type' => $field_type,
                'field_groups_select' => isset( $acf_fields ) ? $acf_fields : [],
                'fields_select' => isset( $acf_fields ) ? $acf_fields : [],
                '_id' => $this->generateRandomString(),
            ];
        }
        return $settings;
    }

    private function change_control_names( $field ){
        switch( $field ){
            case 'product_content':
                return 'description';
            break;
            case 'product_featured_image':
                return 'main_image';
            break;
            case 'product_images':
                return 'images';
            break;
            case 'product_excerpt':
                return 'short_description';
            break;
            case 'product_price':
                return 'price';
            break;
            case 'sold_ind':
                return 'sold_individually';
            break;
        }
    }
	
    public function change_old_to_new( $widget, $args, $remove_old = false ) {
        $widget_type = $widget['widgetType'];
        $settings = $widget['settings'];
        $settings['fields_selection'] = [];
        if( isset( $settings['multi'] ) ){
            if( ! $settings['multi'] ){
                $settings = $this->get_form_fields( $settings, $widget_type );
            }else{
                if( ! empty( $settings['form_steps'] ) ){
                    foreach( $settings['form_steps'] as $index => $step ){
                        if( $index == 0 ){
                            $first_step = [
                                'field_type' => 'step',
                                'overwrite_settings' => true,
                            ];
                            $settings['first_step'][ 0 ] = array_merge( $step, $first_step );
                            $settings['first_step'][ 0 ]['overwrite_settings'] = true;  
                        }else{
                            $step_field = [
                                'field_type' => 'step',
                                '_id' => $this->generateRandomString(),
                                'overwrite_settings' => true,
                            ];
                            $settings['fields_selection'][] = array_merge( $step_field, $step );
                        }
                        
                        $step_fields = $this->get_form_fields( $step, $widget_type );
                        $settings['fields_selection'] = array_merge( $settings['fields_selection'], $step_fields['fields_selection'] );
                    }
                }
            }
        }
        $widget['settings'] = $settings;
          
		return $widget;
    }

    public function update_widget_settings() {
        update_option( 'acfef_migrated_2_5_5', 1 );
		global $wpdb;
		$post_ids = $this->query_col();
		if ( empty( $post_ids ) ) {
			return false;
		}

		foreach ( $post_ids as $post_id ) {
            
            $elementor = acfef_get_elementor_instance();
            if( $elementor->documents ){
                $document = $elementor->documents->get( $post_id );
            }

			if ( ! isset( $document ) || ! $document ) {
				continue;
			}

            $data = $document->get_elements_data();
			if ( empty( $data ) ) {
				continue;
            }
            
            $this->change_widget_controls( $data, $post_id );

		} 
        if( count( $post_ids ) == 100 ){
            $this->update_widget_settings();
        }
	}

    public function query_col() {
        global $wpdb;

        $sql = 'SELECT `post_id` 
        FROM `' . $wpdb->postmeta . '` 
        WHERE (`meta_key` = "_elementor_data") 
        AND (`meta_value` LIKE \'%"widgetType":"acf_ele_form"%\'
        OR `meta_value` LIKE \'%"widgetType":"edit_post"%\'
        OR `meta_value` LIKE \'%"widgetType":"new_post"%\'
        OR `meta_value` LIKE \'%"widgetType":"edit_user"%\'
        OR `meta_value` LIKE \'%"widgetType":"new_user"%\'
        OR `meta_value` LIKE \'%"widgetType":"edit_options"%\'
        OR `meta_value` LIKE \'%"widgetType":"edit_product"%\'
        OR `meta_value` LIKE \'%"widgetType":"new_product"%\'
        OR `meta_value` LIKE \'%"widgetType":"new_comment"%\')
        AND (`meta_value` NOT LIKE \'%"fields_selection"%\')
        LIMIT %d;';
		// Add offset & limit.
		$sql = preg_replace( '/;$/', '', $sql );

		$results = $wpdb->get_col( $wpdb->prepare( $sql, 100 ) ); 

		return $results;
    }

    public function get_early_postid() {
        return url_to_postid( ( isset($_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" );
    }
    
    public function change_widget_controls( $data, $post_id ){
		// loop thru callbacks & array
        $args = [
            'post_id' => $post_id,
            'widget_name' => [
                'acf_ele_form', 'edit_post', 'new_post', 'edit_product', 'new_product', 'new_comment', 'edit_user', 'new_user', 'edit_options' 
            ],
        ];
        $data = $this->iterate_data( $data, $args );

        // We need the `wp_slash` in order to avoid the unslashing during the `update_metadata`
        $json_value = wp_slash( wp_json_encode( $data ) );

        update_metadata( 'post', $post_id, '_elementor_data', $json_value );
    
    }

    public function iterate_data( $data_container, $args = [] ) {
		if ( isset( $data_container['elType'] ) ) {
			if ( ! empty( $data_container['elements'] ) ) {
				$data_container['elements'] = $this->iterate_data( $data_container['elements'], $args );
			}
            if ( empty( $data_container['widgetType'] ) || ! in_array( $data_container['widgetType'], $args['widget_name'] ) || isset( $data_container['settings']['migrated_2_5_5'] ) ) {
                return $data_container;
            }
            return $this->change_old_to_new( $data_container, $args );
		}

        if( is_array( $data_container ) ){
            foreach ( $data_container as $element_key => $element_value ) {
                $element_data = $this->iterate_data( $data_container[ $element_key ], $args );

                if ( null === $element_data ) {
                    continue;
                }

                $data_container[ $element_key ] = $element_data;
            }
        }

		return $data_container;
    }
    
    public function generateRandomString($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function __construct() {
        $this->update_widget_settings();
    }
}

new MigrateSettings();