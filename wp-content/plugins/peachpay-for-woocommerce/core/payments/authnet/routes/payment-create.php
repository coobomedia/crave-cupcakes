<?php
/**
 * Creates a Authorize.net payment for the current cart.
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

require_once PEACHPAY_ABSPATH . 'core/payments/authnet/authnet-function.php';

/**
 * Creates a Authorize.net payment for the given cart.
 */
function peachpay_wc_ajax_create_authnet_payment() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	$session_id = '';
	if ( isset( $_POST['session']['id'] ) ) {
		$session_id = sanitize_text_field( wp_unslash( $_POST['session']['id'] ) );
	}

	$data_value = '';
	if ( isset( $_POST['transaction']['authnet'] ) && isset( $_POST['transaction']['authnet']['data_value'] ) ) {
		$data_value = sanitize_text_field( wp_unslash( $_POST['transaction']['authnet']['data_value'] ) );
	}

	$data_descriptor = '';
	if ( isset( $_POST['transaction']['authnet'] ) && isset( $_POST['transaction']['authnet']['data_descriptor'] ) ) {
		$data_descriptor = sanitize_text_field( wp_unslash( $_POST['transaction']['authnet']['data_descriptor'] ) );
	}

	$order_id = 0;
	if ( isset( $_POST['order']['id'] ) ) {
		$order_id = sanitize_text_field( wp_unslash( $_POST['order']['id'] ) );
	}

	$order_data = null;
	if ( isset( $_POST['order']['data'] ) ) {
		$order_data = json_decode( sanitize_text_field( wp_unslash( $_POST['order']['data'] ) ), true );
	}

	//phpcs:enable

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		wp_send_json_error( 'Order not found', 404 );
	}

	$cart_amount = $order->get_total( 'raw' );
	$response    = wp_remote_post(
		peachpay_api_url() . 'api/v1/authnet/payment/create',
		array(
			'body' => array(
				'session'     => array(
					'id'             => $session_id,
					'merchant_id'    => peachpay_plugin_merchant_id(),
					'merchant_url'   => home_url(),
					'merchant_name'  => get_bloginfo( 'name' ),
					'plugin_version' => PEACHPAY_VERSION,
				),
				'transaction' => array(
					'authnet' => array(
						'login_id'        => peachpay_authnet_login_id(),
						'transaction_key' => peachpay_authnet_transaction_key(),
						'data_descriptor' => $data_descriptor,
						'data_value'      => $data_value,
						'environment'     => peachpay_is_test_mode() ? 'test' : 'production',
						'line_items'      => peachpay_authnet_get_line_items( $order_data['details']['line_items'] ),
					),
				),
				'order'       => array(
					'id'       => $order_id,
					'amount'   => $cart_amount,
					'currency' => peachpay_currency_code(),
					'data'     => $order_data,
				),
			),
		)
	);

	$data   = wp_remote_retrieve_body( $response );
	$result = json_decode( $data, true );

	if ( is_wp_error( $data ) ) {
		wp_send_json(
			array(
				'success' => false,
				'message' => $result['message'],
			)
		);
	}

	$transaction_id = $result['data']['transId'];

	if ( $result['success'] ) {
		$test_charge = peachpay_is_test_mode() ? 'Test ' : '';
		$order_note  = 'Authorize.Net Credit Card ' . $test_charge . 'Charge Approved (Transaction ID ' . $transaction_id . ')';
		$order->set_transaction_id( $transaction_id );
		$order->add_order_note( $order_note );
		$order->payment_complete();
	} else {
		$status_code = $result['data']['statusCode'];
		$error_code  = isset( $result['data']['errorCode'] ) ? $result['data']['errorCode'] : '';
		$error_msg   = $result['message'];
		$order_note  = 'Authorize.Net Credit Card Payment Failed (Status Code ' . $status_code . ': Error Code: ' . $error_code . ' - ' . $error_msg . ' Transaction ID ' . $transaction_id . ')';
		$order->set_transaction_id( $transaction_id );
		if ( ! $order->has_status( 'failed' ) ) {
			$order->update_status( 'failed', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}
	}

	wp_send_json( json_decode( $data ) );
	wp_die();
}
