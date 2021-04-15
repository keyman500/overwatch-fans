<?php
namespace ACFFrontend\Classes;

use ACFFrontend\Plugin;

use ACFFrontend\Classes;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Controls_Stack;
use Elementor\Widget_Base;
use ElementorPro\Modules\QueryControl\Module as Query_Module;
use ACFFrontend\Controls;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class ACFEF_Payments{

	public function credit_card_content( $widget, $condition = false ){
		$widget->start_controls_section(
			'payment_form_section',
			[
				'label' => __( 'Payment Form', 'acf-frontend-form-element' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'pay_for_submission' => 'true',
				]
			]
		);		
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
			'field_label',
			[
				'label' => __( 'Label', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Card Number', 'acf-frontend-form-element' ),
				'default' => __( 'Card Number', 'acf-frontend-form-element' ),
			]
		);   	
		$repeater->add_control(
			'field_placeholder',
			[
				'label' => __( 'Placeholder', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( '•••• •••• •••• ••••', 'acf-frontend-form-element' ),
				'default' => __( '•••• •••• •••• ••••', 'acf-frontend-form-element' ),
			]
		);	
		$repeater->add_responsive_control(
			'field_width',
			[
				'label' => __( 'Width', 'acf-frontend-form-element' ) . ' (%)',
				'type' => Controls_Manager::NUMBER,
				'min' => 30,
				'max' => 100,
				'default' => 100,
				'required' => true,
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'max' => 100,
						'required' => false,
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'default' => 100,
						'required' => false,
					],
				],
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} {{CURRENT_ITEM}}' => 'float: left; clear: none',
					'body.rtl {{WRAPPER}} {{CURRENT_ITEM}}' => 'float: right; clear: none',
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{VALUE}}%',
				],
			]
		);		

		$repeater->add_responsive_control(
			'field_margin',
			[
				'label' => __( 'Margin', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['%', 'px', 'em'],
				'default' => [
					'unit' => '%',
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'false',
				],
				'isLinked' => 'false',
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		

		$repeater->add_responsive_control(
			'field_padding',
			[
				'label' => __( 'Padding', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['%', 'px', 'em'],
				'default' => [
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'false',
					'unit' => '%',
				],
				'min' => 0,
				'isLinked' => 'false',
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		

		
        $widget->add_control(
            'credit_card_fields',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{field_label}}}',
                'prevent_empty' => true,
                'item_actions' => [
                    'add' => false,
                    'duplicate' => false,
                    'remove' => false,
                    'sort' => true,
                ],
                'default' => [
                    [
                        '_id' => 'number',
                        'field_label' => __( 'Number', 'acf-frontend-form-element' ),
                        'field_placeholder' => '•••• •••• •••• ••••',
                        'field_width' => 50,
                    ],	
                    [
                        '_id' => 'name',
                        'field_label' => __( 'Name on Card', 'acf-frontend-form-element' ),
                        'field_placeholder' => __( 'Full Name', 'acf-frontend-form-element' ),
                        'field_width' => 50,
                    ],
                    [
                        '_id' => 'exp',
                        'field_label' => __( 'Expiration', 'acf-frontend-form-element' ),
                        'field_placeholder' => '••/••',
                        'field_width' => 50,
                    ],
                    [
                        '_id' => 'cvc',
                        'field_label' => __( 'CVC', 'acf-frontend-form-element' ),
                        'field_placeholder' => '•••',
                        'field_width' => 50,
					], 
                ],
            ]
		);
		
		$widget->add_control(
			'payment_button_text',
			[
				'label' => __( 'Pay Button Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Pay Now!', 'acf-frontend-form-element' ),
				'default' => __( 'Pay Now!', 'acf-frontend-form-element' ),
			]
		);
		$payment_options = [];
		$default = '';
		if( get_option( 'acfef_stripe_active' ) ){
			$payment_options['stripe'] = [
				'title' => __( 'Stripe', 'acf-frontend-form-element' ),
				'icon' => 'fab fa-stripe',
			];
		}		
		if( get_option( 'acfef_paypal_active' ) ){
			$payment_options['paypal'] = [
				'title' => __( 'Paypal', 'acf-frontend-form-element' ),
				'icon' => 'fab fa-paypal',
			];
		}

		foreach( $payment_options as $key => $option ) { 
			$default = $key; 
			break;  
		} 
		$widget->add_control(
			'payment_processor',
			[
				'label' => __( 'Processor', 'acf-frontend-form-element' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => $payment_options,
				'default' => $default,
				'toggle' => false,
				'frontend_available' => true,
				'render_type' => 'none',
			]
		); 
		$widget->add_control(
			'stripe_currency',
			[
				'label' => __( 'Currency', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'placeholder' => __( 'USD', 'acf-frontend-form-element' ),
				'default' => __( 'USD', 'acf-frontend-form-element' ),
				'options' => acfef_get_stripe_currencies(),
				'required' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'payment_processor' => 'stripe',
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$widget->add_control(
			'paypal_currency',
			[
				'label' => __( 'Currency', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'placeholder' => __( 'USD', 'acf-frontend-form-element' ),
				'default' => __( 'USD', 'acf-frontend-form-element' ),
				'options' => acfef_get_paypal_currencies(),
				'required' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'payment_processor' => 'paypal',
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			'show_total',
			[
				'label' => __( 'Show Total', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',				
			]
		);
		$widget->add_control(
			'before_total',
			[
				'label' => __( 'Prepend Total', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '$',
				'default' => '$',
				'dynamic' => [
					'active' => true,
				],			
				'condition' => [
					'show_total' => 'true',
				],
			]
		);	
		$widget->add_control(
			'after_total',
			[
				'label' => __( 'Append Total', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],	
				'condition' => [
					'show_total' => 'true',
				],		
			]
		);	
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'amount',
			[
				'label' => __( 'Amount To Charge', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '10',
				'default' => '10',
				'required' => true,
				'description' => __( 'Must be equal to or greater than 50 cents in USD', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],			
			]
		);	
	
		$repeater->add_control(
			'value',
			[
				'label' => __( 'Post Submissions', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'placeholder' => '5',
				'default' => '5',
				'required' => true,
				'dynamic' => [
					'active' => true,
				],			
				'render_type' => 'none',
			]
		);	
		$repeater->add_control(
			'description',
			[
				'label' => __( 'Plan Description', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXTAREA,
				'required' => true,
				'placeholder' => __( '5 post submissions', 'acf-frontend-form-element' ),
				'default' => __( '5 post submissions', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],			
				'render_type' => 'none',
			]
		);
		$repeater->add_control(
			'success_message',
			[
				'label' => __( 'Success Message', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Thank you for your payment', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Thank you for your payment', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		
        $widget->add_control(
            'payment_plans',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '<span style="text-transform: capitalize;">{{{ _id.replace(/_/g, " ") }}}</span>',
                'prevent_empty' => true,
                'item_actions' => [
                    'add' => false,
                    'duplicate' => false,
                    'remove' => false,
                    'sort' => true,
                ],
                'default' => [
                    [
						'_id' => 'plan',
						'amount' => 10,
						'value' => 5,
                        'description' => __( '5 post submissions', 'acf-frontend-form-element' ),
					],	 	
				],
				'render_type' => 'none',
				'frontend_available' => true,
            ]
		);

		$widget->end_controls_section();

	}
	
	public function credit_card_styles( $widget ){
		$widget->start_controls_section(
			'payment_form_styles',
			[
				'label' => __( 'Payment Form', 'acf-frontend-form-element' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pay_for_submission' => 'true',
				]
			]
		);		
		$widget->add_responsive_control(
			'submit_button_width',
			[
				'label' => __( 'Submit Button Width', 'elementor' ) . ' (%)',
				'type' => Controls_Manager::NUMBER,
				'min' => 30,
				'max' => 100,
				'default' => 50,
				'required' => true,
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'default' => 50,
						'max' => 100,
						'required' => false,
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'default' => 100,
						'required' => false,
					],
				],
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} .acf-submit .submit-button' => 'width: {{VALUE}}%',
				],
				'separator' => 'after',
			]
		);		
		$widget->add_responsive_control(
			'button_submit_align',
			[
				'label' => __( 'Button Align', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0 auto',
				'options' => [
					'0 auto 0 0' => __( 'Start', 'elementor' ),
					'0 auto' => __( 'Center', 'elementor' ),
					'0 0 0 auto' => __( 'End', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .acf-submit .submit-button' => 'margin: {{VALUE}}',
				],
			]
		);
		$widget->add_control(
			'group_field_background_color',
			[
				'label' => __( 'Background Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cc-group' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$widget->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'group_field_border',
				'label' => __( 'Border', 'elementor' ),
				'selector' => '{{WRAPPER}} .cc-group',
			]
		);

		$widget->add_responsive_control(
			'cc_group_width',
			[
				'label' => __( 'Width', 'elementor' ) . ' (%)',
				'type' => Controls_Manager::NUMBER,
				'min' => 30,
				'max' => 100,
				'default' => 30,
				'required' => true,
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'default' => 50,
						'max' => 100,
						'required' => false,
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'default' => 100,
						'required' => false,
					],
				],
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} .cc-group' => 'width: {{VALUE}}%',
				],
			]
		);		
		$widget->add_responsive_control(
			'cc_group_margin',
			[
				'label' => __( 'Margin', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['%', 'px', 'em'],
				'default' => [
					'unit' => '%',
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'false',
				],
				'isLinked' => 'false',
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} .cc-group' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		

		$widget->add_responsive_control(
			'cc_group_padding',
			[
				'label' => __( 'Padding', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['%', 'px', 'em'],
				'default' => [
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'false',
					'unit' => '%',
				],
				'min' => 0,
				'isLinked' => 'false',
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} .cc-group' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		
        $widget->add_responsive_control(
			'group_field_align',
			[
				'label' => __( 'Horizontal Align', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0 auto',
				'options' => [
					'0 auto 0 0' => __( 'Start', 'elementor' ),
					'0 auto' => __( 'Center', 'elementor' ),
					'0 0 0 auto' => __( 'End', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .cc-group' => 'margin: {{VALUE}}',
				],
			]
		);

		$widget->add_control(
			'payment_total_heading',
			[
				'label' => __( 'Total', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'payment_total_typography',
				'label' => __( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .acfef-payment-total',
			]
		);
	
		$widget->add_responsive_control(
			'payment_total_align',
			[
				'label' => __( 'Align', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .acfef-payment-total' => 'text-align: {{VALUE}}',
				],
			]
		);	
		$widget->add_control(
			'payment_total_text_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .acfef-payment-total' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);
		
		$widget->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'payment_total_text_shadow',
				'selector' => '{{WRAPPER}} .acfef-payment-total',
			]
		);

		$widget->end_controls_section();

	}

	public function credit_card_form( $settings ){
		$wg_id = $settings['hidden_fields']['element_id'];
		$current_user_id = get_current_user_id();
		$user_submissions = get_user_meta( $current_user_id, 'acfef_payed_submissions', true );
		$user_submitted = get_user_meta( $current_user_id, 'acfef_payed_submitted', true );
		$form_hidden = ( $user_submitted < $user_submissions ) ? 'acf-hidden ' : '';

		$cc_form = '<form data-widget="' . $wg_id . '" class="' . $form_hidden . 'cc-purchase" action method="post">';

		$cc_form .= '<div class="acf-field cc-group" data-type="group">';

		$cc_form .= $this->get_total( $settings );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$cc_form .= $this->get_card_preview();
		}else{ 
			$cc_form .= '<div class="card-wrapper"></div>';
		}	

        $cc_form .= '<div class="acf-fields">';		

		$cc_form .= $this->get_card_fields( $settings );

		$cc_form .= '</div>
		<div class="acf-submit"><span class="acf-spinner"></span>		
		<div class="submit-button cc-payment-button">
		<button style="width:100%" type="submit">' . $settings['payment_button_text'] . '</button></div></div>
		</div>
		</form>';
		
		echo $cc_form;
    }

    public function get_card_preview(){ ?>
		<div class="card-wrapper">
			<div class="jp-card-container" style="transform: scale(0.857143);">
				<div class="jp-card">
					<div class="jp-card-front">
						<div class="jp-card-lower">
							<div class="jp-card-shiny"></div>
							<div class="jp-card-cvc jp-card-display">•••</div>
							<div class="jp-card-number jp-card-display">•••• •••• •••• ••••</div>
							<div class="jp-card-name jp-card-display">Full Name</div>
							<div class="jp-card-expiry jp-card-display" data-before="mm/yyyy" data-after="valid date">••/••</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}	
	public function get_total( $settings ){
		$settings = wp_parse_args( $settings, array(
			'before_total' => '',
			'after_total' => '',
		) );
		$form_fields = '';
		$is_hidden = '';
		if( ! $settings['show_total'] ){
			$is_hidden = ' acf-hidden';
		}
		$plan = $settings['payment_plans'][0];

		$fields = $default_value = '';
		if( strpos( $plan['amount'], '[' ) !== 'false' ){
			$dynamic_default = acfef_get_field_names( $plan['amount'] );
			$fields = 'data-default="' . $dynamic_default . '" ';

			$dynamic_value = str_replace( 'acf:', '', $plan['amount'] );
			$default_value = 'data-dynamic_value="' . $dynamic_value . '" ';
		}
		
		$form_fields .= '
			<div class="acfef-payment-total' . $is_hidden . '">' . $settings['before_total'] . '<span ' . $default_value . $fields . 'class="payment-amount"></span>'
				. $settings['after_total'] .
			'</div>';	
		return $form_fields;
	}

	public function get_card_fields( $settings ){
		$form_fields = '';
		$is_hidden = '';
		foreach( $settings['credit_card_fields'] as $field ){
			$data_type = 'text';
			if( $field['_id'] == 'email' ){
				$data_type = 'email';
				if( is_user_logged_in() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) $is_hidden = 'acf-hidden';
			}

			$form_fields .= '
				<div class="acf-field width acf-field-text elementor-repeater-item-' . $field['_id'] . ' ' . $is_hidden . '" data-name="_payment_' . $field['_id'] . '" data-type="' . $data_type . '" data-key="field_acfef_payment_' . $field['_id'] . '">
					<div class="acf-label">
						<label for="acf-field_acfef_payment_' . $field['_id'] . '">' . $field['field_label'] . ' <span class="acf-required">*</span></label>
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap"><input type="' . $data_type . '" id="acf-field_acfef_payment_' . $field['_id'] . '" class="' . $field['_id'] . '" data-stripe="' . $field['_id'] . '" placeholder="' . $field['field_placeholder'] . '"></div>
					</div>
				</div>	
			';	
		}
		return $form_fields;
	}

	public function credit_card_scripts( $processor = '' ){
		$status = ACFEF__DEV_MODE ? '' : '.min';
		wp_enqueue_style( 'acfef-card', ACFEF_URL . 'includes/assets/css/pro/card.min.css', array(), ACFEF_ASSETS_VERSION );
		wp_enqueue_script( 'acfef-card', ACFEF_URL . 'includes/assets/js/pro/card' .$status. '.js', array(), ACFEF_ASSETS_VERSION );	
		wp_enqueue_script( 'acfef-credit-card', ACFEF_URL . 'includes/assets/js/pro/credit-card' .$status. '.js', array( 'acfef-card' ), ACFEF_ASSETS_VERSION );	
		$payments_depends = [];
		if( $processor == 'stripe' ){
			wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v2/', [], ACFEF_ASSETS_VERSION, true );
			$payments_depends[] = 'stripe-js';
		}
		wp_enqueue_script( 'acfef-payments', ACFEF_URL . 'includes/assets/js/pro/payments' .$status. '.js', $payments_depends, ACFEF_ASSETS_VERSION, true );	
		$spk = ( get_option( 'acfef_stripe_live_mode' ) ) ? get_option( 'acfef_stripe_live_publish_key' ) : get_option( 'acfef_stripe_test_publish_key' );
		wp_localize_script( 'acfef-payments', 'acfef_vars', array(
				'cc_nonce' => wp_create_nonce( 'acfef_nonce' ),
				'stripe_spk' => $spk,
				'ajax_url' => admin_url( 'admin-ajax.php' ), 
			)
		);
	}
	public function acfef_preview_scripts( $processor = '' ){
		wp_enqueue_style( 'acfef-card', ACFEF_URL . 'includes/assets/css/pro/card.min.css', array(), ACFEF_ASSETS_VERSION );
	}

	public function __construct() {
		if( get_option( 'acfef_payments_active' ) ){ 
			add_action( 'acfef/content_controls', [ $this, 'credit_card_content'] );
			add_action( 'acfef/styles_controls', [ $this, 'credit_card_styles'] );
			add_action( 'acfef/credit_card_scripts', [ $this, 'credit_card_scripts'] );
			add_action( 'acfef/credit_card_form', [ $this, 'credit_card_form'], 10 );
			add_action( 'acf/enqueue_scripts', [ $this, 'acfef_preview_scripts'] );
		}
	}

}

new ACFEF_Payments();
