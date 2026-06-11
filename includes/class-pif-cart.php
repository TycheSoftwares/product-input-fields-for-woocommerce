<?php
/**
 * PIF Cart Class
 *
 * Handles the cart functionality for the PIF plugin, including adding custom fields to the cart and displaying them in the cart and checkout pages.
 *
 * @package PIF/Cart
 * @since 1.0
 */

defined( 'ABSPATH' ) || exit;

class PIF_Cart {

	/**
	 * Function validate_product_input_fields_on_add_to_cart.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param boolean $passed Is Passed.
	 * @param number  $product_id Product ID.
	 */
	public static function validate_product_input_fields_on_add_to_cart( $passed, $product_id ) {
		$global_fields  = get_option( 'pif_field_settings', array() );
		$product_fields = get_post_meta( $product_id, 'pif_field_settings', true );

		$validate_global = self::validate_fields( $global_fields, 'global', $product_id, $passed );
		$validate_local  = true;
		if ( true === pif_get_option( 'local_enabled' ) || 'yes' === pif_get_option( 'local_enabled' ) ) {
			$validate_local  = self::validate_fields( $product_fields, 'local', $product_id, $passed );
		}
		
		return $validate_global && $validate_local;
	}

	/**
	 * Validates the product input fields based on the provided settings and product categories.
	 * 
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	public static function validate_fields( $fields, $scope, $product_id, $passed ) {
		$is_express = self::is_express_checkout_request();
		if ( $is_express && PIF_Express_Checkout::is_blocks_api_request() ) {
			return $passed;
		}
		$terms     = get_the_terms( $product_id, 'product_cat' );
		$terms_ids = ! empty( $terms ) ? wp_list_pluck( $terms, 'term_id' ) : array();

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $passed;
		}

		$session_data = null;
		if ( $is_express ) {
			$session_data = PIF_Express_Checkout::get_from_session( $product_id );
		}

		foreach ( $fields as $id => $product_input_field ) {
			$field_id = $product_input_field['id'] ?? 1;
			$exl_yes  = true;
			$enabled  = isset( $product_input_field['enabled'] ) && ( true === $product_input_field['enabled'] || 'yes' === $product_input_field['enabled'] ) ? $product_input_field['enabled'] : false; 
			$required = isset( $product_input_field['required'] ) && ( true === $product_input_field['required'] || 'yes' === $product_input_field['required'] ) ? true : false;

			if ( !$enabled ) {
				continue;
			}

			// Generate field name.
			$field_name = ALG_WC_PIF_ID . '_' . $scope . '_' . $field_id;

			// Skip validation if quantity is less than field_min_qty.
			if ( ! empty( $product_input_field['field_min_qty'] ) ) {
				$cart_qty      = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1; // phpcs:ignore
				$field_min_qty = intval( $product_input_field['field_min_qty'] );
				if ( $cart_qty < $field_min_qty ) {
					continue;
				}
			}

			// Validate required fields only if the field is visible.
			if ( $required ) {
				$is_empty = false;
				$field_value = null;
				if ( 'file' === $product_input_field['type'] ) {
					$field_value = ( isset( $_FILES[ $field_name ]['name'] ) ) ? $_FILES[ $field_name ]['name'] : ''; //phpcs:ignore
					if ( '' === $field_value && $is_express && $session_data ) {
						$field_value = $session_data['files'][ $field_name ] ?? '';
					}
				} else {
					if ( isset( $_POST[ $field_name ] ) ) { // phpcs:ignore
						$field_value = sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) ); // phpcs:ignore
						if ( is_array( $field_value ) ) {
							$field_value = implode( '', $field_value );
						}
					}

					if ( '' === trim( (string) $field_value ) && $is_express && $session_data ) {
						$field_value = $session_data['values'][ $field_name ] ?? '';
						if ( is_array( $field_value ) ) {
							$field_value = implode( '', $field_value );
						}
					}

					$is_empty = ( '' === $field_value );
				}

				if ( $is_empty ) {
					$passed = false;
					wc_add_notice( str_replace( '%title%', $product_input_field['title'], $product_input_field['required_message'] ), 'error' );
					$active_theme = wp_get_theme();
					if ( 'Porto Child' === $active_theme->get( 'Name' ) && 'porto' === $active_theme->get( 'Template' ) ) {
						// Redirect to product page when validation fails.
						$product_url = get_permalink( $product_id );
						wp_safe_redirect( $product_url );
						exit;
					}
				}
			}

			if ( 'file' === $product_input_field['type'] && isset( $_FILES[ $field_name ] ) && '' !== $_FILES[ $field_name ]['name'] ) { //phpcs:ignore
				// Validate file type.
				$file_accept = $product_input_field['type_file_accept'];
				if ( '' !== $file_accept ) {
					$file_accept = explode( ',', $file_accept );
					if ( is_array( $file_accept ) && ! empty( $file_accept ) ) {
						$file_type = '.' . pathinfo( $_FILES[ $field_name ]['name'], PATHINFO_EXTENSION ); //phpcs:ignore
						if ( ! in_array( $file_type, $file_accept, true ) ) {
							$passed = false;
							wc_add_notice( $product_input_field['type_file_wrong_type_msg'], 'error' );
						}
					}
				}
				// Validate file max size.
				if ( $product_input_field['type_file_max_size'] > 0 ) {
					if ( $_FILES[ $field_name ]['size'] > intval( $product_input_field['type_file_max_size'] ) ) { //phpcs:ignore
						$passed = false;
						wc_add_notice( $product_input_field['type_file_max_size_msg'], 'error' );
					}
				}
			}
		}

		return $passed;
	}

	public static function add_product_input_fields_to_cart_item_data( $cart_item_data, $product_id ) {
		$product_input_fields = array();
		// Get the product categories.
		$product_categories = wc_get_product_cat_ids( $product_id );
		// Condition to check weather if product added is a group product or not.
		$product_id        = ( isset( $_REQUEST['add-to-cart'] ) && $product_id !== $_REQUEST['add-to-cart'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['add-to-cart'] ) ) : $product_id; // phpcs:ignore
		$global_fields       = get_option( 'pif_field_settings', array() );
		$product_fields      = get_post_meta( $product_id, 'pif_field_settings', true );

		$global_input_fields = self::get_product_input_fields( $global_fields, 'global', $product_categories, $cart_item_data );
		$local_input_fields  = self::get_product_input_fields( $product_fields, 'local', $product_categories, $cart_item_data );
		
		if ( ! empty( $global_input_fields ) ) {
			$cart_item_data[ ALG_WC_PIF_ID . '_global' ] = $global_input_fields;
		}

		if ( ! empty( $local_input_fields ) ) {
			$cart_item_data[ ALG_WC_PIF_ID . '_local' ] = $local_input_fields;
		}

		$is_express   = self::is_express_checkout_request();
		$session_data = null;
		if ( $is_express && ! PIF_Express_Checkout::is_blocks_api_request() ) {
			$session_data = PIF_Express_Checkout::get_from_session( $product_id );
		}
		if ( $is_express && ! PIF_Express_Checkout::is_blocks_api_request() && $session_data ) {
			PIF_Express_Checkout::clear_session( $product_id );
		}
		return $cart_item_data;
	}

	public static function get_product_input_fields( $fields, $scope, $product_categories, $cart_item_data ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return array();
		}

		$is_express   = self::is_express_checkout_request();
		$session_data = null;
		if ( $is_express && ! PIF_Express_Checkout::is_blocks_api_request() ) {
			$session_data = PIF_Express_Checkout::get_from_session( $product_id );
		}
		
		$product_input_fields = array();
		foreach ( $fields as $id => $product_input_field ) {
			$field_id = $product_input_field['id'] ?? 1;
			$enabled  = isset( $product_input_field['enabled'] ) && ( true === $product_input_field['enabled'] || 'yes' === $product_input_field['enabled'] ) ? true : false;
			$required = isset( $product_input_field['required'] ) && ( true === $product_input_field['required'] || 'yes' === $product_input_field['required'] ) ? true : false;
			$multiple = isset( $product_input_field['multiple'] ) && ( true === $product_input_field['multiple'] || 'yes' === $product_input_field['multiple'] ) ? true : false;

			if ( ! $enabled ) {
				continue;
			}

			$product_input_field['_plugin_version'] = PIF_VERSION;
			$product_input_field['_field_nr']       = $field_id;
			$field_name                             = ALG_WC_PIF_ID . '_' . $scope . '_' . $field_id;
			if ( 'file' === $product_input_field['type'] ) {
				if ( ! isset( $cart_item_data['woosb_parent_id'] ) && isset( $_FILES[ $field_name ] ) && '' !== $_FILES[ $field_name ] && isset( $_FILES[ $field_name ]['tmp_name'] ) && '' !== $_FILES[ $field_name ]['tmp_name'] ) { // phpcs:ignore
					$product_input_field['_value'] = $_FILES[ $field_name ]; // phpcs:ignore
					$tmp_dest_file                 = tempnam( sys_get_temp_dir(), 'alg' );
					move_uploaded_file( $_FILES[ $field_name ]['tmp_name'], $tmp_dest_file ); // phpcs:ignore
					$product_input_field['_value']['_tmp_name'] = $tmp_dest_file;
				} elseif ( $session_data && ! empty( $session_data['files'][ $field_name ] ) ) {
					$product_input_field['_value'] = array(
						'name'      => $session_data['files'][ $field_name ],
						'_tmp_name' => '',
						'_express'  => true,
					);
				}
			} else { // phpcs:ignore
				$value = null;
				if ( isset( $_POST[ $field_name ] ) ) { // phpcs:ignore
					$value = stripslashes_deep( sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) ) ); // phpcs:ignore
				} elseif ( $session_data && isset( $session_data['values'][ $field_name ] ) ) {
					$value = $session_data['values'][ $field_name ];
				}

				if ( null !== $value ) {
					if ( 'textarea' === $product_input_field['type'] ) {
						$value = sanitize_textarea_field( $value );
					} elseif ( 'url' === $product_input_field['type'] ) {
						$value = esc_url_raw( $_POST[ $field_name ] ); // phpcs:ignore
					} else {
						$value = ! is_array( $value ) ? sanitize_text_field( $value ) : array_map( 'sanitize_text_field', $value );
					}
					$product_input_field['_value'] = $value;
				}
			}
			$product_input_fields[] = $product_input_field;
		}

		return $product_input_fields;
	}

	/**
	 * Function get_cart_item_product_input_fields_from_session.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param mixed $item Item Details.
	 * @param mixed $values Values.
	 */
	public static function get_cart_item_product_input_fields_from_session( $item, $values ) {
		if ( isset( $values[ ALG_WC_PIF_ID . '_' . 'global' ] ) ) {
			$item[ ALG_WC_PIF_ID . '_' . 'global' ] = $values[ ALG_WC_PIF_ID . '_' . 'global' ];
		}

		if ( isset( $values[ ALG_WC_PIF_ID . '_' . 'local' ] ) ) {
			$item[ ALG_WC_PIF_ID . '_' . 'local' ] = $values[ ALG_WC_PIF_ID . '_' . 'local' ];
		}
		return $item;
	}

