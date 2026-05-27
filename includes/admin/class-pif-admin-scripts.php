<?php
/**
 * PIF Admin Scripts Class
 * 
 * Handles the enqueuing of admin scripts and styles for the PIF plugin, as well as displaying relevant notices.
 * 
 * @package PIF/Admin/Scripts
 * @since 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin Scripts.
 *
 * @since 1.0
 */
class PIF_Admin_Scripts extends PIF_Admin {

	/**
	 * Construct
	 *
	 * @since 1.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_css' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_js' ) );
	}

	/**
	 * CSS.
	 *
	 * @since 1.0
	 */
	public static function enqueue_css() {

		if ( self::is_on_pif_page() ) {

			wp_enqueue_style(
				'product-input-fields-for-woocommerce-wp-styles',
				plugins_url( 'build/style-admin.css', PIF_FILE ),
				array(),
				PIF_VERSION
			);

			wp_enqueue_style(
				'product-input-fields-for-woocommerce-admin',
				plugins_url( 'build/admin.css', PIF_FILE ),
				array(),
				PIF_VERSION
			);
		}

		if ( self::is_on_product_page() ) {
			wp_enqueue_style(
				'product-input-fields-for-woocommerce-product',
				plugins_url( 'build/product.css', PIF_FILE ),
				array(),
				PIF_VERSION
			);
		}
	}

	/**
	 * JS.
	 *
	 * @since 1.0
	 */
	public static function enqueue_js() {
		$asset_file = array(
			'dependencies' => array( 'wp-api-fetch', 'wp-i18n', 'wp-date', 'wp-element', 'wp-components' ),
			'version'      => PIF_VERSION,
		);

		if ( self::is_on_pif_page() ) {

			// Load WordPress media uploader
			wp_enqueue_media();
			
			// Load app.js.
			wp_register_script(
				'product-input-fields-for-woocommerce',
				plugins_url( 'build/admin.js', PIF_FILE ),
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			wp_enqueue_script( 'product-input-fields-for-woocommerce' );

			wp_enqueue_script(
				'jquery-ui-timepicker',
				PIF_PLUGIN_URL . '/assets/js/jquery.timepicker.min.js',
				array( 'jquery' ),
				PIF_VERSION,
				true
			);

			wp_enqueue_style( 'jquery-ui-timepicker', '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css', array(), '1.3.5' );
		}

		wp_register_script(
            'tyche',
            PIF_PLUGIN_URL . '/assets/js/tyche.js',
            array( 'jquery' ),
            PIF_VERSION,
            true
        );

		if ( self::is_on_product_page() ) {
			$current_post_id = get_the_ID();
			wp_register_script(
				'product-input-fields-for-woocommerce-product',
				plugins_url( 'build/product.js', PIF_FILE ),
				$asset_file['dependencies'],
				PIF_VERSION,
				true
			);
       		wp_enqueue_script( 'product-input-fields-for-woocommerce-product' );
			wp_localize_script( 
				'product-input-fields-for-woocommerce-product',
				'pif_product_vars',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'pif_product_nonce' ),
					'post_id'  => $current_post_id,
				)
			);
		}
	}
}