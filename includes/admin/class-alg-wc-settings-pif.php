<?php
/**
 * Product Input Fields for WooCommerce - Settings
 *
 * @version 1.1.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_PIF' ) ) :

class Alg_WC_Settings_PIF extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = ALG_WC_PIF_ID;
		$this->label = __( 'Product Input Fields', 'product-input-fields-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * maybe_fix_settings.
	 *
	 * @version 1.1.1
	 * @since   1.1.1
	 */
	function maybe_fix_settings( $settings ) {
		if ( ! isset( $this->is_wc_version_below_3_2_0 ) ) {
			$this->is_wc_version_below_3_2_0 = version_compare( get_option( 'woocommerce_version', null ), '3.2.0', '<' );
		}
		if ( ! $this->is_wc_version_below_3_2_0 ) {
			foreach ( $settings as &$setting ) {
				if ( isset( $setting['type'] ) && 'select' === $setting['type'] ) {
					if ( ! isset( $setting['class'] ) || '' === $setting['class'] ) {
						$setting['class'] = 'wc-enhanced-select';
					} else {
						$setting['class'] .= ' ' . 'wc-enhanced-select';
					}
				}
			}
		}
		return $settings;
	}

	/**
	 * get_settings.
	 *
	 * @version 1.1.1
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return $this->maybe_fix_settings( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_wc_pif_option( $current_section . '_' . 'reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					delete_option( $value['id'] );
					$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', $autoload );
				}
			}
		}
	}

	/**
	 * Save settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @todo    (maybe) wp_safe_redirect - check if it's the best solution
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
		wp_safe_redirect( add_query_arg( '', '' ) );
		exit;
	}

}

endif;

return new Alg_WC_Settings_PIF();
