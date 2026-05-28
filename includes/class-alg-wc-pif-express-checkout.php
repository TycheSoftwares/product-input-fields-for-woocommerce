<?php
/**
 * Product Input Fields - Blocks & Express Checkout Compatibility
 *
 * ROOT CAUSE: WooCommerce Blocks uses /wp-json/wc/store/v1/cart/add-item
 * (REST API JSON) instead of classic $_POST. So $_POST is always empty.
 *
 * SOLUTION:
 *  1. Intercept fetch() calls to the Store API URL in JS (Blocks uses fetch, not forms).
 *  2. Before the fetch fires, save field values to WC session via AJAX.
 *  3. blocks_inject_pif_into_cart() hook reads from session, injects into $_POST.
 *  4. Existing add_product_input_fields_to_cart_item_data reads $_POST as normal.
 *
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Alg_WC_PIF_Express_Checkout' ) ) :

	/**
	 * Class Alg_WC_PIF_Express_Checkout.
	 *
	 * @version 3.0.0
	 */
	class Alg_WC_PIF_Express_Checkout {

		const SESSION_KEY_PREFIX = 'alg_wc_pif_express_';

		public function __construct() {
			add_action( 'wp_ajax_alg_wc_pif_save_for_express',        array( $this, 'ajax_save_fields_to_session' ) );
			add_action( 'wp_ajax_nopriv_alg_wc_pif_save_for_express', array( $this, 'ajax_save_fields_to_session' ) );

			// Blocks Store API hooks.
			add_filter( 'woocommerce_add_to_cart_validation',         array( $this, 'blocks_inject_from_session_early' ), 1, 2 );
			add_action( 'woocommerce_store_api_validate_add_to_cart', array( $this, 'blocks_validate_pif' ),               10, 2 );
			add_filter( 'woocommerce_add_cart_item_data',             array( $this, 'blocks_inject_via_cart_item_data' ),  1, 3 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		// ─────────────────────────────────────────────────────────
		// JS
		// ─────────────────────────────────────────────────────────

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

			// Pass PHP config to JS via wp_localize_script — avoids heredoc variable conflicts.
			wp_register_script( 'alg-wc-pif-blocks', false, array( 'jquery' ), ALG_WC_PIF_VERSION, true );
			wp_enqueue_script( 'alg-wc-pif-blocks' );
			wp_localize_script( 'alg-wc-pif-blocks', 'algPifBlocksConfig', array(
				'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'alg_wc_pif_express_nonce' ),
				'productId'      => $product_id,
				'requiredFields' => $required_fields,
				'allFields'      => $all_fields,
				'storeApiUrl'    => '/wp-json/wc/store',
				'messages'       => array(
					'required' => __( 'Please fill in all required product fields before adding to cart.', 'product-input-fields-for-woocommerce' ),
				),
			) );

			// Inline JS as a separate enqueue — no heredoc, no PHP variable conflicts.
			$js = $this->get_js();
			wp_add_inline_script( 'alg-wc-pif-blocks', $js );
		}

		private function get_js() {
			return <<<'ENDJS'
			(function($) {
				'use strict';

				if (typeof algPifBlocksConfig === 'undefined') {
					return;
				}

				var PIF = {
					config: algPifBlocksConfig,
					sessionSaved: false,
					fetchPatched: false,

					init: function() {
						if (!this.config.allFields || this.config.allFields.length === 0) {
							return;
						}

						this.patchFetch();
						this.interceptClicks();
					},

					/**
					 * Patch window.fetch to intercept Store API add-item calls.
					 * WooCommerce Blocks uses fetch() internally — this is the most reliable intercept.
					 */
					patchFetch: function() {
						if (this.fetchPatched) return;
						this.fetchPatched = true;

						var self = this;
						var originalFetch = window.fetch;

						window.fetch = function(url, options) {
							var urlStr = (typeof url === 'string') ? url : (url && url.url ? url.url : String(url));

							// Only intercept Store API add-item calls.
							if (urlStr.indexOf('/wc/store') !== -1 && urlStr.indexOf('add-item') !== -1) {
								
								// Validate required fields before proceeding.
								if (!self.validate()) {
									// Return a rejected promise to stop Blocks from proceeding.
									return Promise.reject(new Error('PIF required fields missing.'));
								}

								// Capture url + options in closure BEFORE entering the Promise.
								// Inside .then()/.catch(), `arguments` refers to the Promise callback,
								// not the outer fetch(url, options) — so we must close over them explicitly.
								var capturedUrl     = url;
								var capturedOptions = options;

								return self.saveToSessionAsync().then(function() {
									return originalFetch.call(window, capturedUrl, capturedOptions);
								}).catch(function(err) {
									return originalFetch.call(window, capturedUrl, capturedOptions);
								});
							}

							// All other fetch calls — pass through unchanged.
							return originalFetch.call(window, url, options);
						};
					},

					/**
					 * Also intercept clicks as a fallback (for themes using classic add-to-cart).
					 */
					interceptClicks: function() {
						var self = this;

						// Capture phase — fires before any other handler.
						document.addEventListener('click', function(e) {
							var t = e.target;

							// Check if it's an add-to-cart button.
							var isCart = (
								t.classList.contains('single_add_to_cart_button') ||
								t.classList.contains('add_to_cart_button') ||
								(t.type === 'submit' && $(t).closest('form.cart').length) ||
								$(t).closest('.wp-block-add-to-cart-form').length
							);

							if (!isCart) return;
							if (self.sessionSaved) return;

							if (!self.validate()) {
								e.preventDefault();
								e.stopImmediatePropagation();
								return false;
							}

							// For classic (non-Blocks) add-to-cart — save synchronously.
							self.saveToSessionXhr();

						}, true); // useCapture
					},

					validate: function() {
						var required = this.config.requiredFields;
						if (!required || required.length === 0) return true;

						for (var i = 0; i < required.length; i++) {
							var f = required[i];
							var val = this.getFieldValue(f.fieldName, f.type);
							if (!val || val.toString().trim() === '') {
								var msg = this.config.messages.required;
								if (f.title) msg += ': ' + f.title;
								this.showError(msg);
								return false;
							}
						}
						return true;
					},

					getFieldValue: function(fieldName, type) {
						if (type === 'file') {
							var el = document.querySelector('[name="' + fieldName + '"]');
							return (el && el.files && el.files.length) ? el.files[0].name : '';
						}
						if (type === 'checkbox') {
							// Must target type="checkbox" specifically.
							// PIF renders a hidden input (value="no") + a checkbox (value="yes")
							// with the same name. querySelector returns the hidden one first — always "no".
							var cb = document.querySelector('input[type="checkbox"][name="' + fieldName + '"]');
							return (cb && cb.checked) ? 'yes' : '';
						}
						// text, textarea, select, radio, etc.
						var els = document.querySelectorAll('[name="' + fieldName + '"], [name="' + fieldName + '[]"]');
						if (!els.length) return '';
						// Multi-select or checkboxes.
						var vals = [];
						els.forEach(function(el) {
							if ((el.type === 'radio' || el.type === 'checkbox') && !el.checked) return;
							if (el.value) vals.push(el.value);
						});
						return vals.join('');
					},

					collectValues: function() {
						var values = {};
						var files  = {};

						(this.config.allFields || []).forEach(function(f) {
							var name = f.fieldName;

							if (f.type === 'file') {
								// File input — get filename only (binary not sendable via AJAX).
								var el = document.querySelector('[name="' + name + '"]');
								files[name] = (el && el.files && el.files.length) ? el.files[0].name : '';

							} else if (f.type === 'checkbox') {
								// PIF renders hidden input (value="no") + checkbox (value="yes") with same name.
								// querySelector returns hidden first — always "no". Must target type=checkbox.
								var cb = document.querySelector('input[type="checkbox"][name="' + name + '"]');
								values[name] = (cb && cb.checked) ? 'yes' : 'no';
							} else if (f.type === 'color') {
								// Color pickers (like Spectrum) replace the native input with a custom widget.
								// The original <input type="color"> or <input type="text"> is hidden/replaced.
								// We try multiple strategies to get the chosen color value.

								var colorVal = '';

								// Strategy 1: original input still in DOM (native color picker, non-Safari).
								var colorInput = document.querySelector('input[type="color"][name="' + name + '"]')
									|| document.querySelector('input[type="text"][name="' + name + '"]')
									|| document.querySelector('input[name="' + name + '"]');

								if (colorInput && colorInput.value) {
									colorVal = colorInput.value;
								}

								// Strategy 2: Spectrum stores color on the hidden input it generates.
								// Spectrum sets value on the original input — so strategy 1 should work.
								// But if input is type="hidden" (Spectrum replaces it), try the sp-preview.
								if (!colorVal) {
									var spPreview = document.querySelector('.sp-preview-inner');
									if (spPreview) {
										var bg = spPreview.style.backgroundColor;
										// Convert rgb(r,g,b) → hex if needed.
										if (bg) {
											colorVal = bg; // Store as-is; server can handle rgb too.
										}
									}
								}

								values[name] = colorVal || '';

							} else {
								// text, textarea, select, number, date, etc.
								var input = document.querySelector('[name="' + name + '"]');
								if (!input) {
									// Try multi-select / checkbox-group with [] suffix.
									var inputs = document.querySelectorAll('[name="' + name + '[]"]');
									var arr = [];
									inputs.forEach(function(el) {
										if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
										if (el.value) arr.push(el.value);
									});
									values[name] = arr.length ? arr : '';
								} else {
									values[name] = input.value || '';
								}
							}
						});

						return { values: values, files: files };
					},

					/**
					 * Save to session via Promise (used with fetch intercept).
					 */
					saveToSessionAsync: function() {
						var data = this.collectValues();
						var self = this;
						var config = this.config;

						return new Promise(function(resolve, reject) {
							var xhr = new XMLHttpRequest();
							xhr.open('POST', config.ajaxUrl, false); // sync XHR
							xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

							var params = self.buildParams({
								action:     'alg_wc_pif_save_for_express',
								nonce:      config.nonce,
								product_id: config.productId,
								pif_values: data.values,
								pif_files:  data.files,
							});

							xhr.onload = function() {
								self.sessionSaved = true;
								setTimeout(function() { self.sessionSaved = false; }, 5000);
								resolve();
							};
							xhr.onerror = function() {
								reject(new Error('XHR failed'));
							};
							xhr.send(params);
						});
					},

					/**
					 * Save to session synchronously (used with click intercept for classic checkout).
					 */
					saveToSessionXhr: function() {
						var data   = this.collectValues();
						var config = this.config;
						var self   = this;

						var xhr = new XMLHttpRequest();
						xhr.open('POST', config.ajaxUrl, false); // sync
						xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						var params = this.buildParams({
							action:     'alg_wc_pif_save_for_express',
							nonce:      config.nonce,
							product_id: config.productId,
							pif_values: data.values,
							pif_files:  data.files,
						});
						xhr.send(params);
						self.sessionSaved = true;
						setTimeout(function() { self.sessionSaved = false; }, 5000);
					},

					/**
					 * Serialize a nested object to URL-encoded form params.
					 */
					buildParams: function(obj, prefix) {
						var parts = [];
						for (var key in obj) {
							if (!obj.hasOwnProperty(key)) continue;
							var fullKey = prefix ? prefix + '[' + key + ']' : key;
							var val = obj[key];
							if (val !== null && typeof val === 'object' && !Array.isArray(val)) {
								parts.push(this.buildParams(val, fullKey));
							} else if (Array.isArray(val)) {
								val.forEach(function(v) {
									parts.push(encodeURIComponent(fullKey + '[]') + '=' + encodeURIComponent(v));
								});
							} else {
								parts.push(encodeURIComponent(fullKey) + '=' + encodeURIComponent(val === null || val === undefined ? '' : val));
							}
						}
						return parts.join('&');
					},

					showError: function(msg) {
						var old = document.querySelector('.alg-pif-blocks-error');
						if (old) old.parentNode.removeChild(old);

						var notice = document.createElement('div');
						notice.className = 'woocommerce-error alg-pif-blocks-error';
						notice.style.cssText = 'margin:10px 0;padding:10px;border:1px solid red;color:red;background:#fff0f0;';
						notice.textContent = msg;

						var table = document.querySelector('.alg-product-input-fields-table');
						var form  = document.querySelector('form.cart');
						var anchor = table || form;
						if (anchor && anchor.parentNode) {
							anchor.parentNode.insertBefore(notice, anchor);
						} else {
							document.body.prepend(notice);
						}
						notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
					}
				};

				// Wait for DOM + scripts to be ready.
				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', function() { PIF.init(); });
				} else {
					PIF.init();
				}

			})(jQuery);
			ENDJS;
		}

		// ─────────────────────────────────────────────────────────
		// AJAX — save field values to WC session
		// ─────────────────────────────────────────────────────────
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
			$sanitized_values = array();
			if ( is_array( $pif_values ) ) {
				foreach ( $pif_values as $key => $value ) {
					$key = sanitize_key( $key );
					$sanitized_values[ $key ] = is_array( $value )
						? array_map( 'sanitize_text_field', $value )
						: sanitize_text_field( $value );
				}
			}
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

		// ─────────────────────────────────────────────────────────
		// Blocks Store API hooks
		// ─────────────────────────────────────────────────────────

		/**
		 * Fires at priority 1 of woocommerce_add_to_cart_validation — before everything else.
		 * Injects session-stored PIF values into $_POST so that the existing
		 * add_product_input_fields_to_cart_item_data() method reads them normally.
		 *
		 * This hook DOES fire for Blocks Store API requests.
		 */
		public function blocks_inject_from_session_early( $passed, $product_id ) {
			if ( ! self::is_blocks_api_request() ) {
				return $passed; // Classic checkout — $_POST already has values, leave it alone.
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
		 * Belt-and-suspenders: also inject at woocommerce_add_cart_item_data priority 1.
		 * Runs BEFORE add_product_input_fields_to_cart_item_data (which is at PHP_INT_MAX).
		 */
		public function blocks_inject_via_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			if ( ! self::is_blocks_api_request() ) {
				return $cart_item_data;
			}
			// If $_POST already has PIF values (from blocks_inject_from_session_early), skip.
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
			// Try session as fallback.
			$session_data = self::get_from_session( $product_id );
			if ( $session_data ) {
				foreach ( $session_data['values'] as $field_name => $value ) {
					$_POST[ $field_name ] = $value; // phpcs:ignore
				}
				self::clear_session( $product_id );
			}

			return $cart_item_data;
		}

		public function blocks_validate_pif( $product, $request ) {
			$product_id   = $product->get_id();
			$session_data = self::get_from_session( $product_id );
			foreach ( array( 'global', 'local' ) as $scope ) {
				$total = $this->get_total( $scope, $product_id );
				for ( $i = 1; $i <= $total; $i++ ) {
					$field = alg_get_all_values( $scope, $i, $product_id );
					if ( 'yes' !== ( $field['enabled'] ?? '' ) || 'yes' !== ( $field['required'] ?? '' ) ) {
						continue;
					}
					$field_name  = ALG_WC_PIF_ID . '_' . $scope . '_' . $i;
					$field_value = '';

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
						$msg = str_replace( '%title%', $field['title'] ?? '', $field['required_message'] ?? '%title% is required.' );
						throw new \Exception( esc_html( $msg ) );
					}
				}
			}
		}
		// ─────────────────────────────────────────────────────────
		// Helpers
		// ─────────────────────────────────────────────────────────

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

		private function get_total( $scope, $product_id ) {
			if ( 'local' === $scope ) {
				return intval( get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_local_total_number', true ) );
			}
			return intval( get_option( 'alg_wc_pif_global_total_number', 0 ) );
		}

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

		public static function clear_session( $product_id ) {
			if ( WC()->session ) {
				$key = self::SESSION_KEY_PREFIX . $product_id;
				WC()->session->__unset( $key );
			}
		}

		public static function is_blocks_api_request() {
			$uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : ''; // phpcs:ignore
			return strpos( $uri, '/wc/store/v1/' ) !== false
				|| strpos( $uri, '/wc/store/' ) !== false;
		}
	}
endif;