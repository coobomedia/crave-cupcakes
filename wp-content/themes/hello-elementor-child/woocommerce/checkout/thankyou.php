<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

?>
<div class="woocommerce-order">
    <?php
    if ($order) :
        do_action('woocommerce_before_thankyou', $order->get_id());
        ?>
        <?php if ($order->has_status('failed')) : ?>
        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>
        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
               class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                   class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
            <?php endif; ?>
        </p>
    <?php else : ?>
        <?php
        global $wp;
        if (isset($wp->query_vars['order-received'])) {
            $order_id = absint($wp->query_vars['order-received']); // The order ID
            $order = wc_get_order($order_id);
            $currency_symbol = get_woocommerce_currency_symbol($order->get_currency());
        }
        $type = get_post_meta($order_id, '_decor_type', true); // Cupcakes: Happy birthday |
        $image = get_post_meta($order_id, '_decor_image', true); // Cupcakes: https://cravecupcakstg.wpengine.com/wp-content/uploads/2022/10/happybirthday.png |
        $color1 = get_post_meta($order_id, 'decor_color_code_1', true); // Cupcakes: #f79e38 |
        $color1Title1 = get_post_meta($order_id, '_decor_color_1', true);//Cupcakes: Dark Orange | Mini Cupcakes: Dark Pink |
        $color1Title2 = get_post_meta($order_id, '_decor_color_2', true);//Cupcakes: Red | Mini Cupcakes: Light Orange |
        $color2 = get_post_meta($order_id, 'decor_color_code_2', true); // Cupcakes: #d17b30 |
        $packaging = get_post_meta($order_id, 'packaging', true); // Cupcakes: #d17b30 |
        $type1 = explode('| ', $type);
        $image1 = explode('| ', $image);
        $color11 = explode('| ', $color1);
        $color1Title11 = explode('| ', $color1Title1);
        $color21 = explode('| ', $color2);
        $color1Title21 = explode('| ', $color1Title2);
        $packaging1 = explode('| ', $packaging);
        $totalCount = 0;
        $types = array();
        foreach ($type1 as $type2) {
            $type3 = explode(": ", $type2);
            $types[$type3[0]] = $type3[1];
        }
        $images = array();
        foreach ($image1 as $type2) {
            $type3 = explode(": ", $type2);
            $images[$type3[0]] = $type3[1];
        }
        $colors1 = array();
        foreach ($color11 as $type2) {
            $type3 = explode(": ", $type2);
            $colors1[$type3[0]] = $type3[1];
        }
        $color1Title1 = array();
        foreach ($color1Title11 as $type2) {
            $type3 = explode(": ", $type2);
            $color1Title1[$type3[0]] = $type3[1];
        }
        $color1Title2 = array();
        foreach ($color1Title21 as $type2) {
            $type3 = explode(": ", $type2);
            $color1Title2[$type3[0]] = $type3[1];
        }
        $colors2 = array();
        foreach ($color21 as $type2) {
            $type3 = explode(": ", $type2);
            $colors2[$type3[0]] = $type3[1];
        }
        $packaging2 = array();
        foreach ($packaging1 as $type2) {
            $type3 = explode(": ", $type2);
            $packaging2[$type3[0]] = $type3[1];
        }

        $location = get_post_meta($order_id, '_order_location', true);
        $location = str_replace('_', ' ', $location);
        $decor_type = get_post_meta($order_id, '_decor_type', true);
        $Packaging = get_post_meta($order_id, 'packaging', true);
        $decor_type = explode('|', $decor_type);
        $decor_color = get_post_meta($order_id, '_decor_color_1', true);
        $decor_color2 = get_post_meta($order_id, '_decor_color_2', true);
        $decor_color = explode('|', $decor_color);
        $decor_color2 = explode('|', $decor_color2);
        $order_detail = array(
            'Order Type:' => get_post_meta($order_id, 'delivery_type_', true),
            'Location:' => $location,
            'Date:' => get_post_meta($order_id, '_delivery_date', true),
            'Time:' => get_post_meta($order_id, '_delivery_time', true),
            'Order Number:' => $order_id,
            'Name:' => $order->get_billing_first_name() . " " . $order->get_billing_last_name(),
            'Email:' => $order->get_billing_email(),
            "Number:" => $order->get_billing_phone(),
        );
        $order_payment = array(
            'Sub Total:' => $order->get_subtotal_to_display(),
            'Packaging Fee:' => wc_price($order->get_total_fees()),
            'Payment Method:' => $order->get_payment_method_title(),
            'Shipping Cost:' => wc_price($order->get_shipping_total()),
            'Total:' => $currency_symbol . $order->get_total()
        );
        $all_items = array();
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            $categorieID = $product->category_ids[0];
            $categorie_title = get_the_category_by_ID($categorieID);
            $product_type = $product->get_type();
            $all_items[$categorie_title][] = array(
                'categoryName' => $categorie_title,
                'Product_ID' => $item->get_id(),
                'Image_ID' => $product->get_image_id(),
                "Product_Feature" => wp_get_attachment_url($product->get_image_id()),
                'Product_Name' => $item->get_name(),
                'Product_Quantity' => $item->get_quantity(),
                "Sub_Total" => $item->get_subtotal(),
                "All_Total" => $item->get_total(),
                "Product_Type" => $product_type,
                "Category" => $categorieID,
                "decoration_type" => $types[$categorie_title],
                "decoration_image" => $images[$categorie_title],
                "color_1" => $colors1[$categorie_title],
                "color_2" => $colors2[$categorie_title],
                "packaging" => $packaging2[$categorie_title],
                'color_title_2' => $color1Title2[$categorie_title],
                'color_title_1' => $color1Title1[$categorie_title],
            );

        }
        $billing = $order->get_formatted_billing_address();
        $shipping = $order->get_formatted_shipping_address();
        ?>
        <section class="thankyou-template">
            <div class="thankyou-detail">
                <h2>Thank you. Your order has been received</h2>
                <div class="divider"></div>
                <div class="order-information">
                    <h3>Order Information</h3>
                    <div class="divider row-divider"></div>
                    <div class="order-information-detail">
                        <?php foreach ($order_detail as $key => $value): ?>
                            <p><span><b><?php echo $key; ?> </b></span> <span><?php echo $value; ?></span></p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="divider"></div>
                <?php foreach ($all_items as $key => $item): ?>
                    <div class="mini-cupcake <?php echo ($key == 'Mini Cupcakes') ? '' : 'original-cupcake' ?>">
                        <?php if ($key == 'Mini Cupcakes'): ?>
                            <h3><?php echo $key; ?></h3>
                        <?php endif; ?>
                        <div class="mini-cup-cakes-detail">
                            <table>
                                <thead>
                                <tr>
                                    <th>
                                        <?php if ($key == 'Mini Cupcakes'): ?>
                                            For Those cupcakes 1 Qty = 12 (1 Dzn)
                                        <?php else: ?>
                                            <h3><?php echo $key ?></h3>
                                        <?php endif; ?>

                                    </th>
                                    <th>
                                        Quantity
                                    </th>
                                    <th>
                                        Amount
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($item as $sub_item): ?>
                                    <?php $totalCount += $sub_item['Product_Quantity']; ?>
                                    <tr>
                                        <td>
                                            <div class="item-detail">
                                                <img src="<?php echo $sub_item['Product_Feature'] ?>"
                                                     alt="">
                                                <span>  <?php echo $sub_item['Product_Name']; ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo $sub_item['Product_Quantity']; ?>
                                        </td>
                                        <td>
                                            <?php echo wc_price($sub_item['All_Total']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="packaging-detail">
                                <p><span><b>Packaging:</b></span>
                                    <span><?php echo str_replace('_', ' ', $item[0]['packaging']); ?></span>
                                </p>

                                <?php if ($item[0]['decoration_type'] != ' '): ?>
                                    <p><span><b>Decoration:</b></span>
                                        <span><?php echo $item[0]['decoration_type'] ?> - <?php echo $item[0]['color_title_1'] ?> / <?php echo $item[0]['color_title_2'] ?></span>
                                    </p>
                                <?php else: ?>
                                    <p><span><b>Decoration:</b></span>
                                        <span>Standard Topper</span>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if ($item[0]['decoration_type'] != ' '): ?>
                                <?php if ($item[0]['decoration_image']): ?>
                                    <div class="packaging-items">
                                        <div class="packaging-item">
                                            <div class="item"
                                                 style="background-color:<?php echo $item[0]['color_1'] ?>;">
                                                <img src="/wp-content/uploads/2022/10/base.png" alt=""
                                                     style="position: absolute;left: 0;right: 0;">
                                                <div class="topper-image"
                                                     style="width: 35px; height: 35px; position: absolute;top: 8px; left: 8px; border-radius: 45px;">
                                                    <img src="<?php echo $item[0]['decoration_image'] ?>"
                                                         alt=""
                                                         style="width: 30px;height: 30px;position: absolute;top: 3px;left: 3px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="packaging-item">
                                            <div class="item"
                                                 style="background-color:<?php echo $item[0]['color_2'] ?>;">
                                                <img src="/wp-content/uploads/2022/10/base.png"
                                                     alt=""
                                                     style="position: absolute;left: 0;right: 0;">
                                                <div class="topper-image"
                                                     style="width: 35px; height: 35px; position: absolute;top: 8px; left: 8px; border-radius: 45px;">
                                                    <img src="<?php echo $item[0]['decoration_image'] ?>"
                                                         alt=""
                                                         style="width: 30px;height: 30px;position: absolute;top: 3px;left: 3px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="divider"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="total-amount">
                    <?php foreach ($order_payment as $key => $value): ?>
                        <?php if ($value != ''): ?>
                            <p><span><b><?php echo $key ?></b></span> <span><?php echo $value ?></span></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="billing-info">
                    <h3>Billing Information</h3>
                    <p>
                        <?php echo $billing; ?>
                    </p>
                    <p><?php echo $order->get_billing_email() ?></p>
                    <p><?php echo $order->get_billing_phone() ?></p>
                </div>
                <div class="ship-info">
                    <h3>Shipping Information</h3>
                    <p>
                        <?php echo $shipping; ?>
                    </p>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php endif; ?>
</div>
