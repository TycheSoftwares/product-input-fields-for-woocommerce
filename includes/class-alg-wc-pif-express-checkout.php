<?php
/**
 * Product Input Fields - Blocks & Express Checkout Compatibility
 * Handles compatibility with WooCommerce Blocks Store API add-to-cart requests and Express Checkout buttons.
 *
 * @version 3.0.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Alg_WC_PIF_Express_Checkout' ) ) :

	/**
	 * Handles WooCommerce Blocks and Express Checkout compatibility for Product Input Fields.
	 *
	 * @version 3.0.0
	 */
	class Alg_WC_PIF_Express_Checkout {

		/**
		 * WC session key prefix.
		 */
		const SESSION_KEY_PREFIX = 'alg_wc_pif_express_';

		/**
		 * Constructor. Registers all hooks.
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			// AJAX handlers — save PIF field values to WC session from JS.
			add_action( 'wp_ajax_alg_wc_pif_save_for_express', array( $this, 'ajax_save_fields_to_session' ) );
			add_action( 'wp_ajax_nopriv_alg_wc_pif_save_for_express', array( $this, 'ajax_save_fields_to_session' ) );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'blocks_inject_from_session_early' ), 1, 2 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'blocks_inject_via_cart_item_data' ), 1, 3 );
			add_action( 'woocommerce_store_api_validate_add_to_cart', array( $this, 'blocks_validate_pif' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue the Blocks compatibility JS on single product pages.
		 *
		 * Passes field config to JS via wp_localize_script so PHP variables
		 * are available in the external JS file without any inline PHP.
		 *
		 * The JS file (alg-wc-pif-blocks.js) handles:
		 *  - Intercepting window.fetch() for Store API add-item calls (Blocks)
		 *  - Collecting all PIF field values
		 *  - Saving to WC session via synchronous XHR before the cart request fires
		 *  - Front-end required field validation with inline error display
		 *  - Click intercept fallback for classic add-to-cart and express checkout buttons
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 */
		public function enqueue_scripts() {
			if ( ! is_product() ) {
				return;
			}
			$product_id = get_queried_object_id();
			if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
				return;
			}
			$required_fields = $this->get_required_fields( $product_id );
			$all_fields      = $this->get_all_field_names( $product_id );

			wp_register_script(
				'alg-wc-pif-blocks',
				alg_wc_product_input_fields()->plugin_url() . '/includes/js/alg-wc-pif-blocks.js',
				array( 'jquery' ),
				ALG_WC_PIF_VERSION,
				true
			);

			wp_localize_script(
				'alg-wc-pif-blocks',
				'algPifBlocksConfig',
				array(
					'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
					'nonce'          => wp_create_nonce( 'alg_wc_pif_express_nonce' ),
					'productId'      => $product_id,
					'requiredFields' => $required_fields,
					'allFields'      => $all_fields,
					'storeApiUrl'    => '/wp-json/wc/store',
					'messages'       => array(
						'required' => __( 'Please fill in all required product fields before adding to cart.', 'product-input-fields-for-woocommerce' ),
					),
				)
			);

			wp_enqueue_script( 'alg-wc-pif-blocks' );
		}

		/**
		 * AJAX handler: receives PIF field values from JS and stores them in WC session.
		 * Expected $_POST params:
		 *  - nonce      : alg_wc_pif_express_nonce
		 *  - product_id : int
		 *  - pif_values : array of { field_name => value }
		 *  - pif_files  : array of { field_name => filename_string }
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 */
		public function ajax_save_fields_to_session() {
			if ( ! check_ajax_referer( 'alg_wc_pif_express_nonce', 'nonce', false ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce.' ) );
				return;
			}

			$product_id = intval( $_POST['product_id'] ?? 0 ); // phpcs:ignore
			$pif_values = isset( $_POST['pif_values'] ) ? $_POST['pif_values'] : array(); // phpcs:ignore
			$pif_files  = isset( $_POST['pif_files'] )  ? $_POST['pif_files']  : array(); // phpcs:ignore
			if ( ! $product_id ) {
				wp_send_json_error( array( 'message' => 'Missing product_id.' ) );
				return;
			}
			// Sanitize field values.
			$sanitized_values = array();
			if ( is_array( $pif_values ) ) {
				foreach ( $pif_values as $key => $value ) {
					$key = sanitize_key( $key );
					$sanitized_values[ $key ] = is_array( $value )
						? array_map( 'sanitize_text_field', $value )
						: sanitize_text_field( $value );
				}
			}

			// Sanitize file names (filenames only — no binary data via AJAX).
			$sanitized_files = array();
			if ( is_array( $pif_files ) ) {
				foreach ( $pif_files as $key => $value ) {
					$sanitized_files[ sanitize_key( $key ) ] = sanitize_file_name( $value );
				}
			}

			$data = array(
				'product_id' => $product_id,
				'values'     => $sanitized_values,
				'files'      => $sanitized_files,
				'timestamp'  => time(),
			);

			// Ensure WC session is initialized and a session cookie exists.
			if ( ! WC()->session ) {
				wc_load_cart();
			}
			if ( ! WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
			$session_key = self::SESSION_KEY_PREFIX . $product_id;
			WC()->session->set( $session_key, $data );
			wp_send_json_success( array( 'message' => 'Saved.', 'data' => $data ) );
		}

		/**
		 * Inject PIF session values into $_POST at priority 1 of woocommerce_add_to_cart_validation.
		 *
		 * @param  bool $passed     Whether the product passed validation so far.
		 * @param  int  $product_id The product being added to cart.
		 * @return bool             Unchanged $passed — we only inject, not validate here.
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 */
		public function blocks_inject_from_session_early( $passed, $product_id ) {
			if ( ! self::is_blocks_api_request() ) {
				return $passed;
			}
			$session_data = self::get_from_session( $product_id );
			if ( ! $session_data ) {
				return $passed;
			}
			foreach ( $session_data['values'] as $field_name => $value ) {
				$_POST[ $field_name ] = $value; // phpcs:ignore
			}
			return $passed;
		}

		/**
		 * Belt-and-suspenders: inject session values into $_POST at woocommerce_add_cart_item_data priority 1.
		 *
		 * Runs before add_product_input_fields_to_cart_item_data (which is at PHP_INT_MAX).
		 * Skips injection if $_POST already contains PIF keys (from blocks_inject_from_session_early).
		 * Clears the session entry after injection to avoid stale data on subsequent requests.
		 *
		 * @param  array $cart_item_data Existing cart item data passed through the filter.
		 * @param  int   $product_id     The product being added.
		 * @param  int   $variation_id   The variation ID (0 if not a variable product).
		 * @return array                 Unchanged $cart_item_data — we only inject into $_POST here.
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 */
		public function blocks_inject_via_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			if ( ! self::is_blocks_api_request() ) {
				return $cart_item_data;
			}
			$already_injected = false;
			foreach ( array_keys( $_POST ) as $key ) { // phpcs:ignore
				if ( strpos( $key, 'alg_wc_pif_' ) === 0 ) {
					$already_injected = true;
					break;
				}
			}
			if ( $already_injected ) {
				return $cart_item_data;
			}
			$session_data = self::get_from_session( $product_id );
			if ( $session_data ) {
				foreach ( $session_data['values'] as $field_name => $value ) {
					$_POST[ $field_name ] = $value; // phpcs:ignore
				}
				self::clear_session( $product_id );
			}

			return $cart_item_data;
		}

		/**
		 * Validate required PIF fields for WooCommerce Blocks Store API add-to-cart.
		 */
		public function blocks_validate_pif( $product, $request ) {
			$product_id   = $product->get_id();
			$session_data = self::get_from_session( $product_id );

			foreach ( array( 'global', 'local' ) as $scope ) {
				$total = $this->get_total( $scope, $product_id );

				for ( $i = 1; $i <= $total; $i++ ) {
					$field = alg_get_all_values( $scope, $i, $product_id );

					// Skip disabled or non-required fields.
					if ( 'yes' !== ( $field['enabled'] ?? '' ) || 'yes' !== ( $field['required'] ?? '' ) ) {
						continue;
					}

					$field_name  = ALG_WC_PIF_ID . '_' . $scope . '_' . $i;
					$field_value = '';

					// Try session first (written by JS).
					if ( $session_data && isset( $session_data['values'][ $field_name ] ) ) {
						$field_value = $session_data['values'][ $field_name ];
						if ( is_array( $field_value ) ) {
							$field_value = implode( '', $field_value );
						}
					}

					if ( '' === trim( (string) $field_value ) && isset( $_POST[ $field_name ] ) ) { // phpcs:ignore
						$field_value = $_POST[ $field_name ]; // phpcs:ignore
					}

					if ( '' === trim( (string) $field_value ) ) {
						$msg = str_replace(
							'%title%',
							$field['title'] ?? '',
							$field['required_message'] ?? '%title% is required.'
						);
						// Throwing here causes Blocks to return a 400 with this message.
						throw new \Exception( esc_html( $msg ) );
					}
				}
			}
		}

		/**
		 * Get all enabled PIF field definitions for a product.
		 */
		private function get_all_field_names( $product_id ) {
			$fields = array();
			foreach ( array( 'global', 'local' ) as $scope ) {
				$total = $this->get_total( $scope, $product_id );
				for ( $i = 1; $i <= $total; $i++ ) {
					$field = alg_get_all_values( $scope, $i, $product_id );
					if ( 'yes' !== ( $field['enabled'] ?? '' ) ) {
						continue;
					}
					$fields[] = array(
						'fieldName' => ALG_WC_PIF_ID . '_' . $scope . '_' . $i,
						'type'      => $field['type'] ?? 'text',
						'scope'     => $scope,
						'index'     => $i,
					);
				}
			}
			return $fields;
		}

		/**
		 * Get required PIF field definitions for a product.
		 */
		private function get_required_fields( $product_id ) {
			$fields = array();
			foreach ( array( 'global', 'local' ) as $scope ) {
				$total = $this->get_total( $scope, $product_id );
				for ( $i = 1; $i <= $total; $i++ ) {
					$field = alg_get_all_values( $scope, $i, $product_id );
					if ( 'yes' !== ( $field['enabled'] ?? '' ) || 'yes' !== ( $field['required'] ?? '' ) ) {
						continue;
					}
					$fields[] = array(
						'fieldName' => ALG_WC_PIF_ID . '_' . $scope . '_' . $i,
						'type'      => $field['type'] ?? 'text',
						'title'     => $field['title'] ?? '',
					);
				}
			}
			return $fields;
		}

		/**
		 * Get the total number of PIF fields configured for a given scope and product.
		 */
		private function get_total( $scope, $product_id ) {
			if ( 'local' === $scope ) {
				return intval( get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_local_total_number', true ) );
			}
			return intval( get_option( 'alg_wc_pif_global_total_number', 0 ) );
		}

		/**
		 * Read PIF session data for a product from WC session.
		 */
		public static function get_from_session( $product_id ) {
			if ( ! WC()->session ) {
				if ( function_exists( 'wc_load_cart' ) ) {
					wc_load_cart();
				}
			}
			if ( ! WC()->session ) {
				return null;
			}
			$key  = self::SESSION_KEY_PREFIX . $product_id;
			$data = WC()->session->get( $key );
			return $data ?: null;
		}

		/**
		 * Remove PIF session data for a product from WC session.
		 */
		public static function clear_session( $product_id ) {
			if ( WC()->session ) {
				WC()->session->__unset( self::SESSION_KEY_PREFIX . $product_id );
			}
		}

		/**
		 * Detect whether the current PHP request is a WooCommerce Blocks Store API call.
		 */
		public static function is_blocks_api_request() {
			$uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : ''; // phpcs:ignore
			return strpos( $uri, '/wc/store/v1/' ) !== false
				|| strpos( $uri, '/wc/store/' ) !== false;
		}
	}
endif;
