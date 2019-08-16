<?php
/**
 * Product Input Fields for WooCommerce - Per Product Section Settings
 *
 * @version 1.2.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 *
 * @package product-input-fields-for-woocommerce/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_PIF_Settings_Per_Product' ) ) :

	/**
	 * Per Product Settings Section
	 */
	class Alg_WC_PIF_Settings_Per_Product extends Alg_WC_PIF_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id   = 'per_product';
			$this->desc = __( 'Per Product', 'product-input-fields-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * Get_section_settings.
		 *
		 * @version 1.2.1
		 * @since   1.0.0
		 * @todo    (later) variable - per product or per variation
		 */
		public function get_section_settings() {
			/* translators: %s: Upgrade to Pro URL */
			$upgrade_url = sprintf( __( 'Get <a target="_blank" href="%s">Product Input Fields for WooCommerce Pro</a> plugin to add more than one product input field.', 'product-input-fields-for-woocommerce' ), 'https://www.tychesoftwares.com/store/premium-plugins/product-input-fields-for-woocommerce/?utm_source=pifupgradetopro&utm_medium=link&utm_campaign=ProductInputFieldsLite' );
			$settings    = array(
				array(
					'title' => __( 'Product Input Fields per Product Options', 'product-input-fields-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'When enabled this section will add "Product Input Fields" tab to each product\'s "Edit" page.', 'product-input-fields-for-woocommerce' ),
					'id'    => 'local_options',
				),

				array(
					'title'    => __( 'Product Input Fields - per Product', 'product-input-fields-for-woocommerce' ),
					'desc'     => '<strong>' . __( 'Enable section', 'product-input-fields-for-woocommerce' ) . '</strong>',
					'desc_tip' => __( 'Add custom input field on per product basis.', 'product-input-fields-for-woocommerce' ),
					'id'       => 'local_enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),

				array(
					'title'             => __( 'Default Number of Product Input Fields per Product', 'product-input-fields-for-woocommerce' ),
					'id'                => 'local_total_number_default',
					'desc_tip'          => __( 'You will be able to change this number later as well as define the fields, for each product individually, in product\'s "Edit".', 'product-input-fields-for-woocommerce' ),
					'default'           => 1,
					'type'              => 'number',
					'desc'              => apply_filters( 'alg_wc_product_input_fields', $upgrade_url, 'settings' ),
					'custom_attributes' => apply_filters(
						'alg_wc_product_input_fields',
						array(
							'min' => '1',
							'max' => '1',
						),
						'settings_array'
					),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'local_options',
				),
			);
			return $settings;
		}

	}

endif;

return new Alg_WC_PIF_Settings_Per_Product();
