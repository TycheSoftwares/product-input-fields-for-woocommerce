<?php
/**
 * Product Input Fields for WooCommerce.
 *
 * Main Class.
 *
 * @author      Tyche Softwares
 * @package     PIF/Main
 * @category    Classes
 * @since       1.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Flexi Archiver Core Class.
 *
 * @class Flexi_Archiver.
 */
final class Product_Input_Fields {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected static $plugin_version = '2.0.0';

	/**
	 * Minimum version of WordPress required.
	 *
	 * @var string
	 */
	private static $wordpress_version = '5.2';

	/**
	 * Minimum version of PHP required.
	 *
	 * @var string
	 */
	private static $php_version = '7.4';

	/**
	 * Slug.
	 *
	 * @var string
	 */
	protected static $slug = 'pif';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected static $plugin_slug = 'product-input-fields-for-woocommerce';

	/**
	 * Plugin Name.
	 *
	 * @var string
	 */
	protected static $plugin_name = 'Product Input Fields for WooCommerce';

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	protected static $plugin_url = 'https://www.tychesoftwares.com/store/premium-plugins/product-input-fields-for-woocommerce/';

	/**
	 * The single instance of the class.
	 *
	 * @var Product_Input_Fields
	 */
	protected static $instance = null;

	/**
	 * Retrieve the instance of the class and ensures only one instance is loaded or can be loaded.
	 *
	 * @return Product_Input_Fields
	 *
	 * @since 1.0
	 */
	public static function instance() {
		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Product_Input_Fields ) ) {
			self::$instance = new Product_Input_Fields();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * A dummy constructor to prevent FAW from being loaded more than once.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * A dummy magic method to prevent FAW from being cloned.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Not allowed.', 'product-input-fields-for-woocommerce' ), '1.0' );
	}

	/**
	 * A dummy magic method to prevent FAW from being unserialized.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Not allowed.', 'product-input-fields-for-woocommerce' ), '1.0' );
	}

	/**
	 * Default constructor
	 *
	 * @since 1.0
	 */
	private function setup() {

        self::handle_localization();
		/**
		 * Define Constants.
		 */
		self::define_constants();

		if ( ! self::check_requirements() ) {
			return;
		}

		self::init();

		/**
		 * Include Files.
		 */
		self::maybe_include_files();

		/**
		 * Hooks.
		 */
		self::init_hooks();
	}

	/**
	 * Initializes
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 * @access  public
	 */
	public function init() {
		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . PIF_PLUGIN_BASENAME, array( $this, 'action_links' ) );
		}
		add_action( 'admin_init', array( $this, 'pif_include_updating_data' ) );

		add_filter ( 'woocommerce_settings_tabs_array',
			function ( $tabs ) {
				$tabs['product-input-fields-for-woocommerce'] = __( 'Product Input Fields', 'product-input-fields-for-woocommerce' );
				return $tabs;
			},
			50
		);

		add_action ( 'woocommerce_settings_tabs_product-input-fields-for-woocommerce',
			function () {
				echo '<div id="product-input-fields-for-woocommerce"></div>';
			}
		);
	}

	/**
	 * Action Hooks.
	 *
	 * @since 1.0
	 */
	private static function init_hooks() {
		register_activation_hook( PIF_FILE, array( __CLASS__, 'activate_plugin' ) );
		register_deactivation_hook( PIF_FILE, array( __CLASS__, 'deactivate_plugin' ) );

		// PIF Hooks.
		self::include_file( 'class-pif-hooks.php' );
		PIF_Hooks::init();
	}

	/**
	 * Activation Hook.
	 */
	public static function activate_plugin() {
		
		if ( get_option( 'alg_wc_pif_enabled', null ) !== null ) {
			return;
		}
		
		$defaults = array(
			'enabled'                             => false,
			'local_enabled'                       => false,
			'frontend_position'                   => 'woocommerce_before_add_to_cart_button',
			'frontend_position_priority'          => 10,
			'frontend_before'                     => '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">',
			'frontend_template'                   => '<tr><td><label for="%field_id%">%title%</label></td><td>%field%</td></tr>',
			'frontend_after'                      => '</table>',
			'frontend_required_html'              => '<span class="required">*</span>',
			'frontend_order_table_format'         => '%title% %value%',
			'frontend_refill'                     => true,
			'frontend_smart_textarea'             => true,
			'frontend_textarea_auto_height'       => true,
			'fill_frontend_url_parameter'         => false,
			'attach_to_admin_new_order'           => true,
			'attach_to_customer_processing_order' => true,
			'type_checkbox_yes'                   => 'Yes',
			'type_checkbox_no'                    => 'No',
		);

		if ( ! get_option( 'pif_general_settings', false ) ) {
			update_option( 'pif_general_settings', $defaults );
			update_option( 'alg_wc_pif_version', PIF_VERSION );
		}

	}

	/**
	 * Deactivation Hook.
	 *
	 * @since 1.0
	 */
	public static function deactivate_plugin() {
		do_action( 'pif_deactivate' );
	}

	/**
	 * Function for definining constants.
	 *
	 * @param string $variable Constant which is to be defined.
	 * @param string $value Valueof the Constant.
	 *
	 * @since 1.0
	 */
	public static function define( $variable, $value ) {
		if ( ! defined( $variable ) ) {
			define( $variable, $value );
		}
	}

	/**
	 * Include File.
	 *
	 * @param string $file File to be included.
	 * @param bool   $is_plugin_include_file If it's a plugin file, then we can add the path.
	 * @since 1.0
	 */
	public static function include_file( $file, $is_plugin_include_file = true ) {
		$file = $is_plugin_include_file ? PIF_PLUGIN_DIR_PATH . '/includes/' . $file : $file;

		if ( file_exists( $file ) ) {
			include_once $file;
		}
	}

    /**
	 * Localization
	 *
	 * @version 1.1.3
	 * @since   1.1.3
	 */
	private function handle_localization() {
		$domain = 'product-input-fields-for-woocommerce';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		$loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '-' . $locale . '.mo' );

		if ( $loaded ) {
			return $loaded;
		} else {
			load_plugin_textdomain( $domain, false, dirname( plugin_basename( PIF_FILE ) ) . '/languages/' );
		}
	}

	/**
	 * Define constants to be used accross the plugin.
	 *
	 * @since 1.0
	 */
	public static function define_constants() {
		self::define( 'PIF_URL', self::$plugin_url );
		self::define( 'PIF_SLUG', self::$slug );
		self::define( 'PIF_PLUGIN_SLUG', self::$plugin_slug );
		self::define( 'PIF_VERSION', self::$plugin_version );
		self::define( 'PIF_PLUGIN_BASENAME', plugin_basename( PIF_FILE ) );
		self::define( 'PIF_PLUGIN_DIR_PATH', plugin_dir_path( PIF_FILE ) );
		self::define( 'PIF_PLUGIN_URL', plugins_url( '/', PIF_FILE ) );
		self::define( 'PIF_AJAX_URL', get_admin_url() . 'admin-ajax.php' );
		self::define( 'ALG_WC_PIF_ID', 'alg_wc_pif' );
	}

	/**
	 * Checks that all requirements are met.
	 *
	 * @return bool
	 */
	public static function check_requirements() {

		$messages = array();

		// Check WordPress version.
		if ( version_compare( get_bloginfo( 'version' ), self::$wordpress_version, '<' ) ) {
			/* translators: 1. Plugin Name, 2. WordPress Version */
			$messages[] = sprintf( esc_html__( 'You are using an outdated version of WordPress. %1$s requires WP version %2$s or higher.', 'product-input-fields-for-woocommerce' ), self::$plugin_name, self::$wordpress_version );
		}

		// Check PHP version.
		if ( version_compare( phpversion(), self::$php_version, '<' ) ) {
			/* translators: 1. Plugin Name, 2. PHP Version */
			$messages[] = sprintf( esc_html__( '%1$s requires PHP version %2$s or above. Please update PHP to run this plugin.', 'product-input-fields-for-woocommerce' ), self::$plugin_name, self::$php_version );
		}

		// Check WooCommerce.
		if ( ! self::is_woocommerce_active() ) {
			/* translators: Plugin Name */
			$messages[] = sprintf( esc_html__( 'WooCommerce not found. %s requires a minimum of WooCommerce v3.3.0.', 'product-input-fields-for-woocommerce' ), self::$plugin_name );
		}

		// Check OpenSSL.
		if ( ! function_exists( 'openssl_verify' ) || ! function_exists( 'openssl_pkey_get_public' ) ) {
			/* translators: Plugin Name */
			$messages[] = sprintf( esc_html__( 'OpenSSL extension required.', 'product-input-fields-for-woocommerce' ), self::$plugin_name );
		}

		if ( empty( $messages ) ) {
			return true;
		}

		add_action( 'admin_init', array( __CLASS__, 'deactivate' ) );

		return false;
	}

	/**
	 * Auto-deactivate plugin if requirements are not met.
	 */
	public static function deactivate() {
		if ( is_plugin_active( plugin_basename( PIF_FILE ) ) ) {
			deactivate_plugins( plugin_basename( PIF_FILE ) );
		}

		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore
			unset( $_GET['activate'] ); // phpcs:ignore
		}
	}

	/**
	 * Checks if WooCommerce is installed and active.
	 *
	 * @since 1.0
	 */
	public static function is_woocommerce_active() {

		// WooCommerce is required.
		$woocommerce_path = 'woocommerce/woocommerce.php';
		$active_plugins   = (array) get_option( 'active_plugins', array() );
		$active           = false;

		if ( is_multisite() ) {
			$plugins = get_site_option( 'active_sitewide_plugins' );
			$active  = isset( $plugins[ $woocommerce_path ] );
		}

		return in_array( $woocommerce_path, $active_plugins, true ) || array_key_exists( $woocommerce_path, $active_plugins ) || $active;
	}

	/**
	 * Checks whether to inlcude the plugin files.
	 *
	 * @since 1.0
	 */
	public static function maybe_include_files() {
		self::include_file( 'class-pif-files.php' );
		PIF_Files::include_files();
	}

	/**
	 * Return path/URL for asset file.
	 *
	 * @param string $path Path to the asset file.
	 * @param string $plugin The plugin file path to be relative to. Blank string if no plugin is specified.
	 * @param bool   $use_cdn Use CDN path.
	 * @param bool   $do_minification Whether to skip minification rewriting.
	 * @since 1.0
	 */
	public static function get_asset_url( $path, $plugin = '', $use_cdn = false, $do_minification = true ) {
		return '' === $plugin ? plugins_url( $path ) : plugins_url( $path, $plugin );
	}

	/**
	 * Checks if a child of an object exists and returns the data.
	 *
	 * Returns ean empty value if not set.
	 *
	 * @param object|array $parent Parent Variable.
	 * @param string       $child Child Variable.
	 * @param string       $default_value Default Value.
	 *
	 * @since 1.0
	 */
	public static function check( $parent, $child, $default_value = '' ) {

		$value = '';

		if ( is_object( $parent ) ) {
			return isset( $parent->$child ) && '' !== $parent->$child ? $parent->$child : $default_value;
		}

		if ( is_array( $parent ) ) {
			return isset( $parent[ $child ] ) && '' !== $parent[ $child ] ? $parent[ $child ] : $default_value;
		}

		return '' !== $value ? $value : $default_value;
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.1.3
	 * @since   1.0.0
	 * @param   mixed $links Links.
	 * @return  array
	 */
	public function action_links( $links ) {
		$custom_links   = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=product-input-fields-for-woocommerce' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		$custom_links[] = '<a href="https://www.tychesoftwares.com/products/woocommerce-product-input-fields-plugin/?utm_source=pifupgradetopro&utm_medium=unlockall&utm_campaign=ProductInputFieldsLite">' . __( 'Unlock All', 'product-input-fields-for-woocommerce' ) . '</a>';

		return array_merge( $custom_links, $links );
	}

	/**
	 * Include pif update class file.
	 *
	 * @since 2.5.0
	 */
	public function pif_include_updating_data() {
		if ( ! class_exists( 'PIF_Update' ) ) {
			require_once PIF_PLUGIN_DIR_PATH . 'includes/class-pif-update.php';
		}
		PIF_Update::maybe_update_settings();
	}

}