	/**
	 * Adds product input values to order details (and emails).
	 *
	 * @version 1.1.4
	 * @since   1.0.0
	 * @param mixed   $other_data Other data.
	 * @param boolean $item Item.
	 */
	public static function add_product_input_fields_to_cart_item_name( $other_data, $item ) {
		// Check if the item is a child product of a Composite product and skip if configured.
		if ( isset( $item['composite_parent'] ) && $item['composite_parent'] && apply_filters( 'alg_wc_pif_no_pif_in_child_products', true ) ) {
			return $other_data;
		}

		$scopes = array( 'global', 'local' );

		foreach ( $scopes as $scope ) {
			// Retrieve product input fields data.
			$product_input_fields = isset( $item[ ALG_WC_PIF_ID . '_' . $scope ] ) ? maybe_unserialize( $item[ ALG_WC_PIF_ID . '_' . $scope ] )
			: array();

			foreach ( $product_input_fields as $product_input_field ) {
				$min_qty  = $product_input_field['field_min_qty'] ?? 1;
				$cart_qty = $item['quantity'] ?? 1;
				$value    = $product_input_field['_value'] ?? '';
				// Skip fields that don't meet quantity requirements or have empty values.
				if ( empty( $value ) || $cart_qty < $min_qty ) {
					continue;
				}
				$values      = '';
				$field_type  = $product_input_field['type'] ?? '';
				$field_title = $product_input_field['title'] ?? '';
				// Handle different input field types.
				switch ( $field_type ) {
					case 'file':
						$values = self::process_file_input_field( $product_input_field, $value, $field_title );
						break;
	
					case 'checkbox':
						$values = ( 'yes' === $value ) ? $product_input_field['type_checkbox_yes'] : $product_input_field['type_checkbox_no'];
						break;
	
					case 'textarea':
						$values = nl2br( esc_html( $value ) );
						break;
	
					default:
						$values = esc_html( $value );
						break;
				}

				if ( ! empty( $values ) ) {
					$other_data[] = array(
						'name'  => __( $field_title, 'product-input-fields-for-woocommerce' ),
						'value' => $values,
					);
				}
			}

		}
		return $other_data;
	}

