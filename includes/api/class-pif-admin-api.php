<?php
/**
 * PIF Admin API Class
 * Handles admin-related API functionalities for the PIF plugin.
 * 
 * @package PIF/Admin/API
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class PIF_Admin_API extends \WP_REST_Controller {

    /**
	 * REST Base Endpoint.
	 *
	 * @var string
	 */
	public static $base_endpoint = 'pif/v1';

    /**
	 * Returns the REST API Endpoint.
	 *
	 * @since 1.0
	 */
	public static function endpoint() {
		return self::$base_endpoint;
	}

    /**
	 * Returns the REST API response.
	 *
	 * @param string $type Response Type.
	 * @param string $response Response.
	 *
	 * @since 1.0
	 */
	public static function response( $type, $response ) {
		$response['type'] = $type;
		return self::return_response( $response );
	}

    /**
	 * Returns the REST API response.
	 *
	 * @param string|array $response Response Data.
	 * @param bool         $return_raw Returns the response without passing it through the rest_ensure_response function.
	 *
	 * @since 1.0
	 */
	public static function return_response( $response, $return_raw = false ) {
		return $return_raw ? $response : rest_ensure_response( $response );
	}

	/**
	 * Returns a success message.
	 *
	 * @since 1.0
	 */
	public static function success() {
		return self::return_response( 'success' );
	}

	/**
	 * Returns an error message.
	 *
	 * @since 1.0
	 */
	public static function error() {
		return self::return_response( 'error' );
	}

	/**
	 * Verify nonce.
	 *
	 * @param WP_REST_Request $request Request.
	 * @param bool            $stop_execution TRUE - stops execution, FALSE - return status of nonce verification.
	 *
	 * @since 1.0
	 */
	public static function verify_nonce( $request, $stop_execution = true ) {
		if ( ! wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {

			if ( $stop_execution ) {
				die( self::error() ); // phpcs:ignore
			}

			return false;
		}

		return true;
	}

	/**
	 * Returns a value if the target value is empty.
	 *
	 * @param string $value Target Value.
	 * @param string $return_value_if_empty Value to be returned if target value is empty.
	 *
	 * @since 1.0
	 */
	public static function return_value_if_empty( $value, $return_value_if_empty ) {
		return '' === $value ? $return_value_if_empty : $value;
	}

}

return new PIF_Admin_API();
