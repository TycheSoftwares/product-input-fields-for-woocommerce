<?php
/**
 * PIF Admin API Settings Class
 * Handles admin settings-related API functionalities for the PIF plugin.
 * 
 * @package PIF/Admin/API/Settings
 */


defined( 'ABSPATH' ) || exit;

class PIF_Admin_API_Settings extends PIF_Admin_API {

    /**
	 * Construct
	 *
	 * @since 1.2
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_endpoints' ) );
	}

    /**
	 * Function for registering the API endpoints.
	 *
	 * @since 1.2
	 */
	public static function register_endpoints() {

		// Fetch Settings.
		register_rest_route(
			self::$base_endpoint,
			'general_settings',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'fetch_data' ),
					'permission_callback' => array( __CLASS__, 'get_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'save_data' ),
					'permission_callback' => array( __CLASS__, 'get_permission' ),
				),
			)
		);

		register_rest_route(
			self::$base_endpoint,
			'fields',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'fetch_fields_data' ),
					'permission_callback' => array( __CLASS__, 'get_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( __CLASS__, 'create_field' ),
					'permission_callback' => array( __CLASS__, 'get_permission' ),
				),
			)
		);

		register_rest_route(
			self::$base_endpoint,
			'fields/(?P<id>\d+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'get_field' ),
					'permission_callback' => array( __CLASS__, 'get_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'update_field' ),
					'permission_callback' => array( __CLASS__, 'get_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'delete_field' ),
					'permission_callback' => array( __CLASS__, 'get_permission' ),
				),
			)
		);
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

    /**
	 * Returns General Settings Data.
	 *
	 * @param bool $return_raw Whether to return the Raw response.
	 *
	 * @since 1.2
	 */
	public static function fetch_data( $request ) {
		$settings = get_option( 'pif_general_settings', array() );

		return self::return_response( $settings );
	}

	/**
	 * Saves the settings data.
	 */
	public static function save_data( $request ) {
		$data = json_decode( $request->get_body(), true );

		if ( ! is_array( $data ) ) {
			return self::return_response( array( 'error' => 'Invalid data format' ) );
		}

		update_option( 'pif_general_settings', $data );

		$response = array(
			'message' => 'The rule was successfully updated.',
		);

		return self::return_response( $response );
	}

	public static function fetch_fields_data( $request ) {
		$fields = get_option( 'pif_field_settings', array() );
		return self::return_response( $fields );
	}

	/**
	 * Fetches a specific field by ID.
	 *
	 * @param WP_REST_Request $request The REST request object containing the field ID.
	 *
	 */
	public static function get_field( $request ) {
		$field_id = $request['id'];
		$fields   = get_option( 'pif_field_settings', array() );
		if ( ! is_array( $fields ) || empty( $fields ) ) {
			return self::return_response( array( 'error' => 'No fields found' ) );
		}

		foreach ( $fields as $id => $field ) {
			if ( intval( $field_id ) === $field['id'] ) {
				return self::return_response( $field );
			}
		}
		return self::return_response( array( 'error' => 'Field not found' ) );
	}

	/**
	 * Create a new field for a specific product.
	 * Lite version: only one field per product is allowed.
	 */
	public static function create_field( $request ) {
		$product_id = $request['id'];
		$data       = json_decode( $request->get_body(), true );

		if ( ! is_array( $data ) ) {
			return self::return_response( array( 'error' => 'Invalid data format' ) );
		}

		$fields = get_option( 'pif_field_settings', true );

		if ( ! is_array( $fields ) ) {
			$fields = array();
		}

		// Lite version: only one field allowed per product.
		if ( count( $fields ) >= 1 ) {
			return new \WP_Error( 'field_limit_reached', 'Only one field is allowed. Upgrade to Pro to add more fields.', array( 'status' => 403 ) );
		}

		$data['id']    = 1;
		$data['order'] = 1;

		array_push( $fields, $data );

		update_option( 'pif_field_settings', $fields );

		return self::return_response( array( 'message' => 'Field created successfully', 'id' => 1 ) );
	}

	/**
	 * Updates a specific field by ID.
	 */
	public static function update_field( $request ) {
		$field_id = $request['id'];
		$data     = json_decode( $request->get_body(), true );

		if ( ! is_array( $data ) ) {
			return self::return_response( array( 'error' => 'Invalid data format' ) );
		}

		$fields = get_option( 'pif_field_settings', array() );
		$updated = false;
		foreach ( $fields as $id => $field ) {
			if ( intval( $field_id ) === $field['id'] ) {
				$fields[ $id ] = $data;
				$updated = true;
			}
		}

		if ( $updated ) {
			update_option( 'pif_field_settings', $fields );
			return self::return_response( array( 'message' => 'Field updated successfully' ) );
		} else {
			return self::return_response( array( 'error' => 'Field not found' ) );
		}
	}

	public static function delete_field( $request ) {
		$field_id = $request['id'];
		$fields   = get_option( 'pif_field_settings', array() );
		$deleted  = false;
		foreach ( $fields as $id => $field ) {

			if ( intval( $field_id ) === $field['id'] ) {
				unset( $fields[ $id ] );
				$deleted = true;
				break;
			}
		}

		if ( $deleted ) {
			$fields = array_values($fields);
			update_option( 'pif_field_settings', $fields );
			return self::return_response( array( 'message' => 'Field deleted successfully' ) );
		}

		return self::return_response( array( 'error' => 'Field not found' ) );
	}
}

return new PIF_Admin_API_Settings();