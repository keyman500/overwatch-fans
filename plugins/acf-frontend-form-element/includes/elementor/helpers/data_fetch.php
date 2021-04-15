<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function acfef_get_elementor_instance() {
	return \Elementor\Plugin::$instance;
}

function acfef_get_current_post_id() {
	$el = acfef_get_elementor_instance();
	if ( isset( $el->documents ) ) {
		$current_page = $el->documents->get_current();
		if( isset( $current_page ) ){
			return $el->documents->get_current()->get_main_id();
		}
	}
	return get_the_ID();
}

function acfef_get_field_data( $type = null ){
	$field_types = [];
	$acf_field_groups = acf_get_field_groups();
	// bail early if no field groups
	if( empty($acf_field_groups) ) die();
	// loop through array and add to field 'choices'
	if( $acf_field_groups ) {        
		foreach( $acf_field_groups as $field_group ) {
			$field_group_fields = get_fields( $field_group['key'] );
			
			if( is_array( $field_group_fields ) ) { 
				foreach( $field_group_fields as $acf_field ) {					
					$field_object = get_field_object( $acf_field->post_name );
					
					if( isset( $type ) ){
						if( ( is_array( $type ) && in_array( $field_object['type'], $type ) ) || ( ! is_array( $type ) && $field_object['type'] == $type ) ){
							$field_types[ $acf_field->post_name] = $field_object['label']; 
						}
					}else{
						$field_types[ $acf_field->post_name]['type'] = $field_object['type']; 
						$field_types[ $acf_field->post_name]['label'] = $field_object['label'];  
						$field_types[ $acf_field->post_name]['name'] = $field_object['name'];  
					}
				}
			} 
		}
	}
	return $field_types;
}	


function acfef_get_group_field_keys( $field_group ){
	$fields = get_fields( $field_group['key'] );
	$keys = [];
	if( is_array( $fields ) ){
		foreach( $fields as $field ){
			$keys[] = $field->post_name;
		}
	}
	return $keys;
}

function acfef_get_grouped_fields(){
	$acfef_field_groups = [];
	$field_groups = acf_get_field_groups();
	foreach($field_groups as $field_group){
		$fields = acfef_get_group_field_keys( $field_group );
		$acfef_field_groups[$field_group['key']] = $fields;
	}
	return $acfef_field_groups;
}

function acfef_get_user_id_fields(){
	$fields = acfef_get_acf_field_choices( false, 'user' );
	$keys = array_merge( ['[author]' => __( 'Post Author', 'acf-frontend-form-element' ) ],  $fields );
	return $keys;
}

function acfef_get_default_groups( $post_id ){
	$groups = [];
	global $post;
	$post_id = ( $_GET['post'] ) ? $post_id = $_GET['post'] : $post->ID;
	$post_groups = acf_get_field_groups( array( 'post_id' => $post_id ) );
	foreach($post_groups as $group){
		$groups[] = $group['key'];
	}
	return $groups;
}


function acfef_get_user_roles( $exceptions = [], $all = false ){
	if( ! current_user_can('administrator') ) $exceptions[] = 'administrator';
	
	$user_roles = array();

	if( $all ){
		$user_roles['all'] = __( 'All', 'acf-frontend-form-element' );
	}
	global $wp_roles;
	// loop through array and add to field 'choices'
		foreach( $wp_roles->roles as $role => $settings ) {
			if( ! in_array( strtolower( $role ), $exceptions ) ){
				$user_roles[ $role ] = $settings['name']; 
			}
		}
	return $user_roles;
}


function acfef_get_labeled_fields( $fields ){
	$field_choices = [];
	foreach( $fields as $field ){
		$field_object = get_field_object( $field );			
		$parent_group = get_post( $field_object['parent'] );
		$field_choices[ $field_object['type'] ][ $field ] = $field_object['label'] . ' (' . $parent_group->post_title . ')';
	}
	return $field_choices;
}

