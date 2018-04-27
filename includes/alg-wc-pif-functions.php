<?php
/**
 * Product Input Fields for WooCommerce - Functions
 *
 * @version 1.1.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'alg_display_product_input_fields' ) ) {
	/**
	 * alg_display_product_input_fields.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_display_product_input_fields() {
		echo get_wc_pif_option( 'frontend_before', '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">' );
		$scopes = array( 'global', 'local' );
		foreach ( $scopes as $scope ) {
			if ( 'yes' === get_wc_pif_option( $scope . '_enabled', 'yes' ) ) {
				echo alg_get_frontend_product_input_fields( $scope );
			}
		}
		echo get_wc_pif_option( 'frontend_after', '</table>' );
	}
}
add_shortcode( 'alg_display_product_input_fields', 'alg_display_product_input_fields' );

if ( ! function_exists( 'alg_get_uploads_dir' ) ) {
	/**
	 * alg_get_uploads_dir.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_get_uploads_dir( $subdir = '' ) {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		$upload_dir = $upload_dir . '/woocommerce_uploads/alg_uploads';
		if ( '' != $subdir ) {
			$upload_dir = $upload_dir . '/' . $subdir;
		}
		return $upload_dir;
	}
}

if ( ! function_exists( 'alg_get_all_values' ) ) {
	/**
	 * alg_get_all_values.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_get_all_values( $scope, $field_nr, $product_id = 0 ) {
		$values = array();
		$options = alg_get_product_input_fields_options();
		foreach ( $options as $option ) {
			if ( in_array( $option['type'], array( 'title', 'sectionend' ) ) ) {
				continue;
			}
			$option_id = ALG_WC_PIF_ID . '_' . $option['id'] . '_' . $scope . '_' . $field_nr;
			$values[ $option['id'] ] = ( 'local' === $scope ) ? get_post_meta( $product_id, '_' . $option_id, true ) : get_option( $option_id, $option['default'] );
		}
		return $values;
	}
}

if ( ! function_exists( 'alg_date_format_php_to_js' ) ) {
	/*
	 * Matches each symbol of PHP date format standard with jQuery equivalent codeword.
	 *
	 * @see     http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_date_format_php_to_js( $php_format ) {
		$SYMBOLS_MATCHING = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => ''
		);
		$jqueryui_format = "";
		$escaping = false;
		for ( $i = 0; $i < strlen( $php_format ); $i++ ) {
			$char = $php_format[ $i ];
			if ( $char === '\\' ) { // PHP date format escaping character
				$i++;
				$jqueryui_format .= ( $escaping ) ? $php_format[ $i ] : '\'' . $php_format[ $i ];
				$escaping = true;
			} else {
				if ( $escaping ) {
					$jqueryui_format .= "'";
					$escaping = false;
				}
				$jqueryui_format .= ( isset( $SYMBOLS_MATCHING[ $char ] ) ) ? $SYMBOLS_MATCHING[ $char ] : $char;
			}
		}
		return $jqueryui_format;
	}
}

if ( ! function_exists( 'alg_get_select_options' ) ) {
	/*
	 * alg_get_select_options()
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  array
	 */
	function alg_get_select_options( $select_options_raw, $do_sanitize = true ) {
		$select_options_raw = explode( PHP_EOL, $select_options_raw );
		$select_options = array();
		foreach ( $select_options_raw as $select_options_title ) {
			$select_options_title = str_replace( "\n", '', $select_options_title );
			$select_options_title = str_replace( "\r", '', $select_options_title );
			$select_options_key = ( $do_sanitize ) ? sanitize_title( $select_options_title ) : $select_options_title;
			$select_options[ $select_options_key ] = $select_options_title;
		}
		return $select_options;
	}
}

