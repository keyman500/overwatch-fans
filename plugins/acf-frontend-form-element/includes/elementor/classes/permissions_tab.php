<?php
namespace ACFFrontend\Classes;


use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class PermissionsTab{
    
    public function register_permissions_section( $widget, $step = false ){
        $widget->start_controls_section( 'permissions_section', [
            'label' => __( 'Permissions', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
		$condition = [];
        if ( $step ) {
            $condition = [
                'field_type'                     => 'step',
                'overwrite_permissions_settings' => 'true',
            ];
        }
        $widget->add_control( 'not_allowed', [
            'label'       => __( 'No Permissions Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT,
            'label_block' => true,
            'default'     => 'show_nothing',
            'options'     => [
				'show_nothing'   => __( 'None', 'acf-frontend-form-element' ),
				'show_message'   => __( 'Message', 'acf-frontend-form-element' ),
				'custom_content' => __( 'Custom Content', 'acf-frontend-form-element' ),
			],
        ] );
        $condition['not_allowed'] = 'show_message';
        $widget->add_control( 'not_allowed_message', [
            'label'       => __( 'Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXTAREA,
            'label_block' => true,
            'rows'        => 4,
            'default'     => __( 'You do not have the proper permissions to view this form', 'acf-frontend-form-element' ),
            'placeholder' => __( 'You do not have the proper permissions to view this form', 'acf-frontend-form-element' ),
            'condition'   => $condition,
        ] );
        $condition['not_allowed'] = 'custom_content';
        $widget->add_control( 'not_allowed_content', [
            'label'       => __( 'Content', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::WYSIWYG,
            'label_block' => true,
            'render_type' => 'none',
            'condition'   => $condition,
        ] );
        unset( $condition['not_allowed'] );
        $who_can_see = [
            'logged_in'  => __( 'Only Logged In Users', 'acf-frontend-form-element' ),
            'logged_out' => __( 'Only Logged Out', 'acf-frontend-form-element' ),
            'all'        => __( 'All Users', 'acf-frontend-form-element' ),
        ];
        //get all user role choices
        $user_roles = acfef_get_user_roles( array(), true );
        $widget->add_control( 'who_can_see', [
            'label'       => __( 'Who Can See This...', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'default'     => 'logged_in',
            'options'     => $who_can_see,
            'condition'   => $condition,
        ] );
        $condition['who_can_see'] = 'logged_in';
        $widget->add_control( 'by_role', [
            'label'       => __( 'Select By Role', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'default'     => ['administrator'],
            'options'     => $user_roles,
            'condition'   => $condition,
        ] );
        
        if ( !class_exists( 'ElementorPro\\Modules\\QueryControl\\Module' ) ) {
            $widget->add_control( 'user_id', [
                'label'       => __( 'Select By User', 'acf-frontend-form-element' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __( '18, 12, 11', 'acf-frontend-form-element' ),
                'default'     => get_current_user_id(),
                'description' => __( 'Enter the a comma-seperated list of user ids', 'acf-frontend-form-element' ),
                'condition'   => $condition,
            ] );
        } else {
            $widget->add_control( 'user_id', [
                'label'        => __( 'Select By User', 'acf-frontend-form-element' ),
                'label_block'  => true,
                'type'         => Query_Module::QUERY_CONTROL_ID,
                'autocomplete' => [
                'object'  => Query_Module::QUERY_OBJECT_USER,
                'display' => 'detailed',
            ],
                'multiple'     => true,
                'default'      => [ get_current_user_id() ],
                'condition'    => $condition,
            ] );
        }
        
        $condition['main_action'] = ['edit_post','duplicate_post','delete_post'];
        $widget->add_control( 'dynamic', [
            'label'       => __( 'Dynamic Permissions', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'description' => 'Use a dynamic acf user field that returns a user ID to filter the form for that user dynamically. You may also select the post\'s author',
            'options'     => acfef_get_user_id_fields(),
            'condition'   => $condition,
        ] );
        $condition['main_action'] = 'edit_user';
        $widget->add_control( 'dynamic_manager', [
            'label'       => __( 'Dynamic Permissions', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'options'     => [
            'manager' => __( 'User Manager', 'acf-frontend-form-element' ),
        ],
            'condition'   => $condition,
        ] );
        $widget->add_control( 'wp_uploader', [
            'label'        => __( 'WP Media Library', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'description'  => 'Whether to use the WordPress media library for file fields or just a basic upload button',
            'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
            'label_off'    => __( 'No', 'acf-frontend-form-element' ),
            'default'      => 'true',
            'return_value' => 'true',
        ] );
        $widget->add_control( 'media_privacy_note', [
            'label'           => __( '<h3>Media Privacy</h3>', 'acf-frontend-form-element' ),
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => __( '<p>Click <a target="_blank" href="' . admin_url( '?page=acfef-settings&tab=uploads-privacy' ) . '">here</a> </p> to limit the files displayed in the media library to the user who uploaded them', 'acf-frontend-form-element' ),
            'content_classes' => 'media-privacy-note',
        ] );
        $widget->end_controls_section();
    }

	public function show_widget( $wg_id, $settings, $form_args = array() ){
        if ( empty($wg_id) ) {
            return true;
        }
        global $post ;
        $active_user = wp_get_current_user();
        $display = false;
        if ( 'all' == $settings['who_can_see'] ) {
            return true;
        }
        if ( 'logged_out' == $settings['who_can_see'] ) {
            return !is_user_logged_in();
        }
        if ( 'logged_in' == $settings['who_can_see'] ) {            
            if ( !is_user_logged_in() ) {
                return false;
            } else {
                $by_role = $specific_user = $dynamic = false;
                $user_roles = $settings['by_role'];
                
                if ( $user_roles ) {
                    if ( is_array( $settings['by_role'] ) ) {
                        if ( count( array_intersect( $settings['by_role'], (array) $active_user->roles ) ) != false || in_array( 'all', $settings['by_role'] ) ) {
                            $by_role = true;
                        }
                    }
                } 
                
                $user_ids = $settings['user_id'];
                
                if ( $user_ids ) {
                    if ( !is_array( $user_ids ) ) {
                        $user_ids = explode( ',', $user_ids );
                    }
                    if ( is_array( $user_ids ) ) {
                        if ( in_array( $active_user->ID, $user_ids ) ) {
                            $specific_user = true;
                        }
                    }
                } 

                if( isset( $settings['dynamic'] ) ) {
                   $post_id = '';
                    if( $settings['post_to_edit'] == 'select_post' ){
                        $post_id = $settings['post_select'];
                    }elseif( $settings['post_to_edit'] == 'url_query' && isset( $_GET[ $settings['url_query_post'] ] ) ){
                        $post_id = $_GET[ $settings['url_query_post'] ];
                    }
                    
                    if( $settings['dynamic'] ) {                        
                        if( '[author]' == $settings['dynamic'] ) {
                            $author_id = get_post_field( 'post_author', $post_id );
                        }else{
                            $author_id = get_post_meta( $post_id, $settings['dynamic'], true );
                        }
                        
                        if( $author_id == $active_user->ID ) {
                            $dynamic = true;
                        }
                    }                
                }
                if( isset( $settings['dynamic_manager'] ) ){
                    if( $settings['user_to_edit'] == 'current_user' ){
                        $user_id = $active_user->ID; 
                    }elseif( $settings['user_to_edit'] == 'select_user' ){
                        $user_id = $settings['user_select'];
                    }elseif( $settings['user_to_edit'] == 'url_query' && isset( $_GET[ $settings['url_query_user'] ] ) ){
                        $user_id = $_GET[ $settings['url_query_user'] ];
                    }
        
                    if( $settings['dynamic_manager'] && isset( $user_id[1] ) ){
                        $manager_id = false;
                        
                        if( 'manager' == $settings['dynamic_manager'] ) {
                            $manager_id = get_user_meta( $user_id, 'acfef_manager', true );
                        }else{
                            $manager_id = get_user_meta( $user_id, $settings['dynamic_manager'], true );
                        }
                        
                        if( $manager_id == $active_user->ID ) {
                            $dynamic = true;
                        }
                    }
                }
                                
                if ( $by_role || $specific_user || $dynamic ) {
                    $display = true;
                } else {
                    $display = false;
                }
            
            }
        
        }
        return $display;
    }
    
	public function __construct() {
		add_action( 'acfef/permissions_section', [ $this, 'register_permissions_section'] );
		add_action( 'acfef/show_widget', [ $this, 'show_widget'], 10, 3 );		
	}

}

new PermissionsTab();
