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

if( ! class_exists( 'ActionPost' ) ) :

class ActionPost extends ActionBase {
	
	public function get_name() {
		return 'post';
	}

	public function get_label() {
		return __( 'Post', 'acf-frontend-form-element' );
	}

	public function get_fields_display( $form_field, $local_field, $wg_id = ''){
		$field_appearance = isset( $form_field['field_taxonomy_appearance'] ) ? $form_field['field_taxonomy_appearance'] : 'checkbox';
		$field_add_term = isset( $form_field['field_add_term'] ) ? $form_field['field_add_term'] : 0;

		switch( $form_field['field_type'] ){
			case 'title':
				$local_field['type'] = 'text';
				$local_field['custom_title'] = true;
			break;
			case 'slug':
				$local_field['type'] = 'text';
				$local_field['wrapper']['class'] .= ' post-slug-field';
				$local_field['custom_slug'] = true;
			break;
			case 'content':
				$local_field['type'] = isset( $form_field['editor_type'] ) ? $form_field['editor_type'] : 'wysiwyg';
				$local_field['custom_content'] = true;
			break;
			case 'featured_image':
				$local_field['type'] = 'image';
				$local_field['custom_featured_image'] = true;
				$local_field['default_value'] = empty( $form_field['default_featured_image']['id'] ) ? '' : $form_field['default_featured_image']['id'];
			break;
			case 'excerpt':
				$local_field['type'] = 'textarea';
				$local_field['custom_excerpt'] = true;
			break;
			case 'author':
				$local_field['type'] = 'user';
				$local_field['allow_null'] = false;
				$local_field['default_value'] = get_current_user_id();
				$local_field['custom_post_author'] = true;
			break;
			case 'published_on':
				$local_field['type'] = 'date_time_picker';
				$local_field['display_format'] = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
				$local_field['first_day'] = get_option( 'start_of_week' );
				$local_field['custom_post_date'] = true;
			break;
			case 'menu_order':
				$local_field['type'] = 'number';
				$local_field['custom_menu_order'] = true;
			break;
			case 'taxonomy':
				$taxonomy = isset( $form_field['field_taxonomy'] ) ? $form_field['field_taxonomy'] : 'category';
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = $taxonomy;
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'categories':
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = 'category';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'tags':
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = 'post_tag';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'post_type':
				$post_types = [];
				$all_post_types = acf_get_pretty_post_types();
				if( ! empty( $form_field['post_type_field_options'] ) ){
					foreach( $form_field['post_type_field_options'] as $post_type_option ){
						$post_types[ $post_type_option ] = $all_post_types[ $post_type_option ];
					}
				}
				$local_field['choices'] = $post_types;
				$local_field['type'] = isset( $form_field['post_type_appearance'] ) ? $form_field['post_type_appearance'] : 'select';
				$local_field['layout'] = isset( $form_field['post_type_radio_layout'] ) ? $form_field['post_type_radio_layout'] : 'vertical';
				$local_field['default_value'] = isset( $form_field['default_post_type'] ) ? $form_field['default_post_type'] : 'post';
				$local_field['custom_post_type'] = true;
			break;
		}
		return $local_field;
	}
	

