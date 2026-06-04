<?php
/**
 * Functions File
 * 
 * @package PIF/Functions
 * @version 1.0.0
 * @since 1.0.0
 */

function pif_get_option( $option_name, $default = false ) {
    $options = get_option( 'pif_general_settings', array() );
    return isset( $options[ $option_name ] ) ? $options[ $option_name ] : $default;
}

function pif_get_field_settings( $field_id ) {
    $all_field_settings = get_option( 'pif_field_settings', array() );
    return isset( $all_field_settings[ $field_id ] ) ? $all_field_settings[ $field_id ] : array();
}

function sort_fields_by_order( $fields ) {
	usort($fields, function ($a, $b) {
		return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
	});
	return $fields;
}

/**
 * Get available field types.
 *
 * @return array
 */
function pif_get_field_types() {
	return array(
		'text'       => __( 'Text', 'product-input-fields-for-woocommerce' ),
		'textarea'   => __( 'Textarea', 'product-input-fields-for-woocommerce' ),
		'number'     => __( 'Number', 'product-input-fields-for-woocommerce' ),
		'checkbox'   => __( 'Checkbox', 'product-input-fields-for-woocommerce' ),
		'color'      => __( 'Color', 'product-input-fields-for-woocommerce' ),
		'file'       => __( 'File', 'product-input-fields-for-woocommerce' ),
		'datepicker' => __( 'Datepicker', 'product-input-fields-for-woocommerce' ),
		'weekpicker' => __( 'Weekpicker', 'product-input-fields-for-woocommerce' ),
		'timepicker' => __( 'Timepicker', 'product-input-fields-for-woocommerce' ),
		'select'     => __( 'Select', 'product-input-fields-for-woocommerce' ),
		'radio'      => __( 'Radio', 'product-input-fields-for-woocommerce' ),
		'password'   => __( 'Password', 'product-input-fields-for-woocommerce' ),
		'country'    => __( 'Country', 'product-input-fields-for-woocommerce' ),
		'email'      => __( 'Email', 'product-input-fields-for-woocommerce' ),
		'tel'        => __( 'Phone', 'product-input-fields-for-woocommerce' ),
		'search'     => __( 'Search', 'product-input-fields-for-woocommerce' ),
		'url'        => __( 'URL', 'product-input-fields-for-woocommerce' ),
		'range'      => __( 'Range', 'product-input-fields-for-woocommerce' ),
	);
}

/**
 * Get options for product input fields.
 */
