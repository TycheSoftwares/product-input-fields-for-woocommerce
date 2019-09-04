<?php
/**
 * Product Input Fields for WooCommerce - General Section Settings
 *
 * @version 1.2.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 *
 * @package product-input-fields-for-woocommerce/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Alg_WC_PIF_Settings_General' ) ) :

	/**
	 * General Settings Section
	 */
	class Alg_WC_PIF_Settings_General extends Alg_WC_PIF_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id   = '';
			$this->desc = __( 'General', 'product-input-fields-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * Get_section_settings.
		 *
		 * @version 1.2.1
		 * @since   1.0.0
		 * @todo    (later) major reset settings - including all global and *local* input fields
		 * @todo    (later) add dashboard and move all options (except dashboard) to another settings section(s)
		 * @todo    (later) global required_message, max_size_message, wrong_file_type_message (maybe with replaceable %title%) (and add desc_tip - used when not required by JS enabled / outside the add to cart button form and per field message not set (i.e. leave blank to use default)) - validate_product_input_fields_on_add_to_cart()
		 */
		public function get_section_settings() {
			$settings = array(
				array(
					'title' => __( 'Product Input Fields Options', 'product-input-fields-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'options',
				),
				array(
					'title'    => __( 'Product Input Fields', 'product-input-fields-for-woocommerce' ),
					'desc'     => '<strong>' . __( 'Enable plugin', 'product-input-fields-for-woocommerce' ) . '</strong>',
					'desc_tip' => __( 'WooCommerce Product Input Fields.', 'product-input-fields-for-woocommerce' ),
					'id'       => 'enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'options',
				),
			);
			$settings = array_merge(
				$settings,
				array(
					array(
						'title' => __( 'Frontend Options', 'product-input-fields-for-woocommerce' ),
						'type'  => 'title',
						'id'    => 'frontend_options',
					),
					array(
						'title'   => __( 'Position', 'product-input-fields-for-woocommerce' ),
						'desc'    => sprintf( /* translators: %1$s: Shortcode, %2$s: function */
							__( 'If set to "Do not display", alternatively you can use %1$s shortcode, or PHP %2$s function.', 'product-input-fields-for-woocommerce' ),
							'<code>[alg_display_product_input_fields]</code>',
							'<code>alg_display_product_input_fields()</code>'
						),
						'id'      => 'frontend_position',
						'default' => 'woocommerce_before_add_to_cart_button',
						'type'    => 'select',
						'options' => array(
							'woocommerce_before_add_to_cart_button' => __( 'Before add to cart button', 'product-input-fields-for-woocommerce' ),
							'woocommerce_after_add_to_cart_button' => __( 'After add to cart button', 'product-input-fields-for-woocommerce' ),
							'disable' => __( 'Do not display', 'product-input-fields-for-woocommerce' ),
						),
					),
					array(
						'title'    => __( 'Position Priority', 'product-input-fields-for-woocommerce' ),
						'desc_tip' => __( 'Ignored if "Position" is set to "Do not display".', 'product-input-fields-for-woocommerce' ),
						'id'       => 'frontend_position_priority',
						'default'  => 10,
						'type'     => 'number',
					),
					array(
						'title'   => __( 'HTML to Add Before Product Input Fields', 'product-input-fields-for-woocommerce' ),
						'id'      => 'frontend_before',
						'default' => '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">',
						'type'    => 'textarea',
						'css'     => 'width:60%;min-width:300px;',
					),
					array(
						'title'    => __( 'Product Input Field Template', 'product-input-fields-for-woocommerce' ),
						'desc_tip' => __( 'Replaced values:', 'product-input-fields-for-woocommerce' ) . ' %field_id%, %title%, %field%',
						'desc'     => sprintf( /* translators: %s: html */
							__( 'Alternatively try e.g.: %s', 'product-input-fields-for-woocommerce' ),
							'<code>' . esc_html( '<p><label for="%field_id%">%title%</label>%field%</p>' ) . '</code>'
						),
						'id'       => 'frontend_template',
						'default'  => '<tr><td><label for="%field_id%">%title%</label></td><td>%field%</td></tr>',
						'type'     => 'textarea',
						'css'      => 'width:60%;min-width:300px;',
					),
					array(
						'title'   => __( 'HTML to Add After Product Input Fields', 'product-input-fields-for-woocommerce' ),
						'id'      => 'frontend_after',
						'default' => '</table>',
						'type'    => 'textarea',
						'css'     => 'width:60%;min-width:300px;',
					),
					array(
						'title'   => __( 'HTML to Add After Required Field Title', 'product-input-fields-for-woocommerce' ),
						'id'      => 'frontend_required_html',
						'default' => '&nbsp;<abbr class="required" title="required">*</abbr>',
						'type'    => 'textarea',
						'css'     => 'width:60%;min-width:300px;',
					),
					array(
						'title'   => __( 'Add HTML Required Attribute', 'product-input-fields-for-woocommerce' ),
						'desc'    => __( 'Enable', 'product-input-fields-for-woocommerce' ),
						'id'      => 'frontend_required_js',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'    => __( 'Item Name Order Table Format', 'product-input-fields-for-woocommerce' ),
						'desc_tip' => __( 'Affects Thank You page, Emails etc.', 'product-input-fields-for-woocommerce' ),
						'id'       => 'frontend_order_table_format',
						'default'  => '&nbsp;| %title% %value%',
						'type'     => 'textarea',
						'css'      => 'width:60%;min-width:300px;',
					),
					array(
						'title'   => __( 'Refill Fields with Previous Input', 'product-input-fields-for-woocommerce' ),
						'desc'    => __( 'Refill', 'product-input-fields-for-woocommerce' ),
						'id'      => 'frontend_refill',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'   => __( 'Smart Textarea', 'product-input-fields-for-woocommerce' ),
						'desc'    => __( 'Show only the textarea excerpt on frontend and hovering it will make it display the full content', 'product-input-fields-for-woocommerce' ),
						'id'      => 'frontend_smart_textarea',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'   => __( 'Textarea Auto Height', 'product-input-fields-for-woocommerce' ),
						'desc'    => __( 'Makes the textarea auto increase its height as users type', 'product-input-fields-for-woocommerce' ),
						'id'      => 'frontend_textarea_auto_height',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'    => __( 'Load Timepicker Style', 'product-input-fields-for-woocommerce' ),
						'desc'     => __( 'Enqueues Timepicker Style', 'product-input-fields-for-woocommerce' ),
						'desc_tip' => __( 'If you are not using Timepicker, please disable this option', 'product-input-fields-for-woocommerce' ),
						'id'       => 'frontend_enqueue_timepicker_style',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'Load Datepicker Style', 'product-input-fields-for-woocommerce' ),
						'desc'     => __( 'Enqueues Datepicker and Weekpicker Style', 'product-input-fields-for-woocommerce' ),
						'desc_tip' => __( 'If you are not using Datepicker or Weekpicker, please disable this option', 'product-input-fields-for-woocommerce' ),
						'id'       => 'frontend_enqueue_datepicker_style',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'frontend_options',
					),
				)
			);
			$settings = array_merge(
				$settings,
				array(
					array(
						'title' => __( 'Emails Options', 'product-input-fields-for-woocommerce' ),
						'type'  => 'title',
						'id'    => 'emails_options',
					),
					array(
						'title'   => __( 'Attach Files to Admin\'s New Order Emails', 'product-input-fields-for-woocommerce' ),
						'desc'    => __( 'Attach', 'product-input-fields-for-woocommerce' ),
						'id'      => 'attach_to_admin_new_order',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'   => __( 'Attach Files to Customer\'s Processing Order Emails', 'product-input-fields-for-woocommerce' ),
						'desc'    => __( 'Attach', 'product-input-fields-for-woocommerce' ),
						'id'      => 'attach_to_customer_processing_order',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'emails_options',
					),
				)
			);
			return $settings;
		}

	}

endif;

return new Alg_WC_PIF_Settings_General();
