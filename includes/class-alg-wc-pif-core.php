<?php
/**
 * Product Input Fields for WooCommerce - Core Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PIF_Core' ) ) :

class Alg_WC_PIF_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		if ( 'yes' === get_wc_pif_option( 'enabled', 'yes' ) ) {
			$position = get_wc_pif_option( 'frontend_position', 'woocommerce_before_add_to_cart_button' );
			$priority = get_wc_pif_option( 'frontend_position_priority', 10 );
			if ( 'disable' != $position ) {
				add_action( $position, array( $this, 'add_before_product_input_fields_to_frontend' ), $priority );
			}
			require_once( 'class-alg-wc-pif-main.php' );
			$global = new Alg_WC_PIF_Main( 'global' );
			$local  = new Alg_WC_PIF_Main( 'local' );
			if ( 'disable' != $position ) {
				add_action( $position, array( $this, 'add_after_product_input_fields_to_frontend' ),  $priority );
			}
			add_action( 'woocommerce_delete_order_items',       array( $this, 'delete_order_file_uploads' ) );
			add_action( 'woocommerce_before_delete_order_item', array( $this, 'delete_item_file_uploads' ) );
			add_action( 'admin_init',                           array( $this, 'handle_downloads' ) );
			if ( 'yes' === get_wc_pif_option( 'global_enabled', 'yes' ) || 'yes' === get_wc_pif_option( 'local_enabled', 'yes' ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}
		}
	}

	/**
	 * add_before_product_input_fields_to_frontend.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @todo    (later) output only if there are any product input fields to display; same to add_after_product_input_fields_to_frontend(); same to alg_display_product_input_fields()
	 */
	function add_before_product_input_fields_to_frontend() {
		echo get_wc_pif_option( 'frontend_before', '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">' );
	}

	/**
	 * add_after_product_input_fields_to_frontend.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_after_product_input_fields_to_frontend() {
		echo get_wc_pif_option( 'frontend_after', '</table>' );
	}

	/**
	 * delete_file_uploads.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function delete_item_file_uploads( $item_id ) {
		$scopes = array( 'global', 'local' );
		foreach ( $scopes as $scope ) {
			if ( $product_input_fields = wc_get_order_item_meta( $item_id, '_' . ALG_WC_PIF_ID . '_' . $scope ) ) {
				$product_input_fields = maybe_unserialize( $product_input_fields );
				foreach ( $product_input_fields as $product_input_field ) {
					if ( 'file' === $product_input_field['type'] ) {
						$_value = maybe_unserialize( $product_input_field['_value'] );
						if ( isset( $_value['_tmp_name'] ) ) {
							unlink( $_value['_tmp_name'] );
						}
					}
				}
			}
		}
	}

	/**
	 * delete_order_file_uploads.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function delete_order_file_uploads( $postid ) {
		$_order = wc_get_order( $postid );
		$_items = $_order->get_items();
		$scopes = array( 'global', 'local' );
		foreach ( $scopes as $scope ) {
			foreach ( $_items as $item ) {
				$product_input_fields = maybe_unserialize( $item[ ALG_WC_PIF_ID . '_' . $scope ] );
				foreach ( $product_input_fields as $product_input_field ) {
					if ( 'file' === $product_input_field['type'] ) {
						$_value = maybe_unserialize( $product_input_field['_value'] );
						if ( isset( $_value['_tmp_name'] ) ) {
							unlink( $_value['_tmp_name'] );
						}
					}
				}
			}
		}
	}

	/**
	 * handle_downloads.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function handle_downloads() {
		if ( isset ( $_GET['alg_wc_pif_download_file'] ) ) {
			$file_name = $_GET['alg_wc_pif_download_file'];
			$upload_dir = alg_get_uploads_dir( 'product_input_fields' );
			$file_path = $upload_dir . '/' . $file_name;
			header( "Expires: 0" );
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header( "Cache-Control: private", false );
			header( 'Content-disposition: attachment; filename=' . $file_name );
			header( "Content-Transfer-Encoding: binary" );
			header( "Content-Length: ". filesize( $file_path ) );
			readfile( $file_path );
			exit();
		}
	}

	/**
	 * enqueue_scripts.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function enqueue_scripts() {
		if( ! is_product() ) {
			return;
		}
		wp_enqueue_script( 'jquery-ui-datepicker' );
		$scripts = array(
			'alg-datepicker'              => 'alg-datepicker.js',
			'alg-weekpicker'              => 'alg-weekpicker.js',
			'jquery-ui-timepicker'        => 'jquery.timepicker.min.js',
			'alg-timepicker'              => 'alg-timepicker.js',
			'alg-wc-product-input-fields' => 'alg-wc-product-input-fields.js',
		);
		foreach ( $scripts as $script_id => $script_file ) {
			wp_enqueue_script(
				$script_id,
				alg_wc_product_input_fields()->plugin_url() . '/includes/js/' . $script_file,
				array( 'jquery' ),
				ALG_WC_PIF_VERSION,
				true
			);
		}
	}

}

endif;

return new Alg_WC_PIF_Core();
