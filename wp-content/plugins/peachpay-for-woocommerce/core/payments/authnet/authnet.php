<?php
/**
 * PeachPay Authorize.net payment method.
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

require_once PEACHPAY_ABSPATH . 'core/payments/authnet/util.php';

/**
 * Sets up the PeachPay Authorize.net payment methods/gateway.
 *
 * @param array $supported_gateways An array of supported gateways and their configuration.
 */
function peachpay_action_register_authnet_gateway( $supported_gateways ) {

	require_once PEACHPAY_ABSPATH . 'core/payments/authnet/class-peachpay-authnet-gateway.php';
	require_once PEACHPAY_ABSPATH . 'core/payments/authnet/routes/payment-create.php';

	add_action( 'wc_ajax_pp-create-authnet-payment', 'peachpay_wc_ajax_create_authnet_payment' );

	if ( is_admin() ) {
		require_once PEACHPAY_ABSPATH . 'core/payments/authnet/admin/settings.php';
	}

	$supported_gateways[] = array(
		'gateway_id'    => 'peachpay_authnet',
		'gateway_class' => 'PeachPay_Authnet_Gateway',
		'features'      => array(
			'authnet_payment_method' => array(
				'enabled'  => peachpay_authnet_enabled(),
				'version'  => 1,
				'metadata' => array(
					'login_id'   => peachpay_authnet_login_id(),
					'client_key' => peachpay_authnet_client_key(),
				),
			),
		),
	);

	return $supported_gateways;
}
add_filter( 'peachpay_register_supported_gateways', 'peachpay_action_register_authnet_gateway', 10, 1 );
