<?php

/**
 * Plugin Name: ACF Frontend
 * Plugin URI: https://wordpress.org/plugins/acf-frontend-form-element/
 * Description: An ACF and Elementor extension that allows you to easily display ACF frontend forms on your site so your clients can easily edit the content by themselves from the frontend.
 * Version:     2.8.29
 * Author:      Shabti Kaplan
 * Author URI:  https://kaplanwebdev.com/
 * Text Domain: acf-frontend-form-element
 * Domain Path: /languages/
 *
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'acfef' ) ) {
    acfef()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'acfef' ) ) {
        // Create a helper function for easy SDK access.
        function acfef()
        {
            global  $acfef ;
            
            if ( !isset( $acfef ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
                $acfef = fs_dynamic_init( array(
                    'id'              => '5212',
                    'slug'            => 'acf-frontend-form-element',
                    'premium_slug'    => 'acf-frontend-form-element-pro',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_771aff8259bcf0305b376eceb7637',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Pro',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                    'slug'        => 'acfef-settings',
                    'affiliation' => false,
                ),
                    'is_live'         => true,
                ) );
            }
            
            return $acfef;
        }
        
        // Init Freemius.
        acfef();
        // Signal that SDK was initiated.
        do_action( 'acfef_loaded' );
    }
    
    define( 'ACFEF_VERSION', '2.8.29' );
    define( 'ACFEF_ASSETS_VERSION', '6.6.40' );
    define( 'ACFEF_NAME', plugin_basename( __FILE__ ) );
    define( 'ACFEF_URL', plugin_dir_url( __FILE__ ) );
    define( 'ACFEF_PLUGIN_DIR', WP_PLUGIN_DIR . '/acf-frontend-form-element' );
    define( 'ACFEF__DEV_MODE', false );
    /**
     * Main ACF Elementor Form Class
     *
     * The main class that initiates and runs the plugin.
     *
     * @since 1.0.0
     */
    final class ACF_Frontend
    {
        /**
         * Minimum Elementor Version
         *
         * @since 1.0.0
         *
         * @var string Minimum Elementor version required to run the plugin.
         */
        const  MINIMUM_ELEMENTOR_VERSION = '2.6.0' ;
        /**
         * Minimum PHP Version
         *
         * @since 1.0.0
         *
         * @var string Minimum PHP version required to run the plugin.
         */
        const  MINIMUM_PHP_VERSION = '5.2.4' ;
        /**
         * Instance
         *
         * @since 1.0.0
         *
         * @access private
         * @static
         *
         * @var ACF_Elementor_Form The single instance of the class.
         */
        private static  $_instance = null ;
        /**
         * Instance
         *
         * Ensures only one instance of the class is loaded or can be loaded.
         *
         * @since 1.0.0
         *
         * @access public
         * @static
         *
         * @return ACF_Elementor_Form An instance of the class.
         */
        public static function instance()
        {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        /**
         * Constructor
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function __construct()
        {
            register_activation_hook( __FILE__, [ $this, 'acfef_create_db_tables' ] );
            add_action( 'init', [ $this, 'i18n' ] );
            add_action( 'after_setup_theme', [ $this, 'init' ] );
        }
        
        /**
         * Load Textdomain
         *
         * Load plugin localization files.
         *
         * Fired by `init` action hook.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function i18n()
        {
            load_plugin_textdomain( 'acf-frontend-form-element', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }
        
        /**
         * Initialize the plugin
         *
         * Load the plugin only after Elementor (and other plugins) are loaded.
         * Checks for basic plugin requirements, if one check fail don't continue,
         * if all check have passed load the files required to run the plugin.
         *
         * Fired by `plugins_loaded` action hook.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function init()
        {
            // Check if Elementor installed and activated
            
            if ( !did_action( 'elementor/loaded' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
                return;
            }
            
            
            if ( !class_exists( 'ACF' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_missing_acf_plugin' ] );
                return;
            }
            
            // Check for required Elementor version
            
            if ( !version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
                return;
            }
            
            // Check for required PHP version
            
            if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
                return;
            }
            
            add_action( 'admin_notices', [ $this, 'admin_notice_get_pro' ] );
            $this->acfef_notice_dismissed();
            add_filter(
                'plugin_row_meta',
                [ $this, 'acfef_row_meta' ],
                10,
                2
            );
            $this->plugin_includes();
        }
        
        public function plugin_includes()
        {
            require_once __DIR__ . '/includes/elementor/classes/frontend_forms.php';
            require_once __DIR__ . '/includes/elementor/helpers/data_fetch.php';
            require_once __DIR__ . '/includes/elementor/helpers/shortcodes.php';
            require_once __DIR__ . '/includes/elementor/helpers/permissions.php';
            require_once __DIR__ . '/includes/elementor/helpers/modal.php';
            require_once __DIR__ . '/includes/elementor/module.php';
            require_once __DIR__ . '/includes/acf-field-settings/module.php';
            require_once __DIR__ . '/includes/acfef-settings/module.php';
            do_action( 'acfef/widget_loaded' );
        }
        
        /**
         * Admin notice
         *
         * Warning when the site doesn't have Elementor installed or activated.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_missing_main_plugin()
        {
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor */
                esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'acf-frontend-form-element' ),
                '<strong>' . esc_html__( 'ACF Frontend', 'acf-frontend-form-element' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'acf-frontend-form-element' ) . '</strong>'
            );
            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
        }
        
        /**
         * Admin notice
         *
         * Warning when the site doesn't have ACF installed or activated.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_missing_acf_plugin()
        {
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
            $message = sprintf(
                /* translators: 1: Plugin name 2: Advanced Custom Fields */
                esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'acf-frontend-form-element' ),
                '<strong>' . esc_html__( 'ACF Frontend', 'acf-frontend-form-element' ) . '</strong>',
                '<strong>' . esc_html__( 'Advanced Custom Fields', 'acf-frontend-form-element' ) . '</strong>'
            );
            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
        }
        
        public function admin_notice_get_pro()
        {
            if ( !is_admin() ) {
                return;
            }
            $current_screen = get_current_screen();
            if ( !isset( $current_screen->id ) || $current_screen->id !== 'toplevel_page_acfef-settings' ) {
                return;
            }
            $user_id = get_current_user_id();
            if ( get_user_meta( $user_id, 'acfef_pro_trial_dismiss' ) ) {
                return;
            }
            $img_path = ACFEF_URL . 'assets/plugin-logo.png';
            $image = '<img width="30px" src="' . $img_path . '" style="width:32px;margin-right:10px;margin-bottom: -11px;"/>';
            $user = wp_get_current_user();
            if ( in_array( 'administrator', (array) $user->roles ) ) {
                echo  '<div class="notice notice-info " style="padding-right: 38px; position: relative;">
				  <p> ' . $image . ' Try ACF Frontend <b>Pro</b> for Elementor free for 7 days! <a href="https://frontendform.com/acfef-pro/" target="_blank">Check it out!</a> <a class="button button-primary" style="margin-left:20px;" href="https://frontendform.com/acfef-pro/" target="_blank">Free trial!</a></p>
				<a href="?acfef_pro_trial_dismiss"><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss notice.</span></button></a>
				</div>' ;
            }
        }
        
        public function acfef_notice_dismissed()
        {
            $user_id = get_current_user_id();
            if ( isset( $_GET['acfef_pro_trial_dismiss'] ) ) {
                add_user_meta(
                    $user_id,
                    'acfef_pro_trial_dismiss',
                    'true',
                    true
                );
            }
        }
        
        /**
         * Admin notice
         *
         * Warning when the site doesn't have a minimum required Elementor version.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_minimum_elementor_version()
        {
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'acf-frontend-form-element' ),
                '<strong>' . esc_html__( 'ACF Elementor Form', 'acf-frontend-form-element' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'acf-frontend-form-element' ) . '</strong>',
                self::MINIMUM_ELEMENTOR_VERSION
            );
            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
        }
        
        /**
         * Admin notice
         *
         * Warning when the site doesn't have a minimum required PHP version.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_minimum_php_version()
        {
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
            $message = sprintf(
                /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'acf-frontend-form-element' ),
                '<strong>' . esc_html__( 'ACF Elementor Form', 'acf-frontend-form-element' ) . '</strong>',
                '<strong>' . esc_html__( 'PHP', 'acf-frontend-form-element' ) . '</strong>',
                self::MINIMUM_PHP_VERSION
            );
            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
        }
        
        public function acfef_row_meta( $links, $file )
        {
            
            if ( ACFEF_NAME == $file ) {
                $row_meta = array(
                    'video' => '<a href="' . esc_url( 'https://www.youtube.com/channel/UC8ykyD--K6pJmGmFcYsaD-w/playlists' ) . '" target="_blank" aria-label="' . esc_attr__( 'Video Tutorials', 'acf-frontend-form-element' ) . '" >' . esc_html__( 'Video Tutorials', 'acf-frontend-form-element' ) . '</a>',
                );
                return array_merge( $links, $row_meta );
            }
            
            return (array) $links;
        }
        
        public function acfef_create_db_tables()
        {
        }
    
    }
    ACF_Frontend::instance();
}
