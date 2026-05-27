<?php
/**
 * Product Input Fields for WooCommerce Pro - Tracking Class
 *
 * @version 1.1.7
 * @since   1.1.3
 * @author  Tyche Softwares
 * @package Input Fields for WooCommerce Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Tyche_PIF_Pro_Tracking' ) ) {

	/** Declaration of Class */
	class Tyche_PIF_Pro_Tracking {

		const TRACKING_KEY          = 'pif_allow_tracking';
		const TRACKER_LAST_SEND_KEY = 'ts_tracker_last_send';

		/** Constructor */
		public function __construct() {
			require_once __DIR__ . '/tyche/components/plugin-tracking/class-tyche-pif-plugin-tracking.php';
			require_once __DIR__ . '/tyche/components/plugin-tracking/class-tyche-plugin-tracking.php';
			new Tyche_Plugin_Tracking(
				array(
					'plugin_name'       => 'Product Input Fields for WooCommerce Pro',
					'plugin_locale'     => 'product-input-fields-for-woocommerce-pro',
					'plugin_short_name' => 'pif',
					'version'           => PIF_VERSION,
					'blog_link'         => 'https://www.tychesoftwares.com/docs/docs/product-input-fields-for-woocommerce/product-input-fields-usage-tracking',
				)
			);
		}

		/**
		 * Register tracking options.
		 */
		public static function register_settings( $args ) {
			register_setting( 'options', self::TRACKING_KEY, $args );
			register_setting( 'options', self::TRACKER_LAST_SEND_KEY, $args );
		}
	}

	// Initialize the license class.
	new Tyche_PIF_Pro_Tracking();
}
