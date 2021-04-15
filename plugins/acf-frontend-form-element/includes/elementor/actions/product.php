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

if( ! class_exists( 'ActionProduct' ) ) :

class ActionProduct extends ActionBase {
	
	public function get_name() {
		return 'product';
	}

	public function get_label() {
		return __( 'Product', 'acf-frontend-form-element' );
	}

	public function get_fields_display( $form_field, $local_field, $wg_id = '', $sub_fields = false ){
		$field_appearance = isset( $form_field['field_taxonomy_appearance'] ) ? $form_field['field_taxonomy_appearance'] : 'checkbox';
		$field_add_term = isset( $form_field['field_add_term'] ) ? $form_field['field_add_term'] : 0;

		switch( $form_field['field_type'] ){
			case 'product_title':
				$local_field['type'] = 'text';
				$local_field['custom_title'] = true;
			break;		
			case 'product_slug':
				$local_field['type'] = 'text';
				$local_field['wrapper']['class'] .= ' post-slug-field';
				$local_field['custom_slug'] = true;
			break;	
			case 'price':
				$local_field['type'] = 'number';
				$local_field['custom_price'] = true;
			break;
			case 'sale_price':
				$local_field['type'] = 'number';
				$local_field['custom_sale_price'] = true;
			break;
			case 'description':
				$local_field['type'] = 'wysiwyg';
				$local_field['custom_content'] = true;
			break;
			case 'main_image':
				$local_field['type'] = 'image';
				$local_field['custom_featured_image'] = true;
			break;			
			case 'images':
				$local_field['type'] = 'gallery';
				$local_field['custom_product_gallery'] = true;
			break;
			case 'short_description':
				$local_field['type'] = 'textarea';
				$local_field['custom_excerpt'] = true;
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
			case 'product_categories':
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = 'product_cat';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'product_tags':
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = 'product_tag';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'product_type':
				$local_field['type'] = 'product_types';
				$local_field['field_type'] = isset( $form_field['product_type_appearance'] ) ? $form_field['product_type_appearance'] : 'radio';			
				$local_field['custom_taxonomy'] = true;
			break;
			case 'attributes':
				$sub_fields = acfef_parse_args( $sub_fields, array(
					array(
						'field_label_hide' => 0,
						'label' => '',
						'placeholder' => '',
						'instructions' => '',
					),			
					array(
						'field_label_hide' => 0,
						'label' => '',
						'instructions' => '',
						'products_page' => '',
						'for_variations' => '',
					),				
					array(
						'field_label_hide' => 0,
						'label' => '',
						'instructions' => '',
						'button_label' => '',
					),		
					array(
						'field_label_hide' => 0,
						'label' => '',
						'instructions' => '',
						'button_label' => '',
					),						
				) );

				$local_field['type'] = 'product_attributes';
				$local_field['fields_settings'] = array(
					'name' => array(
						'field_label_hide' => ! $sub_fields[0]['field_label_on'],
						'label' =>  $sub_fields[0]['label'],
						'placeholder' =>  $sub_fields[0]['placeholder'],
						'instructions' =>  $sub_fields[0]['instructions'],
					),
					'locations' => array(
						'field_label_hide' => ! $sub_fields[1]['field_label_on'],
						'label' =>  $sub_fields[1]['label'],
						'instructions' =>  $sub_fields[1]['instructions'],
						'choices' => array(
							'products_page' => $sub_fields[1]['products_page'],
							'for_variations' => $sub_fields[1]['for_variations'],
						),
					),
					'custom_terms' => array(
						'field_label_hide' => ! $sub_fields[2]['field_label_on'],
						'label' =>  $sub_fields[2]['label'],
						'instructions' =>  $sub_fields[2]['instructions'],
						'button_label' =>  $sub_fields[2]['button_label'],
					),
					'terms' => array(
						'field_label_hide' => ! $sub_fields[3]['field_label_on'],
						'label' =>  $sub_fields[3]['label'],
						'instructions' =>  $sub_fields[3]['instructions'],
						'button_label' =>  $sub_fields[3]['button_label'],
					),
				);
			break;
			case 'variations':
				$local_field['type'] = 'product_variations';
				$local_field['custom_taxonomy'] = true;
			break;
			case 'sku':
				$local_field['label'] =  __( 'SKU', 'acf-frontend-form-element' );
				$local_field['type'] = 'text';
				$local_field['custom_sku'] = true;
			break;			
			case 'manage_stock':
				$local_field['type'] = 'true_false';
				$local_field['ui'] = 1;
				$local_field['ui_on_text'] = isset( $form_field['ui_on'] ) ? $form_field['ui_on'] : 'Yes';
				$local_field['ui_off_text'] = isset( $form_field['ui_off'] ) ? $form_field['ui_off'] : 'No';
				$local_field['custom_manage_stock'] = true;
			break;
			case 'stock_quantity':
				$local_field['type'] = 'number';
				$local_field['custom_stock_quantity'] = true;
				$local_field['conditional_logic'] = [
					[
						[
							'field' => 'acfef_' . $wg_id . '_manage_stock',
							'operator' => '==',
							'value' => '1',
						]
					]
				];
			break;			
			case 'allow_backorders':
				$local_field['type'] = 'select';
				$local_field['choices'] = array(
					'no' => isset( $form_field['do_not_allow'] ) ? $form_field['do_not_allow'] :__( 'Do not allow', 'woocommerce' ),
					'notify' => isset( $form_field['notify'] ) ? $form_field['notify'] : __( 'Notify', 'woocommerce' ),
					'yes' => isset( $form_field['allow'] ) ? $form_field['allow'] : __( 'Allow', 'woocommerce' ),
				);
				$local_field['custom_backorders'] = true;
				$local_field['conditional_logic'] = [
					[
						[
							'field' => 'acfef_' . $wg_id . '_manage_stock',
							'operator' => '==',
							'value' => '1',
						]
					]
				];
			break;				
			case 'low_stock_threshold':
				$local_field['type'] = 'number';
				$local_field['custom_low_stock'] = true;
				$local_field['conditional_logic'] = [
					[
						[
							'field' => 'acfef_' . $wg_id . '_manage_stock',
							'operator' => '==',
							'value' => '1',
						]
					]
				];
			break;
			case 'stock_status':
				$local_field['type'] = 'select';
				$local_field['choices'] = array(
					'instock' => isset( $form_field['instock'] ) ? $form_field['instock'] : __( 'In stock', 'woocommerce' ),
					'outofstock' => isset( $form_field['outofstock'] ) ? $form_field['outofstock'] : __( 'Out of stock', 'woocommerce' ),
					'onbackorder' => isset( $form_field['backorder'] ) ? $form_field['backorder'] : __( 'On backorder', 'woocommerce' ),
				);
				$local_field['custom_stock_status'] = true;
				$local_field['conditional_logic'] = [
					[
						[
							'field' => 'acfef_' . $wg_id . '_manage_stock',
							'operator' => '!=',
							'value' => '1',
						]
					]
				];
			break;	
			case 'sold_individually':
				$local_field['type'] = 'true_false';
				$local_field['ui'] = 1;
				$local_field['ui_on_text'] = isset( $form_field['ui_on'] ) ? $form_field['ui_on'] : 'Yes';
				$local_field['ui_off_text'] = isset( $form_field['ui_off'] ) ? $form_field['ui_off'] : 'No';
				$local_field['custom_sold_ind'] = true;
			break;	
		}
		return $local_field;
	}
	
	

