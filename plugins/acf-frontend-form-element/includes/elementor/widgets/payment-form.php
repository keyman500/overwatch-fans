<?php
namespace ACFFrontend\Widgets;

use ACFFrontend\Plugin;

use ACFFrontend\Classes;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Widget_Base;
use ElementorPro\Modules\QueryControl\Module as Query_Module;
use ACFFrontend\Controls;

	
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Elementor ACF Frontend Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class Payment_Form_Widget extends Widget_Base {
	
	/**
	 * Get widget name.
	 *
	 * Retrieve acf ele form widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'payment-form';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve acf ele form widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Payment Form', 'acf-frontend-form-element' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve acf ele form widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-credit-card frontend-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the acf ele form widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['acfef-forms'];
    }
    
	/**
	 * Register acf ele form widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		do_action( 'acfef/content_controls', $this );
		do_action( 'acfef/styles_controls', $this );
	}

	/**
	 * Render acf ele form widget output on the frontend.
	 *
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() { 
		$settings = $this->get_settings_for_display();
		do_action( 'acfef/credit_card_form', $settings, $wg_id );
	}

	public function get_style_depends() {
		return ['acfef-frontend', 'acfef-card', 'acf-input'];
	}

	public function get_script_depends() {
		return ['acfef-card', 'acfef-credit-card', 'acfef-payments', 'acf-input'];
	}

}
