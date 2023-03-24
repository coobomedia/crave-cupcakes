<?php
/**
 * Authorize.net WC gateway.
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * This class allows us to submit orders with the PeachPay PayPal gateway.
 */
class PeachPay_Authnet_Gateway extends PeachPay_Abstract_WC_Gateway {
	/**
	 * Default constructor.
	 */
	public function __construct() {
		$this->id    = 'peachpay_authnet';
		$this->title = 'PeachPay (Authorize.net)';
		// This needs to be here even though it's blank. Some plugins assume
		// gateways have a description and crash if they do not.
		$this->description  = '';
		$this->has_fields   = false;
		$this->method_title = 'PeachPay (Authorize.net)';
		$this->supports     = array(
			'products',
		);
	}
}
