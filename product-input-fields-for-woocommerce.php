<?php
/**
 * Plugin Name: Product Input Fields for WooCommerce
 * Plugin URI: https://www.tychesoftwares.com/store/premium-plugins/product-input-fields-for-woocommerce/
 * Description: Add custom frontend input fields to WooCommerce products.
 * Version: 1.4.0
 * Author: Tyche Softwares
 * Author URI: https://www.tychesoftwares.com/
 * Text Domain: product-input-fields-for-woocommerce
 * Domain Path: /langs
 * Copyright: © 2021 Tyche Softwares
 * WC tested up to: 7.1
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package product-input-fields-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
use Automattic\WooCommerce\Utilities\OrderUtil;

require_once 'vendor/autoload.php';

/** Check if WooCommerce is active */
$plugin_name = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin_name, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin_name, get_site_option( 'active_sitewide_plugins', array() ) ) )
) {
	return;
}

// Constants.
if ( ! defined( 'ALG_WC_PIF_VERSION' ) ) {
	define( 'ALG_WC_PIF_VERSION', '1.4.0' );
}
if ( ! defined( 'ALG_WC_PIF_ID' ) ) {
	define( 'ALG_WC_PIF_ID', 'alg_wc_pif' );
}

if ( ! function_exists( 'get_wc_pif_option' ) ) {
	/**
	 * Get_wc_pif_option.
	 *
	 * @param string $option Option name.
	 * @param bool   $default Default value to return if option doesn't exist.
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_wc_pif_option( $option, $default = false ) {
		return get_option( ALG_WC_PIF_ID . '_' . $option, $default );
	}
}

if ( ! class_exists( 'Alg_WC_PIF' ) ) :

	/**
	 * Main Alg_WC_PIF Class
	 *
	 * @class   Alg_WC_PIF
	 * @version 1.2.1
	 * @since   1.0.0
	 */
	final class Alg_WC_PIF {

		/**
		 * Variable
		 *
		 * @var string Version.
		 * @access public
		 */
		public static $version = '1.3.3';

		/**
		 * Define an instance for the class.
		 *
		 * @var   Alg_WC_PIF The single instance of the class
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Main Alg_WC_PIF Instance
		 *
		 * Ensures only one instance of Alg_WC_PIF is loaded or can be loaded.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @static
		 * @return  Alg_WC_PIF - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Alg_WC_PIF Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @access  public
		 */
		public function __construct() {

			// Deactivation hook.
			register_deactivation_hook( __FILE__, array( &$this, 'pif_deactivate' ) );

			// Set up localisation.
			load_plugin_textdomain( 'product-input-fields-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

			// Include required files.
			$this->includes();

			// Settings & Scripts.
			if ( is_admin() ) {
				add_action( 'before_woocommerce_init', array( $this, 'pif_lite_custom_order_tables_compatibility' ), 999 );
				add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 1.2.1
		 * @since   1.0.0
		 * @param   mixed $links Link to settings from Plugins page.
		 * @return  array
		 */
		public function action_links( $links ) {
			$custom_links   = array();
			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_pif' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
			if ( 'product-input-fields-for-woocommerce.php' === basename( __FILE__ ) ) {
				$custom_links[] = '<a href="https://www.tychesoftwares.com/store/premium-plugins/product-input-fields-for-woocommerce/?utm_source=pifupgradetopro&utm_medium=unlockall&utm_campaign=ProductInputFieldsLite">' . __( 'Unlock All', 'product-input-fields-for-woocommerce' ) . '</a>';
			}
			return array_merge( $custom_links, $links );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 1.1.1
		 * @since   1.0.0
		 */
		public function includes() {

			require_once 'includes/component/plugin-tracking/class-tyche-plugin-tracking.php';
			new Tyche_Plugin_Tracking(
				array(
					'plugin_name'       => 'Product Input Fields for WooCommerce',
					'plugin_locale'     => 'product-input-fields-for-woocommerce',
					'plugin_short_name' => 'pif_lite',
					'version'           => ALG_WC_PIF_VERSION,
					'blog_link'         => 'https://www.tychesoftwares.com/docs/docs/product-input-fields-for-woocommerce/product-input-fields-usage-tracking',
				)
			);

			// Functions.
			require_once 'includes/alg-wc-pif-functions.php';
			// Settings.
			require_once 'includes/alg-wc-pif-options.php';
			require_once 'includes/admin/class-alg-wc-pif-per-product-metabox.php';
			require_once 'includes/admin/class-alg-wc-pif-settings-section.php';
			$this->settings = array();
			require_once 'includes/admin/class-alg-wc-pif-settings-all-products-field.php';
			$this->settings['general']      = require_once 'includes/admin/class-alg-wc-pif-settings-general.php';
			$this->settings['per-product']  = require_once 'includes/admin/class-alg-wc-pif-settings-per-product.php';
			$this->settings['all-products'] = require_once 'includes/admin/class-alg-wc-pif-settings-all-products.php';
			if ( 'yes' === get_wc_pif_option( 'global_enabled', 'yes' ) ) {
				$total_fields = apply_filters( 'alg_wc_product_input_fields', 1, 'all_products_total_fields' );
				for ( $i = 1; $i <= $total_fields; $i++ ) {
					$this->settings[ 'all-products-' . $i ] = new Alg_WC_PIF_Settings_All_Products_Field( $i );
				}
			}
			if ( is_admin() && get_wc_pif_option( 'version', '' ) !== ALG_WC_PIF_VERSION ) {
				foreach ( $this->settings as $section ) {
					foreach ( $section->get_settings() as $value ) {
						if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
							$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
							add_option( $value['id'], $value['default'], '', $autoload );
						}
					}
				}
				update_option( ALG_WC_PIF_ID . '_version', ALG_WC_PIF_VERSION );
			}
			// Core.
			require_once 'includes/class-alg-wc-pif-core.php';

			if ( is_admin() ) {

				require_once 'includes/class-pif-tracking-functions.php';
				add_filter( 'ts_tracker_data', array( __CLASS__, 'pif_lite_ts_add_plugin_tracking_data' ), 10, 1 );

				add_action( 'admin_footer', array( __CLASS__, 'ts_admin_notices_scripts' ) );
				add_action( 'pif_lite_init_tracker_completed', array( __CLASS__, 'init_tracker_completed' ), 10 );
				add_filter( 'pif_lite_ts_tracker_display_notice', array( __CLASS__, 'pif_ts_tracker_display_notice' ), 10, 1 );

				$pif_plugin_url = plugins_url() . '/product-input-fields-for-woocommerce';

				// plugin deactivation.
				require_once 'includes/class-tyche-plugin-deactivation.php';
				new Tyche_Plugin_Deactivation(
					array(
						'plugin_name'       => 'Product Input Fields for WooCommerce',
						'plugin_base'       => 'product-input-fields-for-woocommerce/product-input-fields-for-woocommerce.php',
						'script_file'       => $pif_plugin_url . '/includes/js/plugin-deactivation.js',
						'plugin_short_name' => 'pif_lite',
						'version'           => ALG_WC_PIF_VERSION,
					)
				);
			}
		}

		/**
		 * Send the plugin data when the user has opted in
		 *
		 * @hook ts_tracker_data
		 * @param array $data All data to send to server.
		 *
		 * @return array $plugin_data All data to send to server.
		 */
		public static function pif_lite_ts_add_plugin_tracking_data( $data ) {
			$plugin_short_name = 'pif_lite';
			if ( ! isset( $_GET[ $plugin_short_name . '_tracker_nonce' ] ) ) {
				return $data;
			}

			$tracker_option = isset( $_GET[ $plugin_short_name . '_tracker_optin' ] ) ? $plugin_short_name . '_tracker_optin' : ( isset( $_GET[ $plugin_short_name . '_tracker_optout' ] ) ? $plugin_short_name . '_tracker_optout' : '' ); // phpcs:ignore
			if ( '' === $tracker_option || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ $plugin_short_name . '_tracker_nonce' ] ) ), $tracker_option ) ) {
				return $data;
			}

			$data = pif_Tracking_Functions::pif_lite_plugin_tracking_data( $data );
			return $data;
		}

		/**
		 * Add admin notice script.
		 */
		public static function ts_admin_notices_scripts() {

			$pif_plugin_url = plugins_url() . '/product-input-fields-for-woocommerce';

			wp_enqueue_script(
				'pif_ts_dismiss_notice',
				plugins_url( '/includes/js/tyche-dismiss-tracking-notice.js', __FILE__ ),
				'',
				ALG_WC_PIF_VERSION,
				false
			);

			wp_localize_script(
				'pif_ts_dismiss_notice',
				'pif_ts_dismiss_notice',
				array(
					'ts_prefix_of_plugin' => 'pif_lite',
					'ts_admin_url'        => admin_url( 'admin-ajax.php' ),
				)
			);

		}

		/**
		 * Add tracker completed.
		 */
		public static function init_tracker_completed() {
			$redirect_url = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : admin_url( 'admin.php?page=wc-settings&tab=alg_wc_pif' );
			header( 'Location: ' . $redirect_url );
			exit;
		}

		/**
		 * Display admin notice on specific page.
		 *
		 * @param array $is_flag Is Flag defailt value true.
		 */
		public static function pif_ts_tracker_display_notice( $is_flag ) {
			global $current_section;

			if ( isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] ) { // phpcs:ignore
				$is_flag = false;
				if ( isset( $_GET['tab'] ) && 'alg_wc_pif' === $_GET['tab'] && empty( $current_section ) ) { // phpcs:ignore
					$is_flag = true;
				}
			}

			return $is_flag;
		}

		/**
		 * Add Product Input Fields settings tab to WooCommerce settings.
		 *
		 * @param array $settings Add the settings page in WooCommerce settings.
		 * @version 1.2.1
		 * @since   1.0.0
		 */
		public function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once 'includes/admin/class-alg-wc-settings-pif.php';
			return $settings;
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @return  string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @return  string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
		/**
		 * Sets the compatibility with Woocommerce HPOS.
		 *
		 * @since 1.4.0
		 */
		public function pif_lite_custom_order_tables_compatibility() {

			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'product-input-fields-for-woocommerce/product-input-fields-for-woocommerce.php', true );
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'orders_cache', 'product-input-fields-for-woocommerce/product-input-fields-for-woocommerce.php', true );
			}
		}

		/**
		 * Actions to be performed when the plugin is deactivate.
		 *
		 * @since 1.3.3
		 */
		public function pif_deactivate() {
			if ( false !== as_next_scheduled_action( 'ts_send_data_tracking_usage' ) ) {
				as_unschedule_action( 'ts_send_data_tracking_usage' ); // Remove the scheduled action.
			}
			do_action( 'pif_deactivate' );
		}
	}

endif;

if ( ! function_exists( 'alg_wc_product_input_fields' ) ) {
	/**
	 * Returns the main instance of Alg_WC_PIF to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_PIF
	 */
	function alg_wc_product_input_fields() {
		return Alg_WC_PIF::instance();
	}
}

alg_wc_product_input_fields();
