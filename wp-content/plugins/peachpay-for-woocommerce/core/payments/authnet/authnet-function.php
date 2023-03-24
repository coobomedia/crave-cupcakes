<?php
/**
 * PeachPay Authorize.net utility functions
 *
 * @package PeachPay
 */

/**
 * Outputs line items in the expected format for making Authorize.net payment request.
 *
 * @param array $data array of items in the order cart.
 */
function peachpay_authnet_get_line_items( $data ) {
	if ( ! $data || ! isset( $data ) ) {
		return false;
	}

	$line_items = array();

	foreach ( $data as $item ) {
		$product                  = wc_get_product( $item['product_id'] );
		$line_items['lineItem'][] = array(
			'itemId'    => $item['id'],
			'name'      => $item['name'],
			'quantity'  => $item['quantity'],
			'unitPrice' => $product->get_price(),
			'taxable'   => 'taxable' === $product->get_tax_status(),
		);
	}

	// maximum of 30 line items per order
	if ( isset( $line_items['lineItem'] ) && count( $line_items['lineItem'] ) > 30 ) {
		$line_items['lineItem'] = array_slice( $line_items['lineItem'], 0, 30 );
	}

	return $line_items;
}