	/**
	 * Processes file input fields, handling file uploads and generating thumbnail previews if enabled.
	 */
	public static function process_file_input_field( $product_input_field, $value, $field_title ) {
		$img_url = '';
		$values  = is_array( $value ) && isset( $value['name'] ) ? $value['name'] : $value;
		$show_thumbnail = isset( $product_input_field['file_show_thumbnail'] ) && ( true === $product_input_field['file_show_thumbnail'] || 'yes' === $product_input_field['file_show_thumbnail'] ) ? true : false;

		if ( ! empty( $value ) && $show_thumbnail ) {
			$file_type = $value['type'] ?? '';

			if ( in_array( $file_type, array( 'image/jpeg', 'image/jpg', 'image/png' ), true ) && isset( $value['_tmp_name'] ) ) {
				$upload_dir = wp_upload_dir()['basedir'] . '/pif_temp';
				if ( ! file_exists( $upload_dir ) ) {
					mkdir( $upload_dir, 0755, true ); //phpcs:ignore
				}
				$upload_dir_and_name = $upload_dir . '/' . $value['name'];
				if ( isset( $value['_tmp_name'] ) && ! empty( $value['_tmp_name'] ) && file_exists( $value['_tmp_name'] ) ) {
					$file_data = file_get_contents( $value['_tmp_name'] ); // Read the temporary file.
					file_put_contents( $upload_dir_and_name, $file_data ); // Save to the new location.
				}

				$img_url = home_url() . '/wp-content/uploads/pif_temp/' . $value['name'];
			}
		}

		if ( ! empty( $img_url ) ) {
			$dimensions = apply_filters( 'alg_wc_pif_thumbnail_dimensions', array( 64, 64 ) );
			$height     = $dimensions[0] ?? 64;
			$width      = $dimensions[1] ?? 64;

			return sprintf(
				'<div style="display: flex; align-items: center; gap: 10px;">
					<a href="javascript:void(0)" class="alg_image_preview">
						<img src="%s" style="height:%dpx;width:%dpx;" alt="%s" />
					</a>
					<span>%s</span>
				</div>',
				esc_url( $img_url ),
				esc_html( $height ),
				esc_html( $width ),
				esc_attr( $values ),
				esc_html( $values ) // File name only..
			);
		}

		return $values;
	}

