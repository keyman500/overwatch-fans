<?php
namespace ACFFrontend\Classes;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class ActionBase {

	abstract public function get_name();

	abstract public function get_label();

	public function run( $post_id, $settings, $step = false ){
		return $post_id;
	}

	public function add_field_options( $widget, $field, $label, $options ){
		return;
	}

	abstract public function register_settings_section( $widget );

}