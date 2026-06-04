<?php
/**
 * Product Input Fields for WooCommerce Pro - Deactivation Class
 *
 * @version 1.1.7
 * @since   1.1.3
 * @author  Tyche Softwares
 * @package Input Fields for WooCommerce Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Tyche_PIF_Pro_Deactivation' ) ) {

	/** Declaration of Class */
	class Tyche_PIF_Pro_Deactivation {

		/** Constructor */
		public function __construct() {
			require_once __DIR__ . '/tyche/components/plugin-deactivation/class-tyche-plugin-deactivation.php';
			new Tyche_PIF_Plugin_Deactivation(
				array(
					'plugin_name'       => 'Product Input Fields for WooCommerce',
					'plugin_base'       => 'product-input-fields-for-woocommerce/product-input-fields-for-woocommerce.php',
					'script_file'       => PIF_PLUGIN_URL . 'includes/tyche/assets/js/plugin-deactivation.js',
					'plugin_short_name' => 'pif_lite',
					'version'           => PIF_VERSION,
					'plugin_locale'     => 'product-input-fields-for-woocommerce',
				)
			);
		}
	}

	// Initialize the license class.
	new Tyche_PIF_Pro_Deactivation();
}