	/**
	 * ReCalculate Product Item pricing
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param mixed $cart_object Cart Object.
	 */
	public static function recalculate_product_price( $cart_object ) { // phpcs:ignore
		$pif_price_rule = false;
		foreach ( $cart_object->cart_contents as $key => $value ) {
			if ( isset( $value['alg_wc_pif_local'] ) ) {
				foreach ( $value['alg_wc_pif_local'] as $akey => $avalue ) {
					if ( isset( $avalue['required_price'] ) && $avalue['required_price'] ) {
						$pif_price_rule = true;
					}
				}
			}
			if ( isset( $value['alg_wc_pif_global'] ) ) {
				foreach ( $value['alg_wc_pif_global'] as $akey => $avalue ) {
					if ( isset( $avalue['required_price'] ) && $avalue['required_price'] ) {
						$pif_price_rule = true;
					}
				}
			}

			if ( $pif_price_rule ) {
				$product = wc_get_product( $value['product_id'] );
				if ( in_array( $product->get_type(), array( 'variable', 'variable-subscription' ), true ) ) {
					$converted_price = ( $value['data']->get_sale_price() ) ? $value['data']->get_sale_price() : $value['data']->get_regular_price();
					$regular_price   = get_post_meta( $value['variation_id'], '_regular_price', true );
					$sale_price      = get_post_meta( $value['variation_id'], '_sale_price', true );
					$price           = $sale_price ? $sale_price : $regular_price;
					$p_price		 = $product->get_price();
					if ( 0 != $value['variation_id'] ) { // phpcs:ignore
						$variable_product = wc_get_product( $value['variation_id'] );
						$p_price          = $variable_product->get_price();
					}
				} else {
					// For simple and subscription product type.
					$converted_price = ( $value['data']->get_sale_price() ) ? $value['data']->get_sale_price() : $value['data']->get_regular_price();
					$regular_price   = get_post_meta( $value['product_id'], '_regular_price', true );
					$sale_price      = get_post_meta( $value['product_id'], '_sale_price', true );

					$price   = $sale_price ? $sale_price : $regular_price;
					$p_price = $product->get_price();
				}

				$og_price = $price;
				// $oc_price is equals to only product addons fields sub total without product price.
				$oc_price = 0;
				if ( did_action( 'woocommerce_before_calculate_totals' ) > 1 ) {
					return;
				}
				// $c_price is equals to product addons fields sub total with product price.
				$c_price = $value['data']->get_price();

				// $price is replace by $c_price for bundled product( sub-product with discount ).
				$price = ( ( isset( $value['bundled_items'] ) && ! empty( $value['bundled_items'] ) ) || isset( $value['bundled_by'] ) ) ? $c_price : $price;

				// $p_price is equals to converted product price product price.
				if ( $c_price > $p_price && is_checkout() ) {
					$oc_price = $c_price - $p_price;
				} else { // phpcs:ignore
					if ( $c_price > 0 ) {
						$oc_price = $c_price - $p_price;
					}
				}
				// Convert $oc_price for multi currency.
				if ( ! empty( $price ) ) {
					$oc_price = ( $oc_price * $price ) / $p_price;
				}

				if ( array_key_exists( 'alg_wc_pif_local', $value ) ) {
					$fields = $value['alg_wc_pif_local'];
					$price  = alg_calculate_price( $fields, $value['quantity'], $price );
				}

				if ( array_key_exists( 'alg_wc_pif_global', $value ) ) {
					$fields = $value['alg_wc_pif_global'];
					$price  = alg_calculate_price( $fields, $value['quantity'], $price );
				}

				if ( (float) $og_price === $price ) {
					continue;
				}
				$product_price             = ( $value['data']->get_sale_price() ) ? $value['data']->get_sale_price() : $value['data']->get_regular_price();
				$yith_request_quote_plugin = 'yith-woocommerce-request-a-quote-premium/init.php';
				if ( in_array( $yith_request_quote_plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true ) ) {
					if ( isset( $value['ywraq_price'] ) ) {
						$price = $value['ywraq_price'];
					}
				}
				// Merge product addons fields sub total to pif fields total.
				$price = wc_format_decimal( $price, 2 ) + wc_format_decimal( $oc_price, 2 );
				$price = apply_filters( 'alg_pif_recalculate_product_price', $price, $product_price, $value );
				$value['data']->set_price( ( $price ) );
			}
		}
	}

