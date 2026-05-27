<?php
/**
 * PIF Hooks Class
 *
 * Handles the hooks for the PIF plugin.
 *
 * @author  Tyche Softwares
 * @package PIF/Hooks
 */

defined( 'ABSPATH' ) || exit;

class PIF_Hooks {

	public static function init() {
		add_action(
			'before_woocommerce_init',
			function () {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', PIF_FILE, true );
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'orders_cache', PIF_FILE, true );
				}
			},
			999
		);

		if ( PIF_Admin::is_on_pif_page() ) {
			add_action(
				'admin_notices',
				function () {
					if ( get_option( 'pif_lite_migration_complete' ) ) {
						return;
					}

					if ( get_option( 'pif_migration_running' ) ) {
						echo '<div class="notice notice-info is-dismissible"><p>
						Product Input Fields for WooCommerce is updating product data in the background. You can safely continue working.
						</p></div>';
					}
				}
			);
		}

		add_action( 'pif_run_migration_batch', array( 'PIF_Update', 'pif_run_migration_batch_callback' ) );

		if ( 'yes' === pif_get_option( 'enabled', false ) || true === pif_get_option( 'enabled', false ) ) {
			$position = pif_get_option( 'frontend_position', 'woocommerce_before_add_to_cart_button' );
			$priority = pif_get_option( 'frontend_position_priority', 10 );

			if ( 'disable' !== $position ) {
				if ( 'woocommerce_before_add_to_cart_button' === $position ) {					
					add_action( 'woocommerce_before_add_to_cart_form', array( 'PIF_Product', 'pif_woocommerce_before_add_to_cart_form' ) );
				} else {
					add_action( $position, array( 'PIF_Product', 'add_before_product_input_fields_to_frontend' ), $priority );
					add_action( $position, array( 'PIF_Product', 'add_product_input_fields_to_frontend' ), $priority );
					add_action( $position, array( 'PIF_Product', 'add_after_product_input_fields_to_frontend' ), $priority );
				}
			}

			add_shortcode( 'alg_display_product_input_fields',  array( 'PIF_Product', 'alg_display_product_input_fields' ), $priority );

			add_action( 'wp_enqueue_scripts', array( 'PIF_Product', 'enqueue_scripts' ) );
			add_filter( 'alg_wc_pif_field_html', array( 'PIF_Product', 'handle_uppercase_input_field' ), 10, 3 );

			// Cart hooks.
			add_filter( 'woocommerce_add_to_cart_validation', array( 'PIF_Cart', 'validate_product_input_fields_on_add_to_cart' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_add_cart_item_data', array( 'PIF_Cart', 'add_product_input_fields_to_cart_item_data' ), 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( 'PIF_Cart', 'get_cart_item_product_input_fields_from_session' ), PHP_INT_MAX, 2 );
			// Show details at cart.
			add_filter( 'woocommerce_get_item_data', array( 'PIF_Cart', 'add_product_input_fields_to_cart_item_name' ), PHP_INT_MAX, 2 );
			add_action( 'woocommerce_before_calculate_totals', array( 'PIF_Cart', 'recalculate_product_price' ), 10, 1 );

			// Order hooks.
			// Add item meta from cart to order.
			add_action( 'woocommerce_checkout_create_order_line_item', array( 'PIF_Order', 'save_values_in_item' ), 10, 3 );
			add_action( 'woocommerce_new_order_item', array( 'PIF_Order', 'add_product_input_fields_to_order_item_meta' ), 10, 2 );

			// Show details at order details, emails.
			add_action( 'woocommerce_order_item_meta_start', array( 'PIF_Order', 'output_custom_input_fields_in_admin_order' ), 10, 2 );
			add_action( 'woocommerce_before_order_itemmeta', array( 'PIF_Order', 'output_custom_input_fields_in_admin_order' ), 10, 2 );

			// Output product input fields in invoice plugin.
			add_action( 'wpo_wcpdf_after_item_meta', array( 'PIF_Order', 'output_custom_input_fields_in_invoice_plugin' ), 10, 2 );

			// Add to emails.
			add_filter( 'woocommerce_email_attachments', array( 'PIF_Order', 'add_files_to_email_attachments' ), PHP_INT_MAX, 3 );

			add_action( 'woocommerce_delete_order_items', array( 'PIF_Order', 'delete_order_file_uploads' ) );
			add_action( 'woocommerce_before_delete_order_item', array( 'PIF_Order', 'delete_item_file_uploads' ) );

			add_action( 'admin_init', array( 'PIF_Order', 'handle_downloads' ) );

			// Setups Advanced Order Export For WooCommerce plugin.
			add_filter( 'woe_get_order_product_value_apif', array( 'PIF_Order', 'setup_adv_order_export_plugin_column' ), PHP_INT_MAX, 5 );
			add_filter( 'woe_get_order_product_fields', array( 'PIF_Order', 'add_input_fields_columns_to_adv_order_export_plugin' ), PHP_INT_MAX );
			add_filter( 'woocommerce_order_again_cart_item_data', array( 'PIF_Order', 'pif_order_again_cart_item_data' ), PHP_INT_MAX, 2 );
			add_filter( 'option_use_smilies', '__return_false' );
			add_filter( 'wc_pip_order_item_meta_data_list', array( 'PIF_Order', 'add_input_fields_in_product_meta' ), 1, 4 );

			add_action( 'wp_head', array( __CLASS__, 'hover_textarea_value' ) );
			add_action( 'wp_head', array( __CLASS__, 'textarea_auto_height' ) );

			add_filter( 'astra_get_option_single-product-add-to-cart-action',
					function ( $value, $option, $default ) {
						return $default;
					},
					10,
					3
			);

		}
	}

	/**
	 * Add option to hover textarea value on frontend showing its full value
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 */
	public static function hover_textarea_value() {
		if (
		is_admin() ||
		( ! is_cart() && ! is_checkout() && ! is_wc_endpoint_url( 'view-order' ) )
		) {
			return;
		}
		?>
	<style>
		.alg-pif-dt.textarea{
			cursor: pointer;
		}
		.alg-pif-dt.textarea:after{
			content:'\25bc';
			display:inline-block;
			margin:0 0 0 3px;
			font-size:10px;
		}
		.alg-pif-dt.textarea:hover + .alg-pif-dd, .alg-pif-dd.textarea:hover{
			max-height: 400px;
		}
		.alg-pif-dd.textarea{
			white-space: pre-wrap;
			overflow:hidden;
			max-height:22px;
			transition: max-height 0.4s ease-in-out;
		}
	</style>
		<?php
	}

	/**
	 * Makes the textarea auto increase its height as users type
	 *
	 * @version 1.1.4
	 * @since   1.1.4
	 */
	public static function textarea_auto_height() {
		if (
		is_admin() ||
		( ! is_product() )
		) {
			return;
		}
		?>
	<script>
		var pif_ta_autoheigh = {
			loaded: false,
			textarea_selector: '',
			init: function (textarea_selector) {
				if (this.loaded === false) {
					this.loaded = true;
					this.textarea_selector = textarea_selector;
					var textareas = document.querySelectorAll(this.textarea_selector);
					[].forEach.call(textareas, function (el) {
						el.addEventListener('input', function () {
							pif_ta_autoheigh.auto_grow(this);
						});
					});
				}
			},
			auto_grow: function (element) {
				element.style.height = 'auto';
				element.style.height = (element.scrollHeight) + "px";
			}
		};
		document.addEventListener("DOMContentLoaded", function () {
			pif_ta_autoheigh.init('.alg-product-input-fields-table textarea');
		});
	</script>
	<style>
		.alg-product-input-fields-table textarea {
			overflow: hidden;
		}
	</style>
		<?php
	}
}