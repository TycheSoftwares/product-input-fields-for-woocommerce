<?php
/**
 * PIF Product Class
 *
 * Handles the product related functions for the PIF plugin.
 *
 * @author  Tyche Softwares
 * @package PIF/Product
 */

defined( 'ABSPATH' ) || exit;

/**
 * PIF Product.
 *
 * @since 1.0
 */
class PIF_Product {

	public static function enqueue_scripts() {
		global $product;
		$current_theme = wp_get_theme();
		if ( is_shop() && ( 'Flatsome' === $current_theme->name || 'Flatsome' === $current_theme->parent_theme ) || is_page() ) { //phpcs:ignore
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script(
				'alg-datepicker',
				plugins_url( 'assets/js/alg-datepicker.js', PIF_FILE ),
				array( 'jquery' ),
				PIF_VERSION,
				true
			);
			// Datepicker style.
			wp_enqueue_style( 'jquery-ui-datepicker', '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css', array(), '1.11.4' );
		}

		if ( ! is_product() ) {
			return;
		}

		if ( ! pif_browser_can_render_color_type() ) {
			wp_enqueue_script( 'spectrum', '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js', array( 'jquery' ), '1.8.0', true );
			wp_enqueue_style( 'spectrum', '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css', array(), '1.8.0' );
		}

		wp_enqueue_script( 'jquery-ui-datepicker' );
		$scripts = array(
			'alg-datepicker'              => 'alg-datepicker.js',
			'alg-weekpicker'              => 'alg-weekpicker.js',
			'jquery-ui-timepicker'        => 'jquery.timepicker.min.js',
			'alg-timepicker'              => 'alg-timepicker.js',
			'alg-wc-product-input-fields' => 'alg-wc-product-input-fields.js',
		);
		wp_register_script( 
			'alg-wc-product-input-fields',
			plugins_url( 'assets/js/alg-wc-product-input-fields.js', PIF_FILE ),
		); //phpcs:ignore

		$thousand_separator = get_option( 'woocommerce_price_thousand_sep' );
		$decimal_separator  = get_option( 'woocommerce_price_decimal_sep' );
		$conditional_arr    = check_conditional_options_conditions();
		$separator_value    = array(
			'thousand_separator' => $thousand_separator,
			'decimal_separator'  => $decimal_separator,
			'conditional_data'   => $conditional_arr,
		);
		wp_localize_script( 'alg-wc-product-input-fields', 'separator_value', $separator_value );
		$theme         = wp_get_theme(); // gets the current theme.
		$themify_ultra = array();
		if ( 'Themify Ultra' === $theme->name || 'Themify Ultra' === $theme->parent_theme ) {
			$themify_ultra = array(
				'themify_ultra_active' => 'yes',
			);
		}
		$flatsome_theme = array();
		if ( 'Flatsome' === $theme->name || 'Flatsome' === $theme->parent_theme ) {
			$flatsome_theme['flatsome_active'] = 'yes';
		}
		wp_localize_script( 'alg-wc-product-input-fields', 'themify_ultra', $themify_ultra );
		wp_localize_script( 'alg-wc-product-input-fields', 'flatsome_theme', $flatsome_theme );
		wp_enqueue_script( 'alg-wc-product-input-fields' );
		foreach ( $scripts as $script_id => $script_file ) {
			wp_enqueue_script(
				$script_id,
				plugins_url( 'assets/js/' . $script_file, PIF_FILE ),
				array( 'jquery' ),
				PIF_VERSION,
				true
			);
		}

		// Timepicker style.
		wp_enqueue_style( 'jquery-ui-timepicker', '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css', array(), '1.3.5' );

		// Rangepicker style.
		wp_enqueue_style( 
			'jquery-ui-timepicker',
			plugins_url( 'assets/css/alg-wc-product-input-fields.css', PIF_FILE ),
			array(),
			PIF_VERSION
		);

		// Datepicker style.
		wp_enqueue_style( 'jquery-ui-datepicker', '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css', array(), '1.11.4' );
	}
    
