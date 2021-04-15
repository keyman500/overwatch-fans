<?php

$wppb_general_settings = get_option( 'wppb_general_settings' );

if ( isset( $wppb_general_settings['adminApproval'] ) && $wppb_general_settings['adminApproval'] == 'yes' ) {
    add_action( 'wppb_after_user_approval', 'wppb_toolbox_update_admin_approval_in_usermeta', 20 );

    if ( isset( $wppb_general_settings['emailConfirmation'] ) && $wppb_general_settings['emailConfirmation'] == 'yes' )
        add_action( 'wppb_activate_user', 'wppb_toolbox_save_admin_approval_in_usermeta_ec', 20, 3 );
    else
        add_action( 'wppb_register_success', 'wppb_toolbox_save_admin_approval_in_usermeta', 20, 3 );

}

function wppb_toolbox_save_admin_approval_in_usermeta( $http_request, $form_name, $user_id ){
	update_user_meta( $user_id, 'wppb_approval_status', 'unapproved' );
}

function wppb_toolbox_save_admin_approval_in_usermeta_ec( $user_id, $password, $meta ){
	update_user_meta( $user_id, 'wppb_approval_status', 'unapproved' );
}

function wppb_toolbox_update_admin_approval_in_usermeta( $user_id ){
	update_user_meta( $user_id, 'wppb_approval_status', 'approved' );
}
