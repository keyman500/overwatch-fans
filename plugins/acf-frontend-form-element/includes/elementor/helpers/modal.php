<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function acfef_get_icon( $icon, $attributes = [], $tag = 'i' ){
    if ( empty( $icon['library'] ) ) {
        return false;
    }
    $output = '';
    // handler SVG Icon
    if ( 'svg' === $icon['library'] ) {
        $output = \Elementor\Icons_Manager::render_svg_icon( $icon['value'] );
    } else {
        $output = acfef_render_icon_html( $icon, $attributes, $tag );
    }

    return $output . ' ';
}

function acfef_render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
    $icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
    if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
        return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
    }

    if ( empty( $attributes['class'] ) ) {
        $attributes['class'] = $icon['value'];
    } else {
        if ( is_array( $attributes['class'] ) ) {
            $attributes['class'][] = $icon['value'];
        } else {
            $attributes['class'] .= ' ' . $icon['value'];
        }
    }
    return '<' . $tag . ' ' . \Elementor\Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
}

add_action( 'elementor/widget/print_template', 'acfef_modal_preview', 20, 2 );
add_action( 'elementor/widget/render_content', 'acfef_modal_render', 10, 2 );


function acfef_modal_preview( $content, $element ){
    if (!$content)
    return '';

    //$id_item = $element->get_id();
    $content = '<# if ( settings.show_in_modal ) {
        var iconHTML = elementor.helpers.renderIcon( view, settings.modal_button_icon, {}, "i" , "object" );
        #><button class="acfef-edit-button edit-button" onClick="openModal(\'{{id}}' .get_the_ID(). '\')" >
        <# if ( iconHTML && iconHTML.rendered ) { #>
            <span class="elementor-accordion-icon-closed">{{{ iconHTML.value }}}</span>
        <# } #>
        {{ settings.modal_button_text }}</button>
        <div id="modal_{{id}}' .get_the_ID(). '" class="modal edit-modal">
            <div class="modal-content"> 
                <div class="modal-inner"> 
                <span onClick="closeModal(\'{{id}}' .get_the_ID(). '\')" class="acf-icon -cancel close"></span>
                    <div class="content-container">' . $content . '</div>
                </div>
            </div>
        </div><# } 
    else { #>' . $content . '<# } #>';
    return $content;
}
function acfef_modal_render( $template, $element ){
    $wg_id = $element->get_id();
    $settings = $element->get_settings_for_display();

     if( ! isset( $settings['show_in_modal'] ) || ! $settings['show_in_modal'] ){
        return $template;
    }else{
        $before = acfef_before_element_render( $settings, $wg_id );
        $after = acfef_after_element_render( $settings, $wg_id );
        return $before.$template.$after;
    }   
}
function acfef_before_element_render( $settings, $wg_id  ){
    if( ! isset( $settings['show_in_modal'] ) || ! $settings['show_in_modal'] ){
        return;
    }else{
        wp_enqueue_style( 'acfef-modal' );	
        wp_enqueue_style( 'acf-global' );	
        wp_enqueue_script( 'acfef-modal' );

        $show_modal = 'hide';
        if( isset( $_GET['modal'] ) ){
			if( isset( $_GET['updated'] ) && $_GET['updated'] != 'true' ){
				$modal_instance = explode( '_', $_GET['updated'] );
				if( is_array( $modal_instance ) && count( $modal_instance ) > 1 && $modal_instance[0] == $wg_id && $modal_instance[1] == get_the_ID() ){
					$show_modal = 'show';
				}
			}			
		}

		$modal_num = acfef_random_string();

        $before = '<button class="acfef-edit-button edit-button" onClick="openModal(\'' .$modal_num. '\')" >'; 
        if( $settings['modal_button_icon']['value'] ){
            $before .= acfef_get_icon( $settings['modal_button_icon'], ['aria-hidden' => 'true'] );
        }
        $before .= $settings['modal_button_text']. '</button>';
        
        $before .= '<div id="modal_' .$modal_num. '" class="modal edit-modal ' .$show_modal. '">
                <div class="modal-content"> 
                    <div class="modal-inner"> 
                    <span onClick="closeModal(\'' .$modal_num. '\')" class="acf-icon -cancel close"></span>
                        <div class="content-container">';

        return $before;
                
    } 
}
function acfef_after_element_render( $settings, $wg_id  ){
    if( ! isset( $settings['show_in_modal'] ) || ! $settings['show_in_modal'] ){
        return;
    }
    $after = '</div>
        </div>
      </div>
    </div>';

    return $after;
}

add_action( 'elementor/element/common/_section_style/after_section_end', 'acfef_modal_controls_common', 10, 2 );

function acfef_modal_controls_common( $element, $args ) { 
    acfef_modal_controls( $element, $args, \Elementor\Controls_Manager::TAB_CONTENT );
}
    
function acfef_modal_controls( $element, $args, $tab ) { 
        $element->start_controls_section(
            'modal_section',
            [
                'label' => __( 'Modal Window', 'acf-frontend-form-element' ),
                'tab' => $tab,
            ]
        );
        
        $element->add_control(
            'show_in_modal',
            [
                'label' => __( 'Show in Modal', 'acf-frontend-form-element' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
                'label_off' => __( 'No','acf-frontend-form-element' ),
                'return_value' => 'true',
            ]
        );
            
        $default_text = __( 'Click Here', 'acf-frontend-form-element' );

        $element->add_control(
            'modal_button_text',
            [
                'label' => __( 'Modal Button Text', 'acf-frontend-form-element' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => $default_text,
                'placeholder' => $default_text,
                'condition' => [
                    'show_in_modal' => 'true',
                ],
                'dynamic' => [
                    'active' => true,
                ],		
            ]
        );		
        $element->add_control(
            'modal_button_icon',
            [
                'label' => __( 'Modal Button Icon', 'acf-frontend-form-element' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'condition' => [
                    'show_in_modal' => 'true',
                ],
            ]
        );
        
        
        $element->end_controls_section();

        //Modal Button Style
		
		$element->start_controls_section(
			'style_modal_button_section',
			[
				'label' => __( 'Modal Button', 'acf-frontend-form-elements' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_in_modal' => 'true',
				],
			]
		);
				
		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'modal_button_typography',
				'label' => __( 'Typography', 'elementor' ),
				'selector' => '{{WRAPPER}} .acfef-edit-button',
			]
		);
		
		$element->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'modal_button_text_shadow',
				'selector' => '{{WRAPPER}} .acfef-edit-button',
			]
		);
		
				
		$element->add_responsive_control(
			'modal_button_text_padding',
			[
				'label' => __( 'Padding', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .acfef-edit-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		
		
		$element->add_control(
			'modal_button_text_style_end',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$element->add_control(
			'modal_button_text_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .acfef-edit-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'modal_button_background_color',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acfef-edit-button' => 'background-color: {{VALUE}};',
				],
			]
		);
	
		$element->add_control(
			'modal_button_tabs_end',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		

			
		$element->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'modal_button_border',
				'label' => __( 'Border', 'elementor' ),
				'selector' => '{{WRAPPER}} .acfef-edit-button',
			]
		);
			
		$element->add_control(
			'modal_button_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default' => [
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'true',
				],
				'selectors' => [
					'{{WRAPPER}} .acfef-edit-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'modal_button_box_shadow',
				'selector' => '{{WRAPPER}} .acfef-edit-button',
			]
		);
		
        $element->end_controls_section();	
        
        // Modal Window Styles

        $element->start_controls_section(
			'style_modal_section',
			[
				'label' => __( 'Modal Window', 'acf-frontend-form-elements' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_in_modal' => 'true',
				],
			]
        );		
        
  		$element->add_control(
			'modal_window_background_color',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edit-modal .modal-content' => 'background-color: {{VALUE}};',
				],
			]
		);
			
		$element->add_responsive_control(
			'modal_window_size',
			[
				'label' => __( 'Modal Width', 'elementor' ) . ' (%)',
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 20,
				'max' => 100,
				'required' => true,
				'device_args' => [
					\Elementor\Controls_Stack::RESPONSIVE_TABLET => [
						'max' => 100,
						'required' => false,
					],
					\Elementor\Controls_Stack::RESPONSIVE_MOBILE => [
						'max' => 100,
						'required' => false,
					],
				],
				'min_affected_device' => [
					\Elementor\Controls_Stack::RESPONSIVE_DESKTOP => \Elementor\Controls_Stack::RESPONSIVE_TABLET,
					\Elementor\Controls_Stack::RESPONSIVE_TABLET => \Elementor\Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} .edit-modal .modal-content' => 'width: {{VALUE}}%',
				],
			]
		);
		
		$element->add_responsive_control(
			'modal_content_size',
			[
				'label' => __( 'Modal Content', 'elementor' ) . ' (%)',
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 20,
				'max' => 100,
				'required' => true,
				'device_args' => [
					\Elementor\Controls_Stack::RESPONSIVE_TABLET => [
						'max' => 100,
						'required' => false,
					],
					\Elementor\Controls_Stack::RESPONSIVE_MOBILE => [
						'max' => 100,
						'required' => false,
					],
				],
				'min_affected_device' => [
					\Elementor\Controls_Stack::RESPONSIVE_DESKTOP => \Elementor\Controls_Stack::RESPONSIVE_TABLET,
					\Elementor\Controls_Stack::RESPONSIVE_TABLET => \Elementor\Controls_Stack::RESPONSIVE_TABLET,
				],
				'selectors' => [
					'{{WRAPPER}} .edit-modal .modal-content .modal-inner' => 'width: {{VALUE}}%',
				],
			]
		);
		
		$element->add_responsive_control(
			'modal_inner_align',
			[
				'label' => __( 'Horizontal Align', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'flex-start' => __( 'Start', 'elementor' ),
					'center' => __( 'Center', 'elementor' ),
					'flex-end' => __( 'End', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .edit-modal .modal-content' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$element->end_controls_section();	
	}

