<?php

class WPPB_LE_Export {

	protected $args_to_export;

	/**
	 * this will take labels that will be exported from database.
	 *
	 * @param array  $args_to_export  labels to export.
	 */
	function __construct( $args_to_export ) {
		$this->args_to_export = $args_to_export;
	}

	/* function to export from database */
	public function export_array() {
		/* export labels from database */
		$all_for_export = array();
		foreach( $this->args_to_export as $labels ) {
			$all_for_export['pble'] = get_option( $labels );
		}

		return $all_for_export;
	}

	/* export to json file */
	public function download_to_json_format( $prefix ) {
		$all_for_export = $this->export_array();

		if( isset( $_POST['pble-export'] ) ) {
			$json = json_encode( $all_for_export );
			$filename = $prefix . date( 'Y-m-d_h.i.s', time() );
			$filename .= '.json';
			header( "Content-Disposition: attachment; filename=$filename" );
			header( 'Content-type: application/json' );
			header( 'Content-Length: ' . mb_strlen( $json ) );
			header( 'Connection: close' );
			echo $json;
			exit;
		}
	}
}
