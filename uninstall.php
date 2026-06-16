<?php
/**
 * Currency Per Product Pro Uninstall
 *
 * Deletes all the settings for the plugin from the database when plugin is uninstalled.
 *
 * @author      Tyche Softwares
 * @package Input Fields for WooCommerce Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( file_exists( WP_PLUGIN_DIR . '/product-input-fields-for-woocommerce-pro/product-input-fields-for-woocommerce-pro.php' ) ) {
	return;
}

global $wpdb;

$results = $wpdb->get_results( "SELECT option_name FROM `{$wpdb->prefix}options` WHERE option_name LIKE 'alg_wc_pif%'" );
foreach ( $results as $key => $value ) {
	delete_option( $value->option_name );
}

delete_option( 'pif_general_settings' );
delete_option( 'pif_field_settings' );