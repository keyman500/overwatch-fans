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
class Post_Status_Widget extends Widget_Base
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
        return 'post_status';
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
        return __( 'Post Status Button', 'acf-frontend-form-element' );
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
        $who_can_see = [
            'logged_in'  => __( 'Only Logged In Users', 'acf-frontend-form-element' ),
            'logged_out' => __( 'Only Logged Out', 'acf-frontend-form-element' ),
            'all'        => __( 'All Users', 'acf-frontend-form-element' ),
        ];
        //get all user role choices
        $user_roles = acfef_get_user_roles();
        $this->start_controls_section( 'delete_button_section', [
            'label' => __( 'Trash Button', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
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
            'default'     => __( 'The post will be deleted. Are you sure?', 'acf-frontend-form-element' ),
            'placeholder' => __( 'The post will be deleted. Are you sure?', 'acf-frontend-form-element' ),
        ] );
        $this->add_control( 'force_delete', [
            'label'        => __( 'Force Delete', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'true',
            'return_value' => 'true',
            'description'  => __( 'Whether or not to completely delete the posts right away.' ),
        ] );
        $this->add_control( 'redirect_after_delete', [
            'label'         => __( 'Redirect After Delete', 'acf-frontend-form-element' ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => __( 'Enter Url Here', 'acf-frontend-form-element' ),
            'show_external' => false,
            'dynamic'       => [
            'active' => true,
        ],
        ] );
        $this->end_controls_section();
        $this->start_controls_section( 'permissions_section', [
            'label' => __( 'Permissions', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'who_can_see', [
            'label'       => __( 'Who Can See This...', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'default'     => 'logged_in',
            'options'     => $who_can_see,
        ] );
        $this->add_control( 'by_role', [
            'label'       => __( 'Select By Role', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'default'     => [ 'administrator' ],
            'options'     => $user_roles,
            'condition'   => [
            'who_can_see' => 'logged_in',
        ],
        ] );
        
        if ( !class_exists( 'ElementorPro\\Modules\\QueryControl\\Module' ) ) {
            $this->add_control( 'user_id', [
                'label'       => __( 'Select By User', 'acf-frontend-form-element' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __( '18, 12, 11', 'acf-frontend-form-element' ),
                'default'     => get_current_user_id(),
                'description' => __( 'Enter the a comma-seperated list of user ids', 'acf-frontend-form-element' ),
                'condition'   => [
                'who_can_see' => 'logged_in',
            ],
            ] );
        } else {
            $this->add_control( 'user_id', [
                'label'        => __( 'Select By User', 'acf-frontend-form-element' ),
                'label_block'  => true,
                'type'         => Query_Module::QUERY_CONTROL_ID,
                'autocomplete' => [
                'object'  => Query_Module::QUERY_OBJECT_USER,
                'display' => 'detailed',
            ],
                'multiple'     => true,
                'default'      => [ get_current_user_id() ],
                'condition'    => [
                'who_can_see' => 'logged_in',
            ],
            ] );
        }
        
        $this->add_control( 'dynamic', [
            'label'       => __( 'Dynamic Selection', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'description' => 'Use a dynamic acf user field that returns a user ID to filter the form for that user dynamically. You may also select the post\'s author',
            'options'     => acfef_get_user_id_fields(),
            'condition'   => [
            'who_can_see' => 'logged_in',
        ],
        ] );
        $this->end_controls_section();
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
        
        if ( 'all' == $settings['who_can_see'] ) {
            $display = true;
        } elseif ( 'logged_out' == $settings['who_can_see'] ) {
            $display = !is_user_logged_in();
        } else {
            
            if ( !is_user_logged_in() ) {
                $display = false;
            } else {
                $by_role = $user_id = $dynamic = false;
                if ( is_array( $settings['by_role'] ) ) {
                    
                    if ( count( array_intersect( $settings['by_role'], (array) $active_user->roles ) ) == 0 ) {
                        $by_role = false;
                    } else {
                        $by_role = true;
                    }
                
                }
                $user_ids = $settings['user_id'];
                if ( is_string( $user_ids ) ) {
                    $user_ids = explode( ',', $user_ids );
                }
                if ( is_array( $user_ids ) ) {
                    
                    if ( in_array( $active_user->ID, $user_ids ) ) {
                        $user_id = true;
                    } else {
                        $user_id = false;
                    }
                
                }
                
                if ( $settings['dynamic'] ) {
                    $current_user = '';
                    
                    if ( '[author]' == $settings['dynamic'] ) {
                        $current_user = get_the_author_meta( 'ID' );
                    } else {
                        $current_user = get_field( $settings['dynamic'], get_the_ID() );
                    }
                    
                    
                    if ( $current_user == $active_user->ID ) {
                        $dynamic = true;
                    } else {
                        $dynamic = false;
                    }
                
                }
                
                
                if ( $by_role || $user_id || $dynamic ) {
                    $display = true;
                } else {
                    $display = false;
                }
            
            }
        
        }
        
        
        if ( $display ) {
            echo  ' 
			<form action="" method="POST" >

			<input type="hidden" name="_acf_element_id" value="' . $wg_id . '">
			<input type="hidden" name="_acf_screen_id" value="' . $current_post_id . '">
			<input type="hidden" name="delete_post" value="' . $post->ID . '">

			<div class="acfef-delete-button-container">
			<button onclick="return confirm(\'' . $settings['confirm_delete_message'] . '\')" type="submit" class="button acfef-delete-button">' ;
            
            if ( $settings['delete_button_icon']['value'] ) {
                \Elementor\Icons_Manager::render_icon( $settings['delete_button_icon'] );
                echo  ' ' ;
            }
            
            echo  $settings['delete_button_text'] . '</button>
				</div>
			</form>' ;
        }
    
    }

}