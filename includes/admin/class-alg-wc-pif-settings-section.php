<?php
/**
 * Product Input Fields for WooCommerce - Section Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PIF_Settings_Section' ) ) :

class Alg_WC_PIF_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections' . '_' . ALG_WC_PIF_ID,                   array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings' . '_' . ALG_WC_PIF_ID . '_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * add_wc_pif_id.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_wc_pif_id( $settings ) {
		$settings_with_id = array();
		foreach ( $settings as $setting ) {
			$setting['id'] = ALG_WC_PIF_ID . '_' . $setting['id'];
			$settings_with_id[] = $setting;
		}
		return $settings_with_id;
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_settings() {
		return $this->add_wc_pif_id( array_merge( $this->get_section_settings(), array(
			array(
				'title'     => __( 'Reset Section Settings', 'product-input-fields-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . 'reset_options',
			),
			array(
				'title'     => __( 'Reset Settings', 'product-input-fields-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'product-input-fields-for-woocommerce' ) . '</strong>',
				'id'        => $this->id . '_' . 'reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . 'reset_options',
			),
		) ) );
	}

}

endif;
