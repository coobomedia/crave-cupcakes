<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
//do_action('woocommerce_email_header', $email_heading, $email); ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>"/>
        <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
    </head>
    <body>
    <div id="mail_wrapper">


		<?php
		$order_id      = $order->get_id();
		$type          = get_post_meta( $order_id, '_decor_type', true ); // Cupcakes: Happy birthday |
		$image         = get_post_meta( $order_id, '_decor_image', true ); // Cupcakes: https://cravecupcakstg.wpengine.com/wp-content/uploads/2022/10/happybirthday.png |
		$color1        = get_post_meta( $order_id, 'decor_color_code_1', true ); // Cupcakes: #f79e38 |
		$color1Title1  = get_post_meta( $order_id, '_decor_color_1', true );//Cupcakes: Dark Orange | Mini Cupcakes: Dark Pink |
		$color1Title2  = get_post_meta( $order_id, '_decor_color_2', true );//Cupcakes: Red | Mini Cupcakes: Light Orange |
		$color2        = get_post_meta( $order_id, 'decor_color_code_2', true ); // Cupcakes: #d17b30 |
		$packaging     = get_post_meta( $order_id, 'packaging', true ); // Cupcakes: #d17b30 |
		$type1         = explode( '| ', $type );
		$image1        = explode( '| ', $image );
		$color11       = explode( '| ', $color1 );
		$color1Title11 = explode( '| ', $color1Title1 );
		$color21       = explode( '| ', $color2 );
		$color1Title21 = explode( '| ', $color1Title2 );
		$packaging1    = explode( '| ', $packaging );
		$totalCount    = 0;
		$types         = array();
		foreach ( $type1 as $type2 ) {
			$type3              = explode( ": ", $type2 );
			$types[ $type3[0] ] = $type3[1];
		}
		$images = array();
		foreach ( $image1 as $type2 ) {
			$type3               = explode( ": ", $type2 );
			$images[ $type3[0] ] = $type3[1];
		}
		$colors1 = array();
		foreach ( $color11 as $type2 ) {
			$type3                = explode( ": ", $type2 );
			$colors1[ $type3[0] ] = $type3[1];
		}
		$color1Title1 = array();
		foreach ( $color1Title11 as $type2 ) {
			$type3                     = explode( ": ", $type2 );
			$color1Title1[ $type3[0] ] = $type3[1];
		}
		$color1Title2 = array();
		foreach ( $color1Title21 as $type2 ) {
			$type3                     = explode( ": ", $type2 );
			$color1Title2[ $type3[0] ] = $type3[1];
		}
		$colors2 = array();
		foreach ( $color21 as $type2 ) {
			$type3                = explode( ": ", $type2 );
			$colors2[ $type3[0] ] = $type3[1];
		}
		$packaging2 = array();
		foreach ( $packaging1 as $type2 ) {
			$type3                   = explode( ": ", $type2 );
			$packaging2[ $type3[0] ] = $type3[1];
		}

		$location      = get_post_meta( $order_id, '_order_location', true );
		$location      = str_replace( '_', ' ', $location );
		$decor_type    = get_post_meta( $order_id, '_decor_type', true );
		$Packaging     = get_post_meta( $order_id, 'packaging', true );
		$decor_type    = explode( '|', $decor_type );
		$decor_color   = get_post_meta( $order_id, '_decor_color_1', true );
		$decor_color2  = get_post_meta( $order_id, '_decor_color_2', true );
		$decor_color   = explode( '|', $decor_color );
		$decor_color2  = explode( '|', $decor_color2 );
		$order_detail  = array(
			'Order Type:'   => get_post_meta( $order_id, 'delivery_type_', true ),
			'Location:'     => $location,
			'Date:'         => get_post_meta( $order_id, '_delivery_date', true ),
			'Time:'         => get_post_meta( $order_id, '_delivery_time', true ),
			'Order Number:' => $order_id,
			'Name:'         => $order->get_billing_first_name() . " " . $order->get_billing_last_name(),
			'Email:'        => $order->get_billing_email(),
			"Number:"       => $order->get_billing_phone(),
		);
		$order_payment = array(
			'Sub Total:'      => $order->get_subtotal_to_display(),
			'Packaging Fee:'  => wc_price( $order->get_total_fees() ),
			'Payment Method:' => $order->get_payment_method_title(),
			'Total:'          => wc_price( $order->get_total() )
		);
		$all_items     = array();
		foreach ( $order->get_items() as $item_id => $item ) {
			$product                         = $item->get_product();
			$categorieID                     = $product->category_ids[0];
			$categorie_title                 = get_the_category_by_ID( $categorieID );
			$product_type                    = $product->get_type();
			$all_items[ $categorie_title ][] = array(
				'categoryName'     => $categorie_title,
				'Product_ID'       => $item->get_id(),
				'Image_ID'         => $product->get_image_id(),
				"Product_Feature"  => wp_get_attachment_url( $product->get_image_id() ),
				'Product_Name'     => $item->get_name(),
				'Product_Quantity' => $item->get_quantity(),
				"Sub_Total"        => $item->get_subtotal(),
				"All_Total"        => $item->get_total(),
				"Product_Type"     => $product_type,
				"Category"         => $categorieID,
				"decoration_type"  => $types[ $categorie_title ],
				"decoration_image" => $images[ $categorie_title ],
				"color_1"          => $colors1[ $categorie_title ],
				"color_2"          => $colors2[ $categorie_title ],
				"packaging"        => $packaging2[ $categorie_title ],
				'color_title_2'    => $color1Title2[ $categorie_title ],
				'color_title_1'    => $color1Title1[ $categorie_title ],
			);

		}
		$billing  = $order->get_formatted_billing_address();
		$shipping = $order->get_formatted_shipping_address();
		?>
        <section class="thankyou-template" id="email_template_section">
            <div id="thankyou-detail">
                <h2 style="text-align: center;">Thank you. Your order has been received</h2>
                <div class="divider" style="width: 100%;height: 2px;background-color:#55B7B3;"></div>
                <div id="order-information">
                    <h3 style="color: black; font-weight: 700;font-size: 18px;padding: 25px 0 0 0; margin:0;">Order
                        Information</h3>
                    <div class="divider row-divider"
                         style="width: 100%; height: 2px; background-color: #55B7B3; opacity: 0.3; margin-top: 5px;"></div>
                    <div id="order-information-detail" style="margin:5px;">
						<?php foreach ( $order_detail as $key => $value ): ?>
                            <p><span><b><?php echo $key; ?> </b></span> <span><?php echo $value; ?></span></p>
						<?php endforeach; ?>
                    </div>
                </div>
                <div class="divider" style="width: 100%;height: 2px;background-color:#55B7B3;"></div>
				<?php foreach ( $all_items as $key => $item ): ?>
                    <div id="mini-cupcake"
                         class="mini-cupcake <?php echo ( $key == 'Mini Cupcakes' ) ? '' : 'original-cupcake' ?>">
						<?php if ( $key == 'Mini Cupcakes' ): ?>
                            <h3 style="color: black; font-weight: 700;font-size: 18px;padding: 25px 0 0 0; margin:0"><?php echo $key; ?></h3>
						<?php endif; ?>
                        <div id="mini-cup-cakes-detail">
                            <table border="0" cellpadding="0" cellspacing="0" width="600" id="email_template_container">
                                <thead>
                                <tr>
                                    <th style="width: 67%; text-align: left; border: none; padding: 8px 0; color: #55B7B3;">
										<?php if ( $key == 'Mini Cupcakes' ): ?>
                                            For Those cupcakes 1 Qty = 12 (1 Dzn)
										<?php else: ?>
                                            <h3 style="color: black; font-weight: 700;font-size: 18px;padding: 25px 0 0 0; margin:0"><?php echo $key ?></h3>
										<?php endif; ?>

                                    </th>
                                    <th>
                                        Quantity
                                    </th>
                                    <th style="text-align: right; border: none; padding: 8px 0; color: #55B7B3;">
                                        Amount
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="email_template_tbody">
								<?php foreach ( $item as $sub_item ): ?>
									<?php $totalCount += $sub_item['Product_Quantity']; ?>
                                    <tr style="border-bottom: 2px solid #D8EEE7; border-top: 2px solid #D8EEE7">
                                        <td>
                                            <div style="display: block !important;">
                                                <img src="<?php echo $sub_item['Product_Feature'] ?>"
                                                     alt="" style="width:40px;">
                                                <span>  <?php echo $sub_item['Product_Name']; ?></span>
                                            </div>
                                        </td>
                                        <td>
											<?php echo $sub_item['Product_Quantity']; ?>
                                        </td>
                                        <td>
											<?php echo wc_price( $sub_item['All_Total'] ); ?>
                                        </td>
                                    </tr>
								<?php endforeach; ?>
                                </tbody>
                            </table>
                            <div id="packaging-detail">
                                <p><span><b>Packaging:</b></span>
                                    <span><?php echo str_replace( '_', ' ', $item[0]['packaging'] ); ?></span>
                                </p>

								<?php if ( $item[0]['decoration_type'] != ' ' ): ?>
                                    <p><span><b>Decoration:</b></span>
                                        <span><?php echo $item[0]['decoration_type'] ?> - <?php echo $item[0]['color_title_1'] ?> / <?php echo $item[0]['color_title_2'] ?></span>
                                    </p>
								<?php else: ?>
                                    <p><span><b>Decoration:</b></span>
                                        <span>Standard Topper</span>
                                    </p>
								<?php endif; ?>
                            </div>

							<?php if ( $item[0]['decoration_type'] != ' ' ): ?>
								<?php if ( $item[0]['decoration_image'] ): ?>
                                    <div class="packaging-items" id="p_items">
                                        <div class="packaging-item" style="display: inline-block;">
                                            <div class="item"
                                                 style="width: 50px; height:50px;position: relative;">
                                                <div class="topper-image" id="topp"
                                                     style="background-color:<?php echo $item[0]['color_1'] ?>; width: 35px; height: 35px; position: absolute;top: 8px; left: 8px; border-radius: 45px;">
                                                    <img src="<?php echo $item[0]['decoration_image'] ?>"
                                                         alt=""
                                                         style="width: 35px;height: 35px;position: absolute;top: 3px;left: 3px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="packaging-item " style="display: inline-block;">
                                            <div class="item"
                                                 style="width:50px; height:50px;position:relative;">
                                                <div class="topper-image"
                                                     style="background-color:<?php echo $item[0]['color_2']; ?>; width: 35px; height: 35px; position: absolute;top: 8px; left: 8px; border-radius: 45px;">
                                                    <img src="<?php echo $item[0]['decoration_image'] ?>"
                                                         alt=""
                                                         style="width: 35px;height: 35px;position: absolute;top: 3px;left: 3px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php endif; ?>
							<?php endif; ?>

                            <div class="divider" style="width: 100%;height: 2px;background-color:#55B7B3;"></div>
                        </div>
                    </div>
				<?php endforeach; ?>

                <div class="total-amount">
					<?php foreach ( $order_payment as $key => $value ): ?>
						<?php if ( $value != '' ): ?>
                            <p><span><b><?php echo $key ?></b></span> <span><?php echo $value ?></span></p>
						<?php endif; ?>
					<?php endforeach; ?>
                </div>

                <div id="billing-info">
                    <h3 style="color: black; font-weight: 700;font-size: 18px;padding: 25px 0 10px 0; margin:0;">Billing
                        Information</h3>
                    <p>
						<?php echo $billing; ?>
                    </p>
                    <p><?php echo $order->get_billing_email() ?></p>
                    <p><?php echo $order->get_billing_phone() ?></p>
                </div>
                <div id="ship-info">
                    <h3 style="color: black; font-weight: 700;font-size: 18px;padding: 25px 0 0 0; margin:0;">Shipping
                        Information</h3>
                    <p>
						<?php echo $shipping; ?>
                    </p>
                </div>
            </div>
        </section>
    </div>
    <!--End Thank You-->
    </body>
    </html>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
//do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
//do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
//do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
//if ( $additional_content ) {
//	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
//}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
//do_action( 'woocommerce_email_footer', $email );
