<?php
/**
 * PeachPay Authorize.net utility functions
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * Gets the API url for Authorize.net.
 */
function peachpay_authnet_api_endpoint() {
	if ( peachpay_is_test_mode() ) {
		return 'https://apitest.authorize.net/xml/v1/request.api';
	}

	return 'https://api.authorize.net/xml/v1/request.api';
}

/**
 * Gets the correct Authorize.net login id for signup purposes.
 */
function peachpay_authnet_login_id() {
	return peachpay_is_test_mode() ?
	peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_test_login_id' )
	:
	peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_login_id' );
}

/**
 * Gets the Authorize.net transaction key for the signed up store.
 */
function peachpay_authnet_transaction_key() {
	return peachpay_is_test_mode() ?
	peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_test_transaction_key' )
	:
	peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_transaction_key' );
}

/**
 * Gets the Authorize.net signature id (optional) for the signed up store.
 */
function peachpay_authnet_signature_key() {
	return peachpay_is_test_mode() ?
	peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_test_signature_key' )
	:
	peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_signature_key' );
}

/**
 * Determines whether Authorize.net is enabled
 */
function peachpay_authnet_enabled() {
	return peachpay_authnet_connected() && peachpay_get_settings_option( 'peachpay_payment_options', 'authnet_enable', 0 );
}

/**
 * Determines if Authorize.net is connected.
 */
function peachpay_authnet_connected() {
	$login_id        = peachpay_authnet_login_id();
	$transaction_key = peachpay_authnet_transaction_key();
	$endpoint        = peachpay_authnet_api_endpoint();
	$body            = array(
		'getMerchantDetailsRequest' => array(
			'merchantAuthentication' => array(
				'name'           => $login_id,
				'transactionKey' => $transaction_key,
			),
		),
	);
	$args            = array(
		'headers' => array( 'Content-Type' => 'application/json' ),
		'body'    => wp_json_encode( $body ),
	);

	$response = wp_remote_post( $endpoint, $args );
	if ( is_wp_error( $response ) ) {
		update_option( 'peachpay_connected_authnet_account', 0 );
		return false;
	}

	// Parse the response json into an object array
	$data = json_decode( preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', wp_remote_retrieve_body( $response ) ), true );
	if ( ! isset( $data ) || 'Error' === $data['messages']['resultCode'] ) {
		update_option( 'peachpay_connected_authnet_account', 0 );
		return false;
	}

	'Ok' === $data['messages']['resultCode'] ? update_option( 'peachpay_connected_authnet_account', 1 ) : '';
	$client_key = isset( $data['publicClientKey'] ) ? $data['publicClientKey'] : '';
	isset( $client_key ) ? peachpay_set_settings_option( 'peachpay_payment_options', 'peachpay_authnet_client_key', $client_key ) : '';

	return true;
}

/**
 * Gets the Authorize.net's public client key.
 */
function peachpay_authnet_client_key() {
	return peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_client_key' )
	?
	peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_client_key' )
	:
	'';
}
