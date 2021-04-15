<?php
namespace ACFFrontend\Actions;

use ACFFrontend\Plugin;
use ACFFrontend;
use ACFFrontend\Classes\ActionBase;
use ACFFrontend\Widgets;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( ! class_exists( 'SendWebhook' ) ) :
	
class SendWebhook extends ActionBase{

	public $site_domain = '';

	public function get_name() {
		return 'webhook';
	}

	public function get_label() {
		return __( 'Webhook', 'acf-frontend-form-element' );
	}


	public function register_settings_section( $widget ) {

		$site_domain = acfef_get_site_domain();
		
		$repeater = new \Elementor\Repeater();


		$widget->start_controls_section(
			 'section_webhook',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'more_actions' => $this->get_name(),
				],
			]
		);
        
        $repeater->add_control(
            'webhook_id',
           [
               'label' => __( 'Webhook Name', 'acf-frontend-form-element' ),
               'type' => Controls_Manager::TEXT,
               'placeholder' => __( 'Webhook Name', 'acf-frontend-form-element' ),
               'default' => __( 'Webhook Name', 'acf-frontend-form-element' ),
               'label_block' => true,
               'description' => __( 'Give this webhook an identifier', 'acf-frontend-form-element' ),
           ]
       );
	
		$repeater->add_control(
			 'webhook_url',
			[
				'label' => __( 'Webhook URL', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Webhook URL', 'acf-frontend-form-element' ),
				'default' => __( 'Webhook URL', 'acf-frontend-form-element' ),
				'label_block' => true,
			]
		);
		
		
		$widget->add_control(
			'webhooks_to_send',
			[
				'label' => __( 'Webhooks', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ webhook_id }}}',
			]
		);

		$widget->end_controls_section();
	}
	
/* 	public function run( $post_id, $settings, $step = false ){	

		if( $settings['emails_to_send'] ){
			foreach( $settings['emails_to_send'] as $email ){
				$send_email = true;
				
				if( $step !== false ){
					$step_emails = explode( ',', $step['emails_to_send'] );
					if( ! in_array( $email['email_id'], $step_emails ) ){
						$send_email = false;
					}
				}
				
				if( $send_email ){
					$send_html = 'plain' !== $email['email_content_type'];
					$line_break = $send_html ? '<br>' : "\n";

					$fields = [
						'email_to' => get_option( 'admin_email' ),
						'email_to_cc' => '',
						'email_to_bcc' => '',					
						'email_from' => get_bloginfo( 'admin_email' ),
						'email_from_name' => get_bloginfo( 'name' ),
						'email_reply_to' => 'noreplay@' . acfef_get_site_domain(),
						'email_reply_to_name' => '',
					
						'email_subject' => sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_bloginfo( 'name' ) ),
						'email_content' => 'An ACF Frontend form has been filled out on your site',

					];

					foreach ( $fields as $key => $default ) {
						$setting = trim( $email[ $key ] );
						$setting = acfef_shortcode( $setting, $post_id );
						if ( ! empty( $setting ) ) {
							$fields[ $key ] = $setting;
						}
					}

					$email_meta = $this->get_meta( $email['form_metadata'], $line_break );


					if ( ! empty( $email_meta ) ) {
						$fields['email_content'] .= $line_break . '---' . $line_break . $line_break . $email_meta;
					}

					$headers = sprintf( 'From: %s <%s>' . "\r\n", $fields['email_from_name'], $fields['email_from'] );
					$headers .= sprintf( 'Reply-To: %s <%s>' . "\r\n", $fields['email_reply_to_name'], $fields['email_reply_to'] );

					if ( $send_html ) {
						$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
					}

					$cc_header = '';
					if ( ! empty( $fields['email_to_cc'] ) ) {
						$cc_header = 'Cc: ' . $fields['email_to_cc'] . "\r\n";
					}
					$fields['email_content'] = apply_filters( 'acfef/wp_mail_message', $fields['email_content'] );

					$email_sent = wp_mail( $fields['email_to'], $fields['email_subject'], $fields['email_content'], $headers . $cc_header );

					if ( ! empty( $fields['email_to_bcc'] ) ) {
						$bcc_emails = explode( ',', $fields['email_to_bcc'] );
						foreach ( $bcc_emails as $bcc_email ) {
							wp_mail( trim( $bcc_email ), $fields['email_subject'], $fields['email_content'], $headers );
						}
					}

		
					do_action( 'acfef/mail_sent', $settings, $email, $fields, $step );
				}
			}
		}

		return $post_id;
	}	 */

}
new SendWebhook();

endif;	