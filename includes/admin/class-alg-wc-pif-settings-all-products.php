<?php
/**
 * Product Input Fields for WooCommerce - All Products Section Settings
 *
 * @version 1.1.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PIF_Settings_All_Products' ) ) :

class Alg_WC_PIF_Settings_All_Products extends Alg_WC_PIF_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'all_products';
		$this->desc = __( 'All Products', 'product-input-fields-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_section_settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    (later) when resetting, delete all global products input fields (maybe just max 100?) (except 1st?)
	 */
	function get_section_settings() {
		$settings = array(
			array(
				'title'    => __( 'Product Input Fields Global Options', 'product-input-fields-for-woocommerce' ),
				'desc'     => __( 'When enabled this section will add new settings section for each product input field.', 'product-input-fields-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'global_options',
			),
			array(
				'title'    => __( 'Product Input Fields - All Products', 'product-input-fields-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'product-input-fields-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Add custom input fields to all products.', 'product-input-fields-for-woocommerce' ),
				'id'       => 'global_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Product Input Fields Number', 'product-input-fields-for-woocommerce' ),
				'desc_tip' => __( 'After you save this number, new settings sections for each product input field will appear.', 'product-input-fields-for-woocommerce' ),
				'id'       => 'global_total_number',
				'default'  => 1,
				'type'     => 'number',
				'desc'     => apply_filters( 'alg_wc_product_input_fields', sprintf( __( 'Get <a target="_blank" href="%s">Product Input Fields for WooCommerce Pro</a> plugin to add more than one product input field.', 'product-input-fields-for-woocommerce' ), 'https://wpcodefactory.com/item/product-input-fields-woocommerce/' ), 'settings' ),
				'custom_attributes' => apply_filters( 'alg_wc_product_input_fields', array( 'min' => '1', 'max' => '1' ), 'settings_array' ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'global_options',
			),
		);
		return $settings;
	}

}

endif;

return new Alg_WC_PIF_Settings_All_Products();
