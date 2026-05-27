<?php
/**
 * Product Input Fields for WooCommerce.
 *
 * Admin Product Class.
 *
 * @author      Tyche Softwares
 * @package     PIF/Admin/Product
 * @category    Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin Product Class.
 *
 * @since 1.0
 */
class PIF_Product_Admin extends PIF_Admin {

    public function __construct() {
		parent::__construct();
        add_action( 'add_meta_boxes', array( $this, 'add_local_product_input_fields_meta_box' ) );
	}

    public function add_local_product_input_fields_meta_box() {
        $per_product_enabled = 'yes' === pif_get_option( 'local_enabled', false ) || true === pif_get_option( 'local_enabled', false );

        if ( ! $per_product_enabled ) {
            return;
        }

        add_meta_box(
            'alg-product-input-fields',
            __( 'Product Input Fields', 'product-input-fields-for-woocommerce' ),
            array( $this, 'create_local_product_input_fields_total_number_meta_box' ),
            'product',
            'normal',
            'high'
        );
    }

    public function create_local_product_input_fields_total_number_meta_box() {
        $html            = '';
        $current_post_id = get_the_ID();
        
        echo '<div id="pif-product-settings"></div>';
    }
}

return new PIF_Product_Admin();