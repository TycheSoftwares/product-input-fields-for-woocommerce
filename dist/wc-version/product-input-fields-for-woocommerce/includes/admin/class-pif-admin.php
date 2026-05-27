<?php
/**
 * Product Input Fields for WooCommerce.
 *
 * Admin Base Class.
 *
 * @author      Tyche Softwares
 * @package     PIF/Admin
 * @category    Classes
 * @since       1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin Base Class.
 *
 * @since 1.0
 */
class PIF_Admin {

	/**
	 * Construct
	 *
	 * @since 1.0
	 */
	public function __construct() {
	}

	/**
	 * Checks if the user is on the Admin Section of the Plugin.
	 *
	 * @since 1.0
	 */
	public static function is_on_pif_page() {
		global $pagenow;
		return 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] && isset ( $_GET['tab'] ) &&  'product-input-fields-for-woocommerce' === $_GET['tab']; // phpcs:ignore
	}

	/**
	 * Checks if the user is on theWP Plugin Page.
	 *
	 * @since 1.0
	 */
	public static function is_on_wp_plugin_page() {
		global $pagenow;
		return 'plugins.php' === $pagenow; // phpcs:ignore
	}

	/**
	 * Checks if the user is on the Product Edit Page.
	 * 
	 * @since 1.0
	 */
	public static function is_on_product_page() {
		$screen = get_current_screen();
		return $screen && $screen->id === 'product'; // phpcs:ignore
	}
}