	private static function is_express_checkout_request() {
		if ( PIF_Express_Checkout::is_blocks_api_request() ) {
			return true;
		}
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
		$wc_ajax = isset( $_GET['wc-ajax'] ) ? sanitize_text_field( wp_unslash( $_GET['wc-ajax'] ) ) : ''; // phpcs:ignore
		$action  = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : ''; // phpcs:ignore

		$express_actions = array(
			'wc_stripe_payment_request_create_order',
			'wc_stripe_payment_request_add_to_cart',
			'wc_stripe_create_order',
			'wc_stripe_express_checkout_create_order',
			'wc_stripe_express_checkout_add_to_cart',
			'wcpay_create_order',
			'wcpay_payment_request_create_order',
			'wcpay_payment_request_add_to_cart',
			'wcpay_express_checkout_create_order',
			'wcpay_express_checkout_add_to_cart',
			'wc_ppec_start_checkout',
			'woocommerce_payment_request_button_add_to_cart',
		);
		$express_wc_ajax = array(
			'stripe_payment_request_create_order',
			'stripe_payment_request_add_to_cart',
			'wcpay_create_order',
			'wcpay_payment_request_create_order',
			'wcpay_payment_request_add_to_cart',
			'wcpay_express_checkout_create_order',
			'wcpay_express_checkout_add_to_cart',
			'wc_stripe_express_checkout_create_order',
			'wc_stripe_express_checkout_add_to_cart',
		);
		if ( $is_ajax && in_array( $action, $express_actions, true ) ) {
			return true;
		}
		if ( in_array( $wc_ajax, $express_wc_ajax, true ) ) {
			return true;
		}
		$stripe_header  = isset( $_SERVER['HTTP_X_WCPAY_PLATFORM'] ) ? $_SERVER['HTTP_X_WCPAY_PLATFORM'] : ''; // phpcs:ignore
		$stripe_header2 = isset( $_SERVER['HTTP_X_WC_STRIPE_PAYMENT_REQUEST'] ) ? $_SERVER['HTTP_X_WC_STRIPE_PAYMENT_REQUEST'] : ''; // phpcs:ignore
		if ( ! empty( $stripe_header ) || ! empty( $stripe_header2 ) ) {
			return true;
		}
		return false;
	}
}
