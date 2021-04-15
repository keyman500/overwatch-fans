<?php
/*
Plugin Name: Profile Builder
Plugin URI: https://www.cozmoslabs.com/wordpress-profile-builder/
Description: Login, registration and edit profile shortcodes for the front-end. Also you can choose what fields should be displayed or add new (custom) ones both in the front-end and in the dashboard.
Version: 3.4.2
Author: Cozmoslabs
Author URI: https://www.cozmoslabs.com/
Text Domain: profile-builder
Domain Path: /translation
License: GPL2

== Copyright ==
Copyright 2014 Cozmoslabs (www.cozmoslabs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Check if another version of Profile Builder is activated, to prevent fatal errors*/
function wppb_free_plugin_init() {
    if (function_exists('wppb_return_bytes')) {
        function wppb_admin_notice()
        {
            ?>
            <div class="error">
                <p>
                    <?php
                    /* translators: %s is the plugin version name */
                    echo wp_kses_post( sprintf( __( '%s is also activated. You need to deactivate it before activating this version of the plugin.', 'profile-builder'), PROFILE_BUILDER ) );
                    ?>
                </p>
            </div>
        <?php
        }
        function wppb_plugin_deactivate() {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            unset($_GET['activate']);
        }

        add_action('admin_notices', 'wppb_admin_notice');
        add_action( 'admin_init', 'wppb_plugin_deactivate' );
    } else {

        /**
         * Convert memory value from ini file to a readable form
         *
         * @since v.1.0
         *
         * @return integer
         */
        function wppb_return_bytes($val)
        {
            return wp_convert_hr_to_bytes($val);
        }

        /**
         * Definitions
         *
         *
         */
        define('PROFILE_BUILDER_VERSION', '3.4.2' );
        define('WPPB_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('WPPB_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('WPPB_PLUGIN_BASENAME', plugin_basename(__FILE__));
        define('WPPB_TRANSLATE_DIR', WPPB_PLUGIN_DIR . '/translation');
        define('WPPB_TRANSLATE_DOMAIN', 'profile-builder');

        /* include notices class */
        if (file_exists(WPPB_PLUGIN_DIR . '/assets/lib/class_notices.php'))
            include_once(WPPB_PLUGIN_DIR . '/assets/lib/class_notices.php');

        /* include review class */
        if (file_exists(WPPB_PLUGIN_DIR . '/admin/review.php')){
            include_once(WPPB_PLUGIN_DIR . '/admin/review.php');
            $wppb_review_request = new WPPB_Review_Request ();
        }

        if (file_exists(WPPB_PLUGIN_DIR . '/add-ons/add-ons.php'))
            define('PROFILE_BUILDER', 'Profile Builder Pro');
        elseif (file_exists(WPPB_PLUGIN_DIR . '/front-end/extra-fields/extra-fields.php'))
            define('PROFILE_BUILDER', 'Profile Builder Hobbyist');
        else
            define('PROFILE_BUILDER', 'Profile Builder Free');

        /**
         * Initialize the translation for the Plugin.
         *
         * @since v.1.0
         *
         * @return null
         */
        function wppb_init_translation()
        {
            $current_theme = wp_get_theme();
            if( !empty( $current_theme->stylesheet ) && file_exists( get_theme_root().'/'. $current_theme->stylesheet .'/local_pb_lang' ) )
                load_plugin_textdomain( 'profile-builder', false, basename( dirname( __FILE__ ) ).'/../../themes/'.$current_theme->stylesheet.'/local_pb_lang' );
            else
                load_plugin_textdomain( 'profile-builder', false, basename(dirname(__FILE__)) . '/translation/' );
        }

        add_action('init', 'wppb_init_translation', 8);


        /**
         * Required files
         *
         *
         */
        include_once(WPPB_PLUGIN_DIR . '/assets/lib/wck-api/wordpress-creation-kit.php');
        include_once(WPPB_PLUGIN_DIR . '/features/upgrades/upgrades.php');
        include_once(WPPB_PLUGIN_DIR . '/features/functions.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/admin-functions.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/basic-info.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/general-settings.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/advanced-settings/advanced-settings.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/admin-bar.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/private-website.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/manage-fields.php');
        include_once(WPPB_PLUGIN_DIR . '/admin/pms-cross-promotion.php');
        //include_once(WPPB_PLUGIN_DIR . '/admin/feedback.php');//removed in version 2.9.7
        include_once(WPPB_PLUGIN_DIR . '/features/email-confirmation/email-confirmation.php');
        include_once(WPPB_PLUGIN_DIR . '/features/email-confirmation/class-email-confirmation.php');
        if (file_exists(WPPB_PLUGIN_DIR . '/features/admin-approval/admin-approval.php')) {
            include_once(WPPB_PLUGIN_DIR . '/features/admin-approval/admin-approval.php');
            include_once(WPPB_PLUGIN_DIR . '/features/admin-approval/class-admin-approval.php');
        }
        if ( wppb_conditional_fields_exists() ) {
            include_once(WPPB_PLUGIN_DIR . '/features/conditional-fields/conditional-fields.php');
        }
        include_once(WPPB_PLUGIN_DIR . '/features/login-widget/login-widget.php');
        include_once(WPPB_PLUGIN_DIR . '/features/roles-editor/roles-editor.php');
        include_once(WPPB_PLUGIN_DIR . '/features/content-restriction/content-restriction.php');

        if (file_exists(WPPB_PLUGIN_DIR . '/update/update-checker.php')) {
            include_once(WPPB_PLUGIN_DIR . '/update/update-checker.php');
            include_once(WPPB_PLUGIN_DIR . '/admin/register-version.php');
        }

        if (file_exists(WPPB_PLUGIN_DIR . '/add-ons/add-ons.php')) {
            include_once(WPPB_PLUGIN_DIR . '/add-ons/add-ons.php');
            include_once(WPPB_PLUGIN_DIR . '/add-ons/repeater-field/repeater-module.php');
            include_once(WPPB_PLUGIN_DIR . '/add-ons/custom-redirects/custom-redirects.php');
            include_once(WPPB_PLUGIN_DIR . '/add-ons/email-customizer/email-customizer.php');
            include_once(WPPB_PLUGIN_DIR . '/add-ons/multiple-forms/multiple-forms.php');
            include_once(WPPB_PLUGIN_DIR . '/add-ons/user-listing/userlisting.php');

            $wppb_module_settings = get_option('wppb_module_settings');
            if (isset($wppb_module_settings['wppb_userListing']) && ($wppb_module_settings['wppb_userListing'] == 'show')) {
                add_shortcode('wppb-list-users', 'wppb_user_listing_shortcode');
            } else
            add_shortcode('wppb-list-users', 'wppb_list_all_users_display_error');

            $wppb_email_customizer_activate = 'hide';
            if ( ( !empty( $wppb_module_settings['wppb_emailCustomizer'] ) && $wppb_module_settings['wppb_emailCustomizer'] == 'show' ) || ( !empty( $wppb_module_settings['wppb_emailCustomizerAdmin'] ) && $wppb_module_settings['wppb_emailCustomizerAdmin'] == 'show' ) )
                $wppb_email_customizer_activate = 'show';

            if ( $wppb_email_customizer_activate == 'show')
            include_once(WPPB_PLUGIN_DIR . '/add-ons/email-customizer/admin-email-customizer.php');

            if ( $wppb_email_customizer_activate == 'show' )
            include_once(WPPB_PLUGIN_DIR . '/add-ons/email-customizer/user-email-customizer.php');
        }

        include_once(WPPB_PLUGIN_DIR . '/admin/add-ons.php');
        include_once(WPPB_PLUGIN_DIR . '/assets/misc/plugin-compatibilities.php');

        /* added recaptcha and user role field since version 2.6.2 */
        include_once(WPPB_PLUGIN_DIR . '/front-end/default-fields/recaptcha/recaptcha.php'); //need to load this here for displaying reCAPTCHA on Login and Recover Password forms

        //Elementor Widgets
        if ( did_action( 'elementor/loaded' ) ) {
            if (file_exists(WPPB_PLUGIN_DIR . 'assets/misc/elementor/class-elementor.php'))
                include_once WPPB_PLUGIN_DIR . 'assets/misc/elementor/class-elementor.php';
        }

        //Elementor Content Restriction
        global $content_restriction_activated;
        if ( $content_restriction_activated == 'yes' && did_action( 'elementor/loaded' ) ) {
            if( file_exists( WPPB_PLUGIN_DIR . 'features/content-restriction/class-elementor-content-restriction.php' ) )
                include_once WPPB_PLUGIN_DIR . 'features/content-restriction/class-elementor-content-restriction.php';
        }

        //Include Free Add-ons
        $wppb_free_add_ons_settings = get_option( 'wppb_free_add_ons_settings', array() );
        if( !empty( $wppb_free_add_ons_settings ) ){

            if( isset( $wppb_free_add_ons_settings['custom-css-classes-on-fields'] ) && $wppb_free_add_ons_settings['custom-css-classes-on-fields'] ){
                if( file_exists( WPPB_PLUGIN_DIR . 'add-ons-free/custom-css-classes-on-fields/custom-css-classes-on-fields.php' ) )
                    include_once WPPB_PLUGIN_DIR . 'add-ons-free/custom-css-classes-on-fields/custom-css-classes-on-fields.php';
            }

            if( isset( $wppb_free_add_ons_settings['gdpr-communication-preferences'] ) && $wppb_free_add_ons_settings['gdpr-communication-preferences'] ){
                if( file_exists( WPPB_PLUGIN_DIR . 'add-ons-free/gdpr-communication-preferences/gdpr-communication-preferences.php' ) )
                    include_once WPPB_PLUGIN_DIR . 'add-ons-free/gdpr-communication-preferences/gdpr-communication-preferences.php';
            }

            if( isset( $wppb_free_add_ons_settings['import-export'] ) && $wppb_free_add_ons_settings['import-export'] ){
                if( file_exists( WPPB_PLUGIN_DIR . 'add-ons-free/import-export/import-export.php' ) )
                    include_once WPPB_PLUGIN_DIR . 'add-ons-free/import-export/import-export.php';
            }

            if( isset( $wppb_free_add_ons_settings['labels-edit'] ) && $wppb_free_add_ons_settings['labels-edit'] ){
                if( file_exists( WPPB_PLUGIN_DIR . 'add-ons-free/labels-edit/labels-edit.php' ) )
                    include_once WPPB_PLUGIN_DIR . 'add-ons-free/labels-edit/labels-edit.php';
            }

            if( isset( $wppb_free_add_ons_settings['maximum-character-length'] ) && $wppb_free_add_ons_settings['maximum-character-length'] ){
                if( file_exists( WPPB_PLUGIN_DIR . 'add-ons-free/maximum-character-length/maximum-character-length.php' ) )
                    include_once WPPB_PLUGIN_DIR . 'add-ons-free/maximum-character-length/maximum-character-length.php';
            }

        }

        /**
         * Check for updates
         *
         *
         */
        if (file_exists(WPPB_PLUGIN_DIR . '/update/update-checker.php')) {
            if (file_exists(WPPB_PLUGIN_DIR . '/add-ons/add-ons.php')) {
                $localSerial = get_option('wppb_profile_builder_pro_serial');
                $wppb_update = new wppb_PluginUpdateChecker('http://updatemetadata.cozmoslabs.com/?localSerialNumber=' . $localSerial . '&uniqueproduct=CLPBP', __FILE__, 'profile-builder-pro-update');

            } else {
                $localSerial = get_option('wppb_profile_builder_hobbyist_serial');
                $wppb_update = new wppb_PluginUpdateChecker('http://updatemetadata.cozmoslabs.com/?localSerialNumber=' . $localSerial . '&uniqueproduct=CLPBH', __FILE__, 'profile-builder-hobbyist-update');
            }
        }


// these settings are important, so besides running them on page load, we also need to do a check on plugin activation
        add_action('init', 'wppb_generate_default_settings_defaults');    //prepoulate general settings
        add_action('init', 'wppb_prepopulate_fields');                    //prepopulate manage fields list

    }
} //end wppb_free_plugin_init
add_action( 'plugins_loaded', 'wppb_free_plugin_init' );

if (file_exists( plugin_dir_path(__FILE__) . '/front-end/extra-fields/upload/upload_helper_functions.php'))
    include_once( plugin_dir_path(__FILE__) . '/front-end/extra-fields/upload/upload_helper_functions.php');

/* add a redirect when plugin is activated */
if( !function_exists( 'wppb_activate_plugin_redirect' ) ){
    function wppb_activate_plugin_redirect( $plugin ) {
        if( $plugin == plugin_basename( __FILE__ ) ) {
            wp_safe_redirect( admin_url( 'admin.php?page=profile-builder-basic-info' ) );
            exit();
        }
    }
    add_action( 'activated_plugin', 'wppb_activate_plugin_redirect' );
}
