<?php
/**
 * Class PIF_Store
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Define a custom REST controller class for managing rules.
 */
class PIF_Store extends PIF_Admin_API {

	const SETTINGS_KEY = 'pif_config_settings';

	public static $base = '/store';
	 /**
	 * Construct
	 *
	 * @since 1.2
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_endpoints' ) );
	}

	/**
	 * Register routes to fetch products/categories.
	 */
	public static function register_endpoints() {
		register_rest_route(
			self::$base_endpoint,
			self::$base . '/products',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'get_products' ),
					'args'                => self::get_default_args(),
					'permission_callback' => array( __CLASS__, 'get_permission' ), // nosemgrep: audit.php.wp.security.rest-route.permission-callback.return-true
				),
			)
		);

		register_rest_route(
			self::$base_endpoint,
			self::$base . '/products/categories',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'get_categories' ),
					'args'                => self::get_default_args(),
					'permission_callback' => array( __CLASS__, 'get_permission' ), // nosemgrep: audit.php.wp.security.rest-route.permission-callback.return-true
				),
			)
		);

		register_rest_route(
			self::$base_endpoint,
			self::$base . '/products/tags',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'get_tags' ),
					'args'                => self::get_default_args(),
					'permission_callback' => array( __CLASS__, 'get_permission' ), // nosemgrep: audit.php.wp.security.rest-route.permission-callback.return-true
				),
			)
		);

		register_rest_route(
			self::$base_endpoint,
			self::$base . '/products/variations',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'get_attributes' ),
					'args'                => self::get_default_args(),
					'permission_callback' => array( __CLASS__, 'get_permission' ), // nosemgrep: audit.php.wp.security.rest-route.permission-callback.return-true
				),
			)
		);

		self::register_settings();
	}

	/**
	 * Permission callback for API endpoints
	 */
	public static function get_permission( $request ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
			return true;
		}

		return false;
	}

	public static function register_settings() {

		if ( ! class_exists( 'Tyche_PIF_Pro_Tracking' ) ) {
			return;
		}

		$active_license = get_option( 'alg_wc_pif_edd_license_key_pif', '' );
		$license_status = get_option( 'edd_license_key_pif_status', 'deactive' );
		
		$default_args = array(
			'type'         => 'string',
			'default'      => '',
			'show_in_rest' => ['schema' => ['type' => 'string']]
		);

		$config_setting_args = array(
			'type'    => 'object',
			'default' => array(
				'license_status'              => 'deactive',
				'license_key'                 => $active_license,
				'license_activation_complete' => false,
			),
			'show_in_rest' => array(
				'schema' => array(
					'type' => 'object',
					'properties' => array(
						'license_status' => ['type' => 'string'],
						'license_key' => ['type' => 'string'],
						'license_activation_complete' => ['type' => 'boolean'],
					),
				),
			),
			'description' => 'Product Input Fields Settings',
		);

		Tyche_PIF_Pro_Tracking::register_settings( $default_args );

		register_setting('options', self::SETTINGS_KEY, $config_setting_args);
	}

	/**
	 * Default rest endpoint args
	 */
	private static function get_default_args() {
		return array(
			'search' => array(
				'required'          => false,
				'validate_callback' => function ( $param, $request, $key ) {
					return is_string( $param );
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
			'limit'  => array(
				'required'          => false,
				'validate_callback' => function ( $param, $request, $key ) {
					return is_numeric( $param );
				},
				'sanitize_callback' => 'absint',
				'default'           => 20, // Default limit
			),
		);
	}

	/**
	 * Retrieves a list of all products including product variations.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error The response object or WP_Error on failure.
	 */
	public static function get_products( $request ) {
		$search_query = $request->get_param( 'search' ); // Get the search parameter from the request
		$limit        = $request->get_param( 'limit' ); // Get the limit parameter from the request

		// Load the product data store.
		$data_store = \WC_Data_Store::load( 'product' );

		// Search for products using the data store. Adjust the method call as per your actual method's parameters.
		$ids = $data_store->search_products( $search_query, '', true, false, $limit );

		// Map each product ID to its details, filtering out any invalid IDs.
		$products = array_values(
			array_map(
				function ( $post_id ) {
					$product = wc_get_product( $post_id ); // Get the product object using WooCommerce function.
					return $product ? array(
						'id'    => $post_id,
						'title' => $product->get_name(),
					) : null;
				},
				array_filter( $ids )
			)
		);

		// Check if products were found.
		if ( empty( $products ) ) {
			return new \WP_Error( 'no_products_found', 'No products found matching your criteria.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( $products );
	}

	/**
	 * Retrieves a list of all product categories with the option to search by name and limit the number of results.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error The response object or WP_Error on failure.
	 */
	public static function get_brands( $request ) {
		$searchQuery = $request->get_param( 'search' ); // Get the search parameter from the request.
		$limit       = $request->get_param( 'limit' ); // Get the limit parameter from the request.

		// Fetch the terms with the specified conditions.
		$args  = array(
			'taxonomy'   => 'product_brand',
			'name__like' => $searchQuery,
			'hide_empty' => false,
			'number'     => $limit,
		);
		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			return $terms; // Return error if the terms retrieval failed.
		}

		// Map each term to its formatted response
		$brands = array_map(
			function ( $term ) {
				return array(
					'id'    => $term->term_id,
					'title' => $term->name,
				);
			},
			$terms
		);

		// Check if categories were found.
		if ( empty( $brands ) ) {
			return new \WP_Error( 'no_brands_found', 'No brands found matching your criteria.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( $brands );
	}

	/**
	 * Retrieves a list of all product categories with the option to search by name and limit the number of results.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error The response object or WP_Error on failure.
	 */
	public static function get_categories( $request ) {
		$searchQuery = $request->get_param( 'search' ); // Get the search parameter from the request.
		$limit       = $request->get_param( 'limit' ); // Get the limit parameter from the request.

		// Fetch the terms with the specified conditions.
		$args  = array(
			'taxonomy'   => 'product_cat',
			'name__like' => $searchQuery,
			'hide_empty' => false,
			'number'     => $limit,
		);
		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			return $terms; // Return error if the terms retrieval failed.
		}

		// Map each term to its formatted response
		$categories = array_map(
			function ( $term ) {
				$parentName = self::get_category_path( $term->parent );

				return array(
					'id'    => $term->term_id,
					'title' => $parentName . $term->name,
				);
			},
			$terms
		);

		// Check if categories were found.
		if ( empty( $categories ) ) {
			return new \WP_Error( 'no_categories_found', 'No categories found matching your criteria.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( $categories );
	}

	/**
	 * Retrieves a list of all products tags.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error The response object or WP_Error on failure.
	 */
	public static function get_tags( $request ) {
		$search_query = $request->get_param( 'search' ); // Get the search parameter from the request.
		$limit        = $request->get_param( 'limit' ); // Get the limit parameter from the request.

		$args = array(
			'taxonomy'   => 'product_tag',
			'name__like' => $search_query,
			'hide_empty' => false,
			'number'     => $limit,
		);

		$terms = get_terms( $args );

		$tags = array_map(
			function ( $term ) {
				return array(
					'id'    => $term->term_id,
					'title' => $term->name,
				);
			},
			$terms
		);

		// Check if categories were found.
		if ( empty( $tags ) ) {
			return new \WP_Error( 'no_tags_found', 'No tags found matching your criteria.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( $tags );
	}

	/**
	 * Retrieves a list of all products attributes.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error The response object or WP_Error on failure.
	 */
	public static function get_attributes( $request ) {
		global $wc_product_attributes;

		$search_query = $request->get_param( 'search' ); // Get the search parameter from the request.
		$limit        = $request->get_param( 'limit' ); // Get the limit parameter from the request.

		$args = array(
			'status'  => 'publish',
		    'limit'   => $limit,
		    'return'  => 'objects',
		    'type'    => 'variation',
		    'orderby' => 'title',
			's'  => $search_query,
		);
		

		$products = wc_get_products( $args );	
		$variations = array();

		foreach ( $products as $product ) {
			$variations[] = array(
				'id'    => $product->get_id(),
				'title' => $product->get_name(),
			);
		}
		
		// Check if categories were found.
		if ( empty( $variations ) ) {
			return new \WP_Error( 'no_attributes_found', 'No variations found matching your criteria.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( $variations );
	}

	/**
	 * Recursively fetches the full path for a category by traversing its parents.
	 *
	 * @param int $termId The term ID of the category.
	 * @return string The full category path.
	 */
	protected static function get_category_path( $termId ) {
		if ( empty( $termId ) ) {
			return '';
		}

		$category = get_term( $termId );

		if ( ! is_object( $category ) || is_wp_error( $category ) ) {
			return ''; // Return empty string if the term is invalid or retrieval failed.
		}

		// Recursively prepend parent category names.
		return self::get_category_path( $category->parent ) . get_the_category_by_ID( $termId ) . ' -> ';
	}
}

return new PIF_Store();