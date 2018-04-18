<?php
/**
 * Product Input Fields for WooCommerce - Per Product Metabox
 *
 * @version 1.1.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PIF_Per_Product_Metabox' ) ) :

class Alg_WC_PIF_Per_Product_Metabox {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		if ( 'yes' === get_wc_pif_option( 'local_enabled', 'yes' ) ) {
			add_action( 'add_meta_boxes',    array( $this,  'add_local_product_input_fields_meta_box' ) );
			add_action( 'save_post_product', array( $this, 'save_local_product_input_fields_meta_box' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * Save product input fields on Product Edit.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save_local_product_input_fields_meta_box( $post_id, $post ) {
		// Check that we are saving with input fields displayed.
		if ( ! isset( $_POST[ ALG_WC_PIF_ID . '_' . 'save_post' ] ) ) {
			return;
		}
		// Save options
		$default_total_input_fields = apply_filters( 'alg_wc_product_input_fields', 1, 'per_product_total_fields_default' );
		$total_input_fields_before_saving = apply_filters( 'alg_wc_product_input_fields', 1, 'per_product_total_fields', $post_id );
		$total_input_fields_before_saving = ( '' != $total_input_fields_before_saving ) ? $total_input_fields_before_saving : $default_total_input_fields;
		$options = alg_get_product_input_fields_options();
		for ( $i = 1; $i <= $total_input_fields_before_saving; $i++ ) {
			foreach ( $options as $option ) {
				if ( in_array( $option['type'], array( 'title', 'sectionend' ) ) ) {
					continue;
				}
				$option_id = ALG_WC_PIF_ID . '_' . $option['id'] . '_' . 'local' . '_' . $i;
				if ( isset( $_POST[ $option_id ] ) ) {
					update_post_meta( $post_id, '_' . $option_id, $_POST[ $option_id ] );
				} elseif ( 'checkbox' === $option['type'] ) {
					update_post_meta( $post_id, '_' . $option_id, 'no' );
				}
			}
		}
		// Save total product input fields number
		$option_name = ALG_WC_PIF_ID . '_' . 'local_total_number';
		$total_input_fields = isset( $_POST[ $option_name ] ) ? $_POST[ $option_name ] : $default_total_input_fields;
		update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
	}

	/**
	 * add_local_product_input_fields_meta_box.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_local_product_input_fields_meta_box() {
		add_meta_box(
			'alg-product-input-fields-total-number',
			__( 'Product Input Fields Total Number', 'product-input-fields-for-woocommerce' ),
			array( $this, 'create_local_product_input_fields_total_number_meta_box' ),
			'product',
			'normal',
			'high'
		);
		$current_post_id = get_the_ID();
		if ( ! ( $total_number = apply_filters( 'alg_wc_product_input_fields', 1, 'per_product_total_fields', $current_post_id ) ) ) {
			$total_number = apply_filters( 'alg_wc_product_input_fields', 1, 'per_product_total_fields_default' );
		}
		for ( $i = 1; $i <= $total_number; $i++ ) {
			add_meta_box(
				'alg-product-input-field-' . $i,
				__( 'Product Input Field', 'product-input-fields-for-woocommerce' ) . ' #' . $i,
				array( $this, 'create_local_product_input_fields_meta_box' ),
				'product',
				'normal',
				'high',
				array( 'field_nr' => $i )
			);
		}
	}

	/**
	 * create_local_product_input_fields_total_number_meta_box.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function create_local_product_input_fields_total_number_meta_box() {
		$html = '';
		$current_post_id = get_the_ID();
		if ( ! ( $total_number = apply_filters( 'alg_wc_product_input_fields', 1, 'per_product_total_fields', $current_post_id ) ) ) {
			$total_number = apply_filters( 'alg_wc_product_input_fields', 1, 'per_product_total_fields_default' );
		}
		$html .= '<strong>' . __( 'Total Product Input Fields', 'product-input-fields-for-woocommerce' ) . '</strong>';
		$html .= wc_help_tip( __( 'Click "Update" product after you change this number.', 'product-input-fields-for-woocommerce' ) );
		$html .= ' ' . '<input type="number" min="1" max="100" id="' . ALG_WC_PIF_ID . '_' . 'local_total_number" name="' . ALG_WC_PIF_ID . '_' . 'local_total_number" value="' .
			$total_number . '"' . apply_filters( 'alg_wc_product_input_fields', ' readonly', 'settings' ) . '>';
		$html .= apply_filters( 'alg_wc_product_input_fields', ' <em>' .
			sprintf( __( 'Get <a target="_blank" href="%s">Product Input Fields for WooCommerce Pro</a> plugin to add more than one product input field.', 'product-input-fields-for-woocommerce' ), 'https://wpcodefactory.com/item/product-input-fields-woocommerce/' ) . '</em>', 'settings' );
		$html .= '<input type="hidden" name="' . ALG_WC_PIF_ID . '_' . 'save_post" value="' . ALG_WC_PIF_ID . '_' . 'save_post">';
		echo $html;
	}

	/**
	 * create_local_product_input_fields_meta_box.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @todo    (later) add "reset settings" button
	 */
	function create_local_product_input_fields_meta_box( $post, $callback_args ) {
		$html            = '';
		$field_nr        = $callback_args['args']['field_nr'];
		$current_post_id = get_the_ID();
		$options         = alg_get_product_input_fields_options();
		$data            = array();
		foreach ( $options as $option ) {
			if ( 'sectionend' === $option['type'] || 'options' === $option['id'] ) {
				continue;
			}
			if ( 'title' === $option['type'] ) {
				$data[] = array( '<h4>' . $option['title'] . ( isset( $option['desc'] ) ? wc_help_tip( $option['desc'] ) : '' ) . '</h4>', '' );
				continue;
			}
			$option_id = ALG_WC_PIF_ID . '_' . $option['id'] . '_' . 'local' . '_' . $field_nr;
			if ( '' == ( $option_value = get_post_meta( $current_post_id, '_' . $option_id, true ) ) ) {
				$option_value = $option['default'];
			}
			if ( 'checkbox' === $option['type'] ) {
				$is_checked = checked( $option_value, 'yes', false );
			}
			$select_options_html = '';
			if ( 'select' === $option['type'] ) {
				foreach( $option['options'] as $select_option_id => $select_option_label ) {
					$select_options_html .= '<option value="' . $select_option_id . '"' . selected( $option_value, $select_option_id, false ) . '>' . $select_option_label . '</option>';
				}
			}
			$style = isset( $option['css'] ) ? ' style="' . $option['css'] . '"' : '';
			$custom_attributes = '';
			if ( isset( $option['custom_attributes'] ) ) {
				foreach ( $option['custom_attributes'] as $custom_attribute_key => $custom_attribute_value ) {
					$custom_attributes .= ' ' . $custom_attribute_key . '="' . $custom_attribute_value . '"';
				}
			}
			switch ( $option['type'] ) {
				case 'number':
				case 'text':
					$the_field = '<input' . $custom_attributes . $style .
						' type="' . $option['type'] .
						'" id="' . $option_id .
						'" name="' . $option_id .
						'" value="' . $option_value . '">' .
						( isset( $option['desc'] ) ? ' <em>' . $option['desc'] . '</em>' : '' );
					break;
				case 'textarea':
					$the_field = '<textarea' . $custom_attributes . $style .
						' id="' . $option_id .
						'" name="' . $option_id . '">' . $option_value . '</textarea>';
					break;
				case 'checkbox':
					$the_field = '<input' . $style . ' class="checkbox" type="checkbox" value="yes" name="' . $option_id .
						'" id="' . $option_id . '" ' . $is_checked . ' />' .
						( isset( $option['desc'] ) ? ' ' . $option['desc'] : '' );
					break;
				case 'select':
					$the_field = '<select' . $style . ' id="' . $option_id . '" name="' . $option_id . '">' . $select_options_html . '</select>';
					break;
			}
			$data[] = array( $option['title'] . ( isset( $option['desc_tip'] ) ? wc_help_tip( $option['desc_tip'] ) : '' ), $the_field );
		}
		$html .= alg_get_table_html( $data, array(
			'table_class'        => 'widefat striped',
			'table_heading_type' => 'vertical',
		) );
		echo $html;
	}

}

endif;

return new Alg_WC_PIF_Per_Product_Metabox();
