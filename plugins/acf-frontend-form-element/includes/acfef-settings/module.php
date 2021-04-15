<?php

namespace ACFFrontend;


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( !class_exists( 'ACFFrontend_Settings' ) ) {
    class ACFFrontend_Settings
    {
        private  $components = array() ;
        public function get_name()
        {
            return 'acfef_settings';
        }
        
        public function acfef_plugin_page()
        {
            global  $acfef_settings ;
            $acfef_settings = add_menu_page(
                'ACF Frontend',
                'ACF Frontend',
                'manage_options',
                'acfef-settings',
                [ $this, 'acfef_admin_settings_page' ],
                'dashicons-feedback',
                '87.87778'
            );
            add_submenu_page(
                'acfef-settings',
                __( 'Settings', 'acf-frontend-form-element' ),
                __( 'Settings', 'acf-frontend-form-element' ),
                'manage_options',
                'acfef-settings',
                '',
                0
            );
            if ( get_option( 'acfef_payments_active' ) ) {
                add_submenu_page(
                    'acfef-settings',
                    __( 'Payments', 'acf-frontend-form-element' ),
                    __( 'Payments', 'acf-frontend-form-element' ),
                    'manage_options',
                    'acfef-payments',
                    [ $this, 'acfef_admin_payments_page' ],
                    1
                );
            }
        }
        
        function acfef_admin_settings_page()
        {
            global  $acfef_active_tab ;
            $acfef_active_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome' );
            ?>

			<h2 class="nav-tab-wrapper">
			<?php 
            do_action( 'acfef_settings_tabs' );
            ?>
			</h2>
			<?php 
            do_action( 'acfef_settings_content' );
        }
        
        function acfef_admin_payments_page()
        {
            require_once __DIR__ . '/admin-pages/payments/payments-list.php';
            $option = 'per_page';
            $args = [
                'label'   => 'Payments',
                'default' => 20,
                'option'  => 'payments_per_page',
            ];
            add_screen_option( $option, $args );
            $payments_obj = new \Payments_List();
            ?>
				<h2><?php 
            echo  __( 'Payments', 'acf-frontend-form-element' ) ;
            ?></h2>
				<?php 
            $payments_obj->prepare_items();
            $payments_obj->display();
        }
        
        public function add_tabs()
        {
            add_action( 'acfef_settings_tabs', [ $this, 'acfef_settings_tabs' ], 1 );
            add_action( 'acfef_settings_content', [ $this, 'acfef_settings_render_options_page' ] );
        }
        
        public function acfef_settings_tabs()
        {
            $tabs = [
                'welcome'         => 'Welcome',
                'local-avatar'    => 'Local Avatar',
                'uploads-privacy' => 'Uploads Privacy',
                'hide_admin'      => 'Hide WP Dashboard',
                'google'          => 'Google APIs',
            ];
            global  $acfef_active_tab ;
            foreach ( $tabs as $name => $label ) {
                ?>
				<a class="nav-tab <?php 
                echo  ( $acfef_active_tab == $name || '' ? 'nav-tab-active' : '' ) ;
                ?>" href="<?php 
                echo  admin_url( '?page=acfef-settings&tab=' . $name ) ;
                ?>"><?php 
                _e( $label, 'acf-frontend-form-element' );
                ?> </a>
			<?php 
            }
        }
        
        public function acfef_settings_render_options_page()
        {
            global  $acfef_active_tab ;
            
            if ( '' || 'welcome' == $acfef_active_tab ) {
                ?>
			<style>p.acfef-text{font-size:20px}</style>
			<h3><?php 
                _e( 'Hello and welcome', 'acf-frontend-form-element' );
                ?></h3>
			<p class="acfef-text"><?php 
                _e( 'If this is your first time using ACF Frontend, we recommend you watch Paul Charlton from WPTuts beautifully explain how to use it.', 'acf-frontend-form-element' );
                ?></p>
			<iframe width="560" height="315" src="https://www.youtube.com/embed/iHx7krTqRN0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			<br>
			<p class="acfef-text"><?php 
                _e( 'If you have any questions at all please feel welcome to email shabti at', 'acf-frontend-form-element' );
                ?> <a href="mailto:shabti@frontendform.com">shabti@frontendform.com</a> <?php 
                _e( 'or on whatsapp', 'acf-frontend-form-element' );
                ?> <a href="https://api.whatsapp.com/send?phone=972584526441">+972-58-452-6441</a></p>
			<?php 
            }
            
            
            if ( 'local-avatar' == $acfef_active_tab ) {
                $this->local_avatar = get_option( 'local_avatar' );
                ?>

				<div class="wrap">
					<?php 
                settings_errors();
                ?>
					<form method="post" action="options.php">
						<?php 
                settings_fields( 'local_avatar_settings' );
                do_settings_sections( 'local-avatar-settings-admin' );
                submit_button();
                ?>
					</form>
				</div>
			<?php 
            }
            
            
            if ( 'uploads-privacy' == $acfef_active_tab ) {
                $this->uploads_privacy = get_option( 'filter_media_author' );
                ?>

				<div class="wrap">
					<?php 
                settings_errors();
                ?>
					<form method="post" action="options.php">
						<?php 
                settings_fields( 'uploads_privacy_settings' );
                do_settings_sections( 'uploads-privacy-settings-admin' );
                submit_button();
                ?>
					</form>
				</div>
			<?php 
            }
            
            $form_tabs = [ 'hide_admin', 'google', 'payments' ];
            foreach ( $form_tabs as $form_tab ) {
                
                if ( $form_tab == $acfef_active_tab ) {
                    $hide_admin_fields = apply_filters( 'acfef/' . $acfef_active_tab . '_fields', [] );
                    acfef_render_form( [
                        'post_id'        => 'acfef_options',
                        'hidden_fields'  => [
                        'admin_page' => $acfef_active_tab,
                        'screen_id'  => 'options',
                    ],
                        'fields'         => $hide_admin_fields,
                        'submit_value'   => __( 'Save Settings', 'acf-frontend-form-element' ),
                        'update_message' => __( 'Settings Saved', 'acf-frontend-form-element' ),
                        'return'         => admin_url( '?page=acfef-settings&tab=' . $_GET['tab'] ),
                    ] );
                }
            
            }
        }
        
        public function acfef_configs()
        {
            
            if ( !get_option( 'acfef_hide_wp_dashboard' ) ) {
                add_option( 'acfef_hide_wp_dashboard', true );
                add_option( 'acfef_hide_by', array_map( 'strval', [
                    0 => 'user',
                ] ) );
            }
            
            require_once __DIR__ . '/admin-pages/custom-fields.php';
        }
        
        public function acfef_settings_sections()
        {
            require_once __DIR__ . '/admin-pages/local-avatar/settings.php';
            new ACFEF_Local_Avatar_Settings( $this );
            require_once __DIR__ . '/admin-pages/uploads-privacy/settings.php';
            new ACFEF_Uploads_Privacy_Settings( $this );
            require_once __DIR__ . '/admin-pages/hide_admin/settings.php';
            new ACFEF_Hide_Admin_Settings( $this );
            require_once __DIR__ . '/admin-pages/google/settings.php';
            new ACFEF_Google_API_Settings( $this );
        }
        
        public function acfef_form_head()
        {
            
            if ( is_admin() ) {
                $current_screen = get_current_screen();
                if ( isset( $current_screen->id ) && $current_screen->id === 'toplevel_page_acfef-settings' ) {
                    acf_form_head();
                }
            }
        
        }
        
        public function acfef_validate_save_post()
        {
            if ( isset( $_POST['_acf_post_id'] ) && $_POST['_acf_post_id'] == 'acfef_options' ) {
                
                if ( isset( $_POST['_acf_admin_page'] ) ) {
                    $page_slug = $_POST['_acf_admin_page'];
                    apply_filters( 'acfef/' . $page_slug . '_fields', [] );
                }
            
            }
        }
        
        public function acfef_scripts()
        {
            
            if ( ACFEF__DEV_MODE ) {
                $min = '';
            } else {
                $min = '.min';
            }
            
            wp_register_style(
                'acfef',
                ACFEF_URL . 'includes/assets/css/acfef.min.css',
                array(),
                ACFEF_ASSETS_VERSION
            );
            wp_register_style(
                'acfef-modal',
                ACFEF_URL . 'includes/assets/css/acfef-modal.min.css',
                array(),
                ACFEF_ASSETS_VERSION
            );
            wp_register_script(
                'acfef',
                ACFEF_URL . 'includes/assets/js/acfef' . $min . '.js',
                array( 'jquery', 'acf-input' ),
                ACFEF_ASSETS_VERSION,
                true
            );
            wp_register_script(
                'acfef-modal',
                ACFEF_URL . 'includes/assets/js/acfef-modal.min.js',
                array( 'jquery' ),
                ACFEF_ASSETS_VERSION
            );
            wp_register_script(
                'acfef-password-strength',
                ACFEF_URL . 'includes/assets/js/password-strength.min.js',
                array( 'password-strength-meter' ),
                ACFEF_ASSETS_VERSION,
                true
            );
        }
        
        public function __construct()
        {
            $this->acfef_settings_sections();
            add_action( 'wp_loaded', [ $this, 'acfef_scripts' ] );
            add_action( 'init', [ $this, 'acfef_configs' ] );
            add_action( 'admin_menu', [ $this, 'acfef_plugin_page' ] );
            add_action( 'acf/validate_save_post', [ $this, 'acfef_validate_save_post' ] );
            $this->add_tabs();
        }
    
    }
    acfef()->settings_tabs = new ACFFrontend_Settings();
}
