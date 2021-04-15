<?php

namespace ACFFrontend\Widgets;

use  ACFFrontend\Plugin ;
use  ACFFrontend\Classes ;
use  Elementor\Controls_Manager ;
use  Elementor\Widget_Base ;
use  ElementorPro\Modules\QueryControl\Module as Query_Module ;
/**
 * Elementor ACF Frontend Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class Delete_Term_Widget extends Widget_Base
{
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
    public function get_name()
    {
        return 'delete_term';
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
    public function get_title()
    {
        return __( 'Delete Term', 'acf-frontend-form-element' );
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
    public function get_icon()
    {
        return 'fas fa-trash-alt frontend-icon';
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
    public function get_categories()
    {
        return [ 'acfef-buttons' ];
    }
    
    /**
     * Register acf ele form widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->start_controls_section( 'delete_button_section', [
            'label' => __( 'Trash Button', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'main_action', [
            'type'    => Controls_Manager::HIDDEN,
            'default' => 'delete_term',
        ] );
        $this->add_control( 'delete_button_text', [
            'label'       => __( 'Delete Button Text', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Delete', 'acf-frontend-form-element' ),
            'placeholder' => __( 'Delete', 'acf-frontend-form-element' ),
        ] );
        $this->add_control( 'delete_button_icon', [
            'label' => __( 'Delete Button Icon', 'acf-frontend-form-element' ),
            'type'  => Controls_Manager::ICONS,
        ] );
        $this->add_control( 'confirm_delete_message', [
            'label'       => __( 'Confirm Delete Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'The term will be deleted. Are you sure?', 'acf-frontend-form-element' ),
            'placeholder' => __( 'The term will be deleted. Are you sure?', 'acf-frontend-form-element' ),
        ] );
        $this->add_control( 'show_delete_message', [
            'label'        => __( 'Show Success Message', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
            'label_off'    => __( 'No', 'acf-frontend-form-element' ),
            'default'      => 'true',
            'return_value' => 'true',
        ] );
        $this->add_control( 'delete_message', [
            'label'       => __( 'Success Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => __( 'You have deleted this term', 'acf-frontend-form-element' ),
            'placeholder' => __( 'You have deleted this term', 'acf-frontend-form-element' ),
            'dynamic'     => [
            'active'    => true,
            'condition' => [
            'show_delete_message' => 'true',
        ],
        ],
        ] );
        $this->add_control( 'delete_redirect', [
            'label'   => __( 'Redirect After Delete', 'acf-frontend-form-element' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'custom_url',
            'options' => [
            'current'     => __( 'Reload Current Url', 'acf-frontend-form-element' ),
            'custom_url'  => __( 'Custom Url', 'acf-frontend-form-element' ),
            'referer_url' => __( 'Referer', 'acf-frontend-form-element' ),
        ],
        ] );
        $this->add_control( 'redirect_after_delete', [
            'label'         => __( 'Custom URL', 'acf-frontend-form-element' ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => __( 'Enter Url Here', 'acf-frontend-form-element' ),
            'show_external' => false,
            'dynamic'       => [
            'active' => true,
        ],
            'condition'     => [
            'delete_redirect' => 'custom_url',
        ],
        ] );
        $this->end_controls_section();
        $this->start_controls_section( 'term_section', [
            'label' => __( 'Term', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        acfef()->local_actions['term']->action_controls( $this );
        $this->end_controls_section();
        do_action( 'acfef/permissions_section', $this );
        $this->start_controls_section( 'style_promo_section', [
            'label' => __( 'Styles', 'acf-frontend-form-elements' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );
        $this->add_control( 'styles_promo', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go Pro</b></a> to unlock styles.</p>', 'acf-frontend-form-element' ),
            'content_classes' => 'acf-fields-note',
        ] );
        $this->end_controls_section();
    }
    
    /**
     * Render acf ele form widget output on the frontend.
     *
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $display = false;
        $wg_id = $this->get_id();
        $current_post_id = acfef_get_current_post_id();
        global  $post ;
        $active_user = wp_get_current_user();
        $settings = $this->get_settings_for_display();
        
        if ( $settings['term_to_edit'] == 'select_term' ) {
            $term_id = $settings['term_select'];
        } elseif ( $settings['term_to_edit'] == 'url_query' && isset( $_GET[$settings['url_query_term']] ) ) {
            $term_id = $_GET[$settings['url_query_term']];
            $settings['url_query'] = $settings['url_query_term'];
        } else {
            $term_obj = get_queried_object();
            $term_id = $term_obj->term_id;
        }
        
        if ( !isset( $term_obj ) ) {
            $term_obj = get_term( $term_id );
        }
        $hidden_fields = [
            'screen_id'     => $current_post_id,
            'element_id'    => $wg_id,
            'taxonomy_type' => $term_obj->taxonomy,
        ];
        $btn_args = array(
            'term_id'        => $term_id,
            'hidden_fields'  => $hidden_fields,
            'kses'           => true,
            'delete_message' => $settings['confirm_delete_message'],
            'delete_icon'    => $settings['delete_button_icon'],
            'delete_text'    => $settings['delete_button_text'],
        );
        $delete_redirect = $settings['delete_redirect'];
        $delete_redirect_url = $settings['redirect_after_delete']['url'];
        
        if ( $delete_redirect ) {
            switch ( $delete_redirect ) {
                case 'custom_url':
                    $delete_redirect = $delete_redirect_url;
                    break;
                case 'current':
                    global  $wp ;
                    $delete_redirect = home_url( $wp->request );
                    break;
                case 'referer_url':
                    $delete_redirect = home_url( add_query_arg( NULL, NULL ) );
                    if ( wp_get_referer() ) {
                        $delete_redirect = wp_get_referer();
                    }
                    break;
            }
            $btn_args['redirect'] = $delete_redirect;
        }
        
        $display = apply_filters(
            'acfef/show_widget',
            $wg_id,
            $settings,
            $btn_args
        );
        if ( $display ) {
            acfef_delete_button( $btn_args );
        }
    }

}