<?php // phpcs:ignore
/**
 * Plugin Name: Product Input Fields for WooCommerce
 * Plugin URI: https://www.tychesoftwares.com/store/premium-plugins/product-input-fields-for-woocommerce/
 * Description: Add custom frontend input fields to WooCommerce products.
 * Version: 2.0.0
 * Author: Tyche Softwares
 * Author URI: https://www.tychesoftwares.com
 * Text Domain: product-input-fields-for-woocommerce
 * Domain Path: /langs
 * Copyright: © 2021 Tyche Softwares
 * WC tested up to: 10.6.1
 * Tested up to: 6.9.4
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires Plugins: woocommerce
 *
 * @package Input Fields for WooCommerce Pro
 */

 defined( 'ABSPATH' ) || exit;

if ( ! defined( 'PIF_FILE' ) ) {
	define( 'PIF_FILE', __FILE__ );
}

// Include the Product Input Fields class.
if ( ! class_exists( 'Product_Input_Fields', false ) ) {
	include_once dirname( PIF_FILE ) . '/includes/class-product-input-fields.php';
}

/**
 * Returns the instance of PIF.
 *
 * @since  1.0
 */
function PIF() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return Product_Input_Fields::instance();
}

PIF();
