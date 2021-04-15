<?php
namespace ACFFrontend\Actions;

use ACFFrontend\Plugin;
use ACFFrontend\Classes\ActionBase;
use ACFFrontend\Widgets;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Module as Query_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( ! class_exists( 'ActionComment' ) ) :

class ActionComment extends ActionBase {
	
	public function get_name() {
		return 'comment';
	}

	public function get_label() {
		return __( 'Comment', 'acf-frontend-form-element' );
	}
	
	public function get_fields_display( $form_field, $local_field ){
		switch( $form_field['field_type'] ){
			case 'comment':
				$local_field['type'] = 'textarea';
				$local_field['custom_comment'] = true;
			break;
			case 'author':
				$local_field['type'] = 'text';
				$local_field['custom_author'] = true;
			break;
			case 'author_email':
				$local_field['type'] = 'email';
				$local_field['custom_author_email'] = true;
			break;
		}
		return $local_field;
	}

	public function register_settings_section( $widget ) {
		
						
		$widget->start_controls_section(
			'section_edit_comment',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'main_action',
							'operator' => 'in',
							'value' => ['new_comment', 'edit_comment'],
						],	
						
					],
				],
			]
        );
		
		$this->action_controls( $widget );
		    		
		$widget->end_controls_section();
	}

	public function action_controls( $widget, $step = false ){
		$condition = [
			'main_action' => 'new_comment',
		];
		if( $step ){
			$condition['field_type'] = 'step';
			$condition['overwrite_settings'] = 'true';
		}

		$widget->add_control(
			'comment_parent_post',
			[
				'label' => __( 'Post to Comment On', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'current_post',
				'options' => [
					'current_post'  => __( 'Current Post', 'acf-frontend-form-element' ),
					'select_post' => __( 'Select Post', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);
		$condition['comment_parent_post'] = 'select_post';

        $widget->add_control(
            'select_parent_post',
            [
                'label' => __( 'Select Post', 'acf-frontend-form-element' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( '18', 'acf-frontend-form-element' ),
                'description' => __( 'Enter the post ID', 'acf-frontend-form-element' ) . ' ' .  __( 'Commenting must be turned on for that post', 'acf-frontend-form-element' ),
				'condition' => $condition,
            ]
        );		
	}
    
    public function run( $post_id, $settings, $step = false ){
        global $current_user;		

        $comment_to_insert = [
            'comment_post_ID' => $_POST['acfef_parent_post'], 
            'comment_parent' => $_POST['acfef_parent_comment'], 
        ];		

        $comment_id = wp_insert_comment( $comment_to_insert );
        
        return 'comment_' . $comment_id;
    }					 
	
}
acfef()->local_actions['comment'] = new ActionComment();

endif;	