<?php
/**
 * Product Input Fields for WooCommerce - All Products Section Settings - Field
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 *
 * @package product-input-fields-for-woocommerce/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_PIF_Settings_All_Products_Field' ) ) :

	/**
	 * Global settings for all products
	 */
	class Alg_WC_PIF_Settings_All_Products_Field extends Alg_WC_PIF_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @param string $field_nr Number of fields.
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct( $field_nr ) {
			$this->id       = 'all_products_field_' . $field_nr;
			$this->desc     = __( 'All Products', 'product-input-fields-for-woocommerce' ) . ': ' . __( 'Field', 'product-input-fields-for-woocommerce' ) . ' #' . $field_nr;
			$this->field_nr = $field_nr;
			parent::__construct();
		}

		/**
		 * Get_section_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @todo    (later) add show/hide by categories/tags/products
		 * @todo    (maybe) replace 'textarea' with 'alg_get_product_input_fields_custom_textarea' (same to "General" section etc.)
		 */
		public function get_section_settings() {
			$settings = array();
			$options  = alg_get_product_input_fields_options();
			foreach ( $options as $option ) {
				/* translators: #%d: Number of fields */
				$section_title = sprintf( __( 'Product Input Field #%d', 'product-input-fields-for-woocommerce' ), $this->field_nr );
				$title         = ( 'options' === $option['id'] ) ?
				$section_title :
				( isset( $option['title'] ) ? $option['title'] : '' );
				$settings[]    = array(
					'id'                => $option['id'] . '_global_' . $this->field_nr,
					'type'              => $option['type'],
					'title'             => $title,
					'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
					'desc_tip'          => isset( $option['desc_tip'] ) ? $option['desc_tip'] : '',
					'default'           => isset( $option['default'] ) ? $option['default'] : '',
					'options'           => isset( $option['options'] ) ? $option['options'] : '',
					'css'               => isset( $option['css'] ) ? $option['css'] : '',
					'custom_attributes' => isset( $option['custom_attributes'] ) ? $option['custom_attributes'] : '',
				);
			}
			return $settings;
		}

	}

endif;