if ( ! function_exists( 'alg_get_frontend_product_input_fields' ) ) {
	/**
	 * alg_get_frontend_product_input_fields.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    (maybe) required for 'radio'; and maybe for 'select' and 'country'
	 */
	function alg_get_frontend_product_input_fields( $scope ) {
		global $product;
		if ( ! $product ) {
			return '';
		}
		$html = '';
		$product_id = ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ? $product->id : $product->get_id() );
		$total_number = apply_filters( 'alg_wc_product_input_fields', 1, ( 'local' === $scope ? 'per_product_total_fields' : 'all_products_total_fields' ), $product_id );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			$product_input_field = alg_get_all_values( $scope, $i, $product_id );
			if ( 'yes' === $product_input_field['enabled'] ) {
				// Required
				$required = '';
				if ( 'yes' === $product_input_field['required'] ) {
					$product_input_field['title'] .= get_wc_pif_option( 'frontend_required_html', '&nbsp;<abbr class="required" title="required">*</abbr>' );
					if ( 'yes' === get_wc_pif_option( 'frontend_required_js', 'yes' ) ) {
						$required = ' required';
					}
				}
				// Datepicker/Weekpicker
				if ( '' == $product_input_field['type_datepicker_format'] ) {
					$product_input_field['type_datepicker_format'] = get_option( 'date_format' );
				}
				$product_input_field['type_datepicker_format'] = alg_date_format_php_to_js( $product_input_field['type_datepicker_format'] );
				$datepicker_year = ( 'yes' === $product_input_field['type_datepicker_addyear'] ) ?
					'changeyear="1" yearrange="' . $product_input_field['type_datepicker_yearrange'] . '" ' : '';
				// File
				$custom_attributes = ( 'file' === $product_input_field['type'] ) ? ' accept="' . $product_input_field['type_file_accept'] . '"' : '';
				// Class and style
				$class = ( '' != $product_input_field['class'] ? ' class="' . $product_input_field['class'] . '"' : '' );
				$style = ( '' != $product_input_field['style'] ? ' style="' . $product_input_field['style'] . '"' : '' );
				// Input restrictions
				$min       = ( '' != $product_input_field['input_restrictions_min'] )       ? ' min="'       . $product_input_field['input_restrictions_min']       . '"' : '';
				$max       = ( '' != $product_input_field['input_restrictions_max'] )       ? ' max="'       . $product_input_field['input_restrictions_max']       . '"' : '';
				$step      = ( '' != $product_input_field['input_restrictions_step'] )      ? ' step="'      . $product_input_field['input_restrictions_step']      . '"' : '';
				$maxlength = ( '' != $product_input_field['input_restrictions_maxlength'] ) ? ' maxlength="' . $product_input_field['input_restrictions_maxlength'] . '"' : '';
				$pattern   = ( '' != $product_input_field['input_restrictions_pattern'] )   ? ' pattern="'   . $product_input_field['input_restrictions_pattern']   . '"' : '';
				// Field name and value
				$field_name = ALG_WC_PIF_ID . '_' . $scope . '_' . $i;
				$_value = ( 'yes' === get_wc_pif_option( 'frontend_refill', 'yes' ) && isset( $_POST[ $field_name ] ) ) ?
					$_POST[ $field_name ] : $product_input_field['default_value'];
				$field_id = '';
				// Field HTML
				$field_html = '';
				switch ( $product_input_field['type'] ) {
					case 'checkbox':
						$checked = checked( $_value, 'yes', false );
						$field_html = '<input type="hidden" value="no" name="' . $field_name . '">' . '<input' . $class . $style . ' id="' . $field_name .
							'" type="' . $product_input_field['type'] .
							'" value="yes" name="' . $field_name . '"' . $custom_attributes . $checked . $required . '>';
						break;
					case 'datepicker':
						$field_html = '<input' . $class . $style . ' value="'. $_value . '" id="' . $field_name . '" ' . $datepicker_year .
							'firstday="' . $product_input_field['type_datepicker_firstday'] .
							'" dateformat="' . $product_input_field['type_datepicker_format'] .
							'" mindate="' . $product_input_field['type_datepicker_mindate'] .
							'" maxdate="' . $product_input_field['type_datepicker_maxdate'] .
							'" type="' . $product_input_field['type'] .
							'" display="date" name="' . $field_name .
							'" placeholder="' . $product_input_field['placeholder'] . '"' . $custom_attributes . $required . '>';
						break;
					case 'weekpicker':
						$field_html = '<input' . $class . $style . ' value="'. $_value . '" id="' . $field_name . '" ' . $datepicker_year .
							'firstday="' . $product_input_field['type_datepicker_firstday'] .
							'" dateformat="' . $product_input_field['type_datepicker_format'] .
							'" mindate="' . $product_input_field['type_datepicker_mindate'] .
							'" maxdate="' . $product_input_field['type_datepicker_maxdate'] .
							'" type="' . $product_input_field['type'] .
							'" display="week" name="' . $field_name .
							'" placeholder="' . $product_input_field['placeholder'] . '"' . $custom_attributes . $required . '>';
						break;
					case 'timepicker':
						$field_html = '<input' . $class . $style . ' value="'. $_value . '" id="' . $field_name .
							'" interval="' . $product_input_field['type_timepicker_interval'] .
							'" timeformat="' . $product_input_field['type_timepicker_format'] .
							'" type="' . $product_input_field['type'] .
							'" display="time" name="' . $field_name .
							'" placeholder="' . $product_input_field['placeholder'] . '"' . $custom_attributes . $required . '>';
						break;
					case 'textarea':
						$field_html = '<textarea' . $class . $style . ' id="' . $field_name .
							'" name="' . $field_name .
							'" placeholder="' . $product_input_field['placeholder'] . '"' . $required . '>' . $_value . '</textarea>';
						break;
					case 'select':
						$select_options_raw = $product_input_field['type_select_options'];
						$select_options = alg_get_select_options( $select_options_raw, false );
						if ( '' != $product_input_field['placeholder'] ) {
							$select_options = array_merge( array( '' => $product_input_field['placeholder'] ), $select_options );
						}
						$select_options_html = '';
						if ( ! empty( $select_options ) ) {
							reset( $select_options );
							$value = ( '' != $_value ) ? $_value : key( $select_options );
							foreach ( $select_options as $select_option_key => $select_option_title ) {
								$select_options_html .= '<option value="' . $select_option_key . '" ' . selected( $value, $select_option_key, false ) . '>';
								$select_options_html .= $select_option_title;
								$select_options_html .= '</option>';
							}
						}
						$field_html = '<select' . $class . $style . ' id="' . $field_name . '" name="' . $field_name . '">' . $select_options_html . '</select>';
						break;
					case 'radio':
						$select_options_raw = $product_input_field['type_select_options'];
						$select_options = alg_get_select_options( $select_options_raw, false );
						$select_options_html = '';
						if ( ! empty( $select_options ) ) {
							reset( $select_options );
							$value = ( '' != $_value ) ? $_value : key( $select_options );
							foreach ( $select_options as $option_key => $option_text ) {
								if ( '' == $field_id ) {
									$field_id = $field_name . '_' . esc_attr( $option_key );
								}
								$select_options_html .= '<input' . $class . $style . ' type="radio" value="' . esc_attr( $option_key ) .
									'" name="' . $field_name . '" id="' . $field_name . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
								$select_options_html .= '<label for="' . $field_name . '_' . esc_attr( $option_key ) .
									'">' . $option_text . '</label><br>';
							}
						}
						$field_html = $select_options_html;
						break;
					case 'country':
						$countries = WC()->countries->get_allowed_countries();
						if ( sizeof( $countries ) > 1 ) {
							$value = ( '' != $_value ) ? $_value : key( $countries );
							$field = '<select' . $style . ' name="' . $field_name . '" id="' . $field_name . '" class="country_to_state country_select' .
								( '' != $product_input_field['class'] ? ' ' . $product_input_field['class'] : '' ) . '">' .
								'<option value="">'.__( 'Select a country&hellip;', 'woocommerce' ) .'</option>';
							foreach ( $countries as $ckey => $cvalue ) {
								$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) .'>'.__( $cvalue, 'woocommerce' ) .'</option>';
							}
							$field .= '</select>';
						}
						$field_html = $field;
						break;
					default: // 'number' 'text' 'file' 'password' 'email' 'tel' 'color' etc.
						$field_html = '<input' . $min . $max . $step . $maxlength . $pattern . $class . $style . ' value="'. $_value .
							'" type="' . $product_input_field['type'] .
							'" name="' . $field_name .
							'" id="' . $field_name .
							'" placeholder="' . $product_input_field['placeholder'] . '"' . $custom_attributes . $required . '>';
						break;
				}
				$field_html = apply_filters( 'alg_wc_pif_field_html', $field_html, $product_input_field['type'] );
				$field_id = ( 'radio' === $product_input_field['type'] ) ? $field_id : $field_name;
				$template = get_wc_pif_option( 'frontend_template', '<tr><td><label for="%field_id%">%title%</label></td><td>%field%</td></tr>' );
				$html .= str_replace( array( '%field_id%', '%title%', '%field%' ), array( $field_id, $product_input_field['title'], $field_html ), $template );
			}
		}
		return $html;
	}
}

if ( ! function_exists( 'alg_get_table_html' ) ) {
	/**
	 * alg_get_table_html.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args = array_merge( $defaults, $args );
		extract( $args );
		$table_class = ( '' == $table_class ) ? '' : ' class="' . $table_class . '"';
		$table_style = ( '' == $table_style ) ? '' : ' style="' . $table_style . '"';
		$row_styles  = ( '' == $row_styles )  ? '' : ' style="' . $row_styles  . '"';
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr' . $row_styles . '>';
			foreach( $row as $column_number => $value ) {
				$th_or_td = ( ( 0 === $row_number && 'horizontal' === $table_heading_type ) || ( 0 === $column_number && 'vertical' === $table_heading_type ) ) ? 'th' : 'td';
				$column_class = ( ! empty( $columns_classes ) && isset( $columns_classes[ $column_number ] ) ) ? ' class="' . $columns_classes[ $column_number ] . '"' : '';
				$column_style = ( ! empty( $columns_styles ) && isset( $columns_styles[ $column_number ] ) ) ? ' style="' . $columns_styles[ $column_number ] . '"' : '';
				$html .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}
