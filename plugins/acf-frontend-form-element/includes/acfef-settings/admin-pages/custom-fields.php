<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( function_exists('acf_add_local_field') ):

acf_add_local_field(
	array(
		'key' => 'acfef_title',
		'label' => __( 'Title', 'acf-frontend-form-element' ),
		'required' => true,
		'name' => 'acfef_title',
		'type' => 'text',
		'custom_title' => true,
	)
);	


endif;
