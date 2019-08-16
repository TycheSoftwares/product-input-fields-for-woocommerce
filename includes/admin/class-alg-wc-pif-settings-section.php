<?php
/**
 * Product Input Fields for WooCommerce - Section Settings
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

if ( ! class_exists( 'Alg_WC_PIF_Settings_Section' ) ) :

	/**
	 * Settings Sections in WooCommerce settings.
	 */
	class Alg_WC_PIF_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			add_filter( 'woocommerce_get_sections_' . ALG_WC_PIF_ID, array( $this, 'settings_section' ) );
			add_filter( 'woocommerce_get_settings_' . ALG_WC_PIF_ID . '_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
		}

		/**
		 * Settings_section.
		 *
		 * @param array $sections Section IDs.
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function settings_section( $sections ) {
			$sections[ $this->id ] = $this->desc;
			return $sections;
		}

		/**
		 * Add_wc_pif_id.
		 *
		 * @param array $settings Settings array.
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function add_wc_pif_id( $settings ) {
			$settings_with_id = array();
			foreach ( $settings as $setting ) {
				$setting['id']      = ALG_WC_PIF_ID . '_' . $setting['id'];
				$settings_with_id[] = $setting;
			}
			return $settings_with_id;
		}

		/**
		 * Get_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			return $this->add_wc_pif_id(
				array_merge(
					$this->get_section_settings(),
					array(
						array(
							'title' => __( 'Reset Section Settings', 'product-input-fields-for-woocommerce' ),
							'type'  => 'title',
							'id'    => $this->id . '_reset_options',
						),
						array(
							'title'   => __( 'Reset Settings', 'product-input-fields-for-woocommerce' ),
							'desc'    => '<strong>' . __( 'Reset', 'product-input-fields-for-woocommerce' ) . '</strong>',
							'id'      => $this->id . '_reset',
							'default' => 'no',
							'type'    => 'checkbox',
						),
						array(
							'type' => 'sectionend',
							'id'   => $this->id . '_reset_options',
						),
					)
				)
			);
		}

	}

endif;
