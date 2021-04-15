<?php
function wppb_as_overwrite_mail_to($to){
    $admin_email = get_option( 'admin_email' );
    $wppb_toolbox_admin_settings = get_option('wppb_toolbox_admin_settings');

    if( isset( $wppb_toolbox_admin_settings['admin-emails'] ) && !empty( $wppb_toolbox_admin_settings['admin-emails'] ) && $admin_email == $to ){
        return $wppb_toolbox_admin_settings['admin-emails'];
    } else {
        return $to;
    }
}
add_filter('wppb_send_email_to', 'wppb_as_overwrite_mail_to');

