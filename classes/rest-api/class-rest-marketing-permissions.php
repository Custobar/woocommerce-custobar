<?php
namespace WooCommerceCustobar\RestAPI;

use WP_REST_Server;

class REST_Marketing_Permissions extends \WP_REST_Controller {
	/**
	 * Hook into WordPress ready to init the REST API as needed.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 10 );
	}

	/**
	 * Register route for marketing permissions
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'woocommerce-custobar/v' . $version;
		$base      = 'marketing-permissions';
		register_rest_route(
			$namespace,
			'/' . $base,
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_marketing_permissions' ),
					'permission_callback' => array( $this, 'update_marketing_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Update marketing permissions
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_marketing_permissions( $request ) {
		$request_body = json_decode( $request->get_body(), true );

		if ( isset( $request_body['items'] ) && count( $request_body['items'] ) ) {
			foreach ( $request_body['items'] as $customer ) {
				// Check that customer exists
				if ( isset( $customer['external_id'] ) && (bool) get_user_by( 'id', $customer['external_id'] ) ) {
					// Update sms permissions if needed
					if ( isset( $customer['can_sms'] ) ) {
						update_user_meta( $customer['external_id'], '_woocommerce_custobar_can_sms', (bool) $customer['can_sms'] );
					}
					// Update email permissions if needed
					if ( isset( $customer['can_email'] ) ) {
						update_user_meta( $customer['external_id'], '_woocommerce_custobar_can_email', (bool) $customer['can_email'] );
					}
				}
			}
		}
		return new \WP_REST_Response( '', 200 );
	}

	/**
	 * Check that Authorization header matches secret key saved as option
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return bool
	 */
	public function update_marketing_permissions_check( $request ) {
		$secret_key = $request->get_header( 'Authorization' );
		if ( $secret_key && get_option( 'custobar_wc_rest_api_secret' ) === $secret_key ) {
			return true;
		} else {
			return false;
		}
	}

}

