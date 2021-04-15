<?php

namespace ACFFrontend;

use  Elementor\Core\Base\Module ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

class ACFEF_Google_API_Settings
{
    /**
     * Redirect non-admin users to home page
     *
     * This function is attached to the ‘admin_init’ action hook.
     */
    public function acfef_get_settings_fields( $field_keys )
    {
        $acfef_local_fields = array( array(
            'key'          => 'acfef_google_maps_api',
            'label'        => __( 'Google Maps API Key', 'acf-frontend-form-element' ),
            'name'         => 'google_maps_api',
            'type'         => 'text',
            'instructions' => '',
            'required'     => 0,
            'wrapper'      => array(
            'width' => '50.1',
            'class' => '',
            'id'    => '',
        ),
        ) );
        foreach ( $acfef_local_fields as $local_field ) {
            $local_field['value'] = get_option( $local_field['key'] );
            acf_add_local_field( $local_field );
            $field_keys[] = $local_field['key'];
        }
        return $field_keys;
    }
    
    public function acfef_update_maps_api()
    {
        acf_update_setting( 'google_api_key', get_option( 'acfef_google_maps_api' ) );
    }
    
    public function __construct()
    {
        add_filter( 'acfef/google_fields', [ $this, 'acfef_get_settings_fields' ] );
        add_action( 'acf/init', [ $this, 'acfef_update_maps_api' ] );
    }

}