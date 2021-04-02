<?php
/**
 * Plugin Name: Product Input Fields for WooCommerce
 * Plugin URI: https://www.tychesoftwares.com/store/premium-plugins/product-input-fields-for-woocommerce/
 * Description: Add custom frontend input fields to WooCommerce products.
 * Version: 1.3.0
 * Author: Tyche Softwares
 * Author URI: https://www.tychesoftwares.com/
 * Text Domain: product-input-fields-for-woocommerce
 * Domain Path: /langs
 * Copyright: © 2020 Tyche Softwares
 * WC tested up to: 4.3.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package product-input-fields-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

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
	define( 'ALG_WC_PIF_VERSION', '1.3.0' );
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

			// Set up localisation.
			load_plugin_textdomain( 'product-input-fields-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

			// Include required files.
			$this->includes();

			// Settings & Scripts.
			if ( is_admin() ) {
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
