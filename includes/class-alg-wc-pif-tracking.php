<?php // phpcs:ignore
/**
 *  Product Input Fields for WooCommerce - Data Tracking Functions
 *
 * @since   1.3.3
 * @package  Product Input Fields/Data Tracking
 * @author  Tyche Softwares
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.

}

if ( ! class_exists( 'Pif_Tracking_Functions' ) ) :

	/**
	 *  Product Input Fields Data Tracking Functions.
	 */
	class Pif_Tracking_Functions {
		/**
		 * Construct.
		 *
		 * @since 1.3.3
		 */
		public function __construct() {
			add_filter( 'pif_lite_ts_tracker_data', array( __CLASS__, 'pif_lite_ts_add_plugin_tracking_data' ), 10, 1 );

			add_action( 'admin_footer', array( __CLASS__, 'ts_admin_notices_scripts' ) );
			add_action( 'pif_lite_init_tracker_completed', array( __CLASS__, 'init_tracker_completed' ), 10 );
			add_filter( 'pif_lite_ts_tracker_display_notice', array( __CLASS__, 'pif_ts_tracker_display_notice' ), 10, 1 );
		}

		/**
		 * Send the plugin data when the user has opted in
		 *
		 * @hook ts_tracker_data
		 * @param array $data All data to send to server.
		 *
		 * @return array $plugin_data All data to send to server.
		 */
		public static function pif_lite_ts_add_plugin_tracking_data( $data ) {
			$plugin_short_name = 'pif_lite';
			if ( ! isset( $_GET[ $plugin_short_name . '_tracker_nonce' ] ) ) {
				return $data;
			}

			$tracker_option = isset( $_GET[ $plugin_short_name . '_tracker_optin' ] ) ? $plugin_short_name . '_tracker_optin' : ( isset( $_GET[ $plugin_short_name . '_tracker_optout' ] ) ? $plugin_short_name . '_tracker_optout' : '' ); // phpcs:ignore
			if ( '' === $tracker_option || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ $plugin_short_name . '_tracker_nonce' ] ) ), $tracker_option ) ) {
				return $data;
			}

			$data = self::pif_lite_plugin_tracking_data( $data );
			return $data;
		}

		/**
		 * Add admin notice script.
		 */
		public static function ts_admin_notices_scripts() {

			$pif_plugin_url = plugins_url() . '/product-input-fields-for-woocommerce';
			$nonce          = wp_create_nonce( 'tracking_notice' );

			wp_enqueue_script(
				'pif_ts_dismiss_notice',
				plugins_url( '/js/tyche-dismiss-tracking-notice.js', __FILE__ ),
				'',
				ALG_WC_PIF_VERSION,
				false
			);

			wp_localize_script(
				'pif_ts_dismiss_notice',
				'pif_ts_dismiss_notice',
				array(
					'ts_prefix_of_plugin' => 'pif_lite',
					'ts_admin_url'        => admin_url( 'admin-ajax.php' ),
					'tracking_notice'     => $nonce,
				)
			);

		}

		/**
		 * Add tracker completed.
		 */
		public static function init_tracker_completed() {
			$redirect_url = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : admin_url( 'admin.php?page=wc-settings&tab=alg_wc_pif' );
			header( 'Location: ' . $redirect_url );
			exit;
		}

		/**
		 * Display admin notice on specific page.
		 *
		 * @param array $is_flag Is Flag defailt value true.
		 */
		public static function pif_ts_tracker_display_notice( $is_flag ) {
			global $current_section;

			if ( isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] ) { // phpcs:ignore
				$is_flag = false;
				if ( isset( $_GET['tab'] ) && 'alg_wc_pif' === $_GET['tab'] && empty( $current_section ) ) { // phpcs:ignore
					$is_flag = true;
				}
			}

			return $is_flag;
		}

		/**
		 * Returns plugin data for tracking.
		 *
		 * @param array $data - Generic data related to WP, WC, Theme, Server and so on.
		 * @return array $data - Plugin data included in the original data received.
		 * @since 1.3.3
		 */
		public static function pif_lite_plugin_tracking_data( $data ) {
			$plugin_data         = array(
				'ts_meta_data_table_name'   => 'ts_tracking_pif_lite_meta_data',
				'ts_plugin_name'            => 'Product Input Fields for WooCommerce',
				'plugin_version'            => ALG_WC_PIF_VERSION,
				'general_settings'          => self::pif_get_general_settings(),
				'per_product_settings'      => self::pif_get_per_product_settings(),
				'all_product_settings'      => self::pif_get_all_product_settings(),
				'all_product_fields'        => self::pif_get_all_product_fields(),
				'per_product_count'         => self::pif_get_per_product_count(),
				'per_product_count_enabled' => self::pif_get_per_product_count_enabled(),
			);
			$data['plugin_data'] = $plugin_data;
			return $data;
		}

		/**
		 * Send the general settings for tracking.
		 *
		 * @since 1.3.3
		 */
		public static function pif_get_general_settings() {
			$general_settings = array(
				'alg_wc_pif_enabled'                       => get_option( 'alg_wc_pif_enabled' ),
				'alg_wc_pif_frontend_position'             => get_option( 'alg_wc_pif_frontend_position' ),
				'alg_wc_pif_frontend_position_priority'    => get_option( 'alg_wc_pif_frontend_position_priority' ),
				'alg_wc_pif_frontend_before'               => get_option( 'alg_wc_pif_frontend_before' ),
				'alg_wc_pif_frontend_template'             => get_option( 'alg_wc_pif_frontend_template' ),
				'alg_wc_pif_frontend_after'                => get_option( 'alg_wc_pif_frontend_after' ),
				'alg_wc_pif_frontend_required_html'        => get_option( 'alg_wc_pif_frontend_required_html' ),
				'alg_wc_pif_frontend_required_js'          => get_option( 'alg_wc_pif_frontend_required_js' ),
				'alg_wc_pif_frontend_order_table_format'   => get_option( 'alg_wc_pif_frontend_order_table_format' ),
				'alg_wc_pif_frontend_refill'               => get_option( 'alg_wc_pif_frontend_refill' ),
				'alg_wc_pif_frontend_smart_textarea'       => get_option( 'alg_wc_pif_frontend_smart_textarea' ),
				'alg_wc_pif_frontend_textarea_auto_height' => get_option( 'alg_wc_pif_frontend_textarea_auto_height' ),
				'alg_wc_pif_frontend_enqueue_timepicker_style' => get_option( 'alg_wc_pif_frontend_enqueue_timepicker_style' ),
				'alg_wc_pif_frontend_enqueue_datepicker_style' => get_option( 'alg_wc_pif_frontend_enqueue_datepicker_style' ),
				'alg_wc_pif_attach_to_admin_new_order'     => get_option( 'alg_wc_pif_attach_to_admin_new_order' ),
				'alg_wc_pif_attach_to_customer_processing_order' => get_option( 'alg_wc_pif_attach_to_customer_processing_order' ),
			);

			return wp_json_encode( $general_settings );
		}

		/**
		 * Returns the per product settings.
		 *
		 * @since 1.3.3
		 */
		public static function pif_get_per_product_settings() {
			$per_products_settings = array(
				'alg_wc_pif_local_enabled'              => get_option( 'alg_wc_pif_local_enabled' ),
				'alg_wc_pif_local_total_number_default' => get_option( 'alg_wc_pif_local_total_number_default' ),
			);
			return wp_json_encode( $per_products_settings );
		}

		/**
		 * Returns the all product settings.
		 *
		 * @since 1.3.3
		 */
		public static function pif_get_all_product_settings() {
			$all_products_settings = array(
				'alg_wc_pif_global_enabled'      => get_option( 'alg_wc_pif_global_enabled' ),
				'alg_wc_pif_global_total_number' => get_option( 'alg_wc_pif_global_total_number' ),
			);
			return wp_json_encode( $all_products_settings );
		}

		/**
		 * Returns the all product fields.
		 *
		 * @since 1.3.3
		 */
		public static function pif_get_all_product_fields() {
			$input_counts        = get_option( 'alg_wc_pif_global_total_number', 0 );
			$all_products_fields = array();
			for ( $i = 1; $i <= $input_counts; $i++ ) {
				$args = array(
					'alg_wc_pif_enabled_global_' . $i     => get_option( 'alg_wc_pif_enabled_global_' . $i ),
					'alg_wc_pif_type_global_' . $i        => get_option( 'alg_wc_pif_type_global_' . $i ),
					'alg_wc_pif_required_global_' . $i    => get_option( 'alg_wc_pif_required_global_' . $i ),
					'alg_wc_pif_title_global_' . $i       => get_option( 'alg_wc_pif_title_global_' . $i ),
					'alg_wc_pif_placeholder_global_' . $i => get_option( 'alg_wc_pif_placeholder_global_' . $i ),
					'alg_wc_pif_default_value_global_' . $i => get_option( 'alg_wc_pif_default_value_global_' . $i ),
					'alg_wc_pif_class_global_' . $i       => get_option( 'alg_wc_pif_class_global_' . $i ),
					'alg_wc_pif_style_global_' . $i       => get_option( 'alg_wc_pif_style_global_' . $i ),
					'alg_wc_pif_required_message_global_' . $i => get_option( 'alg_wc_pif_required_message_global_' . $i ),
					'alg_wc_pif_input_restrictions_min_global_' . $i => get_option( 'alg_wc_pif_input_restrictions_min_global_' . $i ),
					'alg_wc_pif_input_restrictions_max_global_' . $i => get_option( 'alg_wc_pif_input_restrictions_max_global_' . $i ),
					'alg_wc_pif_input_restrictions_step_global_' . $i => get_option( 'alg_wc_pif_input_restrictions_step_global_' . $i ),
					'alg_wc_pif_input_restrictions_maxlength_global_' . $i => get_option( 'alg_wc_pif_input_restrictions_maxlength_global_' . $i ),
					'alg_wc_pif_input_restrictions_pattern_global_' . $i => get_option( 'alg_wc_pif_input_restrictions_pattern_global_' . $i ),
					'alg_wc_pif_type_checkbox_yes_global_' . $i => get_option( 'alg_wc_pif_type_checkbox_yes_global_' . $i ),
					'alg_wc_pif_type_checkbox_no_global_' . $i => get_option( 'alg_wc_pif_type_checkbox_no_global_' . $i ),
					'alg_wc_pif_type_file_accept_global_' . $i => get_option( 'alg_wc_pif_type_file_accept_global_' . $i ),
					'alg_wc_pif_type_file_wrong_type_msg_global_' . $i => get_option( 'alg_wc_pif_type_file_wrong_type_msg_global_' . $i ),
					'alg_wc_pif_type_file_max_size_global_' . $i => get_option( 'alg_wc_pif_type_file_max_size_global_' . $i ),
					'alg_wc_pif_type_file_max_size_msg_global_' . $i => get_option( 'alg_wc_pif_type_file_max_size_msg_global_' . $i ),
					'alg_wc_pif_type_datepicker_format_global_' . $i => get_option( 'alg_wc_pif_type_datepicker_format_global_' . $i ),
					'alg_wc_pif_type_datepicker_mindate_global_' . $i => get_option( 'alg_wc_pif_type_datepicker_mindate_global_' . $i ),
					'alg_wc_pif_type_datepicker_maxdate_global_' . $i => get_option( 'alg_wc_pif_type_datepicker_maxdate_global_' . $i ),
					'alg_wc_pif_type_datepicker_addyear_global_' . $i => get_option( 'alg_wc_pif_type_datepicker_addyear_global_' . $i ),
					'alg_wc_pif_type_datepicker_yearrange_global_' . $i => get_option( 'alg_wc_pif_type_datepicker_yearrange_global_' . $i ),
					'alg_wc_pif_type_datepicker_firstday_global_' . $i => get_option( 'alg_wc_pif_type_datepicker_firstday_global_' . $i ),
					'alg_wc_pif_type_timepicker_format_global_' . $i => get_option( 'alg_wc_pif_type_timepicker_format_global_' . $i ),
					'alg_wc_pif_type_timepicker_interval_global_' . $i => get_option( 'alg_wc_pif_type_timepicker_interval_global_' . $i ),
					'alg_wc_pif_type_color_allow_typing_global_' . $i => get_option( 'alg_wc_pif_type_color_allow_typing_global_' . $i ),
					'alg_wc_pif_select_radio_option_type_global_' . $i => get_option( 'alg_wc_pif_select_radio_option_type_global_' . $i ),
				);
				$all_products_fields[ 'alg_wc_pif_global_total_number_' . $i ] = $args;
			}
			return wp_json_encode( $all_products_fields );
		}

		/**
		 * Return the per product count.
		 *
		 * @since 1.3.3
		 */
		public static function pif_get_per_product_count() {
			global $wpdb;
			$per_product_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(DISTINCT(post_id)) FROM `wp_postmeta` WHERE `meta_key` LIKE %s AND `meta_value` = "yes"', '%' . $wpdb->esc_like( '_alg_wc_pif_enabled_local_' ) . '%' ) ); // db call ok. no-cache ok.

			return wp_json_encode( $per_product_count );
		}

		/**
		 * Returns the per product count of which inputs are enabled..
		 *
		 * @since 1.3.3
		 */
		public static function pif_get_per_product_count_enabled() {
			global $wpdb;
			$per_product_count_enabled = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `wp_postmeta` WHERE `meta_key` LIKE %s AND `meta_value` = "yes"', '%' . $wpdb->esc_like( '_alg_wc_pif_enabled_local_' ) . '%' ) ); // db call ok. no-cache ok.
			return wp_json_encode( $per_product_count_enabled );
		}

		/**
		 * Send the per product enabled field type name.
		 *
		 * @since 1.3.3
		 */
		public static function pif_get_all_active_fields_per_product() {
			// Get all published product ids.
			$product_ids = get_posts(
				array(
					'post_type'   => 'product',
					'numberposts' => -1,
					'post_status' => 'publish',
					'fields'      => 'ids',
				)
			);
			if ( is_array( $product_ids ) && ! empty( $product_ids ) ) {
				$active_fields = array();
				foreach ( $product_ids as $id ) {
					$perproduct_active_field = array();
					// Get total number of fields set for per product.
					$total_fields = get_post_meta( $id, '_' . ALG_WC_PIF_ID . '_local_total_number', true );
					if ( $total_fields > 0 ) {
						for ( $i = 1; $i <= $total_fields; $i++ ) {
							// check if the field is enabled.
							$is_enabled = get_post_meta( $id, '_' . ALG_WC_PIF_ID . '_enabled_local_' . $i, true );
							if ( 'yes' === $is_enabled ) {
								// Get the name of field type.
								$perproduct_active_field[] = get_post_meta( $id, '_' . ALG_WC_PIF_ID . '_type_local_' . $i, true );
							}
						}
						if ( ! empty( $perproduct_active_field ) ) {
							$active_fields[] = $perproduct_active_field;
						}
					}
				}
				if ( ! empty( $active_fields ) ) {
					return wp_json_encode( $active_fields, JSON_FORCE_OBJECT );
				}
			}
		}
	}

endif;

$pif_tracking_functions = new Pif_Tracking_Functions();