	public function register_settings_section( $widget ) {
						
		$widget->start_controls_section(
			'section_edit_product',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'main_action',
							'operator' => 'in',
							'value' => ['new_product', 'edit_product'],
						],	
					],
				],
			]
		);
				
		$widget->add_control(
			'product_settings',
			[
				'label' => __( 'Product Settings', 'acf-frontend-form-element' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);
		$widget->add_control(
			'product_title_structure',
			[
				'label' => __( 'Product Title Structure', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Product Title', 'acf-frontend-form-element' ),
				'description' => __( 'Structure the title field. You can use shortcodes for text fields. Foe example: [acf name="text"]', 'acf-frontend-form-element' ),
			]
		);	
		$widget->add_control(
			'product_default_featured_image',
			[
				'label' => __( 'Default Product Image', 'acf-frontend-form-element' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$this->action_controls( $widget );
	/* 	$widget->add_control(
			'delete_post_option',
			[
				'label' => __( 'Delete Product Option', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::RAW_HTML,
				'raw' =>  '<p>' . __( 'To add a delete button, follow this tutorial: https://www.frontendform.com/the-trash-button-widget/', 'acf-frontend-form-element' ) . '</p>',
				'condition' => [
					'main_action' => 'edit_product',
				],
			]
		);	 */
 		$widget->add_control(
			'show_product_delete_button',
			[
				'label' => __( 'Delete Product Option', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => [
					'main_action' => 'edit_product',
				],
			]
		);
		
		$widget->add_control(
			'delete_product_button_text',
			[
				'label' => __( 'Delete Button Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Delete', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Delete', 'acf-frontend-form-element' ),
				'condition' => [
					'main_action' => 'edit_product',
					'show_product_delete_button' => 'true',
				],
			]
		);
		$widget->add_control(
			'delete_product_button_icon',
			[
				'label' => __( 'Delete Button Icon', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'main_action' => 'edit_product',
					'show_product_delete_button' => 'true',
				],
			]
		);

		$widget->add_control(
			'confirm_delete_product_message',
			[
				'label' => __( 'Confirm Delete Message', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 3,
				'default' => __( 'The product will be deleted. Are you sure?', 'acf-frontend-form-element' ),
				'placeholder' => __( 'The product will be deleted. Are you sure?', 'acf-frontend-form-element' ),
				'condition' => [
					'main_action' => 'edit_product',
					'show_product_delete_button' => 'true',
				],
			]
		);
		$widget->add_control( 'show_delete_message_product', [
            'label'        => __( 'Show Success Message', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
            'label_off'    => __( 'No', 'acf-frontend-form-element' ),
            'default'      => 'true',
            'return_value' => 'true',
        ] );
        $widget->add_control( 'delete_message_product', [
            'label'       => __( 'Success Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => __( 'You have deleted this product', 'acf-frontend-form-element' ),
            'placeholder' => __( 'You have deleted this product', 'acf-frontend-form-element' ),
            'dynamic'     => [
            'active' => true,
				'condition' => [
					'show_delete_message' => 'true',
				],	
			],
		] );
		$widget->add_control(
			'force_delete_product',
			[
				'label' => __( 'Force Delete', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'return_value' => 'true',
				'description' => __( 'Whether or not to completely delete the posts right away.' ),
				'condition' => [
					'main_action' => 'edit_product',
					'show_product_delete_button' => 'true',
				],
			]
		);
		$widget->add_control(
			'delete_redirect_product',
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
					'main_action' => 'edit_product',
					'show_product_delete_button' => 'true',
				],
			]
		);
		$widget->add_control(
			'redirect_after_delete_product',
			[
				'label' => __( 'Custom URL', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'Enter Url Here', 'acf-frontend-form-element' ),
				'show_external' => false,
				'dynamic' => [
					'active' => true,
				],	
				'condition' => [
					'delete_redirect_product' => 'custom_url',
					'main_action' => 'edit_product',
					'show_product_delete_button' => 'true',
				],			
			]
		);
			 
		$widget->end_controls_section();
		
	}
	
	
	public function action_controls( $widget, $step = false ){
		$condition = [
			'main_action' => ['edit_product', 'new_product']
		];
		if( $step ){
			$condition = [
				'main_action' => ['edit_product', 'new_product'],
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
		}

		$widget->add_control(
			'new_product_status',
			[
				'label' => __( 'Product Status', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'no_change',
				'options' => [
					'draft' => __( 'Draft', 'acf-frontend-form-element' ),
					'private' => __( 'Private', 'acf-frontend-form-element' ),
					'pending' => __( 'Pending Review', 'acf-frontend-form-element' ),
					'publish'  => __( 'Published', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);

		$condition['main_action'] = 'edit_product';

		$widget->add_control(
			'product_to_edit',
			[
				'label' => __( 'Product To Edit', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'current_product',
				'options' => [
					'current_product'  => __( 'Current Product', 'acf-frontend-form-element' ),
					'url_query' => __( 'Url Query', 'acf-frontend-form-element' ),
					'select_product' => __( 'Select Product', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);
		$condition['product_to_edit'] = 'url_query';
		$widget->add_control(
			'url_query_product',
			[
				'label' => __( 'URL Query', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'product_id', 'acf-frontend-form-element' ),
				'default' => __( 'product_id', 'acf-frontend-form-element' ),
				'required' => true,
				'description' => __( 'Enter the URL query parameter containing the id of the product you want to edit', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);	
		$condition['product_to_edit'] = 'select_product';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){
			$widget->add_control(
				'product_select',
				[
					'label' => __( 'Product', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'acf-frontend-form-element' ),
					'description' => __( 'Enter the product ID', 'acf-frontend-form-element' ),
					'condition' => $condition,
				]
			);		
		}else{
			$widget->add_control(
				'product_select',
				[
					'label' => __( 'Product', 'acf-frontend-form-element' ),
					'type' => Query_Module::QUERY_CONTROL_ID,
					'options' => [
						'' => 0,
					],
					'label_block' => true,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_POST,
						'display' => 'detailed',
						'query' => [
							'post_type' => 'product',
							'post_status' => 'any',
						],
					],
					'default' => 0,
					'condition' => $condition,
				]
			);
		}

		unset( $condition['product_to_edit'] );
		$condition['main_action'] = 'new_product';
	
		$widget->add_control(
			'new_product_terms',
			[
				'label' => __( 'New Product Terms', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'product',
				'options' => [
					'current_term'  => __( 'Current Term', 'acf-frontend-form-element' ),
					'select_terms' => __( 'Select Term', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);
		$condition['new_product_terms'] = 'select_terms';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){
			$widget->add_control(
				'new_product_terms_select',
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
				'new_product_terms_select',
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
		
	}
	

	public function create_product( $args ){	
		// Get an empty instance of the product object (defining it's type)
		$product = $this->get_product_object_type( $args['type'] );
		if( ! $product )
			return false;
	
		// Product name (Title) and slug
		$product->set_name( $args['name'] ); // Name (title).
		if( isset( $args['slug'] ) )
			$product->set_name( $args['slug'] );
	
		// Description and short description:
		$product->set_description( $args['description'] );
		$product->set_short_description( $args['short_description'] );
	
		// Status ('publish', 'pending', 'draft' or 'trash')
		$product->set_status( isset($args['status']) ? $args['status'] : 'publish' );
	
		// Visibility ('hidden', 'visible', 'search' or 'catalog')
		$product->set_catalog_visibility( isset($args['visibility']) ? $args['visibility'] : 'visible' );
	
		// Featured (boolean)
		$product->set_featured(  isset($args['featured']) ? $args['featured'] : false );
	
		// Virtual (boolean)
		$product->set_virtual( isset($args['virtual']) ? $args['virtual'] : false );
	
		// Prices
		$product->set_regular_price( $args['regular_price'] );
		$product->set_sale_price( isset( $args['sale_price'] ) ? $args['sale_price'] : '' );
		$product->set_price( isset( $args['sale_price'] ) ? $args['sale_price'] :  $args['regular_price'] );
		if( isset( $args['sale_price'] ) ){
			$product->set_date_on_sale_from( isset( $args['sale_from'] ) ? $args['sale_from'] : '' );
			$product->set_date_on_sale_to( isset( $args['sale_to'] ) ? $args['sale_to'] : '' );
		}
	
		// Downloadable (boolean)
		$product->set_downloadable(  isset($args['downloadable']) ? $args['downloadable'] : false );
		if( isset($args['downloadable']) && $args['downloadable'] ) {
			$product->set_downloads(  isset($args['downloads']) ? $args['downloads'] : array() );
			$product->set_download_limit(  isset($args['download_limit']) ? $args['download_limit'] : '-1' );
			$product->set_download_expiry(  isset($args['download_expiry']) ? $args['download_expiry'] : '-1' );
		}
	
		// Taxes
		if ( get_option( 'woocommerce_calc_taxes' ) === 'yes' ) {
			$product->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
			$product->set_tax_class(  isset($args['tax_class']) ? $args['tax_class'] : '' );
		}
	
		// SKU and Stock (Not a virtual product)
		if( isset($args['virtual']) && ! $args['virtual'] ) {
			$product->set_sku( isset( $args['sku'] ) ? $args['sku'] : '' );
			$product->set_manage_stock( isset( $args['manage_stock'] ) ? $args['manage_stock'] : false );
			$product->set_stock_status( isset( $args['stock_status'] ) ? $args['stock_status'] : 'instock' );
			if( isset( $args['manage_stock'] ) && $args['manage_stock'] ) {
				$product->set_stock_status( $args['stock_qty'] );
				$product->set_backorders( isset( $args['backorders'] ) ? $args['backorders'] : 'no' ); // 'yes', 'no' or 'notify'
			}
		}
	
		// Sold Individually
		$product->set_sold_individually( isset( $args['sold_individually'] ) ? $args['sold_individually'] : false );
	
		// Weight, dimensions and shipping class
		$product->set_weight( isset( $args['weight'] ) ? $args['weight'] : '' );
		$product->set_length( isset( $args['length'] ) ? $args['length'] : '' );
		$product->set_width( isset(  $args['width'] ) ?  $args['width']  : '' );
		$product->set_height( isset( $args['height'] ) ? $args['height'] : '' );
		if( isset( $args['shipping_class_id'] ) )
			$product->set_shipping_class_id( $args['shipping_class_id'] );
	
		// Upsell and Cross sell (IDs)
		$product->set_upsell_ids( isset( $args['upsells'] ) ? $args['upsells'] : '' );
		$product->set_cross_sell_ids( isset( $args['cross_sells'] ) ? $args['upsells'] : '' );
	
		// Attributes et default attributes
		if( isset( $args['attributes'] ) )
			$product->set_attributes( $this->prepare_product_attributes($args['attributes']) );
		if( isset( $args['default_attributes'] ) )
			$product->set_default_attributes( $args['default_attributes'] ); // Needs a special formatting
	
		// Reviews, purchase note and menu order
		$product->set_reviews_allowed( isset( $args['reviews'] ) ? $args['reviews'] : false );
		$product->set_purchase_note( isset( $args['note'] ) ? $args['note'] : '' );
		if( isset( $args['menu_order'] ) )
			$product->set_menu_order( $args['menu_order'] );
	
		// Product categories and Tags
		if( isset( $args['category_ids'] ) )
			$product->set_category_ids( $args['category_ids'] );
		if( isset( $args['tag_ids'] ) )
			$product->set_tag_ids( $args['tag_ids'] );
	
	
		// Images and Gallery
		$product->set_image_id( isset( $args['image_id'] ) ? $args['image_id'] : "" );
		$product->set_gallery_image_ids( isset( $args['gallery_ids'] ) ? $args['gallery_ids'] : array() );
	
		## --- SAVE PRODUCT --- ##
		$product_id = $product->save();
	
		return $product_id;
	}
	
	// Utility function that returns the correct product object instance
	public function get_product_object_type( $type ) {
		// Get an instance of the WC_Product object (depending on his type)
		if( isset($args['type']) && $args['type'] === 'variable' ){
			$product = new WC_Product_Variable();
		} elseif( isset($args['type']) && $args['type'] === 'grouped' ){
			$product = new WC_Product_Grouped();
		} elseif( isset($args['type']) && $args['type'] === 'external' ){
			$product = new WC_Product_External();
		} else {
			$product = new WC_Product_Simple(); // "simple" By default
		} 
		
		if( ! is_a( $product, 'WC_Product' ) )
			return false;
		else
			return $product;
	}
	
	// Utility function that prepare product attributes before saving
	public function prepare_product_attributes( $attributes ){
		global $woocommerce;
	
		$data = array();
		$position = 0;
	
		foreach( $attributes as $taxonomy => $values ){
			if( ! taxonomy_exists( $taxonomy ) )
				continue;
	
			// Get an instance of the WC_Product_Attribute Object
			$attribute = new WC_Product_Attribute();
	
			$term_ids = array();
	
			// Loop through the term names
			foreach( $values['term_names'] as $term_name ){
				if( term_exists( $term_name, $taxonomy ) )
					// Get and set the term ID in the array from the term name
					$term_ids[] = get_term_by( 'name', $term_name, $taxonomy )->term_id;
				else
					continue;
			}
	
			$taxonomy_id = wc_attribute_taxonomy_id_by_name( $taxonomy ); // Get taxonomy ID
	
			$attribute->set_id( $taxonomy_id );
			$attribute->set_name( $taxonomy );
			$attribute->set_options( $term_ids );
			$attribute->set_position( $position );
			$attribute->set_visible( $values['is_visible'] );
			$attribute->set_variation( $values['for_variation'] );
	
			$data[$taxonomy] = $attribute; // Set in an array
	
			$position++; // Increase position
		}
		return $data;
	}


	public function on_submit( $post_id, $form ){	
		if( ! isset( $form['action'] ) || $form['action'] != 'product' ) return $post_id;
		
		$post_to_edit = array( 'post_type' => 'product', 'tax_input' => ['product_type' => 'simple'] );
		$wg_id = $_POST['_acf_element_id'];

		$main_action = $form['hidden_fields']['main_action'];
				
		if( $main_action == 'edit_product' || ( is_numeric( $post_id ) && $main_action == 'new_product' ) ) {			
			$post_to_edit['ID'] = $post_id;
			$hook_name = 'edit_product';			
		}elseif( $main_action == 'new_product' ) {			
			// merge in new post data
			$post_to_edit['ID'] = 0;	
			$hook_name = 'add_product';							
		}else{
			return $post_id;
		}
		
	 	$submit_keys = [ 
			'product_title' => 'post_title',
			'slug' => 'post_name',
			'description' => 'post_content',
			'short_description' => 'post_excerpt',
			'author' => 'post_author',
			'menu_order' => 'menu_order',
			'published_on' => 'post_date',
		];
		
		foreach( $submit_keys as $key => $name ){
			if( isset( $_POST['acf']['acfef_' . $wg_id . '_' . $key ] ) ) {	
				$post_to_edit[ $name ] = acf_extract_var( $_POST['acf'], 'acfef_' . $wg_id . '_' . $key );
			}
			if( $name == 'post_title' && empty( $post_to_edit[ $name ] ) && $hook_name == 'add_product' ){
				$post_to_edit[ $name ] = '(no-name)';
			}
		}

		if( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'draft' ){
			$post_to_edit['post_status'] = 'draft';
		}else{
			$status = $form['new_product_status'];

			if( ! empty( $current_step['overwrite_settings'] ) ) $status = $current_step['new_product_status'];

			if( $status != 'no_change' ){
				$post_to_edit['post_status'] = $status;
			}elseif( $main_action == 'new_product' ){
				$post_to_edit['post_status'] = 'publish';
			}
			
		}	
			
		if( $hook_name == 'add_product' ){
			$post_id = wp_insert_post( $post_to_edit );
		}else{
			wp_update_post( $post_to_edit );
		}

		if( isset( $form['product_terms'] ) && $form['product_terms'] != '' ){
			$new_terms = $form['product_terms'];					
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

		return $post_id;
	}

	public function __construct(){
		add_filter( 'acf/pre_save_post', array( $this, 'on_submit' ), 4, 2 );
	}

}

acfef()->local_actions['product'] = new ActionProduct();

endif;	