function acfef_get_acf_field_group_choices(){
	$field_group_choices = [];
	$acf_field_groups = acf_get_field_groups();
	// loop through array and add to field 'choices'
	if( is_array( $acf_field_groups ) ) {        
		foreach( $acf_field_groups as $field_group ) {
			if( is_array( $field_group ) && ! isset( $field_group['acfef_group'] ) ){
				$field_group_choices[ $field_group['key'] ] = $field_group['title']; 
			}
		}
	}
	return $field_group_choices;
}	

function acfef_get_acf_field_choices( $groups = false, $type = false ){
	$user_fields = $all_fields = []; 
	if( $groups ){
		$acf_field_groups = $groups;
	}else{
		$acf_field_groups = acf_get_field_groups();
	}

	// bail early if no field groups
	if( empty($acf_field_groups) ) return array();

	foreach( $acf_field_groups as $group ) {
		if( ! is_array( $group ) ) $group = acf_get_field_group( $group );
		
		$group_fields = acf_get_fields( $group );

		if( is_array( $group_fields ) ) { 
			foreach( $group_fields as $acf_field ) {
				if( ! is_array( $acf_field ) ) continue;

				$acf_field_key = isset( $acf_field['_clone'] ) ? $acf_field['__key'] : $acf_field['key'];
				if( $type == 'user' && $acf_field['type'] == 'user' ){
					if( $acf_field['multiple'] == 0 && $acf_field['return_format'] == 'id' ){
						$user_fields[ $acf_field['name'] ] = $acf_field['label'];
					}
				}else{
					if( $groups ){
						$all_fields[] = $acf_field_key;
					}else{
						$all_fields[$acf_field_key] = $acf_field['label']; 
					}
				}
			}
		} 
	}

	if( $type == 'user' && isset( $user_fields ) ) return $user_fields;

	return $all_fields;	
}	
function acfef_get_post_statuses_choices(){
	global $wp_post_statuses;
	
	// Append to choices.
	$choices = array();
	if( $wp_post_statuses ) {
		foreach( $wp_post_statuses as $status ) {
			$choices[ $status->name ] = $status->label;
		}
	}
	return $choices;
}
function acfef_get_post_type_choices(){
	$post_type_choices = [];
	$args = array();
	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
	$post_types = get_post_types( $args, $output, $operator ); 
	// loop through array and add to field 'choices'
	if( is_array( $post_types ) ) {        
		foreach( $post_types as $post_type ) {
			$post_type_choices[ $post_type ] = str_replace( '_', ' ', ucfirst( $post_type ) ); 
		}
	}
	return $post_type_choices;
}

function acfef_get_image_folders(){
	$folders = ['all' => __( 'All Folders', 'acf-frontend-form-element' ) ];
	$taxonomies = get_terms( array(
		'taxonomy' => 'happyfiles_category',
		'hide_empty' => false
	) );

	if ( empty($taxonomies) ) {
		return $folders;
	}
	
	foreach( $taxonomies as $category ) {
		$folders[ $category->name ] = ucfirst( esc_html( $category->name ) );	
	}

	return $folders;
}

