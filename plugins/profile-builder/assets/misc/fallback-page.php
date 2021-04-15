<?php
define( 'ABSPATH', __DIR__ . '/' );//added this because we actually need to access this page directly, sorry about this :)
/*
//load WP if needed
$path_to_wp_install_dir = '';
include_once ( $path_to_wp_install_dir.'wp-load.php' );
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$site_name = ( isset( $_GET['site_name'] ) ? filter_var ( urldecode( $_GET['site_name'] ), FILTER_SANITIZE_STRING ) : '' );
$site_url = ( isset( $_GET['site_url'] ) ? filter_var (urldecode( $_GET['site_url'] ), FILTER_SANITIZE_STRING ) : '' );
$message = ( isset( $_GET['message'] ) ? filter_var (urldecode( $_GET['message'] ), FILTER_SANITIZE_STRING ) : '' );
?> 	
			
<html>
	<head>
		<style type="text/css">
			body {font-family:Arial; padding: 5px; margin-top:100px; text-align: center;}
		</style>
		
		<title><?php echo htmlspecialchars( $site_name, ENT_QUOTES ); ?></title>
	</head>
	
	<body id="wppb_content">
		<h1><?php echo htmlspecialchars( $site_name, ENT_QUOTES ); ?></h1>
		
		<?php echo '<p>'. htmlspecialchars( strip_tags( $message ) ). '</p>'; ?>
		
		<?php echo 'Click <a href="'. htmlspecialchars(  $site_url, ENT_QUOTES ) .'">here</a> to return to the main site'; ?>
	</body>
</html>