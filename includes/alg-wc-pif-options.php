<?php
/**
 * Product Input Fields for WooCommerce - Options
 *
 * @version 1.1.6
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 *
 * @package product-input-fields-for-woocommerce/Settings/Fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'alg_get_product_input_fields_options' ) ) {
	/**
	 * Alg_get_product_input_fields_options.
	 *
	 * @version 1.1.6
	 * @since   1.0.0
	 * @todo    (later) more types - https://www.w3schools.com/html/html_form_input_types.asp - date; datetime-local; month; time; week
	 * @todo    (later) color type - show color instead of color code on frontend and backend
	 * @todo    (later) add 'state' to "Type"
	 * @todo    (later) toggle show/hide specific type options when changing type in select drop down (in admin settings and admin per product metaboxes)
	 * @todo    (later) add "show/hide by user roles" options
	 * @todo    (later) add "pricing" options i.e. "product addons" (paid, discount, free) and optional AJAX (for all fields)
	 * @todo    (maybe) rethink - 'title' - not empty be default & required
	 * @todo    (maybe) more "Input Restrictions": disabled, readonly, size etc. (check https://www.w3schools.com/html/html_form_input_types.asp and https://www.w3schools.com/html/html_form_attributes.asp)
	 * @todo    (maybe) more types - https://www.w3schools.com/html/html_form_input_types.asp - submit; reset; button;
	 * @todo    (maybe) separate position/priority options for each field (instead of global frontend_position option)
	 */
	function alg_get_product_input_fields_options() {
		/* translators: %s: Input type */
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
}
