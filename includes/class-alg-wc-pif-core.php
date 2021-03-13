<?php
/**
 * Product Input Fields for WooCommerce - Core Class
 *
 * @version 1.1.8
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 *
 * @package product-input-fields-for-woocommerce/Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PIF_ASSETS_VERSION', '1.2.5' );

if ( ! class_exists( 'Alg_WC_PIF_Core' ) ) :

	/**
	 * Core functions
	 */
	class Alg_WC_PIF_Core {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			if ( 'yes' === get_wc_pif_option( 'enabled', 'yes' ) ) {
				$position = get_wc_pif_option( 'frontend_position', 'woocommerce_before_add_to_cart_button' );
				$priority = get_wc_pif_option( 'frontend_position_priority', 10 );
				if ( 'disable' !== $position ) {
					add_action( $position, array( $this, 'add_before_product_input_fields_to_frontend' ), $priority );
				}
				require_once 'class-alg-wc-pif-main.php';
				$global = new Alg_WC_PIF_Main( 'global' );
				$local  = new Alg_WC_PIF_Main( 'local' );
				if ( 'disable' !== $position ) {
					add_action( $position, array( $this, 'add_after_product_input_fields_to_frontend' ), $priority );
				}
				add_action( 'woocommerce_delete_order_items', array( $this, 'delete_order_file_uploads' ) );
				add_action( 'woocommerce_before_delete_order_item', array( $this, 'delete_item_file_uploads' ) );
				add_action( 'admin_init', array( $this, 'handle_downloads' ) );
				if ( 'yes' === get_wc_pif_option( 'global_enabled', 'yes' ) || 'yes' === get_wc_pif_option( 'local_enabled', 'yes' ) ) {
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
				}
			}
		}

		/**
		 * Add_before_product_input_fields_to_frontend.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @todo    (later) output only if there are any product input fields to display; same to add_after_product_input_fields_to_frontend(); same to alg_display_product_input_fields()
		 */
		public function add_before_product_input_fields_to_frontend() {
			global $product;
			$frontend_html       = false;
			$product_id          = $product->get_id();
			$input_counts_local  = get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_local_total_number', true );
			$input_counts_global = get_option( 'alg_wc_pif_global_total_number', 0 );
			for ( $i = 1; $i <= $input_counts_global; $i++ ) {
				if ( 'yes' === get_option( 'alg_wc_pif_enabled_global_' . $i ) ) {
					$frontend_html = true;
				}
			}
			for ( $i = 1; $i <= $input_counts_local; $i++ ) {
				if ( 'yes' === get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_enabled_local_' . $i, true ) ) {
					$frontend_html = true;
				}
			}
			if ( $frontend_html ) {
				echo wp_kses_post( get_wc_pif_option( 'frontend_before', '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">' ) );

			} else {
				echo ( '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">' );
			}
		}

		/**
		 * Add_after_product_input_fields_to_frontend.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function add_after_product_input_fields_to_frontend() {
			global $product;
			$frontend_html       = false;
			$product_id          = $product->get_id();
			$input_counts_local  = get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_local_total_number', true );
			$input_counts_global = get_option( 'alg_wc_pif_global_total_number', 0 );
			for ( $i = 1; $i <= $input_counts_global; $i++ ) {
				if ( 'yes' === get_option( 'alg_wc_pif_enabled_global_' . $i ) ) {
					$frontend_html = true;
				}
			}
			for ( $i = 1; $i <= $input_counts_local; $i++ ) {
				if ( 'yes' === get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_enabled_local_' . $i, true ) ) {
					$frontend_html = true;
				}
			}
			if ( $frontend_html ) {
				echo wp_kses_post( get_wc_pif_option( 'frontend_after', '</table>' ) );

			} else {
				echo ( '</table>' );
			}
		}

		/**
		 * Delete_file_uploads.
		 *
		 * @param int $item_id Order Item ID.
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function delete_item_file_uploads( $item_id ) {
			$scopes = array( 'global', 'local' );
			foreach ( $scopes as $scope ) {
				$product_input_fields = wc_get_order_item_meta( $item_id, '_' . ALG_WC_PIF_ID . '_' . $scope );
				if ( $product_input_fields ) {
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
		 * Delete_order_file_uploads.
		 *
		 * @param int $postid Order ID.
		 * @version 1.1.4
		 * @since   1.0.0
		 */
		public function delete_order_file_uploads( $postid ) {
			$_order = wc_get_order( $postid );
			if ( ! $_order ) {
				return;
			}
			$_items = $_order->get_items();
			$scopes = array( 'global', 'local' );
			foreach ( $scopes as $scope ) {
				foreach ( $_items as $item ) {
					$product_input_fields = maybe_unserialize( $item[ ALG_WC_PIF_ID . '_' . $scope ] );
					if ( ! $product_input_fields || empty( $product_input_fields ) ) {
						return;
					}
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
		 * Handle_downloads.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function handle_downloads() {
			if ( current_user_can( 'edit_posts' ) && isset( $_GET['alg_wc_pif_download_file'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification

				$file_name  = sanitize_text_field( wp_unslash( $_GET['alg_wc_pif_download_file'] ) ); // phpcs:ignore
				$file_name  = preg_replace( '/..\//', '', $file_name );
				$file_name  = preg_replace( '/.\//', '', $file_name );
				$file_array = explode( '/', $file_name );
				$file_type  = wp_check_filetype( $file_array[ count( $file_array ) - 1 ] );
				$upload_dir = alg_get_uploads_dir( 'product_input_fields' );

				if ( '' !== $file_type['ext'] && file_exists( $upload_dir . '/' . $file_name ) ) {
					$file_path = $upload_dir . '/' . $file_name;
					header( 'Expires: 0' );
					header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
					header( 'Cache-Control: private', false );
					header( 'Content-disposition: attachment; filename=' . $file_name );
					header( 'Content-Transfer-Encoding: binary' );
					header( 'Content-Length: ' . filesize( $file_path ) );
					readfile( $file_path );
					exit();
				}
			}
		}

		/**
		 * Gets browser name
		 *
		 * @version 1.1.7
		 * @since   1.1.7
		 * @return string
		 */
		public function get_browser_name() {
			$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : ''; // phpcs:ignore
			if ( strpos( $user_agent, 'Opera' ) || strpos( $user_agent, 'OPR/' ) ) {
				return 'Opera';
			} elseif ( strpos( $user_agent, 'Edge' ) ) {
				return 'Edge';
			} elseif ( strpos( $user_agent, 'Chrome' ) ) {
				return 'Chrome';
			} elseif ( strpos( $user_agent, 'Safari' ) ) {
				return 'Safari';
			} elseif ( strpos( $user_agent, 'Firefox' ) ) {
				return 'Firefox';
			} elseif ( strpos( $user_agent, 'MSIE' ) || strpos( $user_agent, 'Trident/7' ) ) {
				return 'Internet Explorer';
			}

			return 'Other';
		}

		/**
		 * Current browser can render color type?
		 *
		 * @version 1.1.7
		 * @since   1.1.7
		 * @return bool
		 */
		public function browser_can_render_color_type() {
			if ( $this->get_browser_name() === 'Safari' || $this->get_browser_name() === 'Opera' ) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 * Enqueue_scripts.
		 *
		 * @version 1.1.8
		 * @since   1.0.0
		 */
		public function enqueue_scripts() {
			if ( ! is_product() ) {
				return;
			}

			if ( ! $this->browser_can_render_color_type() ) {
				wp_enqueue_script( 'spectrum', '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js', array( 'jquery' ), PIF_ASSETS_VERSION, false );
				wp_enqueue_style( 'spectrum', '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css', '', PIF_ASSETS_VERSION );
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

			// Timepicker style.
			if ( 'yes' === get_wc_pif_option( 'frontend_enqueue_timepicker_style', 'yes' ) ) {
				wp_enqueue_style( 'jquery-ui-timepicker', '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css', '', PIF_ASSETS_VERSION );
			}

			// Datepicker style.
			if ( 'yes' === get_wc_pif_option( 'frontend_enqueue_datepicker_style', 'yes' ) ) {
				wp_enqueue_style( 'jquery-ui-datepicker', '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css', '', PIF_ASSETS_VERSION );
			}

		}

	}

endif;

return new Alg_WC_PIF_Core();
