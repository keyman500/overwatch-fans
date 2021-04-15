<?php
namespace ACFFrontend\Widgets;

use ACFFrontend\Widgets\ACF_Elementor_Form_Base;


	
/**
 * Elementor ACF Frontend Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class Edit_Options_Widget extends ACF_Frontend_Form_Widget {
	
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
		return 'edit_options';
	}

	/**
	* Get widget action.
	*
	* Retrieve acf ele form widget action.
	*
	* @since 1.0.0
	* @access public
	*
	* @return string Widget action.
	*/
	public function get_form_defaults() {
		return [ 
				'main_action' => 'edit_options',
				'form_title' => __( 'Edit Site', 'acf-frontend-form-element' ),
				'submit' => __( 'Update', 'acf-frontend-form-element' ),
				'success_message' => __( 'Your site info has been updated successfully.', 'acf-frontend-form-element' ),
				'field_type' => 'site_title',
				'fields' => [
					[
						'field_type' => 'site_title',
						'field_label_on' => 'true',
						'field_required' => 'true',
					],						
					[
						'field_type' => 'site_logo',
						'field_label_on' => 'true',
						'field_required' => 'true',
					],		
				],
			];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['site editing', 'edit options'];
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
		return __( 'Site Options Form', 'acf-frontend-form-element' );
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
		return 'fa fa-cogs frontend-icon';
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

}
