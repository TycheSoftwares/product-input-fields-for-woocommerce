<?php
/**
 * Product Input Fields for WooCommerce - Data Tracking Class
 *
 * @since   1.3.3
 * @package Product Input Fields/Data Tracking
 * @author  Tyche Softwares
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Pif_Data_Tracking' ) ) :

	/**
	 * Product Input Fields Data Tracking Core.
	 */
	class Pif_Data_Tracking {

		/**
		 * Construct.
		 *
		 * @since 1.3.3
		 */
		public function __construct() {
			// Add notice on Admin pages.
			add_action( 'admin_notices', array( __CLASS__, 'ts_admin_notice' ) );
			// Include JS script for the notice.
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'ts_admin_notices_scripts' ) );
			// Track user's choice.
			add_action( 'wp_ajax_pif_lite_admin_choice', array( __CLASS__, 'pif_lite_admin_choice' ) );
			// Add a recurring As action.
			add_action( 'init', array( __CLASS__, 'ts_add_recurring_action' ) );
			// Send Tracker Data.
			add_action( 'ts_send_data_tracking_usage', array( __CLASS__, 'pif_lite_send_tracking_data' ) );
		}

		/**
		 * Add admin notice to entice site admin to allow data tracking.
		 *
		 * @since 1.3.3
		 */
		public static function ts_admin_notice() {
			global $current_screen;
			$ts_current_screen = get_current_screen();

			// Return when we're on any edit screen, as notices are distracting in there.
			if ( ( method_exists( $ts_current_screen, 'is_block_editor' ) && $ts_current_screen->is_block_editor() ) || ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) ) {
				return;
			}

			// Allow the submit data to be saved if needed.
			self::ts_update_tracking();

			$plugin_url  = plugins_url() . '/product-input-fields-for-woocommerce';
			$plugin_name = 'Product Input Fields for WooCommerce';
			$doc_link    = 'https://www.tychesoftwares.com/docs/docs/product-input-fields-for-woocommerce/product-input-fields-usage-tracking';

			// Condition to check if user has already made a choice.
			if ( '' === get_option( 'pif_lite_allow_tracking', '' ) ) {
				?>
				<div class=''>
					<div class="pif-lite-message pif-lite-tracker notice notice-info is-dismissible" style="position: relative;">
						<div style="position: absolute;"><img class="site-logo" src= "<?php echo esc_html( $plugin_url . '/assets/images/site-logo-new.jpg' ); ?>"></div>
						<p style="margin: 10px 0 10px 130px; font-size: medium;">
							<?php
								printf(
									wp_kses_post(
										// translators: Plugin Name & Documentation Link.
										__( 'Want to help make %1$s even more awesome? Allow %1$s to collect non-sensitive diagnostic data and usage information and get 20%% off on your next purchase. <a href="%2$s" target="_blank">Find out more</a>.', 'woocommerce-call-for-price' )
									),
									esc_html( $plugin_name ),
									esc_url( $doc_link )
								);
							?>
						</p>
						<p class="submit">
							<a class="button-primary button button-large" id="pif-lite-allow" href="<?php echo esc_url( add_query_arg( 'pif_lite_tracker_optin', 'true' ) ); ?>"><?php esc_html_e( 'Allow', 'woocommerce-call-for-price' ); ?></a>
							<a class="button-secondary button button-large skip" id="pif-lite-disallow" href="<?php echo esc_url( add_query_arg( 'pif_lite_tracker_optout', 'true' ) ); ?>"><?php esc_html_e( 'No thanks', 'woocommerce-call-for-price' ); ?></a>
						</p>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Admin enqueue scripts for data tracking.
		 *
		 * @since 1.3.3
		 */
		public static function ts_admin_notices_scripts() {

			// Add these files only if user has not yet made a choice.
			if ( '' === get_option( 'pif_lite_allow_tracking', '' ) ) {
				$plugin_url       = plugins_url() . '/product-input-fields-for-woocommerce';
				$numbers_instance = Alg_WC_PIF::$version;
				wp_enqueue_script(
					'pif_dismiss_notice',
					$plugin_url . '/assets/js/pif-tracking-notice.js',
					'',
					$numbers_instance,
					false
				);

				wp_localize_script(
					'pif_dismiss_notice',
					'pif_dismiss_params',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
					)
				);
			}
		}

		/**
		 * Update admin's tracking choice.
		 *
		 * @since 1.3.3
		 */
		public static function ts_update_tracking() {

			if ( current_user_can( 'administrator' ) ) {
				$url = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
				if ( isset( $_GET['pif_lite_tracker_optin'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
					update_option( 'pif_lite_allow_tracking', 'yes' );
					if ( '' !== $url ) {
						header( "Location: $url" );
					}
				} elseif ( isset( $_GET['pif_lite_tracker_optout'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
					update_option( 'pif_lite_allow_tracking', 'no' );
					// Run a single call to send this to our servers.
					self::pif_lite_send_tracking_data();
					// Reload the page.
					if ( '' !== $url ) {
						header( "Location: $url" );
					}
				}
			}
		}

		/**
		 * Tracking Notice dismissed.
		 *
		 * @since 1.3.3
		 */
		public static function pif_lite_admin_choice() {
			$admin_choice = isset( $_POST['admin_choice'] ) ? sanitize_text_field( wp_unslash( $_POST['admin_choice'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification
			update_option( 'pif_lite_allow_tracking', $admin_choice );
			// Run a single call to send this to our servers.
			self::pif_lite_send_tracking_data();
		}

		/**
		 * Add a weekly scheduled action.
		 *
		 * @since 1.3.3
		 */
		public static function ts_add_recurring_action() {
			if ( function_exists( 'as_next_scheduled_action' ) ) { // Indicates that the AS library is present.
				if ( false === as_next_scheduled_action( 'ts_send_data_tracking_usage' ) && 'yes' === get_option( 'pif_lite_allow_tracking', '' ) ) {
					as_schedule_recurring_action( time(), 86400 * 7, 'ts_send_data_tracking_usage' );
				}
			}
		}

		/**
		 * Send the tracking data to our servers.
		 *
		 * @since 1.3.3
		 */
		public static function pif_lite_send_tracking_data() {

			$allow_tracking = get_option( 'pif_lite_allow_tracking', '' );
			$override       = 'yes' === $allow_tracking ? true : false;
			$api_url        = 'http://tracking.tychesoftwares.com/v1/';

			if ( false === $override ) {
				$params = array(
					'tracking_usage' => 'no',
					'url'            => home_url(),
					'email'          => '',
				);
				$params = apply_filters( 'ts_tracker_opt_out_data', $params );
			} else {
				$params = self::ts_get_tracking_data();
			}
			wp_safe_remote_post(
				$api_url,
				array(
					'method'      => 'POST',
					'timeout'     => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => false,
					'headers'     => array( 'user-agent' => 'TSTracker/' . md5( esc_url( home_url( '/' ) ) ) . ';' ),
					'body'        => wp_json_encode( $params ),
					'cookies'     => array(),
				)
			);
		}

		/**
		 * Return Tracking Data which is a combination of generic site data
		 * & plugin specific data.
		 *
		 * @return array $data - Tracking Data.
		 * @since 1.3.3
		 */
		public static function ts_get_tracking_data() {

			// Plugin info.
			$all_plugins = self::ts_get_all_plugins();
			$data        = array(
				// General Site Info.
				'url'               => home_url(),
				'email'             => apply_filters( 'ts_tracker_admin_email', get_option( 'admin_email', '' ) ),
				// WP Info.
				'wp'                => self::ts_get_wordpress_info(),
				'theme_info'        => self::ts_get_theme_info(),
				// Server Info.
				'server'            => self::ts_get_server_info(),
				// Plugin Info.
				'active_plugins'    => $all_plugins['active_plugins'],
				'inactive_plugins'  => $all_plugins['inactive_plugins'],
				// WC Version.
				'wc_plugin_version' => WC()->version,
			);
			$data        = pif_Tracking_Functions::pif_lite_plugin_tracking_data( $data );
			return $data;
		}

		/**
		 * Return WP information such as name, description, setup etc.
		 *
		 * @return array $wp_data - WP Data.
		 * @since 1.3.3
		 */
		public static function ts_get_wordpress_info() {
			$wp_data = array();
			$memory  = wc_let_to_num( WP_MEMORY_LIMIT );

			if ( function_exists( 'memory_get_usage' ) ) {
				$system_memory = wc_let_to_num( @ini_get( 'memory_limit' ) ); //phpcs:ignore
				$memory        = max( $memory, $system_memory );
			}

			$wp_data = array(
				'memory_limit'    => size_format( $memory ),
				'debug_mode'      => defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Yes' : 'No',
				'locale'          => get_locale(),
				'wp_version'      => get_bloginfo( 'version' ),
				'multisite'       => is_multisite() ? 'Yes' : 'No',
				'blogdescription' => get_option( 'blogdescription' ),
				'blogname'        => get_option( 'blogname' ),
				'wc_city'         => get_option( 'woocommerce_store_city' ),
				'wc_country'      => get_option( 'woocommerce_default_country' ),
			);
			return $wp_data;
		}

		/**
		 * Get the current theme info, theme name and version.
		 *
		 * @return array Theme information.
		 * @since 1.3.3
		 */
		public static function ts_get_theme_info() {
			$theme_data        = wp_get_theme();
			$theme_child_theme = is_child_theme() ? 'Yes' : 'No';
			return array(
				'theme_name'    => $theme_data->name,
				'theme_version' => $theme_data->version,
				'child_theme'   => $theme_child_theme,
			);
		}

		/**
		 * Returns the Server Specific Information.
		 *
		 * @return array $server_data - Server Data.
		 * @since 1.3.3
		 */
		public static function ts_get_server_info() {
			global $wpdb;
			$server_data = array(
				'mysql_version'        => $wpdb->db_version(),
				'php_max_upload_size'  => size_format( wp_max_upload_size() ),
				'php_default_timezone' => date_default_timezone_get(),
				'php_soap'             => class_exists( 'SoapClient' ) ? 'Yes' : 'No',
				'php_fsockopen'        => function_exists( 'fsockopen' ) ? 'Yes' : 'No',
				'php_curl'             => function_exists( 'curl_init' ) ? 'Yes' : 'No',
			);

			if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
				$server_data['software'] = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );
			}

			if ( function_exists( 'phpversion' ) ) {
				$server_data['php_version'] = phpversion();
			}

			if ( function_exists( 'ini_get' ) ) {
				$server_data['php_post_max_size']  = size_format( wc_let_to_num( ini_get( 'post_max_size' ) ) );
				$server_data['php_time_limt']      = ini_get( 'max_execution_time' );
				$server_data['php_max_input_vars'] = ini_get( 'max_input_vars' );
				$server_data['php_suhosin']        = extension_loaded( 'suhosin' ) ? 'Yes' : 'No';
			}
			return $server_data;
		}

		/**
		 * Get all plugins grouped into activated or not.
		 *
		 * @return array with 2 keys 'active_plugins' & 'inactive_plugins'.
		 * @since 1.3.3
		 */
		public static function ts_get_all_plugins() {
			// Ensure get_plugins function is loaded.
			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$plugins             = get_plugins();
			$active_plugins_keys = get_option( 'active_plugins', array() );
			$active_plugins      = array();
			foreach ( $plugins as $k => $v ) {
				// Format the data as needed.
				$formatted = array(
					'name' => wp_strip_all_tags( $v['Name'] ),
				);

				if ( isset( $v['Version'] ) ) {
					$formatted['version'] = wp_strip_all_tags( $v['Version'] );
				}

				if ( isset( $v['Author'] ) ) {
					$formatted['author'] = wp_strip_all_tags( $v['Author'] );
				}

				if ( isset( $v['Network'] ) ) {
					$formatted['network'] = wp_strip_all_tags( $v['Network'] );
				}
				if ( isset( $v['PluginURI'] ) ) {
					$formatted['plugin_uri'] = wp_strip_all_tags( $v['PluginURI'] );
				}
				if ( in_array( $k, $active_plugins_keys, true ) ) {
					// Remove active plugins from list so we can show active and inactive separately.
					unset( $plugins[ $k ] );
					$active_plugins[ $k ] = $formatted;
				} else {
					$plugins[ $k ] = $formatted;
				}
			}
			return array(
				'active_plugins'   => $active_plugins,
				'inactive_plugins' => $plugins,
			);
		}
	}

endif;

$pif_data_tracking = new Pif_Data_Tracking();