function acfef_random_string($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function acfef_get_stripe_currencies(){
	return [
		'AFN' => 'Afghan Afghani',
		'ALL' => 'Albanian Lek',
		'DZD' => 'Algerian Dinar',
		'AOA' => 'Angolan Kwanza',
		'ARS' => 'Argentine Peso',
		'AMD' => 'Armenian Dram',
		'AWG' => 'Aruban Florin',
		'AUD' => 'Australian Dollar',
		'AZN' => 'Azerbaijani Manat',
		'BSD' => 'Bahamian Dollar',
		'BDT' => 'Bangladeshi Taka',
		'BBD' => 'Barbadian Dollar',
		'BZD' => 'Belize Dollar',
		'BMD' => 'Bermudian Dollar',
		'BOB' => 'Bolivian Boliviano',
		'BAM' => 'Bosnia & Herzegovina Convertible Mark',
		'BWP' => 'Botswana Pula',
		'BRL' => 'Brazilian Real',
		'GBP' => 'British Pound',
		'BND' => 'Brunei Dollar',
		'BGN' => 'Bulgarian Lev',
		'BIF' => 'Burundian Franc',
		'KHR' => 'Cambodian Riel',
		'CAD' => 'Canadian Dollar',
		'CVE' => 'Cape Verdean Escudo',
		'KYD' => 'Cayman Islands Dollar',
		'XAF' => 'Central African Cfa Franc',
		'XPF' => 'Cfp Franc',
		'CLP' => 'Chilean Peso',
		'CNY' => 'Chinese Renminbi Yuan',
		'COP' => 'Colombian Peso',
		'KMF' => 'Comorian Franc',
		'CDF' => 'Congolese Franc',
		'CRC' => 'Costa Rican Colón',
		'HRK' => 'Croatian Kuna',
		'CZK' => 'Czech Koruna',
		'DKK' => 'Danish Krone',
		'DJF' => 'Djiboutian Franc',
		'DOP' => 'Dominican Peso',
		'XCD' => 'East Caribbean Dollar',
		'EGP' => 'Egyptian Pound',
		'ETB' => 'Ethiopian Birr',
		'EUR' => 'Euro',
		'FKP' => 'Falkland Islands Pound',
		'FJD' => 'Fijian Dollar',
		'GMD' => 'Gambian Dalasi',
		'GEL' => 'Georgian Lari',
		'GIP' => 'Gibraltar Pound',
		'GTQ' => 'Guatemalan Quetzal',
		'GNF' => 'Guinean Franc',
		'GYD' => 'Guyanese Dollar',
		'HTG' => 'Haitian Gourde',
		'HNL' => 'Honduran Lempira',
		'HKD' => 'Hong Kong Dollar',
		'HUF' => 'Hungarian Forint',
		'ISK' => 'Icelandic Króna',
		'INR' => 'Indian Rupee',
		'IDR' => 'Indonesian Rupiah',
		'ILS' => 'Israeli New Sheqel',
		'JMD' => 'Jamaican Dollar',
		'JPY' => 'Japanese Yen',
		'KZT' => 'Kazakhstani Tenge',
		'KES' => 'Kenyan Shilling',
		'KGS' => 'Kyrgyzstani Som',
		'LAK' => 'Lao Kip',
		'LBP' => 'Lebanese Pound',
		'LSL' => 'Lesotho Loti',
		'LRD' => 'Liberian Dollar',
		'MOP' => 'Macanese Pataca',
		'MKD' => 'Macedonian Denar',
		'MGA' => 'Malagasy Ariary',
		'MWK' => 'Malawian Kwacha',
		'MYR' => 'Malaysian Ringgit',
		'MVR' => 'Maldivian Rufiyaa',
		'MRO' => 'Mauritanian Ouguiya',
		'MUR' => 'Mauritian Rupee',
		'MXN' => 'Mexican Peso',
		'MDL' => 'Moldovan Leu',
		'MNT' => 'Mongolian Tögrög',
		'MAD' => 'Moroccan Dirham',
		'MZN' => 'Mozambican Metical',
		'MMK' => 'Myanmar Kyat',
		'NAD' => 'Namibian Dollar',
		'NPR' => 'Nepalese Rupee',
		'ANG' => 'Netherlands Antillean Gulden',
		'TWD' => 'New Taiwan Dollar',
		'NZD' => 'New Zealand Dollar',
		'NIO' => 'Nicaraguan Córdoba',
		'NGN' => 'Nigerian Naira',
		'NOK' => 'Norwegian Krone',
		'PKR' => 'Pakistani Rupee',
		'PAB' => 'Panamanian Balboa',
		'PGK' => 'Papua New Guinean Kina',
		'PYG' => 'Paraguayan Guaraní',
		'PEN' => 'Peruvian Nuevo Sol',
		'PHP' => 'Philippine Peso',
		'PLN' => 'Polish Złoty',
		'QAR' => 'Qatari Riyal',
		'RON' => 'Romanian Leu',
		'RUB' => 'Russian Ruble',
		'RWF' => 'Rwandan Franc',
		'STD' => 'São Tomé and Príncipe Dobra',
		'SHP' => 'Saint Helenian Pound',
		'SVC' => 'Salvadoran Colón',
		'WST' => 'Samoan Tala',
		'SAR' => 'Saudi Riyal',
		'RSD' => 'Serbian Dinar',
		'SCR' => 'Seychellois Rupee',
		'SLL' => 'Sierra Leonean Leone',
		'SGD' => 'Singapore Dollar',
		'SBD' => 'Solomon Islands Dollar',
		'SOS' => 'Somali Shilling',
		'ZAR' => 'South African Rand',
		'KRW' => 'South Korean Won',
		'LKR' => 'Sri Lankan Rupee',
		'SRD' => 'Surinamese Dollar',
		'SZL' => 'Swazi Lilangeni',
		'SEK' => 'Swedish Krona',
		'CHF' => 'Swiss Franc',
		'TJS' => 'Tajikistani Somoni',
		'TZS' => 'Tanzanian Shilling',
		'THB' => 'Thai Baht',
		'TOP' => 'Tongan Paʻanga',
		'TTD' => 'Trinidad and Tobago Dollar',
		'TRY' => 'Turkish Lira',
		'UGX' => 'Ugandan Shilling',
		'UAH' => 'Ukrainian Hryvnia',
		'AED' => 'United Arab Emirates Dirham',
		'USD' => 'United States Dollar',
		'UYU' => 'Uruguayan Peso',
		'UZS' => 'Uzbekistani Som',
		'VUV' => 'Vanuatu Vatu',
		'VND' => 'Vietnamese Đồng',
		'XOF' => 'West African Cfa Franc',
		'YER' => 'Yemeni Rial',
		'ZMW' => 'Zambian Kwacha'
	];
}

function acfef_get_paypal_currencies(){
	return [	
		'AUD' => 'Australian Dollar',
		'BRL' => 'Brazilian',
		'CAD' => 'Canadian Dollar',
		'CNY' => 'Chinese Renmenbi',	
		'CZK' => 'Czech Koruna',
		'DKK' => 'Danish Krone',	
		'EUR' => 'Euro',	
		'HKD' => 'Hong Kong Dollar',	
		'HUF' => 'Hungarian Forint',
		'INR' => 'Indian Rupee',
		'ILS' => 'Israeli new Shekel',
		'JPY' => 'Japanese yen',
		'MYR' => 'Malaysian Ringgit',
		'MXN' => 'Mexican Peso',	
		'TWD' => 'New Taiwan Dollar',
		'NZD' => 'New Zealand Dollar',
		'NOK' => 'Norwegian Krone',	
		'PHP' => 'Philippine Peso',	
		'PLN' => 'Polish Złoty',	
		'GBP' => 'Pound Sterling',	
		'RUB' => 'Russian Ruble',	
		'SGD' => 'Singapore Dollar',	
		'SEK' => 'Swedish Krona',	
		'CHF' => 'Swiss Franc',	
		'THB' => 'Thai Baht',	
		'USD' => 'United States Dollar',	
	];
}



function acfef_get_client_ip() {
	$server_ip_keys = [
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'REMOTE_ADDR',
	];

	foreach ( $server_ip_keys as $key ) {
		if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
			return $_SERVER[ $key ];
		}
	}

	// Fallback local ip.
	return '127.0.0.1';
}

function acfef_get_site_domain() {
	return str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
}


function acfef_esc_attrs( $attrs ) {
	$html = '';
	
	// Loop over attrs and validate data types.
	foreach( $attrs as $k => $v ) {
		
		// String (but don't trim value).
		if( is_string($v) && ($k !== 'value') ) {
			$v = trim($v);
			
		// Boolean	
		} elseif( is_bool($v) ) {
			$v = $v ? 1 : 0;
			
		// Object
		} elseif( is_array($v) || is_object($v) ) {
			$v = json_encode($v);
		}
		
		// Generate HTML.
		$html .= sprintf( ' %s="%s"', esc_attr($k), esc_attr($v) );
	}
	
	// Return trimmed.
	return trim( $html );
}
	

function acfef_slug_exists($post_name) {
    global $wpdb;
    if($wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = '$post_name'", 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

function acfef_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = acfef_parse_args( $value, $new_args[ $key ] );
		}
		else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}