	public function register_settings_section( $widget ) {		
						
		$widget->start_controls_section(
			'section_edit_post',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'main_action',
							'operator' => 'in',
							'value' => ['new_post', 'edit_post', 'duplicate_post'],
						],	
						
					],
				],
			]
		);
				
		$widget->add_control(
			'post_settings',
			[
				'label' => __( 'Post Settings', 'acf-frontend-form-element' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);		

		$this->action_controls( $widget );		

		$widget->add_control(
			'show_delete_button',
			[
				'label' => __( 'Delete Post Option', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => [
					'main_action' => 'edit_post',
				],
			]
		);
		
		$widget->add_control(
			'delete_button_text',
			[
				'label' => __( 'Delete Button Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Delete', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Delete', 'acf-frontend-form-element' ),
				'condition' => [
					'main_action' => 'edit_post',
					'show_delete_button' => 'true',
				],
			]
		);
		$widget->add_control(
			'delete_button_icon',
			[
				'label' => __( 'Delete Button Icon', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'main_action' => 'edit_post',
					'show_delete_button' => 'true',
				],
			]
		);
	
		$widget->add_control(
			'confirm_delete_message',
			[
				'label' => __( 'Confirm Delete Message', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'The post will be deleted. Are you sure?', 'acf-frontend-form-element' ),
				'placeholder' => __( 'The post will be deleted. Are you sure?', 'acf-frontend-form-element' ),
				'condition' => [
					'main_action' => 'edit_post',
					'show_delete_button' => 'true',
				],
			]
		);
		$widget->add_control( 'show_delete_message', [
            'label'        => __( 'Show Deleted Message', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
            'label_off'    => __( 'No', 'acf-frontend-form-element' ),
            'default'      => 'true',
            'return_value' => 'true',
			'condition' => [
				'main_action' => 'edit_post',
				'show_delete_button' => 'true',
			],
        ] );
        $widget->add_control( 'delete_message', [
            'label'       => __( 'Success Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => __( 'You have deleted this post', 'acf-frontend-form-element' ),
            'placeholder' => __( 'You have deleted this post', 'acf-frontend-form-element' ),
            'dynamic'     => [
            	'active' => true,
			],
			'condition' => [
				'main_action' => 'edit_post',
				'show_delete_button' => 'true',
				'show_delete_message' => 'true',
			],
		] );
		$widget->add_control(
			'force_delete',
			[
				'label' => __( 'Force Delete', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'return_value' => 'true',
				'description' => __( 'Whether or not to completely delete the posts right away.' ),
				'condition' => [
					'main_action' => 'edit_post',
					'show_delete_button' => 'true',
				],
			]
		);
		$widget->add_control(
			'delete_redirect',
			[
				'label' => __( 'Redirect After Delete', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom_url',
				'options' => [
					'current'  => __( 'Reload Current Url', 'acf-frontend-form-element' ),
					'custom_url' => __( 'Custom Url', 'acf-frontend-form-element' ),
					'referer_url' => __( 'Referer', 'acf-frontend-form-element' ),
				],
				'condition' => [
					'main_action' => 'edit_post',
					'show_delete_button' => 'true',
				],
			]
		);
		
		$widget->add_control(
			'redirect_after_delete',
			[
				'label' => __( 'Custom URL', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'Enter Url Here', 'acf-frontend-form-element' ),
				'show_external' => false,
				'dynamic' => [
					'active' => true,
				],			
				'condition' => [
					'main_action' => 'edit_post',
					'show_delete_button' => 'true',
					'delete_redirect' => 'custom_url',
				],	
			]
		);
 
			
		$widget->add_control(
			'post_settings_end',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
				
		$widget->end_controls_section();
	}
	
	public function action_controls( $widget, $step = false ){
		$condition = [
			'main_action' => ['edit_post', 'new_post', 'duplicate_post'],
		];
		if( $step ){
			$condition = [
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
		}
		$widget->add_control(
			'new_post_status',
			[
				'label' => __( 'Post Status', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'no_change',
				'options' => [
					'draft' => __( 'Draft', 'acf-frontend-form-element' ),
					'private' => __( 'Private', 'acf-frontend-form-element' ),
					'pending' => __( 'Pending Review', 'acf-frontend-form-element' ),
					'publish'  => __( 'Published', 'acf-frontend-form-element' ),
				],
				'condition' => $condition
			]
		);		

		$condition['main_action'][] = 'delete_post';

		$widget->add_control(
			'post_to_edit',
			[
				'label' => __( 'Select Post', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'current_post',
				'options' => [
					'current_post'  => __( 'Current Post', 'acf-frontend-form-element' ),
					'url_query' => __( 'Url Query', 'acf-frontend-form-element' ),
					'select_post' => __( 'Select Post', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);

		$condition['post_to_edit'] = 'url_query';
		$widget->add_control(
			'url_query_post',
			[
				'label' => __( 'URL Query', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'post_id', 'acf-frontend-form-element' ),
				'default' => __( 'post_id', 'acf-frontend-form-element' ),
				'required' => true,
				'description' => __( 'Enter the URL query parameter containing the id of the post you want to edit', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);	
		$condition['post_to_edit'] = 'select_post';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){
			$widget->add_control(
				'post_select',
				[
					'label' => __( 'Post', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'acf-frontend-form-element' ),
					'description' => __( 'Enter the post ID', 'acf-frontend-form-element' ),
					'condition' => $condition,
				]
			);		
		}else{
			$widget->add_control(
				'post_select',
				[
					'label' => __( 'Post', 'acf-frontend-form-element' ),
					'type' => Query_Module::QUERY_CONTROL_ID,
					'options' => [
						'' => 0,
					],
					'label_block' => true,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_POST,
						'display' => 'detailed',
						'query' => [
							'post_type' => 'any',
							'post_status' => 'any',
						],
					],
					'default' => 0,
					'condition' => $condition,
				]
			);
		}
	
		unset( $condition['post_to_edit'] );
		$condition['main_action'] = 'new_post';

		$post_type_choices = acfef_get_post_type_choices();    
		
		$widget->add_control(
			'new_post_type',
			[
				'label' => __( 'New Post Type', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'post',
				'options' => $post_type_choices,
				'condition' => $condition,
			]
		);
		$widget->add_control(
			'new_post_terms',
			[
				'label' => __( 'New Post Terms', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'post',
				'options' => [
					'current_term'  => __( 'Current Term', 'acf-frontend-form-element' ),
					'select_terms' => __( 'Select Term', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);

		$condition['new_post_terms'] = 'select_terms';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){
			$widget->add_control(
				'new_terms_select',
				[
					'label' => __( 'Terms', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18, 12, 11', 'acf-frontend-form-element' ),
					'description' => __( 'Enter the a comma-seperated list of term ids', 'acf-frontend-form-element' ),
					'condition' => $condition,
				]
			);		
		}else{		
			$widget->add_control(
				'new_terms_select',
				[
					'label' => __( 'Terms', 'acf-frontend-form-element' ),
					'type' => Query_Module::QUERY_CONTROL_ID,
					'label_block' => true,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_TAX,
						'display' => 'detailed',
					],		
					'multiple' => true,
					'condition' => $condition,
				]
			);
		}
		
		unset( $condition['new_post_terms'] );
		$condition['new_post_status!'] = 'draft';

		$condition['main_action'] = [ 'new_post', 'edit_post' ];

		$widget->add_control(
			'save_progress_button',
			[
				'label' => __( 'Save Progress Option', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => $condition,
			]
		);
		$condition['save_progress_button'] = 'true';
		$widget->add_control(
			'saved_draft_text',
			[
				'label' => __( 'Save Progress Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Save Progress', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Save Progress', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],				
				'condition' => $condition,
			]
		);
		$widget->add_control(
			'saved_draft_desc',
			[
				'label' => __( 'Save Progress Description', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Want to finish later?', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],				
				'condition' => $condition,
			]
		);
 		$widget->add_control(
			'saved_draft_message',
			[
				'label' => __( 'Progress Saved Message', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Progress Saved', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Progress Saved', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => $condition,
			]
		);  

		$condition['main_action'] = 'new_post';
		$widget->add_control(
			'saved_drafts',
			[
				'label' => __( 'Show Saved Drafts Selection', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => $condition,
				'seperator' => 'before',
			]
		);
		$condition['saved_drafts'] = 'true';
		$widget->add_control(
			'saved_drafts_label',
			[
				'label' => __( 'Edit Draft Label', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Edit a draft', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Edit a draft', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);		
		$widget->add_control(
			'saved_drafts_new',
			[
				'label' => __( 'New Draft Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '&mdash; New Post &mdash;', 'acf-frontend-form-element' ),
				'placeholder' => __( '&mdash; New Post &mdash;', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);
		unset( $condition['saved_drafts'] );

		$condition['main_action'] = 'edit_post';
		$widget->add_control(
			'saved_revisions',
			[
				'label' => __( 'Show Saved Revisions Selection', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => $condition,
				'seperator' => 'before',
			]
		);
 		$condition['saved_revisions'] = 'true';
		$widget->add_control(
			'saved_revisions_label',
			[
				'label' => __( 'Edit Revisions Label', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Edit a revision', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Edit a revision', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);		
		$widget->add_control(
			'saved_revisions_edit_main',
			[
				'label' => __( 'Edit Post Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '&mdash; Edit Main Post &mdash;', 'acf-frontend-form-element' ),
				'placeholder' => __( '&mdash; Edit Main Post &mdash;', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);
 
	}

	private function duplicate_slug( $prefix = '' ) {	
		static $i;
		if ( null === $i ) {
			$i = 2;
		} else {
			$i ++;
		}
		$new_slug = sprintf( '%s_copy%s', $prefix, $i );
		if ( ! acfef_slug_exists( $new_slug ) ) {
			return $new_slug;
		} else {
			return $this->duplicate_slug( $prefix );
		}
	}

	public function on_submit( $post_id, $form ){	
		if( ! isset( $form['action'] ) || $form['action'] != 'post' ) return $post_id;
		$post_to_edit = array();

		$main_action = $form['hidden_fields']['main_action'];
		$wg_id = isset( $_POST['_acf_element_id'] ) ? '_' . $_POST['_acf_element_id'] : '';

		if( isset( $form['step_index'] ) ){
			$current_step = $form['fields']['steps'][$form['step_index']];
		}

		switch( $main_action ){
			case 'edit_post':
				if( get_post_type( $post_id ) == 'revision' && isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'publish' ){
					$revision_id = $post_id;
					$post_id = wp_get_post_parent_id( $revision_id );
					wp_delete_post_revision( $revision_id );
				}
				$post_to_edit['ID'] = $post_id;
			break;
			case 'new_post':
				$post_to_edit['ID'] = 0;	
				$post_to_edit['post_type'] = $form['new_post_type'];
				if( ! empty( $current_step['overwrite_settings'] ) ) $post_to_edit['post_type'] = $current_step['new_post_type'];	
			break;
			case 'duplicate_post':				
				$post_to_duplicate = get_post( $post_id );
				$post_to_edit = get_object_vars( $post_to_duplicate );	
				$post_to_edit['ID'] = 0;	
				$post_to_edit['post_author'] = get_current_user_id();

			break;
			default: 
				return $post_id;
		}
		
	 	$submit_keys = [ 
			'title' => 'post_title',
			'slug' => 'post_name',
			'content' => 'post_content',
			'excerpt' => 'post_excerpt',
			'author' => 'post_author',
			'menu_order' => 'menu_order',
			'published_on' => 'post_date',
			'post_type' => 'post_type',
		];
		
		foreach( $submit_keys as $key => $name ){
			if( isset( $_POST['acf']['acfef' . $wg_id . '_' . $key ] ) ) {	
				$post_to_edit[ $name ] = acf_extract_var( $_POST['acf'], 'acfef' . $wg_id . '_' . $key );
			}
			if( $name == 'post_title' && empty( $post_to_edit[ $name ] ) && $main_action == 'new_post' ){
				$post_to_edit[ $name ] = '(no-name)';
			}
		}

		if( $main_action == 'duplicate_post' ){
			if( $post_to_edit[ 'post_name' ] == $post_to_duplicate->post_name ){
				$post_name = sanitize_title( $post_to_edit['post_title'] );
				if( ! acfef_slug_exists( $post_name ) ){				
					$post_to_edit['post_name'] = $post_name;
				}else{
					$post_to_edit['post_name'] = $this->duplicate_slug( $post_to_duplicate->post_name );
				}
			}
		}


		if( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'draft' ){
			$post_to_edit['post_status'] = 'draft';
		}else{
			$status = $form['new_post_status'];

			if( ! empty( $current_step['overwrite_settings'] ) ) $status = $current_step['new_post_status'];

			if( $status != 'no_change' ){
				$post_to_edit['post_status'] = $status;
			}elseif( $main_action == 'new_post' ){
				$post_to_edit['post_status'] = 'publish';
			}
			
		}
			
		if( $post_to_edit['ID'] == 0 ){
			$post_id = wp_insert_post( $post_to_edit );
		}else{
			wp_update_post( $post_to_edit );
		}
			
		if( isset( $form['post_terms'] ) && $form['post_terms'] != '' ){
			$new_terms = $form['post_terms'];				
			if( is_string( $new_terms ) ){
				$new_terms = explode( ',', $new_terms );
			}
			if( is_array( $new_terms ) ){
				foreach( $new_terms as $term_id ){
					$term = get_term( $term_id );
					if( $term ){
						wp_set_object_terms( $post_id, $term->term_id, $term->taxonomy, true );
					}
				}
			}
		}

		if( $main_action == 'duplicate_post' ){
			$taxonomies = get_object_taxonomies($post_to_duplicate->post_type);
			foreach ($taxonomies as $taxonomy) {
			  $post_terms = wp_get_object_terms($post_to_duplicate->ID, $taxonomy, array('fields' => 'slugs'));
			  wp_set_object_terms($post_id, $post_terms, $taxonomy, false);		
			}
 
			global $wpdb;
			$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_to_duplicate->ID");
			if( count($post_meta_infos) != 0 ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach($post_meta_infos as $meta_info) {
					$meta_key        = $meta_info->meta_key;
					$meta_value      = addslashes($meta_info->meta_value);
					$sql_query_sel[] = "SELECT $post_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}
		}

	/* 	if( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'revision' ){
			if( isset( $_POST['acf']['acfef' . $wg_id . '_featured_image' ] ) ) {
				update_post_meta( $post_id, 'revision_thumbnail', acf_extract_var( $_POST['acf'], 'acfef' . $wg_id . '_featured_image' ) );
			}
		} */

		do_action( 'acfef/' . $main_action, $post_id, $form );
		return $post_id;
	}

	public function __construct(){
		add_filter( 'acf/pre_save_post', array( $this, 'on_submit' ), 4, 2 );
	}
	
}

acfef()->local_actions['post'] = new ActionPost();

endif;	