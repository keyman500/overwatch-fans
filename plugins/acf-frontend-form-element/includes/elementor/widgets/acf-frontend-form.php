<?php

namespace ACFFrontend\Widgets;

use  ACFFrontend\Plugin ;
use  ACFFrontend\ACFEF_Module ;
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
class ACF_Frontend_Form_Widget extends Widget_Base
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
        return 'acf_ele_form';
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
        return [
            'main_action'     => 'all',
            'form_title'      => __( 'Edit Post', 'acf-frontend-form-element' ),
            'submit'          => __( 'Update', 'acf-frontend-form-element' ),
            'success_message' => __( 'Your post has been updated successfully.', 'acf-frontend-form-element' ),
            'field_type'      => 'title',
            'fields'          => [ [
            'field_type'     => 'title',
            'field_label_on' => 'true',
            'field_required' => 'true',
        ], [
            'field_type'     => 'featured_image',
            'field_label_on' => 'true',
            'field_required' => 'true',
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
        return __( 'ACF Frontend Form', 'acf-frontend-form-element' );
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
        return [ 'acfef-forms' ];
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
        $this->register_form_structure_controls();
        $this->register_steps_controls();
        $this->register_actions_controls();
        $this->main_action_controls_section();
        do_action( 'acfef/permissions_section', $this );
        do_action( 'acfef/display_section', $this );
        $this->register_limit_controls();
        $this->register_shortcodes_section();
        $this->register_style_tab_controls();
        
        if ( get_option( 'acfef_payments_active' ) && (get_option( 'acfef_stripe_active' ) || get_option( 'acfef_paypal_active' )) ) {
            do_action( 'acfef/content_controls', $this );
            do_action( 'acfef/styles_controls', $this );
        }
    
    }
    
    protected function register_form_structure_controls()
    {
        //get all field group choices
        $field_group_choices = acfef_get_acf_field_group_choices();
        $field_choices = acfef_get_acf_field_choices();
        $this->start_controls_section( 'fields_section', [
            'label' => __( 'Form Fields', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'form_title', [
            'label'       => __( 'Form Title', 'acf-frontend-form-element' ),
            'label_block' => true,
            'type'        => Controls_Manager::TEXT,
            'default'     => $this->form_defaults['form_title'],
            'placeholder' => $this->form_defaults['form_title'],
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $this->add_control( 'form_id', [
            'label'       => __( 'Form ID', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => sanitize_title( $this->form_defaults['form_title'] ),
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        do_action( 'acfef/fields_controls', $this );
        $this->add_control( 'submit_button_text', [
            'label'       => __( 'Submit Button Text', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'default'     => $this->form_defaults['submit'],
            'placeholder' => $this->form_defaults['submit'],
            'condition'   => [
            'multi!' => 'true',
        ],
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $this->add_control( 'submit_button_desc', [
            'label'       => __( 'Submit Button Description', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => __( 'All done?', 'acf-frontend-form-element' ),
            'condition'   => [
            'multi!' => 'true',
        ],
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $this->end_controls_section();
        do_action( 'acfef/sub_fields_controls', $this );
    }
    
    public function register_steps_controls()
    {
    }
    
    protected function register_actions_controls()
    {
        $this->start_controls_section( 'actions_section', [
            'label' => __( 'Actions', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->main_action_control();
        $this->add_control( 'more_actions_promo', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go pro</b></a> to unlock more actions.</p>', 'acf-frontend-form-element' ),
            'content_classes' => 'acf-fields-note',
        ] );
        $this->add_control( 'redirect', [
            'label'   => __( 'Redirect After Submit', 'acf-frontend-form-element' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'current',
            'options' => [
            'current'     => __( 'Stay on Current Page/Post', 'acf-frontend-form-element' ),
            'custom_url'  => __( 'Custom Url', 'acf-frontend-form-element' ),
            'referer_url' => __( 'Referer', 'acf-frontend-form-element' ),
            'post_url'    => __( 'Post Url', 'acf-frontend-form-element' ),
        ],
        ] );
        $this->add_control( 'open_modal', [
            'label'        => __( 'Leave Modal Open After Submit', 'acf-frontend-form-element' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'true',
            'condition'    => [
            'show_in_modal' => 'true',
        ],
        ] );
        $this->add_control( 'redirect_action', [
            'label'     => __( 'After Reload', 'acf-frontend-form-element' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'clear',
            'options'   => [
            'clear' => __( 'Clear Form', 'acf-frontend-form-element' ),
            'edit'  => __( 'Edit Form', 'acf-frontend-form-element' ),
        ],
            'condition' => [
            'redirect'    => 'current',
            'main_action' => [ 'new_post', 'new_user', 'new_product' ],
        ],
        ] );
        $this->add_control( 'custom_url', [
            'label'       => __( 'Custom Url', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::URL,
            'placeholder' => __( 'Enter Url Here', 'acf-frontend-form-element' ),
            'options'     => false,
            'show_label'  => false,
            'condition'   => [
            'redirect' => 'custom_url',
        ],
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'param_key', [
            'label'       => __( 'Key', 'acf-frontend-form-element' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => __( 'page_id', 'acf-frontend-form-element' ),
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $repeater->add_control( 'param_value', [
            'label'       => __( 'Value', 'acf-frontend-form-element' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => __( '18', 'acf-frontend-form-element' ),
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $this->add_control( 'url_parameters', [
            'label'         => __( 'URL Parameters', 'acf-frontend-form-element' ),
            'type'          => Controls_Manager::REPEATER,
            'fields'        => $repeater->get_controls(),
            'prevent_empty' => false,
            'title_field'   => '{{{ param_key }}}',
        ] );
        $this->add_control( 'preview_redirect', [
            'label'        => __( 'Preview Redirect URL', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'description'  => 'View the redirect URL structure to confirm all is set.',
            'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
            'label_off'    => __( 'No', 'acf-frontend-form-element' ),
            'return_value' => 'true',
            'seperator'    => 'after',
        ] );
        $this->add_control( 'show_success_message', [
            'label'        => __( 'Show Success Message', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
            'label_off'    => __( 'No', 'acf-frontend-form-element' ),
            'default'      => 'true',
            'return_value' => 'true',
        ] );
        $this->add_control( 'update_message', [
            'label'       => __( 'Submit Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => $this->form_defaults['success_message'],
            'placeholder' => $this->form_defaults['success_message'],
            'dynamic'     => [
            'active'    => true,
            'condition' => [
            'show_success_message' => 'true',
        ],
        ],
        ] );
        $this->end_controls_section();
    }
    
    protected function main_action_controls_section()
    {
        $module = acfef()->elementor;
        $local_actions = acfef()->local_actions;
        foreach ( $local_actions as $name => $action ) {
            if ( strpos( $this->form_defaults['main_action'], $name ) !== false || $this->form_defaults['main_action'] == 'all' ) {
                $action->register_settings_section( $this );
            }
        }
    }
    
    protected function register_display_controls()
    {
        $this->start_controls_section( 'display_section', [
            'label' => __( 'Display Options', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'hide_field_labels', [
            'label'        => __( 'Hide Field Labels', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Hide', 'acf-frontend-form-element' ),
            'label_off'    => __( 'Show', 'acf-frontend-form-element' ),
            'return_value' => 'true',
            'separator'    => 'before',
            'selectors'    => [
            '{{WRAPPER}} .acf-label' => 'display: none',
        ],
        ] );
        $this->add_control( 'field_label_position', [
            'label'     => __( 'Label Position', 'elementor-pro' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'top'  => __( 'Above', 'elementor-pro' ),
            'left' => __( 'Inline', 'elementor-pro' ),
        ],
            'default'   => 'top',
            'condition' => [
            'hide_field_labels!' => 'true',
        ],
        ] );
        $this->add_control( 'hide_mark_required', [
            'label'        => __( 'Hide Required Mark', 'elementor-pro' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Hide', 'elementor-pro' ),
            'label_off'    => __( 'Show', 'elementor-pro' ),
            'return_value' => 'true',
            'condition'    => [
            'hide_field_labels!' => 'true',
        ],
            'selectors'    => [
            '{{WRAPPER}} .acf-required' => 'display: none',
        ],
        ] );
        $this->add_control( 'field_instruction_position', [
            'label'     => __( 'Instruction Position', 'elementor-pro' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'label' => __( 'Above Field', 'elementor-pro' ),
            'field' => __( 'Below Field', 'elementor-pro' ),
        ],
            'default'   => 'label',
            'separator' => 'before',
        ] );
        $this->add_control( 'field_seperator', [
            'label'        => __( 'Field Seperator', 'elementor-pro' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Hide', 'elementor-pro' ),
            'label_off'    => __( 'Show', 'elementor-pro' ),
            'default'      => 'true',
            'return_value' => 'true',
            'separator'    => 'before',
            'selectors'    => [
            '{{WRAPPER}} .acf-fields>.acf-field'                        => 'border-top: none',
            '{{WRAPPER}} .acf-field[data-width]+.acf-field[data-width]' => 'border-left: none',
        ],
        ] );
        $this->end_controls_section();
    }
    
    public function register_limit_controls()
    {
        $this->start_controls_section( 'limit_submit_section', [
            'label' => __( 'Limit Submissions', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'limit_submit_promo', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go pro</b></a> to unlock limit submissions.</p>', 'acf-frontend-form-element' ),
            'content_classes' => 'acf-fields-note',
        ] );
        do_action( 'acfef/limit_submit_settings', $this );
        $this->end_controls_section();
    }
    
    public function register_shortcodes_section()
    {
        $this->start_controls_section( 'shortcodes_section', [
            'label' => __( 'Shortcodes for Dynamic Values', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'custom_field_shortcode', [
            'label'       => __( 'ACF Text Field', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[acf:field_name]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'image_field_shortcode', [
            'label'       => __( 'ACF Image Field', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[acf:field_name:image]" readonly /><br><input class="elementor-form-field-shortcode" value="[acf:field_name:image_link]" readonly /><br><input class="elementor-form-field-shortcode" value="[acf:field_name:image_id]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_title_shortcode', [
            'label'       => __( 'Post Title', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:title]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_id_shortcode', [
            'label'       => __( 'Post ID', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:id]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_content_shortcode', [
            'label'       => __( 'Post Content', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:content]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_excerpt_shortcode', [
            'label'       => __( 'Post Excerpt', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:excerpt]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'featured_image_shortcode', [
            'label'       => __( 'Featured Image', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:featured_image]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_url_shortcode', [
            'label'       => __( 'Post URL', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:url]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'username_shortcode', [
            'label'       => __( 'Username', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:username]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_email_shortcode', [
            'label'       => __( 'User Email', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:email]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_first_shortcode', [
            'label'       => __( 'User First Name', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:first_name]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_last_shortcode', [
            'label'       => __( 'User Last Name', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:last_name]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_role_shortcode', [
            'label'       => __( 'User Role', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:role]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_bio_shortcode', [
            'label'       => __( 'User Bio', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:bio]" readonly />',
            'separator'   => 'after',
        ] );
        $this->end_controls_section();
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
        $fields['user'] = [
            'label'   => __( 'User', 'acf-frontend-form-element' ),
            'options' => [
            'username'         => __( 'Username', 'acf-frontend-form-element' ),
            'password'         => __( 'Password', 'acf-frontend-form-element' ),
            'confirm_password' => __( 'Confirm Password', 'acf-frontend-form-element' ),
            'email'            => __( 'Email', 'acf-frontend-form-element' ),
            'first_name'       => __( 'First Name', 'acf-frontend-form-element' ),
            'last_name'        => __( 'Last Name', 'acf-frontend-form-element' ),
            'nickname'         => __( 'Nickname', 'acf-frontend-form-element' ),
            'display_name'     => __( 'Display Name', 'acf-frontend-form-element' ),
            'bio'              => __( 'Biography', 'acf-frontend-form-element' ),
            'role'             => __( 'Role', 'acf-frontend-form-element' ),
        ],
        ];
        $fields['term'] = [
            'label'   => __( 'Term', 'acf-frontend-form-element' ),
            'options' => [
            'term_name' => __( 'Term Name', 'acf-frontend-form-element' ),
        ],
        ];
        $fields['layout'] = [
            'label'   => __( 'Layout', 'acf-frontend-form-element' ),
            'options' => [
            'message' => __( 'Message', 'acf-frontend-form-element' ),
            'column'  => __( 'Column', 'acf-frontend-form-element' ),
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
        ];
        switch ( $this->form_defaults['main_action'] ) {
            case 'new_post':
            case 'edit_post':
                $fields['post'] = $groups['post'];
                break;
            case 'edit_user':
            case 'new_user':
                $fields['user'] = $groups['user'];
                break;
            case 'edit_options':
                $fields['site'] = $groups['options'];
                break;
            case 'edit_term':
            case 'new_term':
                $fields['term'] = $groups['term'];
                break;
            case 'new_comment':
                $fields['comment'] = $groups['comment'];
                break;
            case 'new_product':
            case 'edit_product':
                $fields = array_merge( $fields, [
                    'product'   => $groups['product'],
                    'inventory' => $groups['product_inventory'],
                ] );
                break;
            default:
                $fields = array_merge( $fields, [
                    'post' => $groups['post'],
                    'user' => $groups['user'],
                    'term' => $groups['term'],
                ] );
        }
        return $fields;
    }
    
    public function main_action_control( $repeater = false )
    {
        $controls = $this;
        $continue_action = [];
        $controls_settings = [
            'label'   => __( 'Main Action', 'acf-frontend-form-element' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'edit_post',
        ];
        
        if ( $repeater ) {
            $controls = $repeater;
            $controls_settings['condition'] = [
                'field_type'         => 'step',
                'overwrite_settings' => 'true',
            ];
        }
        
        
        if ( $this->form_defaults['main_action'] == 'all' ) {
            $main_action_options = array(
                'edit_post' => __( 'Edit Post', 'acf-frontend-form-element' ),
                'new_post'  => __( 'New Post', 'acf-frontend-form-element' ),
                'edit_user' => __( 'Edit User', 'acf-frontend-form-element' ),
                'new_user'  => __( 'New User', 'acf-frontend-form-element' ),
                'edit_term' => __( 'Edit Term', 'acf-frontend-form-element' ),
                'new_term'  => __( 'New Term', 'acf-frontend-form-element' ),
            );
            $main_action_options = apply_filters( 'acfef/main_actions', $main_action_options );
            $controls_settings['options'] = $main_action_options;
            $controls->add_control( 'main_action', $controls_settings );
        } else {
            $controls->add_control( 'main_action', [
                'type'    => Controls_Manager::HIDDEN,
                'default' => $this->form_defaults['main_action'],
            ] );
        }
    
    }
    
    public function get_form_fields( $settings, $wg_id, $form_args = array() )
    {
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
            $field_keys = [];
            
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
                        $field_keys = array_merge( $field_keys, $acf_fields );
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
                case 'column':
                    
                    if ( $form_field['endpoint'] == 'true' ) {
                        $fields[] = [
                            'column' => 'endpoint',
                        ];
                    } else {
                        $column = [
                            'column' => $form_field['_id'],
                        ];
                        if ( $form_field['nested'] ) {
                            $column['nested'] = true;
                        }
                        $fields[] = $column;
                    }
                    
                    break;
                case 'tab':
                    
                    if ( $form_field['endpoint'] == 'true' ) {
                        $fields[] = [
                            'tab' => 'endpoint',
                        ];
                    } else {
                        $tab = [
                            'tab' => $form_field['_id'],
                        ];
                        $fields[] = $tab;
                    }
                    
                    break;
                case 'message':
                    $fields[] = [
                        'render_content' => $form_field['field_message'],
                    ];
                    break;
                case 'recaptcha':
                    $local_field = array(
                        'key'          => $wg_id . '_' . $form_field['field_type'] . '_' . $form_field['_id'],
                        'type'         => 'acfef_recaptcha',
                        'wrapper'      => [
                        'class' => '',
                        'id'    => '',
                        'width' => '',
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
                    
                    if ( strpos( $default_value, '[' ) !== false ) {
                        $data_default = acfef_get_field_names( $default_value );
                        $default_value = acfef_get_dynamic_preview( $default_value );
                    }
                    
                    $local_field = array(
                        'label'         => '',
                        'wrapper'       => [
                        'class' => '',
                        'id'    => '',
                        'width' => '',
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
            
            if ( $acf_field_groups ) {
                $fields_exclude = $form_field['fields_select_exclude'];
                
                if ( $fields_exclude ) {
                    $acf_fields = array_diff( $acf_field_groups, $fields_exclude );
                } else {
                    $acf_fields = $acf_field_groups;
                }
                
                $field_keys = array_merge( $field_keys, $acf_fields );
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
            $sub_fields = false;
            if ( $form_field['field_type'] == 'attributes' ) {
                $sub_fields = $settings['attribute_fields'];
            }
            
            if ( isset( $main_action ) ) {
                $local_field = $main_action->get_fields_display(
                    $form_field,
                    $local_field,
                    $wg_id,
                    $sub_fields
                );
                
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
                
                if ( !empty($form_field['default_terms']) ) {
                    $local_field['default_terms'] = $form_field['default_terms'];
                }
            }
            
            if ( isset( $local_field['label'] ) ) {
                
                if ( !$form_field['field_label_on'] ) {
                    $local_field['field_label_hide'] = 1;
                } else {
                    $local_field['field_label_hide'] = 0;
                }
            
            }
            if ( isset( $form_field['button_text'] ) && $form_field['button_text'] ) {
                $local_field['button_text'] = $form_field['button_text'];
            }
            
            if ( isset( $local_field['key'] ) ) {
                $field_key = '';
                
                if ( $edit_mode || !acf_get_field( 'acfef_' . $local_field['key'] ) ) {
                    acf_add_local_field( $local_field );
                    $field_key = $local_field['key'];
                } else {
                    $field_key = 'acfef_' . $local_field['key'];
                }
                
                $field_keys[] = $field_key;
            }
            
            if ( $field_keys ) {
                foreach ( $field_keys as $acf_key ) {
                    $field_data = [
                        'acf'       => $acf_key,
                        'elementor' => $form_field['_id'],
                        'form'      => $wg_id,
                    ];
                    $fields[] = $field_data;
                }
            }
            
            if ( $settings['multi'] == 'true' ) {
                $form['steps'][$current_step]['fields'] = $fields;
            } else {
                $form = $fields;
            }
            
            if ( isset( $password_strength ) ) {
                $form['password_strength'] = true;
            }
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
        global  $post ;
        $current_post_id = acfef_get_current_post_id();
        $settings = $this->get_settings_for_display();
        $defaults = $new_post = $show_title = $show_content = $show_form = $display = $message = $fields = $fields_exclude = false;
        $hidden_submit = $disabled_submit = '';
        $module = acfef()->elementor;
        $hidden_fields = [
            'screen_id'   => $current_post_id,
            'main_action' => $settings['main_action'],
        ];
        $form_attributes = [
            'class' => 'acfef-form',
        ];
        
        if ( isset( $_POST['field_key'] ) ) {
            $args = wp_parse_args( $_POST, array(
                'field_key'   => '',
                'parent_form' => '',
            ) );
            $hidden_fields['field_id'] = $form_attributes['data-field'] = $args['field_key'];
            $ajax_submit = true;
        } else {
            $hidden_fields['element_id'] = $form_attributes['data-widget'] = $wg_id;
        }
        
        if ( $settings['show_in_modal'] && $settings['open_modal'] ) {
            $hidden_fields['modal'] = 1;
        }
        $form_id = 'acf-form-' . $wg_id . get_the_ID();
        if ( $settings['form_id'] ) {
            $form_id = sanitize_title( $settings['form_id'] );
        }
        $form_args = array(
            'id'                    => $form_id,
            'post_title'            => $show_title,
            'form_attributes'       => $form_attributes,
            'post_content'          => $show_content,
            'submit_value'          => $settings['submit_button_text'],
            'hidden_fields'         => $hidden_fields,
            'instruction_placement' => $settings['field_instruction_position'],
            'html_submit_spinner'   => '',
            'update_message'        => $settings['update_message'],
            'label_placement'       => 'top',
            'field_el'              => 'div',
            'kses'                  => true,
            'html_after_fields'     => '',
            'redirect_action'       => $settings['redirect_action'],
        );
        $settings_to_pass = [
            'form_title',
            'new_post_type',
            'new_post_status',
            'saved_draft_message',
            'new_post_terms',
            'new_terms_select',
            'post_to_edit',
            'url_query_post',
            'post_select',
            'new_product_status',
            'new_product_terms',
            'new_product_terms_select',
            'product_to_edit',
            'product_select',
            'url_query_product',
            'new_term_taxonomy',
            'url_query_term',
            'term_to_edit',
            'term_select',
            'user_to_edit',
            'url_query_user',
            'username_prefix',
            'username_suffix',
            'new_user_role',
            'hide_admin_bar',
            'username_default',
            'login_user',
            'steps_tabs_display',
            'steps_counter_display',
            'counter_prefix',
            'counter_suffix',
            'steps_display',
            'tab_links',
            'step_number',
            'dynamic',
            'dynamic_manager',
            'display_name_default',
            'pay_for_submission',
            'payment_processor',
            'payment_button_text',
            'show_total',
            'payment_plans',
            'before_total',
            'after_total',
            'credit_card_fields'
        ];
        foreach ( $settings_to_pass as $setting ) {
            if ( isset( $settings[$setting] ) ) {
                $form_args[$setting] = $settings[$setting];
            }
        }
        $delete_redirect = '';
        
        if ( 'edit_post' == $settings['main_action'] && $settings['show_delete_button'] ) {
            $form_args['show_delete_button'] = true;
            $form_args['delete_message'] = $settings['confirm_delete_message'];
            $form_args['delete_icon'] = $settings['delete_button_icon'];
            $form_args['delete_text'] = $settings['delete_button_text'];
            $form_args['force_delete'] = $settings['force_delete'];
            $delete_redirect = $settings['delete_redirect'];
            if ( isset( $settings['redirect_after_delete']['url'] ) ) {
                $delete_redirect_url = $settings['redirect_after_delete']['url'];
            }
        }
        
        
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
            $form_args['redirect'] = $delete_redirect;
        }
        
        if ( isset( $settings['saved_drafts'] ) && $settings['saved_drafts'] && $settings['main_action'] == 'new_post' ) {
            $form_args['saved_drafts'] = [
                'saved_drafts_label' => $settings['saved_drafts_label'],
                'saved_drafts_new'   => $settings['saved_drafts_new'],
            ];
        }
        if ( isset( $settings['saved_revisions'] ) && $settings['saved_revisions'] && $settings['main_action'] == 'edit_post' ) {
            $form_args['saved_revisions'] = [
                'saved_revisions_label'     => $settings['saved_revisions_label'],
                'saved_revisions_edit_main' => $settings['saved_revisions_edit_main'],
            ];
        }
        if ( isset( $settings['save_progress_button'] ) && $settings['save_progress_button'] ) {
            $form_args['save_progress'] = [
                'desc' => $settings['saved_draft_desc'],
                'text' => $settings['saved_draft_text'],
            ];
        }
        
        if ( $settings['wp_uploader'] ) {
            $form_args['uploader'] = 'wp';
        } else {
            $form_args['uploader'] = 'basic';
        }
        
        
        if ( isset( $_GET['updated'] ) && $_GET['updated'] !== 'true' ) {
            $form_args['show_update_message'] = true;
            
            if ( isset( $_GET['step'] ) ) {
                $form_args['show_update_message'] = false;
            } else {
                $form_id = explode( '_', $_GET['updated'] );
                if ( $form_id[0] != $wg_id || $form_id[1] != $current_post_id || $settings['show_success_message'] != 'true' ) {
                    $form_args['show_update_message'] = false;
                }
            }
            
            if ( $form_args['show_update_message'] ) {
                $form_args['html_updated_message'] = '<div class="acfef-message elementor-' . $current_post_id . '">
				<div class="elementor-element elementor-element-' . $wg_id . '">
					<div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">%s</p><span  class="acfef-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div>
				</div>
				</div>';
            }
        }
        
        if ( isset( $settings['emails_to_send'] ) && $settings['emails_to_send'] ) {
            $form_args['emails'] = $settings['emails_to_send'];
        }
        if ( $settings['url_parameters'] ) {
            foreach ( $settings['url_parameters'] as $param ) {
                $form_args['redirect_params'][$param['param_key']] = $param['param_value'];
            }
        }
        $redirect_url = '';
        $form_args['message_location'] = 'other';
        switch ( $settings['redirect'] ) {
            case 'custom_url':
                $redirect_url = $settings['custom_url']['url'];
                break;
            case 'current':
                global  $wp ;
                $redirect_url = home_url( $wp->request );
                $preview_current = true;
                $form_args['message_location'] = 'current';
                break;
            case 'referer_url':
                $redirect_url = home_url( add_query_arg( NULL, NULL ) );
                if ( wp_get_referer() ) {
                    $redirect_url = wp_get_referer();
                }
                break;
            case 'post_url':
                $redirect_url = '%post_url%';
                break;
        }
        $form_args['return'] = $redirect_url;
        
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['preview_redirect'] ) {
            $query_args = [];
            if ( isset( $form_args['redirect_params'] ) ) {
                $query_args = $form_args['redirect_params'];
            }
            if ( isset( $preview_current ) ) {
                $redirect_url = get_the_permalink();
            }
            $return = add_query_arg( $query_args, $redirect_url );
            echo  'Redirect to: ' . $return ;
        }
        
        if ( $settings['show_in_modal'] && $settings['open_modal'] ) {
            $form_args['redirect_params']['modal'] = 1;
        }
        if ( isset( $args['parent_form'] ) ) {
            $form_args['parent_form'] = $args['parent_form'];
        }
        
        if ( isset( $settings['pay_for_submission'] ) && $settings['pay_for_submission'] == 'true' ) {
            $form_args['form_attributes']['class'] .= ' pay-to-post';
            $form_args['hidden_fields']['acfef_pay_to_submit'] = 1;
            $form_args['pay_for_submission'] = 1;
        }
        
        $form_fields = [];
        if ( $settings['fields_selection'] ) {
            $form_fields = $this->get_form_fields( $settings, $wg_id, $form_args );
        }
        
        if ( $form_fields ) {
            $form_args['fields'] = $form_fields;
        } else {
            $form_args['fields'] = [ 'none' ];
        }
        
        
        if ( isset( $settings['user_manager'] ) && $settings['user_manager'] != 'none' ) {
            
            if ( $settings['user_manager'] == 'current_user' ) {
                $user_manager = get_current_user_id();
            } else {
                $user_manager = $settings['manager_select'];
            }
            
            $form_args['user_manager'] = $user_manager;
        }
        
        $fields = $form_args['fields'];
        $fields = apply_filters( 'acfef/chosen_fields', $fields, $settings );
        if ( !$settings['hide_field_labels'] ) {
            $form_args['label_placement'] = $settings['field_label_position'];
        }
        $form_args = apply_filters( 'acfef/form_args', $form_args, $settings );
        $message = apply_filters(
            'acfef/form_message',
            $message,
            $settings,
            $wg_id
        );
        $display = apply_filters(
            'acfef/show_widget',
            $wg_id,
            $settings,
            $form_args
        );
        
        if ( $message ) {
            $display = false;
            if ( $message !== 'NOTHING' ) {
                echo  $message ;
            }
        } elseif ( $display ) {
            acfef_render_form( $form_args );
        } else {
            
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo  '<div class="preview-display">' ;
                acfef_render_form( $form_args );
                echo  '</div>' ;
            }
            
            switch ( $settings['not_allowed'] ) {
                case 'show_message':
                    echo  '<div class="acf-notice -error acf-error-message"><p>' . $settings['not_allowed_message'] . '</p></div>' ;
                    break;
                case 'custom_content':
                    echo  '<div class="not_allowed_message">' . $settings['not_allowed_content'] . '</div>' ;
                    break;
            }
        }
    
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