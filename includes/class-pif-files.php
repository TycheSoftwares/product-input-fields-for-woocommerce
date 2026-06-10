<?php
/**
 * Product Input Fields for WooCommerce - Admin Files Class
 *
 * Class for including files for the Admin.
 *
 * @author      Tyche Softwares
 * @package     PIF/Admin/Files
 * @category    Classes
 * @since       1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * FAW Admin Files.
 *
 * @since 1.0
 */
class PIF_Files {

	/**
	 * Include files.
	 *
	 * @since 1.0
	 */
	public static function include_files() {

        PIF_Lite()::include_file( 'api/class-pif-admin-api.php' );
        PIF_Lite()::include_file( 'api/class-pif-admin-api-settings.php' );
        PIF_Lite()::include_file( 'api/class-pif-product-api.php' );
        PIF_Lite()::include_file( 'api/class-pif-store.php' );

        $tyche_files = array(
            'class-tyche-pif-tracking.php',
            'class-tyche-pif-deactivation.php',
        );

        foreach ( $tyche_files as $tyche_file ) {
            if ( file_exists( PIF_PLUGIN_DIR_PATH . '/includes/' . $tyche_file ) ) {
                PIF_Lite()::include_file( $tyche_file );
            }
        }
		// Functions.
		PIF_Lite()::include_file( 'pif-functions.php' );

		PIF_Lite()::include_file( 'admin/class-pif-admin.php' );
		PIF_Lite()::include_file( 'admin/class-pif-product-admin.php' );

		// // Scripts.
		PIF_Lite()::include_file( 'admin/class-pif-admin-scripts.php' );
		new PIF_Admin_Scripts();

		PIF_Lite()::include_file( 'class-pif-update.php' );
		
		// Frontend
		PIF_Lite()::include_file( 'class-pif-product.php' );
		PIF_Lite()::include_file( 'class-pif-cart.php' );
		PIF_Lite()::include_file( 'class-pif-order.php' );
		PIF_Lite()::include_file( 'class-pif-express-checkout.php' );
		new PIF_Express_Checkout();
	}

	/**
	 * Loads Dependency Files.
	 * If there are required files needed ( to be included before ) for the execution of the view file, those dependencies can be added here.
	 *
	 * @param string $section Section Directory.
	 * @param string $filename File in the section Directory to be loaded.
	 * @since 5.19.0
	 */
	public static function load_dependencies( $section, $filename ) {

		if ( '' === $section || '' === $filename ) {
			return;
		}
	}
}