function pif_get_old_options() {
	// Translators: %s is the type of input that needs to be filled.
	$fill_only_on_type_message = __( 'Fill this only if <strong>%s</strong> type is selected.', 'product-input-fields-for-woocommerce' );
	$options                   = array(

		// Main.
		array(
			'id'    => 'options',
			'title' => __( 'Options', 'product-input-fields-for-woocommerce' ),
			'type'  => 'title',
		),
		array(
			'id'      => 'enabled',
			'title'   => __( 'Enable/Disable', 'product-input-fields-for-woocommerce' ),
			'desc'    => '<strong>' . __( 'Enable', 'product-input-fields-for-woocommerce' ) . '</strong>',
			'type'    => 'checkbox',
			'default' => 'no',
		),
		array(
			'id'   => 'options',
			'type' => 'sectionend',
		),

		// General.
		array(
			'id'    => 'general_options',
			'title' => __( 'General Options', 'product-input-fields-for-woocommerce' ),
			'type'  => 'title',
		),
		array(
			'id'      => 'type',
			'title'   => __( 'Type', 'product-input-fields-for-woocommerce' ),
			'type'    => 'select',
			'default' => 'text',
			'options' => array(
				'text'       => __( 'Text', 'product-input-fields-for-woocommerce' ),
				'textarea'   => __( 'Textarea', 'product-input-fields-for-woocommerce' ),
				'number'     => __( 'Number', 'product-input-fields-for-woocommerce' ),
				'checkbox'   => __( 'Checkbox', 'product-input-fields-for-woocommerce' ),
				'color'      => __( 'Color', 'product-input-fields-for-woocommerce' ),
				'file'       => __( 'File', 'product-input-fields-for-woocommerce' ),
				'datepicker' => __( 'Datepicker', 'product-input-fields-for-woocommerce' ),
				'weekpicker' => __( 'Weekpicker', 'product-input-fields-for-woocommerce' ),
				'timepicker' => __( 'Timepicker', 'product-input-fields-for-woocommerce' ),
				'select'     => __( 'Select', 'product-input-fields-for-woocommerce' ),
				'radio'      => __( 'Radio', 'product-input-fields-for-woocommerce' ),
				'password'   => __( 'Password', 'product-input-fields-for-woocommerce' ),
				'country'    => __( 'Country', 'product-input-fields-for-woocommerce' ),
				'email'      => __( 'Email', 'product-input-fields-for-woocommerce' ),
				'tel'        => __( 'Phone', 'product-input-fields-for-woocommerce' ),
				'search'     => __( 'Search', 'product-input-fields-for-woocommerce' ),
				'url'        => __( 'URL', 'product-input-fields-for-woocommerce' ),
				'range'      => __( 'Range', 'product-input-fields-for-woocommerce' ),
			),
		),
		array(
			'id'      => 'required',
			'title'   => __( 'Is Required', 'product-input-fields-for-woocommerce' ),
			'desc'    => __( 'Required', 'product-input-fields-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
		),
		array(
			'id'                => 'title',
			'title'             => __( 'Title', 'product-input-fields-for-woocommerce' ),
			'type'              => 'textarea',
			'default'           => __( 'Input Field', 'product-input-fields-for-woocommerce' ),
			'css'               => 'width:300px;',
			'custom_attributes' => array( 'required' => 'required' ),
		),
		array(
			'id'      => 'placeholder',
			'title'   => __( 'Placeholder', 'product-input-fields-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '',
			'css'     => 'width:300px;',
		),
		array(
			'id'       => 'default_value',
			'desc_tip' => __( 'Default value. E.g. for <strong>Color</strong> type enter color code; for <strong>Checkbox</strong> type enter <em>yes</em> or <em>no</em>.', 'product-input-fields-for-woocommerce' ) . ' ' . __( 'Leave blank to disable.', 'product-input-fields-for-woocommerce' ),
			'title'    => __( 'Default Value', 'product-input-fields-for-woocommerce' ),
			'type'     => 'textarea',
			'default'  => '',
			'css'      => 'width:300px;',
		),
		array(
			'id'      => 'class',
			'title'   => __( 'Class', 'product-input-fields-for-woocommerce' ),
			'type'    => 'text',
			'default' => '',
			'css'     => 'width:300px;',
		),
		array(
			'id'      => 'style',
			'title'   => __( 'Style', 'product-input-fields-for-woocommerce' ),
			'type'    => 'text',
			'default' => '',
			'css'     => 'width:300px;',
		),
		array(
			'id'       => 'required_message',
			'title'    => __( 'Message on Required', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Used if "Add HTML Required Attribute" option in plugin\'s "General > Frontend Options" settings is disabled, or product input fields are displayed outside the add to cart button form.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'textarea',
			'default'  => __( 'Field "%title%" is required!', 'product-input-fields-for-woocommerce' ),
			'css'      => 'width:300px;',
		),
		array(
			'id'   => 'general_options',
			'type' => 'sectionend',
		),

		// Input Restrictions.
		array(
			'id'    => 'input_restrictions_options',
			'title' => __( 'Input Restrictions', 'product-input-fields-for-woocommerce' ),
			'type'  => 'title',
		),
		array(
			'id'       => 'input_restrictions_min',
			'title'    => __( 'Min', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Minimum value for an input field. E.g. for <strong>Number/Range</strong> type.', 'product-input-fields-for-woocommerce' ) . ' ' . __( 'Leave blank to disable.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'text',
			'default'  => '',
		),
		array(
			'id'       => 'input_restrictions_max',
			'title'    => __( 'Max', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Maximum value for an input field. E.g. for <strong>Number/Range</strong> type.', 'product-input-fields-for-woocommerce' ) . ' ' . __( 'Leave blank to disable.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'text',
			'default'  => '',
		),
		array(
			'id'       => 'input_restrictions_step',
			'title'    => __( 'Step', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Legal number intervals for an input field. E.g. for <strong>Number/Range</strong> type.', 'product-input-fields-for-woocommerce' ) . ' ' . __( 'Leave blank to disable.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'text',
			'default'  => '',
		),
		array(
			'id'       => 'input_restrictions_maxlength',
			'title'    => __( 'Max Length', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Maximum number of character for an input field. E.g. for <strong>Text</strong> type.', 'product-input-fields-for-woocommerce' ) . ' ' . __( 'Leave blank to disable.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'text',
			'default'  => '',
		),
		array(
			'id'       => 'input_restrictions_pattern',
			'title'    => __( 'Pattern', 'product-input-fields-for-woocommerce' ),
			'desc'     => __( 'Visit <a href="https://www.w3schools.com/tags/att_input_pattern.asp" target="_blank">HTML pattern Attribute</a> for valid option formats.', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Regular expression to check the input value against. E.g. for <strong>Text</strong> type.', 'product-input-fields-for-woocommerce' ) . ' ' . __( 'Leave blank to disable.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'text',
			'default'  => '',
		),
		array(
			'id'   => 'input_restrictions_options',
			'type' => 'sectionend',
		),

		// Checkbox.
		array(
			'id'    => 'type_checkbox_options',
			'title' => __( 'Checkbox Type Options', 'product-input-fields-for-woocommerce' ),
			'desc'  => sprintf( $fill_only_on_type_message, __( 'Checkbox', 'product-input-fields-for-woocommerce' ) ),
			'type'  => 'title',
		),
		array(
			'id'      => 'type_checkbox_yes',
			'title'   => __( 'Value for ON', 'product-input-fields-for-woocommerce' ),
			'type'    => 'text',
			'default' => __( 'Yes', 'product-input-fields-for-woocommerce' ),
		),
		array(
			'id'      => 'type_checkbox_no',
			'title'   => __( 'Value for OFF', 'product-input-fields-for-woocommerce' ),
			'type'    => 'text',
			'default' => __( 'No', 'product-input-fields-for-woocommerce' ),
		),
		array(
			'id'   => 'type_checkbox_options',
			'type' => 'sectionend',
		),

		// File.
		array(
			'id'    => 'type_file_options',
			'title' => __( 'File Type Options', 'product-input-fields-for-woocommerce' ),
			'desc'  => sprintf( $fill_only_on_type_message, __( 'File', 'product-input-fields-for-woocommerce' ) ),
			'type'  => 'title',
		),
		array(
			'id'       => 'type_file_accept',
			'title'    => __( 'Accepted File Types', 'product-input-fields-for-woocommerce' ),
			'desc'     => __( 'Visit <a href="https://www.w3schools.com/tags/att_input_accept.asp" target="_blank">documentation on input accept attribute</a> for valid option formats.', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'E.g.: ".jpg,.jpeg,.png". Leave blank to accept all files.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'text',
			'default'  => __( '.jpg,.jpeg,.png', 'product-input-fields-for-woocommerce' ),
		),
		array(
			'id'      => 'type_file_wrong_type_msg',
			'title'   => __( 'Message on Wrong File Type', 'product-input-fields-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Wrong file type!', 'product-input-fields-for-woocommerce' ),
			'css'     => 'width:300px;',
		),
		array(
			'id'                => 'type_file_max_size',
			'title'             => __( 'Max File Size', 'product-input-fields-for-woocommerce' ),
			'desc'              => __( 'bytes.', 'product-input-fields-for-woocommerce' ),
			'desc_tip'          => __( 'Set to zero to accept all files.', 'product-input-fields-for-woocommerce' ),
			'type'              => 'number',
			'default'           => 0,
			'custom_attributes' => array( 'min' => 0 ),
		),
		array(
			'id'      => 'type_file_max_size_msg',
			'title'   => __( 'Message on Max File Size Exceeded', 'product-input-fields-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'File is too big!', 'product-input-fields-for-woocommerce' ),
			'css'     => 'width:300px;',
		),
		array(
			'id'   => 'type_file_options',
			'type' => 'sectionend',
		),

		// Datepicker/Weekpicker.
		array(
			'id'    => 'type_date_options',
			'title' => __( 'Datepicker/Weekpicker Type Options', 'product-input-fields-for-woocommerce' ),
			'desc'  => sprintf( $fill_only_on_type_message, __( 'Datepicker/Weekpicker', 'product-input-fields-for-woocommerce' ) ),
			'type'  => 'title',
		),
		array(
			'id'       => 'type_datepicker_format',
			'title'    => __( 'Date Format', 'product-input-fields-for-woocommerce' ),
			'desc'     => __( 'Visit <a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">documentation on date and time formatting</a> for valid date formats.', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Leave blank to use your current WordPress format', 'product-input-fields-for-woocommerce' ) . ': ' . get_option( 'date_format' ),
			'type'     => 'text',
			'default'  => '',
		),
		array(
			'id'      => 'type_datepicker_mindate',
			'title'   => __( 'Min Date', 'product-input-fields-for-woocommerce' ),
			'desc'    => __( 'days.', 'product-input-fields-for-woocommerce' ),
			'type'    => 'number',
			'default' => -365,
		),
		array(
			'id'      => 'type_datepicker_maxdate',
			'title'   => __( 'Max Date', 'product-input-fields-for-woocommerce' ),
			'desc'    => __( 'days.', 'product-input-fields-for-woocommerce' ),
			'type'    => 'number',
			'default' => 365,
		),
		array(
			'id'      => 'type_datepicker_addyear',
			'title'   => __( 'Add Year Selector', 'product-input-fields-for-woocommerce' ),
			'desc'    => __( 'Add', 'product-input-fields-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
		),
		array(
			'id'       => 'type_datepicker_yearrange',
			'title'    => __( 'Year Selector - Year Range', 'product-input-fields-for-woocommerce' ),
			'desc'     => __( 'Visit <a href="https://api.jqueryui.com/datepicker/#option-yearRange" target="_blank">Datepicker > yearRange</a> documentation for valid year range formats.', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'Remember to set "Min Date" and "Max Date" options accordingly.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'text',
			'default'  => 'c-10:c+10',
		),
		array(
			'id'      => 'type_datepicker_firstday',
			'title'   => __( 'First Week Day', 'product-input-fields-for-woocommerce' ),
			'type'    => 'select',
			'default' => 0,
			'options' => array(
				__( 'Sunday', 'product-input-fields-for-woocommerce' ),
				__( 'Monday', 'product-input-fields-for-woocommerce' ),
				__( 'Tuesday', 'product-input-fields-for-woocommerce' ),
				__( 'Wednesday', 'product-input-fields-for-woocommerce' ),
				__( 'Thursday', 'product-input-fields-for-woocommerce' ),
				__( 'Friday', 'product-input-fields-for-woocommerce' ),
				__( 'Saturday', 'product-input-fields-for-woocommerce' ),
			),
		),
		array(
			'id'   => 'type_date_options',
			'type' => 'sectionend',
		),

		// Timepicker.
		array(
			'id'    => 'type_time_options',
			'title' => __( 'Timepicker Type Options', 'product-input-fields-for-woocommerce' ),
			'desc'  => sprintf( $fill_only_on_type_message, __( 'Timepicker', 'product-input-fields-for-woocommerce' ) ),
			'type'  => 'title',
		),
		array(
			'id'      => 'type_timepicker_format',
			'title'   => __( 'Time Format', 'product-input-fields-for-woocommerce' ),
			'desc'    => __( 'Visit <a href="http://timepicker.co/options/" target="_blank">timepicker options page</a> for valid time formats.', 'product-input-fields-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'hh:mm p',
		),
		array(
			'id'                => 'type_timepicker_interval',
			'title'             => __( 'Interval', 'product-input-fields-for-woocommerce' ),
			'desc'              => __( 'minutes.', 'product-input-fields-for-woocommerce' ),
			'type'              => 'number',
			'default'           => 15,
			'custom_attributes' => array( 'min' => 1 ),
		),
		array(
			'id'   => 'type_time_options',
			'type' => 'sectionend',
		),

		// Color.
		array(
			'id'    => 'color_options',
			'title' => __( 'Color Type Options', 'product-input-fields-for-woocommerce' ),
			'desc'  => sprintf( $fill_only_on_type_message, __( 'Color', 'product-input-fields-for-woocommerce' ) ),
			'type'  => 'title',
		),
		array(
			'id'      => 'type_color_allow_typing',
			'title'   => __( 'Allow color typing', 'product-input-fields-for-woocommerce' ),
			'desc'    => __( 'Allows typing or pasting the color manually', 'product-input-fields-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => '',
		),
		array(
			'id'   => 'color_options',
			'type' => 'sectionend',
		),

		// Select/Radio.
		array(
			'id'    => 'type_select_radio_options',
			'title' => __( 'Select/Radio Type Options', 'product-input-fields-for-woocommerce' ),
			'desc'  => sprintf( $fill_only_on_type_message, __( 'Select/Radio', 'product-input-fields-for-woocommerce' ) ),
			'type'  => 'title',
		),
		array(
			'id'       => 'select_radio_option_type',
			'title'    => __( 'Options', 'product-input-fields-for-woocommerce' ),
			'desc_tip' => __( 'One option per line.', 'product-input-fields-for-woocommerce' ),
			'type'     => 'textarea',
			'default'  => '',
			'css'      => 'height:150px;width:300px;',
		),
		array(
			'id'   => 'type_select_radio_options',
			'type' => 'sectionend',
		),
	);
	return apply_filters( 'alg_product_input_fields_options', $options );
}


/**
 * Function to check input fileds are enabled or not.
 *
 * @version 1.3.6
 * @since   1.3.6
 */
function pif_check_field_exisits() {
	global $product;
	$check_field_exisits = false;
	$product_id          = $product->get_id();
	$input_counts_local  = get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_local_total_number', true );
	$input_counts_global = get_option( 'alg_wc_pif_global_total_number', 0 );
	for ( $i = 1; $i <= $input_counts_global; $i++ ) {
		if ( 'yes' === get_option( 'alg_wc_pif_enabled_global_' . $i ) ) {
			$check_field_exisits = true;
			break;
		}
	}
	if ( ! $check_field_exisits ) {
		for ( $i = 1; $i <= $input_counts_local; $i++ ) {
			if ( 'yes' === get_post_meta( $product_id, '_' . ALG_WC_PIF_ID . '_enabled_local_' . $i, true ) ) {
				$check_field_exisits = true;
				break;
			}
		}
	}
	return $check_field_exisits;
}

/**
 * Gets browser name
 *
 * @version 1.1.7
 * @since   1.1.7
 * @return string
 */
function pif_get_browser_name() {
	$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ); //phpcs:ignore
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
function pif_browser_can_render_color_type() {
	if ( pif_get_browser_name() === 'Safari' || pif_get_browser_name() === 'Opera' ) {
		return false;
	} else {
		return true;
	}
}

if ( ! function_exists( 'alg_price_calculation_conditions' ) ) {
	/**
	 * Funtion alg_price_calculation_conditions
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param string $type Calculation Type.
	 * @param number $qty Qantity.
	 * @param number $price Product Price.
	 * @param number $field_price Option Price.
	 * @param mixed  $option_value Option Value.
	 */
	function alg_price_calculation_conditions( $type, $qty, $price, $field_price, $option_value ) {
		switch ( $type ) {
			case '':
			case 'flat':
				return (float) $field_price;
				break; // phpcs:ignore
			case 'qty':
				return $qty * (float) $field_price;
				break; // phpcs:ignore
			case 'percent':
				return ( ( (float) $price / 100 ) * (float) $field_price );
				break; // phpcs:ignore
			case 'percent_qty':
				return ( ( ( (float) $price / 100 ) * (float) $field_price ) * $qty );
				break; // phpcs:ignore
			case 'nos':
				return ( (float) $field_price * (float) $option_value );
				break; // phpcs:ignore
			case 'nos_qty':
				return ( (float) $field_price * (float) $option_value * $qty );
				break; // phpcs:ignore
			case 'nos_char':
				return ( (float) $field_price * strlen( $option_value ) );
				break; // phpcs:ignore
			case 'nos_char_qty':
				return ( (float) $field_price * strlen( $option_value ) * $qty );
				break; // phpcs:ignore
		}
	}
}

if ( ! function_exists( 'alg_calculate_price' ) ) {
	/**
	 * Function alg_calculate_price.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param Array  $fields Selected Fields.
	 * @param number $qty Quantity.
	 * @param number $price Product price.
	 */
	function alg_calculate_price( $fields, $qty, $price ) {
		if ( is_array( $fields ) ) {
			$new_price = $qty * (float) $price;
			foreach ( $fields as $key => $field ) {
				if ( isset( $field['required_price'] ) && $field['required_price'] && array_key_exists( '_value', $field ) ) {
					$min_qty   = isset( $field['field_min_qty'] ) ? $field['field_min_qty'] : '';
					$field_val = ( ! empty( $field['_value'] ) ) ? $field['_value'] : '';
					if ( ( $min_qty <= $qty ) && ! empty( $field_val ) ) {
						switch ( $field['type'] ) {
							case 'url':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'tel':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'email':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'country':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								break;
							case 'password':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'timepicker':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								break;
							case 'weekpicker':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								break;
							case 'datepicker':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								break;
							case 'file':
								$f_price = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';

								$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price, $field['_value'] );
								break;
							case 'search':
								$f_price   = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';
								$condition = isset( $field['required_price_character_condition'] ) ? $field['required_price_character_condition'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( $condition, $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'color':
								$f_price   = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';
								$condition = isset( $field['required_price_character_condition'] ) ? $field['required_price_character_condition'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( $condition, $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'textarea':
								$f_price   = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';
								$condition = isset( $field['required_price_character_condition'] ) ? $field['required_price_character_condition'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( $condition, $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'text':
								$f_price   = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';
								$condition = isset( $field['required_price_character_condition'] ) ? $field['required_price_character_condition'] : '';

								if ( '' !== $field['_value'] ) {
									$new_price += alg_price_calculation_conditions( $condition, $qty, $price, $f_price, $field['_value'] );
								}
								break;
							case 'range':
								$f_price   = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';
								$condition = isset( $field['required_price_range_condition'] ) ? $field['required_price_range_condition'] : '';

								$new_price += alg_price_calculation_conditions( $condition, $qty, $price, $f_price, $field['_value'] );
								break;
							case 'number':
								$f_price   = isset( $field['input_restrictions_price'] ) ? $field['input_restrictions_price'] : '';
								$condition = isset( $field['required_price_range_condition'] ) ? $field['required_price_range_condition'] : '';

								$new_price += alg_price_calculation_conditions( $condition, $qty, $price, $f_price, $field['_value'] );
								break;
							case 'multicheck':
								$select_options = isset( $field['type_select_options'] ) ? $field['type_select_options'] : '';

								foreach ( $field['_value'] as $k => $val ) {
									$row 	    = pif_get_option_row( $field['type_select_options'], $val );
									$new_price += alg_price_calculation_conditions( $row['type_select_options_condition'], $qty, $price, $row['type_select_options_price'], $field['_value'] );
								}

								break;
							case 'checkbox':
							case 'select':
							case 'radio':
								$select_options = isset( $field['type_select_options'] ) ? $field['type_select_options'] : '';

								if ( is_array( $field['_value'] ) ) {
									foreach ( $field['_value'] as $k => $val ) {
										$row = pif_get_option_row( $field['type_select_options'], $val );
										if ( !$row ) {
											break;
										}
										$new_price += alg_price_calculation_conditions( $row['type_select_options_condition'], $qty, $price, $row['type_select_options_price'], $field['_value'] );
									}
								} else {
									$row = pif_get_option_row( $field['type_select_options'], $field['_value'] );
									if ( !$row ) {
										break;
									}
									$new_price += alg_price_calculation_conditions( $row['type_select_options_condition'], $qty, $price, $row['type_select_options_price'], $field['_value'] );
								}

								break;
							case 'country':
								$options = $field['type_select_options_option'];
								$f_price = $field['type_select_options_price'];

								$new_price += alg_price_calculation_conditions( 'flat', $qty, $price, $f_price[0], $field['_value'] );
								break;
						}
					}
				}
			}
			return ( $new_price / $qty );
		}
		return 0;
	}
}

/**
 * Function to get option row based on search value for Select/Radio/Checkbox.
 */
function pif_get_option_row( $rows, $search_value ) {
    foreach ( $rows as $row ) {
        if ( ($row['type_select_options_option'] ?? '') === $search_value ) {
            return $row;
        }
    }

    return null;
}

if ( ! function_exists( 'alg_display_product_input_fields' ) ) {
	/**
	 * Function alg_display_product_input_fields.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_display_product_input_fields() {

		if ( class_exists( 'PIF_Product' ) ) {
			PIF_Product::alg_display_product_input_fields();
		}
	}
}

if ( ! function_exists( 'alg_get_uploads_dir' ) ) {
	/**
	 * Function alg_get_uploads_dir.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param string $subdir Sub Directory.
	 */
	function alg_get_uploads_dir( $subdir = '' ) {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		$upload_dir = $upload_dir . '/woocommerce_uploads/alg_uploads';
		if ( '' !== $subdir ) {
			$upload_dir = $upload_dir . '/' . $subdir;
		}
		return $upload_dir;
	}
}

if ( ! function_exists( 'alg_get_all_values' ) ) {
	/**
	 * Function alg_get_all_values.
	 *
	 * @version 1.0.1
	 * @since   1.0.0
	 * @param string $scope Scope.
	 * @param string $field_nr Field.
	 * @param number $product_id Product ID.
	 */
	function alg_get_all_values( $scope, $field_nr, $product_id = 0 ) {
		$values  = array();
		$options = pif_get_old_options();
		$global_fields = get_option( 'pif_field_settings', array() );
		$product_fields = get_post_meta( $product_id, 'pif_field_settings', true );
		
		$fields = ( 'local' === $scope ) ? $product_fields : $global_fields;

		foreach ( $fields as $key => $field ) {
			$values['custom_show_hide'] = isset( $fields['custom_show_hide'] ) ? $fields['custom_show_hide'] : 'show';
			$values[ $key ] = $field;
		}

		
		foreach ( $options as $option ) {


			if ( 'type_select_options' === $option['id'] ) {
				$option_id        = ALG_WC_PIF_ID . '_' . $option['id'] . '_' . $scope . '_' . $field_nr;
				$old_value_exists = ( 'local' === $scope ) ? $product_fields['type_select_options'] : $global_fields['type_select_options'];

				if ( '' !== $old_value_exists ) {
					$values[ $option['id'] ] = $old_value_exists;
				} else {
					foreach ( $option['options'] as $opt ) {
						if ( is_array( $opt ) ) {
							$global_settings = $global_fields[ $opt['id'] ];
							if ( 'local' === $scope ) {
								$values[ $opt['id'] ] = $product_fields[ $opt['id'] ];
							} else {
								$values[ $opt['id'] ] = '';
								if ( is_array( $global_settings ) && isset( $global_settings[ $opt['id'] ] ) ) {
									$values[ $opt['id'] ] = $global_settings[ $opt['id'] ];
								}
							}
						}
					}
				}
			} else if ( 'local' === $scope && 'type_conditional_options' === $option['id'] ) {
				$option_id        = ALG_WC_PIF_ID . '_' . $option['id'] . '_' . $scope . '_' . $field_nr;
				foreach ( $option['options'] as $opt ) {
					if ( is_array( $opt ) ) {
						$inner_option_id = ALG_WC_PIF_ID . '_' . $opt['id'] . '_' . $scope . '_' . $field_nr;
						$values['type_conditional_options'][$opt['id']] = $product_fields[ $opt['id'] ];
					}
				}
			} else {
				$option_id               = ALG_WC_PIF_ID . '_' . $option['id'] . '_' . $scope . '_' . $field_nr;
				$option['default']       = isset( $option['default'] ) ? $option['default'] : '';
				$values[ $option['id'] ] = ( 'local' === $scope ) ? $product_fields[ $option['id'] ] : $global_fields[ $option['id'] ];
			}
		}
		return $values;
	}
}

if ( ! function_exists( 'alg_date_format_php_to_js' ) ) {
	/**
	 * Matches each symbol of PHP date format standard with jQuery equivalent codeword.
	 *
	 * @see     http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	 * @version 1.0.1
	 * @since   1.0.0
	 * @param string $php_format PHP Format.
	 */
	function alg_date_format_php_to_js( $php_format ) {
		$symbols_matching  = array(
			// Day.
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week.
			'W' => '',
			// Month.
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year.
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time.
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => '',
		);
		$jqueryui_format   = '';
		$escaping          = false;
		$php_format_length = strlen( $php_format );
		for ( $i = 0; $i < $php_format_length; $i++ ) {
			$char = $php_format[ $i ];
			if ( '\\' === $char ) { // PHP date format escaping character.
				++$i;
				$jqueryui_format .= ( $escaping ) ? $php_format[ $i ] : '\'' . $php_format[ $i ];
				$escaping         = true;
			} else {
				if ( $escaping ) {
					$jqueryui_format .= "'";
					$escaping         = false;
				}
				if ( isset( $symbols_matching[ $char ] ) ) {
					$jqueryui_format .= substr_count( $jqueryui_format, $char ) < 2 ? $symbols_matching[ $char ] : '';
				} else {
					$jqueryui_format .= $char;
				}
			}
		}
		return $jqueryui_format;
	}
}

if ( ! function_exists( 'alg_get_select_options' ) ) {
	/**
	 * Function alg_get_select_options()
	 *
	 * @version 1.0.1
	 * @since   1.0.0
	 * @return  array
	 * @param array   $options_raw Options.
	 * @param boolean $do_sanitize Do Sanitize.
	 */
	function alg_get_select_options( $options_raw, $do_sanitize = true ) {
		$select_options = array();
		$select_options_raw = is_array( $options_raw ) ? $options_raw : array();
		foreach ( $select_options_raw as $index => $row ) {
			$title = isset( $row['type_select_options_option'] ) ? $row['type_select_options_option']: '';
			$price = isset( $row['type_select_options_price'] ) ? $row['type_select_options_price']: '';
			$condition  = isset( $row['type_select_options_condition'] ) ? $row['type_select_options_condition']: '';
			$image = isset( $row['type_select_options_image'] ) ? $row['type_select_options_image'] : '';

			$select_options_key                    = ( $do_sanitize ) ? sanitize_title( $title ) : $title;
			$select_options[ $select_options_key ] = array(
				'title' => $title,
				'price' => $price,
				'condition' => $condition,
				'image' => $image,
			);
		}
		return $select_options;
	}
}

if ( ! function_exists( 'alg_get_settings_table_html' ) ) {
	/**
	 * Function alg_get_settings_table_html.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @param mixed $data Data.
	 * @param array $args Arguments.
	 */
	function alg_get_settings_table_html( $data, $args = array() ) {
		$defaults   = array(
			'table_id'           => '',
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$plugin_url = plugins_url() . '/product-input-fields-for-woocommerce-pro';
		$args       = array_merge( $defaults, $args );
		if ( isset( $args['table_id'] ) ) {
			$table_id = $args['table_id'];
		}
		if ( isset( $args['table_class'] ) ) {
			$table_class = $args['table_class'];
		}
		if ( isset( $args['table_style'] ) ) {
			$table_style = $args['table_style'];
		}
		if ( isset( $args['row_styles'] ) ) {
			$row_styles = $args['row_styles'];
		}
		if ( isset( $args['table_heading_type'] ) ) {
			$table_heading_type = $args['table_heading_type'];
		}
		if ( isset( $args['columns_classes'] ) ) {
			$columns_classes = $args['columns_classes'];
		}
		if ( isset( $args['column_styles'] ) ) {
			$column_styles = $args['column_styles'];
		}
		$tbl_main_id = $data['id'];
		$table_id    = ( '' === $table_id ) ? '' : ' id="' . $table_id . '"';
		$table_class = ( '' === $table_class ) ? '' : ' class="' . $table_class . ' ' . $tbl_main_id . '"';
		$table_style = ( '' === $table_style ) ? '' : ' style="' . $table_style . '"';
		$row_styles  = ( '' === $row_styles ) ? '' : ' style="' . $row_styles . '"';
		$html        = '';
		if ( strpos( $tbl_main_id, 'alg_wc_pif_type_conditional_options_global_' ) !== false ) {
			$class = 'pif_options_table_conditional pif_options_table';
		} else {
			$class = 'pif_options_table';
		}
		$html .= '<tbody class="' . $class . '" id=' . $tbl_main_id . '>';
		if ( strpos( $tbl_main_id, 'alg_wc_pif_type_select_options_global_' ) !== false ) {
			$field_nr = str_replace( 'alg_wc_pif_type_select_options_global_', '', $tbl_main_id );
		} elseif ( strpos( $tbl_main_id, 'alg_wc_pif_type_conditional_options_global_' ) !== false ) {
			$field_nr = str_replace( 'alg_wc_pif_type_conditional_options_global_', '', $tbl_main_id );
		}
		$h = 0;

		if ( ! empty( $data['options'] ) ) {
			$options = $data['options'];

			// Header.
			$html .= '<tr>';
			foreach ( $options as $col => $item ) {
				$class = ALG_WC_PIF_ID . '_' . $item['id'] . '_global_' . $field_nr;
				$html .= '<th class="' . $class . '">' . $item['title'] . '</th>';
			}
			$html .= '<th class="' . $item['id'] . '">' . __( 'Action', 'product-input-fields-for-woocommerce' ) . '</th>';
			$html .= '</tr>';
			// Header.

			// Rows.
			$html .= '<tr class="cloneme">';
			$x     = 0;

			if ( empty( $data['value'] ) ) {
				foreach ( $options as $col => $item ) {
					$custom_attributes = '';
					if ( isset( $item['custom_attributes'] ) ) {
						foreach ( $item['custom_attributes'] as $custom_attribute_key => $custom_attribute_value ) {
							$custom_attributes .= ' ' . $custom_attribute_key . '="' . $custom_attribute_value . '"';
						}
					}
					$inner_option_id           = ALG_WC_PIF_ID . '_' . $item['id'] . '_global_' . $field_nr;
					$inner_select_options_html = '';
					if ( 'select' === $item['type'] && isset( $item['options'] ) ) {
						foreach ( $item['options'] as $select_option_id => $select_option_label ) {
							$inner_select_options_html .= '<option value="' . $select_option_id . '">' . $select_option_label . '</option>';
						}
					}
					$name = $tbl_main_id . '[' . $item['id'] . '][]';
					$multiple = '';

					switch ( $item['id'] ) {
						case 'type_conditional_options_products':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . '][0][]';
						break; //phpcs:ignore
						case 'type_conditional_options_tags':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . '][0][]';
						break; //phpcs:ignore
						case 'type_conditional_options_variations':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . '][0][]';
						break; //phpcs:ignore
						case 'type_conditional_options_categories':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . '][0][]';
						break; //phpcs:ignore
						case 'type_conditional_options_country':
									$multiple = 'multiple="multiple"';
									$name =  $tbl_main_id . '[' . $item['id'] . '][0][]';
						break; //phpcs:ignore
						case 'type_conditional_options_select':
									$name =  $tbl_main_id . '[' . $item['id'] . '][0][]';
						break; //phpcs:ignore
					}

					switch ( $item['type'] ) {
						case 'text':
							$html .= '<td class="' . $inner_option_id . '">';
							$html .= '<input' . $custom_attributes . ' type="' . $item['type'] . '" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]" value="">' . ( isset( $item['desc'] ) ? ' <em>' . $item['desc'] . '</em>' : '' );
							$html .= '</td>';
						break; // phpcs:ignore
						case 'number':
							$html .= '<td class="' . $inner_option_id . '">';
							$html .= '<input' . $custom_attributes . ' type="' . $item['type'] . '" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]" value="">' . ( isset( $item['desc'] ) ? ' <em>' . $item['desc'] . '</em>' : '' );
							$html .= '</td>';
						break; // phpcs:ignore
						case 'select':
							$html .= '<td class="' . $inner_option_id . '">';
							$html .= '<select id="' . $inner_option_id . '" name="' . $name . '" '.$multiple.' class="'.$inner_option_id.'">' . $inner_select_options_html . '</select>';
							$html .= '</td>';
						break; // phpcs:ignore
						case 'media':
							$html .= '<td class="' . $inner_option_id . '">';
							$html .= '<input' . $custom_attributes . ' type="hidden" value="" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]">';
							$html .= '<div class="alg_opt_img" style="position: relative; display: inline-block;"><a href="#" id="' . ALG_WC_PIF_ID . '_type_select_options_upl" class="alg_image_upl">' . __( 'Upload image', 'product-input-fields-for-woocommerce' ) . '</a>';
							$html .= '<a href="#" id="' . ALG_WC_PIF_ID . '_type_select_options_rmv" class="alg_image_rmv" style="display: none; position: absolute;top: 0;right: 0;opacity: 0;"><img class="site-logo" src= "' . esc_html( $plugin_url . '/vendor/algoritmika/product-input-fields-for-woocommerce/assets/images/trash-can-solid.svg' ) . '" style="height: 15px; width: 15px;" /></a></div>';
							$html .= '</td>';
						break; // phpcs:ignore
					}
					++$x;
				}
				$html .= '<td><button type="button" id="remove_option_' . $field_nr . '" class="button">x</button></td>';

			} elseif ( is_array( $data['value'] ) && ! empty( $data['value'] ) && array_key_exists( 'type_select_options_option', $data['value'] ) && count( $data['value']['type_select_options_option'] ) > 0 ) {
				for ( $i=0; $i < count( $data['value']['type_select_options_option'] ); $i++ ) { // phpcs:ignore
					$value_option_option    = $data['value']['type_select_options_option'];
					$value_option_condition = $data['value']['type_select_options_condition'];
					$value_option_price     = $data['value']['type_select_options_price'];
					$value_option_image     = $data['value']['type_select_options_image'];
					foreach ( $options as $col => $item ) {
						$custom_attributes = '';
						if ( isset( $item['custom_attributes'] ) ) {
							foreach ( $item['custom_attributes'] as $custom_attribute_key => $custom_attribute_value ) {
								$custom_attributes .= ' ' . $custom_attribute_key . '="' . $custom_attribute_value . '"';
							}
						}
						$inner_option_id           = ALG_WC_PIF_ID . '_' . $item['id'] . '_global_' . $field_nr;
						$inner_select_options_html = '';
						if ( 'select' === $item['type'] && isset( $item['options'] ) ) {
							foreach ( $item['options'] as $select_option_id => $select_option_label ) {
								$selected_value             = isset( $value_option_condition[$i] ) ? $value_option_condition[$i] : '';
								$inner_select_options_html .= '<option value="' . esc_attr( $select_option_id ) . '"' . selected( $selected_value, esc_attr( $select_option_id ), false ) . '>' . $select_option_label . '</option>';
							}
						}

						switch ( $item['type'] ) {
							case 'text':
								$html .= '<td class="' . $inner_option_id . '">';
								$html .= '<input' . $custom_attributes . ' type="' . $item['type'] . '" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]" value="' . esc_attr( $value_option_option[ $i ] ) .'">' . ( isset( $item['desc'] ) ? ' <em>' . $item['desc'] . '</em>' : '' ); //phpcs:ignore
								$html .= '</td>';
							break; //phpcs:ignore
							case 'number':
								$html       .= '<td class="' . $inner_option_id . '">';
								$price_value = isset( $value_option_price[$i] ) ? $value_option_price[$i] : '';
								$html       .= '<input' . $custom_attributes . ' type="' . $item['type'] .  '" step="0.01" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]" value="' . esc_attr( $price_value ) . '">' . ( isset( $item['desc'] ) ? ' <em>' . $item['desc'] . '</em>' : '' );
								$html       .= '</td>';
							break; //phpcs:ignore
							case 'select':
								$html .= '<td class="' . $inner_option_id . '">';
								$html .= '<select id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]">' . $inner_select_options_html . '</select>';
								$html .= '</td>';
							break; //phpcs:ignore
							case 'media':
								$html .= '<td class="' . $inner_option_id . '">';
								if ( ! empty( $value_option_image[ $i ] ) && $image = wp_get_attachment_image_src( $value_option_image[ $i ] ) ) { //phpcs:ignore
									$html .= '<input' . $custom_attributes . ' type="hidden" height="50" width="50" value="' . $value_option_image[ $i ] . '" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]">
									<div class="alg_opt_img" style="position: relative; display: inline-block;"><a href="#" id="' . ALG_WC_PIF_ID . '_type_select_options_upl" class="alg_image_upl"><img src="' . $image[0] . '" height="50" width="50" /></a>
									<a href="#" id="' . ALG_WC_PIF_ID . '_type_select_options_rmv" class="alg_image_rmv" style="position: absolute;top: 0;right: 0;opacity: 0;"><img class="site-logo" src= "'. esc_html( $plugin_url . '/vendor/algoritmika/product-input-fields-for-woocommerce/assets/images/trash-can-solid.svg' ) . '" style="height: 15px; width: 15px;" /></a></div>'; //phpcs:ignore
								} else {
									$html .= '<input' . $custom_attributes . ' type="hidden" value="" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]">';
									$html .= '<div class="alg_opt_img" style="position: relative; display: inline-block;"><a href="#" id="' . ALG_WC_PIF_ID . '_type_select_options_upl" class="alg_image_upl">' . __( 'Upload image', 'product-input-fields-for-woocommerce' ) . '</a></div>';
									$html .= '<a href="#" id="' . ALG_WC_PIF_ID .'_type_select_options_rmv" class="alg_image_rmv" style="display: none; position: absolute;top: 0;right: 0;opacity: 0;"><img class="site-logo" src= "'. esc_html( $plugin_url . '/vendor/algoritmika/product-input-fields-for-woocommerce/assets/images/trash-can-solid.svg' ) . '" style="height: 15px; width: 15px;" /></a></div>'; //phpcs:ignore
								}
								$html .= '</td>';
							break; //phpcs:ignore
						}
						++$x;
					}
					if ( $i >= 0 ) {
						$html .= '<td><button type="button" id="remove_option_' . $field_nr . '" class="button">x</button></td>';
					}
					$html .= '</tr><tr>';
				}
			} elseif ( is_array( $data['value'] ) && ! empty( $data['value'] ) && array_key_exists( 'type_conditional_options_condition', $data['value'] ) && count( $data['value']['type_conditional_options_condition'] ) > 0 ) {
				for ( $i=0; $i < count( $data['value']['type_conditional_options_condition'] ); $i++ ) { // phpcs:ignore
					$value_option_condition  = $data['value']['type_conditional_options_condition'];
					$value_option_relation   = $data['value']['type_conditional_options_relation'];
					$value_option_value      = $data['value']['type_conditional_options_value'];
					foreach ( $options as $col => $item ) {
						$custom_attributes = '';
						if ( isset( $item['custom_attributes'] ) ) {
							foreach ( $item['custom_attributes'] as $custom_attribute_key => $custom_attribute_value ) {
								$custom_attributes .= ' ' . $custom_attribute_key . '="' . $custom_attribute_value . '"';
							}
						}
						$inner_option_id           = ALG_WC_PIF_ID . '_' . $item['id'] . '_global_' . $field_nr;
						$inner_select_options_html = '';
						$multiple = '';
						$name = $tbl_main_id . '[' . $item['id'] . '][]';
						$relation_options = get_relation_options();

						if ( 'select' === $item['type'] ) {
							
							if ( 'type_conditional_options_relation' === $item['id'] ) {
								if ( strpos( $value_option_condition[$i], '_global_' ) !== false ) {
    								$types = explode( '_global_', $value_option_condition[$i] );
    								$type  = $types[0];
								} else {
									$type = $value_option_condition[$i];
								}
								unset( $item['options']);
								$item['options']              = $relation_options[$type];
								$value_option_condition[ $i ] = $value_option_relation[$i];
							}

							if ( 'type_conditional_options_select' === $item['id'] ) {
								$name = $tbl_main_id . '[' . $item['id'] . ']['.$i.'][]';
								if ( isset( $data['value']['type_conditional_options_select'] ) ) {
										$option_ids = explode( '_global_', $data['value']['type_conditional_options_condition'][$i] );
    									$option_id  = isset( $option_ids[1] ) ? $option_ids[1] : $option_ids[0];
    									unset( $item['options']);
										$item['options'] = get_enabled_global_input_selected_option_values( $option_id );
										$value_option_condition[ $i ] = isset( $data['value']['type_conditional_options_select'][$i][0] ) ? $data['value']['type_conditional_options_select'][$i][0] : '';
								}
							}

							switch ( $item['id'] ) {
								case 'type_conditional_options_products':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . ']['.$i.'][]';
									if ( isset( $data['value'][$item['id']][$i] ) ) {
										$product_ids = $data['value'][$item['id']][$i];
										if ( is_array( $product_ids ) && count( $product_ids ) > 0 ) {
											// Get the products
											$products = get_posts([
											    'post_type' => 'product',
											    'post__in'  => $product_ids,
											    'numberposts' => -1,
											]);

											foreach ( $products as $product ) {
											    $inner_select_options_html.= '<option value="' . esc_attr( $product->ID ) . '" selected="selected">' . esc_html( $product->post_title ) . '</option>';
											}
										}
									}
								break; //phpcs:ignore
								case 'type_conditional_options_tags':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . ']['.$i.'][]';
									if ( isset( $data['value'][$item['id']][$i] ) ) {
										$selected_ids = $data['value'][$item['id']][$i];
										if ( is_array( $selected_ids ) && count( $selected_ids ) > 0 ) {
											$terms = get_terms([
												    'taxonomy'   => 'product_tag',
												    'include'    => $selected_ids,
												    'hide_empty' => false,
											]);

											foreach ( $terms as $term ) {
											    $inner_select_options_html.= '<option value="' . esc_attr( $term->term_id ) . '" selected="selected">' . esc_html( $term->name ) . '</option>';
											}
										}
									}
								break; //phpcs:ignore
								case 'type_conditional_options_variations':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . ']['.$i.'][]';

									if ( isset( $data['value'][$item['id']][$i]) ) {
										$selected_ids = $data['value'][$item['id']][$i];

										foreach ( $selected_ids as $variation_id ) {
										    $variation = wc_get_product( $variation_id );

										    if ( $variation && $variation->is_type( 'variation' ) ) {
	    										
										        $variation_name = $variation->get_name(); // Get attribute string

										        $inner_select_options_html.= '<option value="' . esc_attr( $variation_id ) . '" selected="selected">';
										        $inner_select_options_html.= esc_html( $variation_name );
										        $inner_select_options_html.= '</option>';
										    }
										}
									}

								break; //phpcs:ignore
								case 'type_conditional_options_categories':
									$multiple = 'multiple="multiple"';
									$name = $tbl_main_id . '[' . $item['id'] . ']['.$i.'][]';
									if ( isset( $data['value'][$item['id']][$i] ) ) {
										$term_ids = $data['value'][$item['id']][$i];
										if ( is_array( $term_ids ) && count( $term_ids ) > 0 ) {
											$terms = get_terms([
												'taxonomy' => 'product_cat',
												'include' => $term_ids,
												'hide_empty' => false,
												]);

												foreach ( $terms as $term ) {
												$inner_select_options_html.= '<option value="' . esc_attr( $term->term_id ) . '" selected="selected">' . esc_html( $term->name ) . '</option>';
												}
										}
									}
								break; //phpcs:ignore
								case 'type_conditional_options_country':
									$multiple = 'multiple="multiple"';
									$selected_ids = isset( $data['value'][$item['id']][$i] ) ? $data['value'][$item['id']][$i] : array() ;
									$name = $tbl_main_id . '[' . $item['id'] . ']['.$i.'][]';
									$wc_countries = new WC_Countries();
									$all_countries = $wc_countries->get_countries();

									foreach ( $selected_ids as $country_code ) {
									    if ( isset( $all_countries[ $country_code ] ) ) {
									        $inner_select_options_html.= '<option value="' . esc_attr( $country_code ) . '" selected="selected">';
									        $inner_select_options_html.= esc_html( $all_countries[ $country_code ] );
									        $inner_select_options_html.= '</option>';
									    }
									}
								break; //phpcs:ignore
							}
							if ( isset( $item['options'])) {
								foreach ( $item['options'] as $select_option_id => $select_option_label ) {
									$inner_select_options_html .= '<option value="' . esc_attr( $select_option_id ) . '"' . selected( $value_option_condition[ $i ], esc_attr( $select_option_id ), false ) . '>' . $select_option_label . '</option>';
								}
							}
						}
						switch ( $item['type'] ) {
							case 'text':
								$html .= '<td class="' . $inner_option_id . '">';
								$html .= '<input' . $custom_attributes . ' type="' . $item['type'] . '" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]" value="' . esc_attr( $value_option_value[ $i ] ) .'">' . ( isset( $item['desc'] ) ? ' <em>' . $item['desc'] . '</em>' : '' ); //phpcs:ignore
									$html .= '</td>';
							break; //phpcs:ignore
							case 'number':
								$html .= '<td class="' . $inner_option_id . '">';
								$html .= '<input' . $custom_attributes . ' type="' . $item['type'] . '" id="' . $inner_option_id . '" name="' . $tbl_main_id . '[' . $item['id'] . '][]" value="' . esc_attr( $value_option_value[ $i ] ) . '">' . ( isset( $item['desc'] ) ? ' <em>' . $item['desc'] . '</em>' : '' );
								$html .= '</td>';
							break; //phpcs:ignore
							case 'select':
								$html .= '<td class="' . $inner_option_id . '">';
								$html .= '<select ' . $multiple . ' id="' . $inner_option_id . '" name="' . $name . '" class="'.$inner_option_id.'">' . $inner_select_options_html . '</select>';
								$html .= '</td>';
							break; //phpcs:ignore
						}
						++$x;
					}
					if ( $i >= 0 ) {
						$html .= '<td><button type="button" id="remove_option_' . $field_nr . '" class="button">x</button></td>';
					}
					$html .= '</tr><tr>';
					$h = $i;
				}
			}
			$html .= '</tr>';
			// Rows.
			// Add New Row Button.
			$id_array = explode( '_', $tbl_main_id );
			$html    .= '<tr><th colspan="3"><button id="add_more_options_' . $id_array [ count( $id_array ) - 1 ] . '" data-id="' . $tbl_main_id . '_button" type="button" class="button">' . __( 'Add Option', 'product-input-fields-for-woocommerce' ) . '</button></th></tr>';
			// Add New Row Button.
		}
		$html .= '<input type="hidden" id="row_id" value="'.$h.'">';

		$html .= '</tbody>';
		$html .= '</table>';
		return ( 'table' === $data['type'] ) ? $html : '';
	}
}

if ( ! function_exists( 'alg_get_table_html' ) ) {
	/**
	 * Function alg_get_table_html.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @param mixed $data Data.
	 * @param array $args Arguments.
	 */
	function alg_get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_id'           => '',
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args     = array_merge( $defaults, $args );
		if ( isset( $args['table_id'] ) ) {
			$table_id = $args['table_id'];
		}
		if ( isset( $args['table_class'] ) ) {
			$table_class = $args['table_class'];
		}
		if ( isset( $args['table_style'] ) ) {
			$table_style = $args['table_style'];
		}
		if ( isset( $args['row_styles'] ) ) {
			$row_styles = $args['row_styles'];
		}
		if ( isset( $args['table_heading_type'] ) ) {
			$table_heading_type = $args['table_heading_type'];
		}
		if ( isset( $args['columns_classes'] ) ) {
			$columns_classes = $args['columns_classes'];
		}
		if ( isset( $args['column_styles'] ) ) {
			$column_styles = $args['column_styles'];
		}
		$tbl_main_id = $table_id;
		$table_id    = ( '' === $table_id ) ? '' : ' id="' . $table_id . '"';
		$table_class = ( '' === $table_class ) ? '' : ' class="' . $table_class . '"';
		$table_style = ( '' === $table_style ) ? '' : ' style="' . $table_style . '"';
		$row_styles  = ( '' === $row_styles ) ? '' : ' style="' . $row_styles . '"';
		$html        = '';
		$html       .= '<table' . $table_class . $table_id . $table_style . '>';
		$html       .= '<tbody>';

		if ( 'horizontal' === $table_heading_type ) {

			$html       .= '<tr ' . $table_class . ' ' . $row_styles . '>';
			$is_th_added = false;
			// ** Table Header */

			if ( array_key_exists( '0', $data ) && array_key_exists( '0', $data[0] ) && is_array( $data[0][0] ) && false === $is_th_added ) {
				$row = $data[0];
				for ( $y = 0; $y < count( $row ); $y++ ) { //phpcs:ignore
					$str_one    = substr( $row[ $y ][1], strpos( $row[ $y ][1], 'id="' ) + 4, strlen( $row[ $y ][1] ) );
					$str_arr    = explode( '"', $str_one );
					$attr_class = $str_arr[0];

					$html .= '<th class="' . $attr_class . '">' . $row[ $y ][0] . '</th>';
				}
				$is_th_added = true;
			} elseif ( ! array_key_exists( '0', $data[0] ) ) {
				$str_one    = substr( $data[0][1], strpos( $data[0][1], 'id="' ) + 4, strlen( $data[0][1] ) );
				$str_arr    = explode( '"', $str_one );
				$attr_class = $str_arr[0];

				$html .= '<th class="' . $attr_class . '">' . $data[0][0] . '</th>';
			}
			$html .= '</tr>';
			// ** Table Header */

			$x = 0;
			foreach ( $data as $row_number => $row ) {
				if ( is_array( $row[0] ) ) {
					$html   .= '<tr class="cloneme"' . $row_styles . '>';
					$columns = count( $row );

					for ( $i = 0; $i < $columns; $i++ ) {
						$str_one      = substr( $row[ $i ][1], strpos( $row[ $i ][1], 'id="' ) + 4, strlen( $row[ $i ][1] ) );
						$str_arr      = explode( '"', $str_one );
						$attr_class   = $str_arr[0];
						$column_class = ( ! empty( $columns_classes ) && isset( $columns_classes[ $row_number ] ) ) ? ' class="' . $columns_classes[ $row_number ] . '"' : '';
						$column_style = ( ! empty( $columns_styles ) && isset( $columns_styles[ $row_number ] ) ) ? ' style="' . $columns_styles[ $row_number ] . '"' : '';

						$html .= '<td class="' . $attr_class . '" ' . $column_style . '>';
						$html .= $row[ $i ][1];
						$html .= '</td>';
					}
					$id_val_array = explode( '_', $column_class );
					$e            = $id_val_array[ count( $id_val_array ) - 1 ];
					if ( $x >= 0 ) {
						$html .= '<td><button type="button" id="remove_option_' . $e . ' class="button">x</button></td>';
						$html .= '</tr>';
					}
				} else {
					if ( 0 === $x ) {
						$html .= '<tr class="cloneme"' . $row_styles . '>';
					}

					$column_class = ( ! empty( $columns_classes ) && isset( $columns_classes[ $row_number ] ) ) ? ' class="' . $columns_classes[ $row_number ] . '"' : '';
					$column_style = ( ! empty( $columns_styles ) && isset( $columns_styles[ $row_number ] ) ) ? ' style="' . $columns_styles[ $row_number ] . '"' : '';
					$id_val_array = explode( '_', $column_class );

					$html .= '<td' . $column_class . $column_style . '>';
					$html .= $row[1];
					$html .= '</td>';

					$id_val_array = explode( '_', $column_class );
					$e            = $id_val_array[ count( $id_val_array ) - 1 ];
					if ( count( $data ) - 1 === $x ) {
						$html .= '<td><button type="button" id="remove_option_' . $e . ' class="button">x</button></td>';
						$html .= '</tr>';
					}
				}
				++$x;
			}
			$id_array = explode( '_', $tbl_main_id );
			$html    .= '<tr><th colspan="3"><button id="add_more_options_' . $id_array [ count( $id_array ) - 1 ] . '" data-id="' . $tbl_main_id . '_button" type="button" class="button">Add Option</button></th></tr>';
		} else {
			foreach ( $data as $row_number => $row ) {
				$str_one    = substr( $row[1], strpos( $row[1], 'id="' ) + 4, strlen( $row[1] ) );
				$str_arr    = explode( '"', $str_one );
				$attr_class = $str_arr[0];

				if ( '' === $row[1] && '' === $attr_class ) {
					$head_one   = substr( $row[0], strpos( $row[0], 'class="' ) + 7, strlen( $row[0] ) );
					$head_arr   = explode( '"', $head_one );
					$attr_class = $head_arr[0] . '_heading';
				}

				$html .= '<tr class="' . $attr_class . '" ' . $row_styles . '>';
				foreach ( $row as $column_number => $value ) {
					$th_or_td     = ( ( 0 === $row_number && 'horizontal' === $table_heading_type ) || ( 0 === $column_number && 'vertical' === $table_heading_type ) ) ? 'th' : 'td';
					$column_class = ( ! empty( $columns_classes ) && isset( $columns_classes[ $column_number ] ) ) ? ' class="' . $columns_classes[ $column_number ] . '"' : '';
					$column_style = ( ! empty( $columns_styles ) && isset( $columns_styles[ $column_number ] ) ) ? ' style="' . $columns_styles[ $column_number ] . '"' : '';
					$html        .= '<' . $th_or_td . $column_class . $column_style . '>';
					$html        .= $value;
					$html        .= '</' . $th_or_td . '>';
				}
				$html .= '</tr>';
			}
		}

		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}

if ( ! function_exists( 'get_enabled_global_input_titles' ) ) {
		function get_enabled_global_input_titles() {
			global $post;
			$titles = array( 'value' => __( 'Select Condition Type', 'product-input-fields-for-woocommerce' ),
					  );

			$total_number = get_option( 'alg_wc_pif_global_total_number', 1 );

			for ( $i = 1; $i <= $total_number; $i++ ) {
				$enabled = get_option( "alg_wc_pif_enabled_global_{$i}", 'no' );
				if ( 'yes' === $enabled ) {
					$title = get_option( "alg_wc_pif_title_global_{$i}", '' );
					$type  = get_option( "alg_wc_pif_type_global_{$i}", 'text');
					if ( ! empty( $title ) ) {
						$titles[ $type.'_global_'.$i ] = $title;
					}
				}

				// Unset index based on 'section' parameter
				if ( isset( $_GET['section'] ) && strpos( $_GET['section'], 'all_products_field_' ) === 0 ) {
					$field_index = str_replace( 'all_products_field_', '', wp_unslash ( $_GET['section'] ) );
					if ( is_numeric( $field_index ) ) {
						$type  = get_option( "alg_wc_pif_type_global_{$i}", 'text');
						unset( $titles[ $type . '_global_' . $field_index ] );
					}
				}
			}

			if ( is_edit_or_add_product_page() ) {
				$product_id   = is_object( $post ) && isset( $post->ID ) ? $post->ID : 0;
				$total_number = get_post_meta( $product_id,'_alg_wc_pif_local_total_number', true );

				for ( $i = 1; $i <= $total_number; $i++ ) {
					$enabled = get_post_meta( $product_id,"_alg_wc_pif_enabled_local_{$i}", true );
					if ( 'yes' === $enabled ) {
						$title = get_post_meta( $product_id, "_alg_wc_pif_title_local_{$i}", true );
						$type  = get_post_meta( $product_id, "_alg_wc_pif_type_local_{$i}", true );
						if ( ! empty( $title ) ) {
							$titles[ $type.'_local_'.$i ] = $title;
						}
					}
				}
			}

			$titles['products']            = __( 'Products', 'product-input-fields-for-woocommerce' );
			$titles['products_categories'] = __( 'Products Categories', 'product-input-fields-for-woocommerce' );
			$titles['product_tags']        = __( 'Product Tags', 'product-input-fields-for-woocommerce' );
			$titles['product_variations']  = __( 'Product Variations', 'product-input-fields-for-woocommerce' );

			return $titles;
		}
}

	function get_enabled_global_input_option_values() {
			$titles = array();
			global $post;

			$total_number = get_option( 'alg_wc_pif_global_total_number', 1 );

			for ( $i = 1; $i <= $total_number; $i++ ) {
				$enabled = get_option( "alg_wc_pif_enabled_global_{$i}", 'no' );
				if ( 'yes' === $enabled ) {
					$type  = get_option( "alg_wc_pif_type_global_{$i}", 'text');
					if ( in_array( $type, array( 'select','radio','checkbox','multicheck' ) ) ) {
						$options_arr = get_option( "alg_wc_pif_type_select_options_global_{$i}", '');
						if ( isset( $options_arr['type_select_options_option'] ) ) {
							foreach ( $options_arr['type_select_options_option'] as $key => $value ) {
								$titles[ $type.'_global_'.$i.'_'.$value ] = $value;
							}
						}
					}
				}
			}
			if ( is_edit_or_add_product_page() ) {
				$product_id   = is_object( $post ) && isset( $post->ID ) ? $post->ID : 0;
				$total_number = get_post_meta( $product_id,'_alg_wc_pif_local_total_number', true );

				for ( $i = 1; $i <= $total_number; $i++ ) {
					$enabled = get_post_meta( $product_id,"_alg_wc_pif_enabled_local_{$i}", true );
					$type    = get_post_meta( $product_id,"_alg_wc_pif_type_local_{$i}", true);
					if ( in_array( $type, array( 'select','radio','checkbox','multicheck' ), true ) ) {
						$options_arr = get_post_meta( $product_id, "_alg_wc_pif_type_select_options_option_local_{$i}", '');
						if ( isset( $options_arr[0] ) && is_array( $options_arr[0] ) && count( $options_arr[0] ) > 0 ) {
							foreach ( $options_arr[0] as $key => $value ) {
								$titles[ $type.'_local_'.$i.'_'.$value ] = $value;
							}
						}
					}
				}
			}
			return $titles;
	}

	function get_datepicker_input_option_value_format() {
			$titles = array();
			global $post;

			$total_number = get_option( 'alg_wc_pif_global_total_number', 1 );

			for ( $i = 1; $i <= $total_number; $i++ ) {
				$enabled = get_option( "alg_wc_pif_enabled_global_{$i}", 'no' );
				if ( 'yes' === $enabled ) {
					$type  = get_option( "alg_wc_pif_type_global_{$i}", 'text');
					if ( in_array( $type, array( 'datepicker' ) ) ) {
						$options_arr = get_option( "alg_wc_pif_type_datepicker_format_global_{$i}", '');
						$options_arr = '' !== $options_arr ? $options_arr : get_option( 'date_format' );
						$options_arr = alg_date_format_php_to_js( esc_attr( $options_arr ) );
						$titles[ 'global_'.$i ] = $options_arr;
					}
					if ( in_array( $type, array( 'weekpicker' ) ) ) {
						$options_arr = get_option( "alg_wc_pif_type_weekpicker_format_global_{$i}", '');
						$options_arr = '' !== $options_arr ? $options_arr : get_option( 'date_format' );
						$options_arr = alg_date_format_php_to_js( esc_attr( $options_arr ) );
						$titles[ 'global_'.$i ] = $options_arr;
					}

					if ( in_array( $type, array( 'timepicker' ) ) ) {
						$options_arr = get_option( "alg_wc_pif_type_timepicker_format_global_{$i}", '');
						$options_arr = '' !== $options_arr ? $options_arr : get_option( 'time_format' );
						$interval    = get_option( "alg_wc_pif_type_timepicker_interval_global_{$i}", 15);
						$titles[ 'global_'.$i ] = $options_arr.'_'.$interval;
					}
				}
			}
			if ( is_edit_or_add_product_page() ) {
				$product_id   = is_object( $post ) && isset( $post->ID ) ? $post->ID : 0;
				$total_number = get_post_meta( $product_id,'_alg_wc_pif_local_total_number', true );

				for ( $i = 1; $i <= $total_number; $i++ ) {
					$enabled = get_post_meta( $product_id,"_alg_wc_pif_enabled_local_{$i}", true );
					$type    = get_post_meta( $product_id,"_alg_wc_pif_type_local_{$i}", true);
					if ( in_array( $type, array( 'datepicker' ), true ) ) {
						$options_arr = get_post_meta( $product_id, "_alg_wc_pif_type_datepicker_format_local_{$i}", true);
						$options_arr = '' !== $options_arr ? $options_arr : get_option( 'date_format' );
						$options_arr = alg_date_format_php_to_js( esc_attr( $options_arr ) );
						$titles[ 'local_'.$i ] = $options_arr;
					}
					if ( in_array( $type, array( 'weekpicker' ), true ) ) {
						$options_arr = get_post_meta( $product_id, "_alg_wc_pif_type_weekpicker_format_local_{$i}", true);
						$options_arr = '' !== $options_arr ? $options_arr : get_option( 'date_format' );
						$options_arr = alg_date_format_php_to_js( esc_attr( $options_arr ) );
						$titles[ 'local_'.$i ] = $options_arr;
					}

					if ( in_array( $type, array( 'timepicker' ) ) ) {
						$options_arr = get_post_meta( $product_id, "_alg_wc_pif_type_timepicker_format_local_{$i}", true);
						$options_arr = '' !== $options_arr ? $options_arr : get_option( 'time_format' );
						$interval    = get_post_meta( $product_id, "_alg_wc_pif_type_timepicker_interval_local_{$i}", true);
						$interval    = '' !== $interval ? $interval : 15;
						$titles[ 'local_'.$i ] = $options_arr.'_'.$interval;
					}
				}
			}
			return $titles;
	}

	function get_enabled_global_input_selected_option_values( $i ) {
		global $post;
		$titles = array();
		$enabled = get_option( "alg_wc_pif_enabled_global_{$i}", 'no' );
		if ( 'yes' === $enabled ) {
			$type  = get_option( "alg_wc_pif_type_global_{$i}", 'text');
			if ( in_array( $type, array( 'select','radio','checkbox','multicheck' ) ) ) {
				$options_arr = get_option( "alg_wc_pif_type_select_options_global_{$i}", '');
				if ( isset( $options_arr['type_select_options_option'] ) ) {
					foreach ( $options_arr['type_select_options_option'] as $key => $value ) {
						$titles[ $type.'_global_'.$i.'_'.$value ] = $value;
					}
				}
			}
		}

		if ( is_edit_or_add_product_page() ) {
			$product_id   = is_object( $post ) && isset( $post->ID ) ? $post->ID : 0;
			$enabled      = get_post_meta( $product_id, "_alg_wc_pif_enabled_local_{$i}", true );
			if ( 'yes' === $enabled ) {
				$type = get_post_meta($product_id, "_alg_wc_pif_type_local_{$i}", true );
				if ( in_array( $type, array( 'select','radio','checkbox','multicheck' ),true ) ) {
					$options_arr = get_post_meta($product_id, "_alg_wc_pif_type_select_options_option_local_{$i}", true);
						if ( isset( $options_arr) && is_array( $options_arr) && count( $options_arr) > 0 ) {
							foreach ( $options_arr as $key => $value ) {
								$titles[ $type.'_local_'.$i.'_'.$value ] = $value;
							}
						}
				}
			}
		}

		return $titles;
	}

	function check_conditional_options_conditions() {
		global $product;
		$product_id       = get_the_ID();
		$scopes           = array( 'global', 'local');
		$conditional_data = array(
		    'tags'          => wp_get_post_terms( get_the_ID(), 'product_tag', array( 'fields' => 'ids' ) ),
		    'categories'    => wp_get_post_terms( get_the_ID(), 'product_cat', array( 'fields' => 'ids' ) ),
		    'product_id'    => get_the_ID(),
		    'variation_ids' => array(),
		);

		$product = wc_get_product( get_the_ID() );
		if ( $product && $product->is_type( 'variable' ) ) {
		    $conditional_data['variation_ids'] = $product->get_children();
		}

		$global_fields 	= get_option( 'pif_field_settings', array() );
		$product_fields = get_post_meta( $product_id, 'pif_field_settings', true );

		foreach ( $scopes as $scope ) {
			$product_input_fields = 'local' === $scope ? $product_fields : $global_fields;
			if ( is_array( $product_input_fields ) && count( $product_input_fields ) > 0 ) {
				foreach ( $product_input_fields as $id => $product_input_field ) {
					if ( isset( $product_input_field['enabled'] ) && ( true === $product_input_field['enabled'] || 'yes' === $product_input_field['enabled'] ) ) {
						$conditional_data[ 'alg_wc_pif_'. $scope . '_' . $product_input_field['id'] ] = $product_input_field;
					}
				}
			}
		}
		return $conditional_data;	
	}

	function get_relation_options() {
		$relation_options = array(
			'checkbox' => array(
				'is'     => __( 'Is', 'product-input-fields-for-woocommerce' ),
				'is_not' => __( 'Is not', 'product-input-fields-for-woocommerce' ),
			),
			'select' => array(
				'equals'       => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals'   => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
				'contains'     => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'not_contains' => __( 'Not contains', 'product-input-fields-for-woocommerce' ),
			),
			'multicheck' => array(
				'is'     => __( 'Is', 'product-input-fields-for-woocommerce' ),
				'is_not' => __( 'Is not', 'product-input-fields-for-woocommerce' ),
			),
			'radio' => array(
				'equals'       => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals'   => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
				'contains'     => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'not_contains' => __( 'Not contains', 'product-input-fields-for-woocommerce' ),
			),
			'text' => array(
				'equals'      => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals'  => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
				'contains'    => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'starts_with' => __( 'Starts with', 'product-input-fields-for-woocommerce' ),
				'ends_with'   => __( 'Ends with', 'product-input-fields-for-woocommerce' ),
			),
			'textarea' => array(
				'equals'      => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals'  => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
				'contains'    => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'starts_with' => __( 'Starts with', 'product-input-fields-for-woocommerce' ),
				'ends_with'   => __( 'Ends with', 'product-input-fields-for-woocommerce' ),
			),
			'color' => array(
				'equals'     => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals' => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
			),
			'email' => array(
				'equals'      => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals'  => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
				'contains'    => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'starts_with' => __( 'Starts with', 'product-input-fields-for-woocommerce' ),
				'ends_with'   => __( 'Ends with', 'product-input-fields-for-woocommerce' ),
			),
			'search' => array(
				'equals'       => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals'   => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
				'contains'     => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'not_contains' => __( 'Not contains', 'product-input-fields-for-woocommerce' ),
			),
			'password' => array(
				'length_greater_than' => __( 'Length greater than', 'product-input-fields-for-woocommerce' ),
				'length_less_than'    => __( 'Length less than', 'product-input-fields-for-woocommerce' ),
				'contains'            => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'not_contains'        => __( 'Not contains', 'product-input-fields-for-woocommerce' ),
			),
			'url' => array(
				'contains'     => __( 'Contains', 'product-input-fields-for-woocommerce' ),
				'not_contains' => __( 'Not contains', 'product-input-fields-for-woocommerce' ),
			),
			'datepicker' => array(
				'equals'     => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals' => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
			),
			'timepicker' => array(
				'equals'     => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals' => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
			),
			'weekpicker' => array(
				'includes'     => __( 'Includes', 'product-input-fields-for-woocommerce' ),
				'not_includes' => __( 'Not Includes', 'product-input-fields-for-woocommerce' ),
			),
			'range' => array(
				'includes'     => __( 'Includes', 'product-input-fields-for-woocommerce' ),
				'not_includes' => __( 'Not Includes', 'product-input-fields-for-woocommerce' ),
			),
			'number' => array(
				'greater_than' => __( 'Greater than', 'product-input-fields-for-woocommerce' ),
				'less_than'    => __( 'Less than', 'product-input-fields-for-woocommerce' ),
				'equal_to'     => __( 'Equal to', 'product-input-fields-for-woocommerce' ),
			),
			'file' => array(
				'has_file' => __( 'Has file', 'product-input-fields-for-woocommerce' ),
				'no_file'  => __( 'No file', 'product-input-fields-for-woocommerce' ),
			),
			'products' => array(
				'in_list'     => __( 'In list', 'product-input-fields-for-woocommerce' ),
				'not_in_list' => __( 'Not in list', 'product-input-fields-for-woocommerce' ),
			),
			'products_categories' => array(
				'in_list'     => __( 'In list', 'product-input-fields-for-woocommerce' ),
				'not_in_list' => __( 'Not in list', 'product-input-fields-for-woocommerce' ),
			),
			'product_tags' => array(
				'in_list'     => __( 'In list', 'product-input-fields-for-woocommerce' ),
				'not_in_list' => __( 'Not in list', 'product-input-fields-for-woocommerce' ),
			),
			'product_variations' => array(
				'in_list'     => __( 'In list', 'product-input-fields-for-woocommerce' ),
				'not_in_list' => __( 'Not in list', 'product-input-fields-for-woocommerce' ),
			),
			'country' => array(
				'in_list'     => __( 'In list', 'product-input-fields-for-woocommerce' ),
				'not_in_list' => __( 'Not in list', 'product-input-fields-for-woocommerce' ),
			),
			'value' => array(
				'equals' => __( 'Select Relation', 'product-input-fields-for-woocommerce' ),
			),
			'tel' => array(
				'equals'     => __( 'Equals', 'product-input-fields-for-woocommerce' ),
				'not_equals' => __( 'Not equals', 'product-input-fields-for-woocommerce' ),
			),
		);

		return $relation_options;
	}
	
function is_edit_or_add_product_page() {
	if ( is_admin() ) {
		if ( isset( $_GET['post'] ) ) {
			$post_id = absint( $_GET['post'] );
			return 'product' === get_post_type( $post_id );
		}
		// Add new product.
		if ( isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) {
			return true;
		}
	}
	return false;
}
