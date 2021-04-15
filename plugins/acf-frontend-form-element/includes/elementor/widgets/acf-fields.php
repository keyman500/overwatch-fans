<?php

namespace ACFFrontend\Widgets;

use  ACFFrontend\Plugin ;
use  ACFFrontend\Classes ;
use  Elementor\Controls_Manager ;
use  Elementor\Controls_Stack ;
use  Elementor\Widget_Base ;
use  ElementorPro\Modules\QueryControl\Module as Query_Module ;
use  ACFFrontend\Controls ;
use  Elementor\Group_Control_Typography ;
use  Elementor\Group_Control_Background ;
use  Elementor\Group_Control_Border ;
use  Elementor\Group_Control_Text_Shadow ;
use  Elementor\Group_Control_Box_Shadow ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * Elementor ACF Frontend Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class ACF_Fields_Widget extends Widget_Base
{
    public  $form_defaults ;
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
        return 'acf_form_fields';
    }
    
    /**
     * Get widget defaults.
     *
     * Retrieve acf form widget defaults.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget defaults.
     */
    public function get_form_defaults()
    {
        $all_post_types = acf_get_pretty_post_types();
        return [
            'field_type' => 'title',
            'fields'     => [ [
            'field_type'     => 'title',
            'field_label_on' => 'true',
            'field_required' => 'true',
        ], [
            'field_type'     => 'featured_image',
            'field_label_on' => 'true',
            'field_required' => 'true',
        ], [
            'field_type'              => 'post_type',
            'field_label_on'          => 'true',
            'field_required'          => 'true',
            'post_type_field_options' => array_keys( $all_post_types ),
            'default_post_type'       => array_keys( $all_post_types )[0],
        ] ],
        ];
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
        return __( 'ACF Fields', 'acf-frontend-form-element' );
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
    public function get_keywords()
    {
        return [
            'frontend editing',
            'edit post',
            'add post',
            'add user',
            'edit user',
            'edit site'
        ];
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
        return 'fa fa-wpforms frontend-icon';
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
        return [ 'post_form' ];
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
        do_action( 'acfef/form_structure_section', $this );
        do_action( 'acfef/display_section', $this );
        $this->register_style_tab_controls();
    }
    
    public function register_style_tab_controls()
    {
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
    
    public function get_field_type_groups()
    {
        $fields = [];
        $fields['acf'] = [
            'label'   => __( 'ACF Field', 'acf-frontend-form-element' ),
            'options' => [
            'ACF_fields'       => __( 'ACF Fields', 'acf-frontend-form-element' ),
            'ACF_field_groups' => __( 'ACF Field Groups', 'acf-frontend-form-element' ),
        ],
        ];
        $fields['post'] = [
            'label'   => __( 'Post', 'acf-frontend-form-element' ),
            'options' => [
            'title'          => __( 'Post Title', 'acf-frontend-form-element' ),
            'slug'           => __( 'Slug', 'acf-frontend-form-element' ),
            'content'        => __( 'Post Content', 'acf-frontend-form-element' ),
            'featured_image' => __( 'Featured Image', 'acf-frontend-form-element' ),
            'excerpt'        => __( 'Post Excerpt', 'acf-frontend-form-element' ),
            'categories'     => __( 'Categories', 'acf-frontend-form-element' ),
            'tags'           => __( 'Tags', 'acf-frontend-form-element' ),
            'author'         => __( 'Post Author', 'acf-frontend-form-element' ),
            'published_on'   => __( 'Published On', 'acf-frontend-form-element' ),
            'post_type'      => __( 'Post Type', 'acf-frontend-form-element' ),
            'menu_order'     => __( 'Menu Order', 'acf-frontend-form-element' ),
            'taxonomy'       => __( 'Custom Taxonomy', 'acf-frontend-form-element' ),
        ],
        ];
        $fields['layout'] = [
            'label'   => __( 'Layout', 'acf-frontend-form-element' ),
            'options' => [
            'message' => __( 'Message', 'acf-frontend-form-element' ),
        ],
        ];
        return $fields;
    }
    
    public function get_field_type_options()
    {
        $groups = $this->get_field_type_groups();
        $fields = [
            'acf'    => $groups['acf'],
            'layout' => $groups['layout'],
            'post'   => $groups['post'],
        ];
        return $fields;
    }
    
    public function get_form_fields( $settings, $wg_id, $form_args = array() )
    {
        $post_id = ( isset( $form_args['post_id'] ) ? $form_args['post_id'] : 0 );
        $preview_mode = \Elementor\Plugin::$instance->preview->is_preview_mode();
        $edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $groups = $this->get_field_type_groups();
        $group_names = array_keys( $groups );
        $current_step = 0;
        $form = [];
        if ( !isset( $settings['multi'] ) ) {
            $settings['multi'] = 'false';
        }
        
        if ( $settings['multi'] == 'true' ) {
            $current_step++;
            $form['steps'][$current_step] = $settings['first_step'][0];
            $form['steps'][$current_step]['fields'] = [];
        }
        
        foreach ( $settings['fields_selection'] as $key => $form_field ) {
            
            if ( $settings['multi'] == 'true' ) {
                $fields = $form['steps'][$current_step]['fields'];
            } else {
                $fields = $form;
            }
            
            $local_field = $acf_field_groups = $acf_fields = [];
            switch ( $form_field['field_type'] ) {
                case 'ACF_field_groups':
                    if ( $form_field['field_groups_select'] ) {
                        $acf_field_groups = acfef_get_acf_field_choices( $form_field['field_groups_select'] );
                    }
                    break;
                case 'ACF_fields':
                    $acf_fields = $form_field['fields_select'];
                    if ( $acf_fields ) {
                        $fields = array_merge( $fields, $acf_fields );
                    }
                    break;
                case 'step':
                    
                    if ( $settings['multi'] !== 'true' ) {
                        
                        if ( $current_step == 0 && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                            echo  '<div class="acf-notice -error acf-error-message -dismiss"><p>' . __( 'Note: You must turn on "Multi Step" for your steps to work.', 'acf-frontend-form-element' ) . '</p></div>' ;
                            $current_step++;
                        }
                    
                    } else {
                        $current_step++;
                        $form['steps'][$current_step] = $form_field;
                        $fields = [];
                    }
                    
                    break;
                case 'message':
                    $local_field = array(
                        'key'       => $wg_id . '_' . $form_field['field_type'] . $form_field['_id'],
                        'type'      => 'message',
                        'wrapper'   => [
                        'class' => 'elementor-repeater-item-' . $form_field['_id'],
                    ],
                        'required'  => 0,
                        'message'   => $form_field['field_message'],
                        'new_lines' => 'wpautop',
                        'esc_html'  => 0,
                    );
                    break;
                case 'recaptcha':
                    $local_field = array(
                        'key'          => $wg_id . '_' . $form_field['field_type'] . $form_field['_id'],
                        'type'         => 'acfef_recaptcha',
                        'wrapper'      => [
                        'class' => 'elementor-repeater-item-' . $form_field['_id'],
                    ],
                        'required'     => 0,
                        'version'      => $form_field['recaptcha_version'],
                        'v2_theme'     => $form_field['recaptcha_theme'],
                        'v2_size'      => $form_field['recaptcha_size'],
                        'site_key'     => $form_field['recaptcha_site_key'],
                        'secret_key'   => $form_field['recaptcha_secret_key'],
                        'disabled'     => 0,
                        'readonly'     => 0,
                        'v3_hide_logo' => $form_field['recaptcha_hide_logo'],
                    );
                    break;
                default:
                    $default_value = $form_field['field_default_value'];
                    
                    if ( strpos( $default_value, '[$' ) !== 'false' || strpos( $default_value, '[' ) !== 'false' ) {
                        $data_default = acfef_get_field_names( $default_value );
                        $default_value = acfef_get_code_value( $default_value, $post_id, true );
                    }
                    
                    if ( !empty($form_field['default_featured_image']['id']) ) {
                        $default_value = $form_field['default_featured_image']['id'];
                    }
                    $local_field = array(
                        'label'         => '',
                        'wrapper'       => [
                        'class' => 'elementor-repeater-item-' . $form_field['_id'],
                    ],
                        'instructions'  => $form_field['field_instruction'],
                        'required'      => ( $form_field['field_required'] ? 1 : 0 ),
                        'placeholder'   => $form_field['field_placeholder'],
                        'default_value' => $default_value,
                        'disabled'      => $form_field['field_disabled'],
                        'readonly'      => $form_field['field_readonly'],
                    );
                    
                    if ( isset( $data_default ) ) {
                        $local_field['wrapper']['data-default'] = $data_default;
                        $local_field['wrapper']['data-dynamic_value'] = $default_value;
                    }
                    
                    if ( $form_field['field_hidden'] ) {
                        $local_field['wrapper']['class'] = 'acf-hidden';
                    }
                    break;
            }
            $module = acfef()->elementor;
            
            if ( isset( $acf_field_groups ) && $acf_field_groups ) {
                $fields_exclude = $form_field['fields_select_exclude'];
                if ( $fields_exclude ) {
                    $acf_field_groups = array_diff( $acf_field_groups, $fields_exclude );
                }
                $fields = array_merge( $fields, $acf_field_groups );
            }
            
            if ( isset( $local_field ) ) {
                foreach ( $groups as $name => $group ) {
                    
                    if ( in_array( $form_field['field_type'], array_keys( $group['options'] ) ) ) {
                        $action_name = explode( '_', $name )[0];
                        if ( isset( acfef()->local_actions[$action_name] ) ) {
                            $main_action = acfef()->local_actions[$action_name];
                        }
                        break;
                    }
                
                }
            }
            
            if ( isset( $main_action ) ) {
                $local_field = $main_action->get_fields_display( $form_field, $local_field, $wg_id );
                
                if ( isset( $form_field['field_label_on'] ) ) {
                    $field_label = ucwords( str_replace( '_', ' ', $form_field['field_type'] ) );
                    $local_field['label'] = ( $form_field['field_label'] ? $form_field['field_label'] : $field_label );
                }
                
                
                if ( isset( $local_field['type'] ) ) {
                    
                    if ( $local_field['type'] == 'password' ) {
                        $local_field['password_strength'] = $form_field['password_strength'];
                        $password_strength = true;
                    }
                    
                    
                    if ( $form_field['field_type'] == 'taxonomy' ) {
                        $taxonomy = ( isset( $form_field['field_taxonomy'] ) ? $form_field['field_taxonomy'] : 'category' );
                        $local_field['name'] = $wg_id . '_' . $taxonomy;
                        $local_field['key'] = $wg_id . '_' . $taxonomy;
                    } else {
                        $local_field['name'] = $wg_id . '_' . $form_field['field_type'];
                        $local_field['key'] = $wg_id . '_' . $form_field['field_type'];
                    }
                
                }
            
            }
            
            if ( isset( $local_field['label'] ) ) {
                if ( !$form_field['field_label_on'] ) {
                    unset( $local_field['label'] );
                }
            }
            if ( isset( $form_field['button_text'] ) && $form_field['button_text'] ) {
                $local_field['button_text'] = $form_field['button_text'];
            }
            
            if ( isset( $local_field['key'] ) ) {
                $field_key = '';
                
                if ( $edit_mode || !acf_get_field( 'acfef_' . $local_field['key'] ) || $local_field['type'] == 'message' ) {
                    acf_add_local_field( $local_field );
                    $field_key = $local_field['key'];
                } else {
                    $field_key = 'acfef_' . $local_field['key'];
                }
                
                $fields[] = $field_key;
            }
            
            $form = $fields;
        }
        return $form;
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
        $wg_id = $this->get_id();
        $current_post_id = acfef_get_current_post_id();
        $settings = $this->get_settings_for_display();
        $post_id = ( isset( $_POST['form_action'] ) ? $_POST['form_action'] : $current_post_id );
        $form_fields = [];
        if ( $settings['fields_selection'] ) {
            $form_fields = $this->get_form_fields( $settings, $wg_id, [
                'post_id' => $post_id,
            ] );
        }
        $fields = [];
        
        if ( $form_fields ) {
            foreach ( $form_fields as $selector ) {
                $fields[] = acf_maybe_get_field( $selector, $post_id, false );
            }
        } else {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo  '<div class="acf-notice -error acf-error-message -dismiss"><p>' . __( 'Please choose some fields.', 'acf-frontend-form-element' ) . '</p></div>' ;
            }
        }
        
        ?>
        <div class="acf-fields acf-form-fields -<?php 
        echo  esc_attr( $settings['field_label_position'] ) ;
        ?>">
        <?php 
        acf_render_fields(
            $fields,
            $post_id,
            'div',
            $settings['field_instruction_position']
        );
        ?>
        </div>
        <?php 
    }
    
    public function __construct( $data = array(), $args = null )
    {
        parent::__construct( $data, $args );
        acf_enqueue_scripts();
        if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
            acf_enqueue_uploader();
        }
        $this->form_defaults = $this->get_form_defaults();
        acfef()->elementor->form_widgets[] = $this->get_name();
    }

}