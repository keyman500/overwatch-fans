<?php
namespace ACFFrontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACFEF_Uploads_Privacy_Settings{
	
	public function get_name() {
		return 'uploads_privacy';
	}
	
	public function media_uploads_privacy_section(){	
		register_setting( 'uploads_privacy_settings', 'filter_media_author' );
		add_settings_section(
			'uploads_privacy_settings',
			__( 'Media Uploads Privacy', 'acf-frontend-form-element' ),
			'',
			'uploads-privacy-settings-admin'
		);
		add_settings_field(
			'filter_media_author', 
			__( 'Filter Media by Author', 'acf-frontend-form-element' ),
			[ $this, 'filter_by_author_field'],
			'uploads-privacy-settings-admin',
			'uploads_privacy_settings', 
			[
				'label_for' => 'filter_media_author'
			] 
		);
	}
	
	function filter_by_author_field($args) {
		$value = ( get_option( 'filter_media_author' ) == 1 ) ? $value  = ' checked' : '';
    	echo '<input type="checkbox" id="filter_media_author" name="filter_media_author" value="1"' . $value . '/>';
	}
	
	function filter_media_author( $query ){
    	if ( get_option( 'filter_media_author' ) == '1' ) {
			$user_id = get_current_user_id();
			if ( $user_id && ! current_user_can( 'activate_plugins' ) ) {
				$query['author'] = $user_id;
			}
		}
		return $query;
	}
	
	public function __construct() {	
		add_action( 'admin_init', [ $this, 'media_uploads_privacy_section'] );		  
		add_filter( 'ajax_query_attachments_args', [ $this, 'filter_media_author'] );
		
	}
	
}

