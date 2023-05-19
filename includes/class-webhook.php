<?php
/**
 * Newspack Network Node webhook handler.
 *
 * @package Newspack
 */

namespace Newspack_Network_Node;

/**
 * Class to handle the plugin admin pages
 */
class Webhook {

	/**
	 * The endpoint ID.
	 *
	 * @var string
	 */
	const ENDPOINT_ID = 'newspack-network-node';

	/**
	 * The endpoint URL suffix.
	 *
	 * @var string
	 */
	const ENDPOINT_SUFFIX = 'wp-json/newspack-hub/v1/webhook';

	/**
	 * Runs the initialization.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', [ __CLASS__, 'register_endpoint' ] );
		add_filter( 'newspack_webhooks_request_body', [ __CLASS__, 'filter_webhook_body' ], 10, 2 );
	}

	/**
	 * Registers the endpoint.
	 *
	 * @return void
	 */
	public static function register_endpoint() {
		if ( ! class_exists( 'Newspack\Data_Events\Webhooks' ) || ! method_exists( 'Newspack\Data_Events\Webhooks', 'register_system_endpoint' ) ) {
			return;
		}
		$events = [
			'reader_registered',
			'newspack_node_order_changed',
			'newspack_node_subscription_changed',
		];
		\Newspack\Data_Events\Webhooks::register_system_endpoint( self::ENDPOINT_ID, self::get_url(), $events );
	}

	/**
	 * Gets the endpoint URL
	 *
	 * @return string
	 */
	public static function get_url() {
		return trailingslashit( Settings::get_hub_url() ) . self::ENDPOINT_SUFFIX;
	}

	/**
	 * Filters the event body and signs the data
	 *
	 * @param array  $body The Webhook Event body.
	 * @param string $endpoint_id The endpoint ID.
	 * @return array
	 */
	public static function filter_webhook_body( $body, $endpoint_id ) {
		if ( self::ENDPOINT_ID !== $endpoint_id ) {
			return $body;
		}

		$data = wp_json_encode( $body['data'] );
		$data = self::sign( $data );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$body['data'] = $data;
		$body['site'] = get_bloginfo( 'url' );

		return $body;

	}

	/**
	 * Signs the data
	 *
	 * @param string $data The data to be signed.
	 * @param string $private_key The private key to use for signing. Default is to use the stored private key.
	 * @return WP_Error|string The signed data or error.
	 */
	public static function sign( $data, $private_key = null ) {
		if ( ! $private_key ) {
			$private_key = Settings::get_private_key();
		}
		if ( empty( $private_key ) ) {
			return new \WP_Error( 'newspack-network-node-webhook-signing-error', __( 'Missing Private key', 'newspack-network-node' ) );
		}

		try {
			$signed = sodium_crypto_sign( $data, sodium_base642bin( $private_key, SODIUM_BASE64_VARIANT_ORIGINAL ) );
			return sodium_bin2base64( $signed, SODIUM_BASE64_VARIANT_ORIGINAL );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'newspack-network-node-webhook-signing-error', $e->getMessage() );
		}
	}

}
