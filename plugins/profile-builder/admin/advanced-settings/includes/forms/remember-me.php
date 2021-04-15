<?php

add_action( 'wppb_login_form_bottom', 'wppb_toolbox_rememberme_checked', 99 );
function wppb_toolbox_rememberme_checked() {
	return '<script>if ( document.getElementById("rememberme") ) document.getElementById("rememberme").checked = true;</script>';
}
