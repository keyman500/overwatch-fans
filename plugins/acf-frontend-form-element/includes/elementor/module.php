<?php

namespace ACFFrontend;

use  ACFFrontend\Plugin ;
use  Elementor\Core\Base\Module ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( !class_exists( 'ACFFrontend_Elementor' ) ) {
    class ACFFrontend_Elementor
    {
        public  $form_widgets = array() ;
        public  $submit_actions = array() ;
        public  $elementor_categories = array() ;
        public function get_name()
        {
            return 'acf_frontend_form';
        }
        
        public static function find_element_recursive( $elements, $widget_id )
        {
            foreach ( $elements as $element ) {
                if ( $widget_id == $element['id'] ) {
                    return $element;
                }
                
                if ( !empty($element['elements']) ) {
                    $element = self::find_element_recursive( $element['elements'], $widget_id );
                    if ( $element ) {
                        return $element;
                    }
                }
            
            }
            return false;
        }
        
        public function widgets()
        {
            // Include Widget files
            require_once __DIR__ . '/widgets/acf-frontend-form.php';
            require_once __DIR__ . '/widgets/acf-fields.php';
            require_once __DIR__ . '/widgets/submit_button.php';
            //require_once( __DIR__ . '/widgets/payment-form.php' );
            require_once __DIR__ . '/widgets/edit_post.php';
            require_once __DIR__ . '/widgets/duplicate_post.php';
            require_once __DIR__ . '/widgets/edit_term.php';
            require_once __DIR__ . '/widgets/edit_button.php';
            require_once __DIR__ . '/widgets/edit_user.php';
            require_once __DIR__ . '/widgets/new_post.php';
            require_once __DIR__ . '/widgets/new_term.php';
            require_once __DIR__ . '/widgets/new_user.php';
            require_once __DIR__ . '/widgets/delete_post.php';
            require_once __DIR__ . '/widgets/delete_term.php';
            require_once __DIR__ . '/widgets/delete_user.php';
            // Register widget
            $elementor = acfef_get_elementor_instance();
            $elementor->widgets_manager->register_widget_type( new Widgets\Edit_Button_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Edit_Post_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\New_Post_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Duplicate_Post_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Edit_Term_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\New_Term_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Delete_Post_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Delete_Term_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Delete_User_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Edit_User_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\New_User_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\ACF_Frontend_Form_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\ACF_Fields_Widget() );
            $elementor->widgets_manager->register_widget_type( new Widgets\Submit_Post_Widget() );
        }
        
        public function widget_categories( $elements_manager )
        {
            $categories = [
                'acfef-forms'   => [
                'title' => __( 'FRONTEND FORMS', 'acf-frontend-form-element' ),
                'icon'  => 'fa fa-plug',
            ],
                'acfef-buttons' => [
                'title' => __( 'FRONTEND BUTTONS', 'acf-frontend-form-element' ),
                'icon'  => 'fa fa-plug',
            ],
            ];
            foreach ( $categories as $name => $args ) {
                $this->elementor_categories[$name] = $args;
                $elements_manager->add_category( $name, $args );
            }
        }
        
        public function dynamic_tags( $dynamic_tags )
        {
            
            if ( class_exists( 'ElementorPro\\Modules\\DynamicTags\\Tags\\Base\\Data_Tag' ) ) {
                \Elementor\Plugin::$instance->dynamic_tags->register_group( 'acfef-user-data', [
                    'title' => 'User',
                ] );
                require_once __DIR__ . '/dynamic-tags/user-local-avatar.php';
                require_once __DIR__ . '/dynamic-tags/author-local-avatar.php';
                $dynamic_tags->register_tag( new DynamicTags\User_Local_Avatar_Tag() );
                $dynamic_tags->register_tag( new DynamicTags\Author_Local_Avatar_Tag() );
            }
        
        }
        
        public function document_types()
        {
            require_once __DIR__ . '/documents/post-form.php';
            \Elementor\Plugin::$instance->documents->register_document_type( 'post_form', Documents\PostFormTemplate::get_class_full_name() );
            //require_once( __DIR__ . '/documents/list-item.php' );
            //\Elementor\Plugin::$instance->documents->register_document_type( 'list_item', Documents\ListItemTemplate::get_class_full_name() );
        }
        
        public function icon_file()
        {
            wp_enqueue_style(
                'acfef-icon',
                ACFEF_URL . 'includes/assets/css/icon.css',
                array(),
                ACFEF_ASSETS_VERSION
            );
            wp_enqueue_style(
                'acfef-editor',
                ACFEF_URL . 'includes/assets/css/editor.min.css',
                array(),
                ACFEF_ASSETS_VERSION
            );
            
            if ( ACFEF__DEV_MODE ) {
                $min = '';
            } else {
                $min = '.min';
            }
            
            wp_enqueue_script(
                'acfef-editor',
                ACFEF_URL . 'includes/assets/js/editor' . $min . '.js',
                array( 'elementor-editor' ),
                ACFEF_ASSETS_VERSION,
                true
            );
            wp_enqueue_style( 'acf-global' );
        }
        
        public function migrate_field_controls()
        {
            if ( !get_option( 'acfef_migrated_2_5_5' ) ) {
                require_once __DIR__ . '/classes/migrate_settings.php';
            }
        }
        
        public function __construct()
        {
            require_once __DIR__ . '/classes/save_fields.php';
            require_once __DIR__ . '/classes/action_base.php';
            //actions
            require_once __DIR__ . '/actions/term.php';
            require_once __DIR__ . '/actions/user.php';
            require_once __DIR__ . '/actions/post.php';
            require_once __DIR__ . '/classes/content_tab.php';
            require_once __DIR__ . '/classes/permissions_tab.php';
            add_action( 'elementor/elements/categories_registered', array( $this, 'widget_categories' ) );
            add_action( 'elementor/widgets/widgets_registered', [ $this, 'widgets' ] );
            add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'dynamic_tags' ] );
            add_action( 'elementor/documents/register', [ $this, 'document_types' ] );
            add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'icon_file' ] );
            add_action( 'init', [ $this, 'migrate_field_controls' ] );
        }
    
    }
    acfef()->elementor = new ACFFrontend_Elementor();
}