    /**
	 * Function for applying hooks based on the product type.
	 */
	public static function pif_woocommerce_before_add_to_cart_form() {
		$priority = pif_get_option( 'frontend_position_priority', 10 );

		if ( 'product' === get_post_type() ) {
			$product = wc_get_product( get_the_ID() );
			if ( 'variable' === $product->get_type() ) {
				add_action( 'woocommerce_before_single_variation', array( __CLASS__, 'add_before_product_input_fields_to_frontend' ), $priority );
				add_action( 'woocommerce_before_single_variation', array( __CLASS__, 'add_product_input_fields_to_frontend' ), $priority );
				add_action( 'woocommerce_before_single_variation', array( __CLASS__, 'add_after_product_input_fields_to_frontend' ), $priority );
			} else {
				add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'add_before_product_input_fields_to_frontend' ), $priority );
				add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'add_product_input_fields_to_frontend' ), $priority );
				add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'add_after_product_input_fields_to_frontend' ), $priority );
			}
		}
	}

	public static function check_field_exists() {
		global $product;
		$check_field_exists = false;
		$product_id          = $product->get_id();

		$global_fields 	= get_option( 'pif_field_settings', array() );
		$product_fields = get_post_meta( $product_id, 'pif_field_settings', true );

		if ( is_array( $global_fields ) && ! empty( $global_fields ) ) {
			foreach ( $global_fields as $id => $product_input_field ) {
				if ( isset( $product_input_field['enabled'] ) && ( 'yes' === $product_input_field['enabled'] || true === $product_input_field['enabled'] ) ) {
					$check_field_exists = true;
					break;
				}
			}
		}

		if ( ! $check_field_exists && is_array( $product_fields ) && ! empty( $product_fields ) ) {
			foreach ( $product_fields as $id => $product_input_field ) {
				if ( isset( $product_input_field['enabled'] ) && ( 'yes' === $product_input_field['enabled'] || true === $product_input_field['enabled'] ) ) {
					$check_field_exists = true;
					break;
				}
			}
		}

		return $check_field_exists;
	}

    /**
	 * Function add_before_product_input_fields_to_frontend.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @todo    (later) output only if there are any product input fields to display; same to add_after_product_input_fields_to_frontend(); same to alg_display_product_input_fields()
	 */
	public static function add_before_product_input_fields_to_frontend() {
		global $product;
		if ( ! $product ) {
			return;
		}
		$price_html = '';
		$min_html   = '';
		$omin       = 0;		
		if ( $product->get_type() === 'variable' ) {
			$price_html = "<span class='original-price' style='display:none'></span>";
			$args       = array(
				'post_type'   => 'product_variation',
				'post_status' => array( 'private', 'publish' ),
				'numberposts' => -1,
				'orderby'     => 'menu_order',
				'order'       => 'asc',
				'fields'      => 'ids', // Only get post IDs.
				'post_parent' => $product->get_id(), // get parent post-ID.
			);
			$variations = get_posts( $args );

			$min_array = array();
			foreach ( $variations as $variation_id ) {
				$regular_price = get_post_meta( $variation_id, '_regular_price', true );
				$sale_price    = get_post_meta( $variation_id, '_sale_price', true );

				$price = $sale_price ? $sale_price : $regular_price;
				array_push( $min_array, $price );
				$price_html .= '<span class="original-price-' . $variation_id . '" style="display:none">' . $price . '</span>';
			}
			$omin       = min( $min_array );
			$cmin_price = $product->get_variation_sale_price( 'min' );
			if ( $omin > 0 ) {
				$min_html  = '<span class="original-min-price" style="display:none">' . $omin . '</span>';
				$min_html .= '<span class="converted-min-price" style="display:none">' . $cmin_price . '</span>';
			}
		} else {
			$regular_price = get_post_meta( $product->get_id(), '_regular_price', true );
			$sale_price    = get_post_meta( $product->get_id(), '_sale_price', true );
			$price         = $sale_price ? $sale_price : $regular_price;

			$price_html = '<span class="original-price" style="display:none">' . $price . '</span>';
		}
		$frontend_html = self::check_field_exists();

		if ( $frontend_html ) {
			$html  = wp_kses_post( pif_get_option( 'frontend_before', '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">' ) );
			$html .= '<span class="currency-pos" style="display:none">' . get_option( 'woocommerce_currency_pos' ) . '</span>';
			$html .= $price_html . $min_html;
			echo $html; //phpcs:ignore
		} else {
			$html  = '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">';
			$html .= '<span class="currency-pos" style="display:none">' . get_option( 'woocommerce_currency_pos' ) . '</span>';
			$html .= $price_html . $min_html;
			echo $html; //phpcs:ignore
		}
	}

	/**
	 * Function add_after_product_input_fields_to_frontend.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public static function add_after_product_input_fields_to_frontend() {
		$frontend_html = true;
		if ( $frontend_html ) {
			echo wp_kses_post( pif_get_option( 'frontend_after', '</table>' ) );
		} else {
			echo ( '</table>' );
		}
	}

	/**
	 * Display fields with shortcode.
	 * 
	 * @version 3.1.0
	 */
	public static function alg_display_product_input_fields() {
		ob_start();
		self::add_before_product_input_fields_to_frontend();

		if ( 'yes' === pif_get_option( 'enabled', false ) || true === pif_get_option( 'enabled', false ) ) {
			self::add_product_input_fields_to_frontend();
		}

		self::add_after_product_input_fields_to_frontend();

		$html = ob_get_clean();
		// Patch for WP Bakery theme builder plugin to prevent duplicate / wrong placement of fields.s
        // :fire: ONLY for WPBakery
        if ( defined( 'WPB_VC_VERSION' ) ) {

            add_action( 'woocommerce_before_add_to_cart_button', function() use ( $html ) {
                echo $html;
            }, 1 );

            // prevent duplicate / wrong placement
            return '';
        }
		return $html;
	}

	/**
	 * Add product input fields to frontend.
	 */
	public static function add_product_input_fields_to_frontend() {
		global $product;
		if ( ! $product ) {
			return '';
		}

		$html           = '';
		$product_id     = ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ? $product->id : $product->get_id() );
		$global_fields 	= get_option( 'pif_field_settings', array() );
		$product_fields = get_post_meta( $product_id, 'pif_field_settings', true );

		if ( count( $global_fields ) >= 2 ) {
			$global_fields = array_slice( $global_fields, 0, 1, true );
		}

		$html = self::get_field_html( $global_fields, 'global', $product_id );

		if ( true === pif_get_option( 'local_enabled' ) || 'yes' === pif_get_option( 'local_enabled' ) ) {
			$html .= self::get_field_html( $product_fields, 'local', $product_id );
		}

		echo $html; //phpcs:ignore
	}

	public static function get_field_html( $fields, $scope, $product_id ) {
		$html = '';
		$price_symbol   = get_woocommerce_currency_symbol();

		if ( ! is_array( $fields ) || empty( $fields ) ) {
			return $html;
		}

		foreach ( $fields as $id => $product_input_field ) {

			$field_id      = $product_input_field['id'];
			$exl_yes       = true;
			$field_enabled = isset( $product_input_field['enabled'] ) && ( true === $product_input_field['enabled'] || 'yes' === $product_input_field['enabled'] ) ? true : false;

			if ( $field_enabled ) {
				$field_required  = isset( $product_input_field['required'] ) && ( true === $product_input_field['required'] || 'yes' === $product_input_field['required'] ) ? true : false;

				// Required.
				$required = '';
				if ( $field_required ) {
					$product_input_field['title'] .= pif_get_option( 'frontend_required_html', '&nbsp;<abbr class="required" title="required">*</abbr>' );
					if ( 'yes' === pif_get_option( 'frontend_required_js', 'yes' ) ) {
						$required = ' required';
					}
				}

				// Datepicker/Weekpicker.
				if ( isset( $product_input_field['type_datepicker_format'] ) && '' === $product_input_field['type_datepicker_format'] ) {
					$product_input_field['type_datepicker_format'] = get_option( 'date_format' );
				}
				$product_input_field['type_datepicker_format'] = isset( $product_input_field['type_datepicker_format'] ) ? esc_attr( $product_input_field['type_datepicker_format'] ) : '';
				$product_input_field['type_datepicker_format'] = isset( $product_input_field['type_datepicker_format'] ) ? alg_date_format_php_to_js( $product_input_field['type_datepicker_format'] ) : '';
				$datepicker_year                               = ( isset( $product_input_field['type_datepicker_addyear'] ) && $product_input_field['type_datepicker_addyear'] ) ? 'changeyear="1" yearrange="' . $product_input_field['type_datepicker_yearrange'] . '" ' : '';
				// File.
				$custom_attributes = ( isset( $product_input_field['type'] ) && 'file' === $product_input_field['type'] ) ? ' accept="' . $product_input_field['type_file_accept'] . '"' : '';
				// Class and style.
				$class = ( isset( $product_input_field['class'] ) && '' !== $product_input_field['class'] ? ' class="' . $product_input_field['class'] . '"' : '' );
				$style = ( isset( $product_input_field['style'] ) && '' !== $product_input_field['style'] ? ' style="' . $product_input_field['style'] . '"' : '' );
				// Input restrictions.
				$min       = ( isset( $product_input_field['input_restrictions_min'] ) && '' !== $product_input_field['input_restrictions_min'] ) ? ' min="' . $product_input_field['input_restrictions_min'] . '"' : '';
				$max       = ( isset( $product_input_field['input_restrictions_max'] ) && '' !== $product_input_field['input_restrictions_max'] ) ? ' max="' . $product_input_field['input_restrictions_max'] . '"' : '';
				$step      = ( isset( $product_input_field['input_restrictions_step'] ) && '' !== $product_input_field['input_restrictions_step'] ) ? ' step="' . $product_input_field['input_restrictions_step'] . '"' : '';
				$maxlength = ( isset( $product_input_field['input_restrictions_maxlength'] ) && '' !== $product_input_field['input_restrictions_maxlength'] ) ? ' maxlength="' . $product_input_field['input_restrictions_maxlength'] . '"' : '';
				$pattern   = ( isset( $product_input_field['input_restrictions_pattern'] ) && '' !== $product_input_field['input_restrictions_pattern'] ) ? ' pattern="' . $product_input_field['input_restrictions_pattern'] . '"' : '';
				// Field name and value.
				$field_name    = ALG_WC_PIF_ID . '_' . $scope . '_' . $field_id;
				$field_min_qty = ( isset( $product_input_field['field_min_qty'] ) && '' !== $product_input_field['field_min_qty'] ? $product_input_field['field_min_qty'] : 1 );
				$_value        = ( 'yes' === pif_get_option( 'frontend_refill', 'yes' ) && isset( $_POST[ $field_name ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) ) : $product_input_field['default_value']; // phpcs:ignore
				$_value        = stripslashes_deep( $_value );
				$_value        = esc_attr( $_value );
				if ( isset( $product_input_field['type'] ) && 'textarea' === $product_input_field['type'] ) {
					$_value = sanitize_textarea_field( $_value );
				} else {
					$_value = ! is_array( $_value ) ? sanitize_text_field( $_value ) : array_map( 'sanitize_text_field', $_value );
				}

				$field_price = 0;
				$field_id    = '';
				// Field HTML.
				$field_html          = '';
				$current_theme       = wp_get_theme();
				$form_cart_attribute = apply_filters( 'alg_wc_pif_remove_form_cart_attribute', false );
				if ( $form_cart_attribute || is_plugin_active( 'js_composer/js_composer.php' ) || 'Flatsome' == $current_theme->name || 'Flatsome' == $current_theme->parent_theme || 'Divi' == $current_theme->name || 'Divi' == $current_theme->parent_theme ) { // phpcs:ignore
					$form_cart = '';
				} else {
					$form_cart = ' form="cart"';
				}
				switch ( $product_input_field['type'] ) {
					case 'checkbox':
						$checked = checked( $_value, 'yes', false );
						$field_html = '<input type="hidden" value="no" name="' . $field_name . '"/><input' . $form_cart . $class . $style . ' id="' . $field_name .
							'" type="' . $product_input_field['type'] .
							'" value="yes" name="' . $field_name . '"' . $custom_attributes . $checked . $required . '/>';
						break;
					case 'datepicker':
						/** Pricing Option */
						$field_price = isset( $product_input_field['input_restrictions_price'] ) ? $product_input_field['input_restrictions_price'] : 0;
						$datasets    = '';
						/** Pricing Option */
						$field_html = '<input ' . $datasets . $form_cart . $class . $style . ' value="' . $_value . '" data-min_qty="' . $field_min_qty . '" id="' . $field_name . '" ' . $datepicker_year . '
							firstday="' . ( $product_input_field['type_datepicker_firstday'] ?? '' ) . '"
							dateformat="' . ( $product_input_field['type_datepicker_format'] ?? '' ) . '" autocomplete="off" 
							mindate="' . ( $product_input_field['type_datepicker_mindate'] ?? '' ) . '"
							maxdate="' . ( $product_input_field['type_datepicker_maxdate'] ?? '' ) . '"
							type="' . ( $product_input_field['type'] ?? 'text' ) . '"
							display="date" name="' . $field_name . '"
							placeholder="' . ( $product_input_field['placeholder'] ?? '' ) . '"'
							. $custom_attributes . $required . '>';
						break;
					case 'weekpicker':
						/** Pricing Option */
						$field_price = isset( $product_input_field['input_restrictions_price'] ) ? $product_input_field['input_restrictions_price'] : 0;
						$datasets    = '';
						/** Pricing Option */
						$field_html = '<input ' . $datasets . $form_cart . $class . $style . ' value="' . $_value . '" data-min_qty="' . $field_min_qty . '" id="' . $field_name . '" ' . $datepicker_year .
							'firstday="' . $product_input_field['type_datepicker_firstday'] .
							'" dateformat="' . $product_input_field['type_datepicker_format'] .
							'" autocomplete="off" mindate="' . $product_input_field['type_datepicker_mindate'] .
							'" maxdate="' . $product_input_field['type_datepicker_maxdate'] .
							'" type="' . $product_input_field['type'] .
							'" display="week" name="' . $field_name .
							'" placeholder="' . $product_input_field['placeholder'] . '"' . $custom_attributes . $required . '>';
						break;
					case 'timepicker':
						/** Pricing Option */
						$field_price = isset( $product_input_field['input_restrictions_price'] ) ? $product_input_field['input_restrictions_price'] : 0;
						$datasets    = '';
						/** Pricing Option */
						$field_html = '<input ' . $datasets . $form_cart . $class . $style . ' value="' . $_value . '" data-min_qty="' . $field_min_qty . '" id="' . $field_name . '"
							interval="' . ( $product_input_field['type_timepicker_interval'] ?? '' ) . '"
							data-timeformat="' . ( $product_input_field['type_timepicker_format'] ?? '' ) . '"
							autocomplete="off" type="' . ( $product_input_field['type'] ?? 'text' ) . '"
							display="time" name="' . $field_name . '"
							placeholder="' . ( $product_input_field['placeholder'] ?? '' ) . '"'
							. $custom_attributes . $required . '>';
						break;
					case 'textarea':
						/** Pricing Option */
						$field_price = isset( $product_input_field['input_restrictions_price'] ) ? $product_input_field['input_restrictions_price'] : 0;
						$condition   = isset( $product_input_field['required_price_character_condition'] ) ? $product_input_field['required_price_character_condition'] : '';
						$datasets    = '';
						/** Pricing Option */
						$field_html = '<textarea ' . $datasets . $form_cart . $maxlength . $class . $style . ' data-min_qty="' . $field_min_qty . '" id="' . $field_name .
							'" name="' . $field_name .
							'" placeholder="' . $product_input_field['placeholder'] . '"' . $required . '>' . $_value . '</textarea>';
						break;
					case 'select':
						$select_options_raw = isset( $product_input_field['type_select_options'] ) ? $product_input_field['type_select_options']: array();
						$select_options     = alg_get_select_options( $select_options_raw, false );

						$select_options_html = '';
						$default_img         = '';
						$img                 = '';
						$show_img            = 'display: none;';
						if ( ! empty( $select_options ) ) {
							reset( $select_options );
							$key = 0;
							if ( '' === $_value && '' !== $product_input_field['placeholder'] ) {
								$_value              = 'placeholder';
								$select_options_html = '<option value="" selected="selected">' . $product_input_field['placeholder'] . '</option>';
								$default_img         = 'none';
							}
							$value = ( '' !== $_value ) ? $_value : key( $select_options );
							foreach ( $select_options as $select_option_key => $select_option_title ) {
								if ( ! empty( $select_option_title['title'] ) ) {
									$datasets             = ''; // phpcs:ignore
									$select_options_html .= '<option ' . $datasets . ' value="' . esc_attr( $select_option_key ) . '" ' . selected( $value, $select_option_key, false ) . '>';
									$select_options_html .= $select_option_title['title'];
									$select_options_html .= '</option>';
								}
								++$key;
							}
						}
						$field_html = '<select style="vertical-align: middle;" ' . $form_cart . $class . $style . ' data-min_qty="' . $field_min_qty . '" id="' . $field_name . '" name="' . $field_name . '">' . $select_options_html . '</select><img style="vertical-align: middle; margin-left:5px; ' . $show_img . ' height:50px !important; width:50px !important;" src="' . $default_img . '" />';
						break;
					case 'radio':
						$select_options_raw = isset( $product_input_field['type_select_options'] ) ? $product_input_field['type_select_options']: array();
						$select_options     = alg_get_select_options( $select_options_raw, false );

						$select_options_html = '';
						if ( ! empty( $select_options ) ) {
							reset( $select_options );
							$key   = 0;
							$value = ( '' !== $_value ) ? $_value : key( $select_options );
							foreach ( $select_options as $option_key => $option_text ) {
								if ( '' === $field_id ) {
									$field_id = $field_name . '_' . esc_attr( $option_key );
								}
								if ( ! empty( $option_text['title'] ) ) {
									$datasets             = '';
									$select_options_html .= '<label for="' . $field_name . '_' . esc_attr( $option_key ) . '" style="display:flex; align-items:center; gap:8px; cursor:pointer;"> 
										<input ' . $datasets . $form_cart . $class . $style . '
											type="radio"
											value="' . esc_attr( $option_key ) . '"
											name="' . $field_name . '"
											data-min_qty="' . $field_min_qty . '"
											id="' . $field_name . '_' . esc_attr( $option_key ) . '" ' 
											. checked( $value, $option_key, false ) . ' 
										/>
										' . $option_text['title'] . '
									</label><br>';
								}
								++$key;
							}
						}
						$field_html = $select_options_html;
						break;
					case 'country':
						/** Pricing Option */
						$field_price = isset( $product_input_field['input_restrictions_price'] ) ? $product_input_field['input_restrictions_price'] : 0;
						$datasets    = '';

						/** Pricing Option */
						$countries = WC()->countries->get_allowed_countries();
						$field     = '';
						if ( ! empty( $countries ) ) {
							$value = ( '' !== $_value ) ? $_value : key( $countries );
							$style = 'style="max-width:90%;"';
							$field = '<select ' . $datasets . $form_cart . $style . ' name="' . $field_name . '" data-min_qty="' . $field_min_qty . '" id="' . $field_name . '" class="country_to_state country_select' .
								( '' !== $product_input_field['class'] ? ' ' . $product_input_field['class'] : '' ) . '">' .
								'<option value="">' . __( 'Select a country&hellip;', 'woocommerce' ) . '</option>';
							foreach ( $countries as $ckey => $cvalue ) {
								$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . __( $cvalue, 'woocommerce' ) . '</option>'; // phpcs:ignore
							}
							$field .= '</select>';
						} else {
							$field_html = '';
						}
						$field_html = $field;
						break;
					case 'color':
						$field_price           = isset( $product_input_field['input_restrictions_price'] ) ? $product_input_field['input_restrictions_price'] : 0;
						$condition             = isset( $product_input_field['required_price_character_condition'] ) ? $product_input_field['required_price_character_condition'] : '';
						$datasets              = '';
						$input_value           = is_array( $_value ) ? implode( ', ', $_value ) : $_value;
						$allow_color_typing    = isset( $product_input_field['type_color_allow_typing'] ) && ( true === $product_input_field['type_color_allow_typing'] || 'yes' === $product_input_field['type_color_allow_typing'] ) ? true : false;
						$color_text_input_html = ( $allow_color_typing ) ? '<input style="margin-right:10px" type="text" class="alg-pif-color-text-input" ' . $form_cart . 'name="" />' : '';
						$field_html            = '<span class="alg-pif-color-wrapper">' . $color_text_input_html . '<input ' . $datasets . $form_cart . $min . $max . $step . $maxlength . $pattern . $class . $style . ' value="' . $input_value . '" type="' . $product_input_field['type'] . '" name="' . $field_name . '" data-min_qty="' . $field_min_qty . '" id="' . $field_name . '" placeholder="' . $product_input_field['placeholder'] . '"' . $custom_attributes . $required . '></span>';
						break;
					default: // number, text, file, password, email, tel etc.s.
						$field_price                        = isset( $product_input_field['input_restrictions_price'] ) ? $product_input_field['input_restrictions_price'] : '';
						$required_price_range_condition     = isset( $product_input_field['required_price_range_condition'] ) ? $product_input_field['required_price_range_condition'] : '';
						$required_price_character_condition = isset( $product_input_field['required_price_character_condition'] ) ? $product_input_field['required_price_character_condition'] : '';
						$input_value           = is_array( $_value ) ? implode( ', ', $_value ) : $_value;

						$condition   = ( 'number' === $product_input_field['type'] || 'range' === $product_input_field['type'] ) ? $required_price_range_condition : $required_price_character_condition;
						$datasets    = $_value;
						$field_html  = '<input ' . $datasets . $form_cart . $min . $max . $step . $maxlength . $pattern . $class . $style . ' value="' . $input_value . '" type="' . $product_input_field['type'] . '" name="' . $field_name . '" data-min_qty="' . $field_min_qty . '" id="' . $field_name . '" placeholder="' . $product_input_field['placeholder'] . '"' . $custom_attributes . $required . '>';
						break;
				}

				$field_html = apply_filters( 'alg_wc_pif_field_html', $field_html, $product_input_field['type'], $product_input_field, $_value, $field_name, $class, $required, $style );
				$field_id   = $field_name;
				$template   = pif_get_option( 'frontend_template', '<tr><td><label for="%field_id%">%title%</label></td><td>%field%</td></tr>' );
				$html      .= str_replace( array( '%field_id%', '%title%', '%field%' ), array( $field_id, $product_input_field['title'], $field_html ), $template );
			}
		}

		return $html;
	}

	/**
	 * Handles Uppercase input fields
	 *
	 * @version 1.1.3
	 * @since   1.1.3
	 * @param String $field_html Field HTML.
	 * @param String $field_type Field Type.
	 * @param Array  $field_options Field Options.
	 */
	public static function handle_uppercase_input_field( $field_html, $field_type, $field_options ) {
		if ( 'text' !== $field_type && 'textarea' !== $field_type ) {
			return $field_html;
		}
		if ( isset( $field_options['to_uppercase'] ) && 'all_letters_uppercase' === $field_options['to_uppercase'] ) {
			$field_html = preg_replace( '/(<*\b[^><]*)>/i', '$1 data-uppercase="true" oninput="this.value = this.value.toUpperCase()">', $field_html );
		} elseif ( isset( $field_options['to_uppercase'] ) && 'only_first_letter_uppercase' === $field_options['to_uppercase'] ) {
			$field_html = preg_replace( '/(<*\b[^><]*)>/i', '$1 data-uppercase="true" oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);">', $field_html );
		}
		return $field_html;
	}
}
