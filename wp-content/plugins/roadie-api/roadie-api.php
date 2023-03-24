<?php
/*
Plugin Name: Roadie Api
Plugin URI: #
Description: shipping method plugin
Version: 1.0.0
Author: Ropstam Team
Author URI: #
*/
/**
 * Check if WooCommerce is active
 */
if (!defined('WPINC')) {
    die('security by preventing any direct access to your plugin file');
}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    function roadie_shipping_method()
    {
//        session_destroy();
        if (!class_exists('Roadie_Shipping_Method')) {
            class Roadie_Shipping_Method extends WC_Shipping_Method
            {
                public function __construct()
                {
                    $this->id = 'roadie_';
                    $this->method_title = __('Roadie Shipping', 'ropstam');
                    $this->method_description = __('Roadie Shipping Method', 'ropstam');
                    $this->result = array();
                    $this->shipping = array();
                    $this->r_status = array();
                    $this->init();
                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Roadie Shipping', 'ropstam');
                    $this->token = isset($this->settings['token']) ? $this->settings['token'] : __('xx-xxx-xx', 'ropstam');
                    $this->end_point = isset($this->settings['end_point']) ? $this->settings['end_point'] : __('xx-xx-xxx', 'ropstam');
                }

                /**
                 * Load the settings API
                 */
                function init()
                {
                    $this->init_form_fields();
                    $this->init_settings();
                    add_action('woocommerce_update_options_shipping_' . $this->id, array(
                        $this,
                        'process_admin_options'
                    ));
                    add_action('woocommerce_proceed_to_checkout', array($this, 'maybe_clear_wc_shipping_rates_cache'));
                    add_action('woocommerce_review_order_before_cart_contents', array($this, 'validate_shipping_address'), 10);
                    add_action('woocommerce_after_checkout_validation', array($this, 'validate_shipping_address'), 10);
                    add_action('woocommerce_payment_complete', array($this, 'order_complete'));


                }

                function init_form_fields()
                {
                    $this->form_fields = array(); // No global options for table rates
                    $this->form_fields = array(
                        'enabled' => array(
                            'title' => __('Enable', 'ropstam'),
                            'type' => 'checkbox',
                            'default' => 'yes'
                        ),
                        'title_front' => array(
                            'title' => __('Title', 'ropstam'),
                            'type' => 'text',
                            'default' => __('Roadie Shipping', 'ropstam')
                        ),
                        'token' => array(
                            'title' => __('Bearer Token', 'ropstam'),
                            'type' => 'text',
                            'placeholder' => __('xx-xxx-xx', 'ropstam'),
                        ),
                        'end_point' => array(
                            'title' => __('End Point', 'ropstam'),
                            'type' => 'text',
                            'placeholder' => __('https://connect-sandbox.roadie.com/v1/', 'ropstam'),
                        ),
                    );
                }

                public function maybe_clear_wc_shipping_rates_cache()
                {
                    if ($this->get_option('clear_wc_shipping_cache') == 'yes') {
                        $packages = WC()->cart->get_shipping_packages();
                        foreach ($packages as $key => $value) {
                            $shipping_session = "shipping_for_package_$key";
                            unset(WC()->session->$shipping_session);
                        }
                    }
                }

                public function validate_shipping_address($posted)
                {
                    wc_clear_notices();
                    if ($this->r_status) {
                        $message = '';
                        $result = $this->result;
                        if ($result['errors']) {
                            wc_clear_notices();
                            foreach ($result['errors'] as $error) {
                                $message = $error['message'];
                            }
                            wc_add_notice($message, 'error');
                        }
                    }
                }

                public function order_complete($order_id)
                {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $this->end_point . 'shipments ');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // 2. Set the CURLOPT_POST option to true for POST request
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->shipping);
                    /* set the content type json */
                    $headers = [];
                    $headers[] = 'Content-Type:application/json';
                    $headers[] = "Authorization: Bearer " . $this->token;
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    /* set return type json */
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    /* close cURL resource */
                    curl_close($ch);
                    $result = json_decode($result, true);
                }

                public function calculate_shipping($package = array())
                {
                    if ($_SESSION['date_sec']) {
                        $date = $_SESSION['date_sec'];
                        $time = $_SESSION['sel_time'];
                        $t = explode('-', $time);
                        $start_date = $date . " " . $t['0'];
                        $end_date = $date . " " . $t['1'];
                        $sdate = new DateTime($start_date, new DateTimeZone('UTC'));
                        $start_date = $sdate->format('Y-m-d\TH:i:s\Z');
                        $edata = new DateTime($end_date, new DateTimeZone('UTC'));
                        $end_date = $edata->format('Y-m-d\TH:i:s\Z');
                    }
                    if (is_checkout()) {
                        $rate_estimate = array();
                        $location = $_SESSION['location'];
                        if ($location != '' && $_SESSION['order_type'] == 'delivery') {
                            $catObj = get_term_by('slug', $_SESSION['location'], 'store_location');
                            $street1 = get_field('street_address_1', $catObj->taxonomy . '_' . $catObj->term_id);
                            $city = get_field('city', $catObj->taxonomy . '_' . $catObj->term_id);
                            $zip = get_field('zip', $catObj->taxonomy . '_' . $catObj->term_id);
                            $state = get_field('state', $catObj->taxonomy . '_' . $catObj->term_id);
                            $name = $catObj->name;
                            $rate_estimate['pickup_location'] = array(
                                'address' => array(
                                    'street1' => $street1,
                                    'city' => $city,
                                    'state' => $state,
                                    'zip' => $zip
                                )
                            );
                        }
                        $data = array();
                        $idempotency_key = random_int(100000, 999999);
                        $data['reference_id'] = '123456987';
                        $data['idempotency_key'] = $idempotency_key;
                        $data['pickup_after'] = $start_date;
                        $data['deliver_between'] = array(
                            'start' => $start_date,
                            'end' => $end_date
                        );
                        $data['pickup_location'] = array(
                            'address' => array(
                                'name' => $name,
                                'store_number' => 123456,
                                'street1' => $street1,
                                'street2' => 'street2',
                                'city' => $city,
                                'state' => $state,
                                'zip' => $zip,
                                'latitude' => '',
                                'longitude' => ''
                            ),
                            'contact' => array(
                                'name' => "Test Name",
                                'phone' => '03365455412'
                            ),
                        );

                        $data['options'] = array(
                            'signature_required' => true,
                            'notifications_enabled' => false,
                            "over_21_required" => false,
                            "extra_compensation" => 5.0,
                            "trailer_required" => false,
                            "decline_insurance" => true
                        );

                        $data['delivery_location'] = array(
                            'address' => array(
                                'name' => $package['destination']['country'],
                                'store_number' => 123456,
                                'city' => $package['destination']['city'],
                                'street1' => $package['destination']['address_1'],
                                'street2' => $package['destination']['address_2'],
                                'state' => $package['destination']['state'],
                                'zip' => $package['destination']['postcode'],
                            ),
                            'contact' => array(
                                'name' => "",
                                'phone' => '03365455412'
                            ),
                        );
                        $_items = array();
                        foreach ($package['contents'] as $item_id => $values) {
                            $items = $values['data'];
                            $_items[] = array(
                                'description' => $items->name,
                                'reference_id' => '',
                                'length' => '1',
                                'width' => '1',
                                'height' => '1',
                                'weight' => '1',
                                'quantity' => $values['quantity'],
                            );
                        }
                        $data['items'] = $_items;
//                         echo "<pre>";
//                         print_r($data);
//                         echo "</pre>";

                        $this->shipping = json_encode($data);
//                        $_GLOBAL['data'] = $this->shipping;

                        $p_item[] = array(
                            "length" => 1.0,
                            "width" => 1.0,
                            "height" => 1.0,
                            "weight" => 1.0,
                            "quantity" => 1,
                            "value" => 20
                        );
                        $rate_estimate['items'] = $p_item;

                        $rate_estimate['delivery_location'] = array(
                            'address' => array(
                                'street1' => $package['destination']['address_1'],
                                'city' => $package['destination']['city'],
                                'state' => $package['destination']['state'],
                                'zip' => $package['destination']['postcode'],
                            ),

                        );
                        $rate_estimate['pickup_after'] = $start_date;
                        $rate_estimate['deliver_between'] = array(
                            'start' => $start_date,
                            'end' => $end_date
                        );

                        $token = $this->token;
                        $endpoint = $this->end_point;
                        if (!function_exists('curl_version')) {
                            exit ("Enable cURL in PHP");
                        }
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $endpoint . 'estimates');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // 2. Set the CURLOPT_POST option to true for POST request
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($rate_estimate));
                        /* set the content type json */
                        $headers = [];
                        $headers[] = 'Content-Type:application/json';
                        $headers[] = "Authorization: Bearer " . $token;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        /* set return type json */
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        /* execute request */
                        if ($rate_estimate['delivery_location']['street1'] !== '') {
                            $this->r_status[] = true;
                            if ($location != '' && $_SESSION['order_type'] == 'delivery') {
                                $result = curl_exec($ch);
                            }
                            if (curl_errno($ch)) {
                                print curl_error($ch);
                            }
                            /* close cURL resource */
                            curl_close($ch);
                            $result = json_decode($result, true);
                            $this->result = $result;
                            if ($result['price']) {
                                $cost = $result['price'];
                                $rate = array(
                                    'id' => $this->id,
                                    'label' => $this->title,
                                    'cost' => $cost
                                );
                            }
                            $this->add_rate($rate);
                        }
                    }
                }
            }
        }
    }

    function add_roadie_shipping_method($methods)
    {
        $methods['roadie_'] = 'Roadie_Shipping_Method';
        return $methods;
    }

    function custom_shipping_rate_cost_calculation($rates, $package)
    {

        if ($rates) {
            $rate_cost = array();
            foreach ($rates as $rate_id => $rate) {
                if ('roadie_' == $rate->get_method_id()) {

                }
                $rate_cost[] = $rate->cost;
            }
            array_multisort($rate_cost, $rates);
        }
        if ($_SESSION['order_type'] != '' && ($_SESSION['order_type'] == 'pickup')) {
            unset($rates['roadie_']);
        } else if ($_SESSION['order_type'] == '') {
            unset($rates['roadie_']);
        }

        return $rates;

    }

//    function order_complete($order_id)
//    {
////        $order = wc_get_order($order_id);
////        echo "<pre>";
////        print_r($order);
////        echo "</pre>";
////        die;
//    }


    add_filter('woocommerce_shipping_methods', 'add_roadie_shipping_method');
    do_action('woocommerce_set_cart_cookies', true);
    add_action('woocommerce_shipping_init', 'roadie_shipping_method');
    add_filter('woocommerce_package_rates', 'custom_shipping_rate_cost_calculation', 10, 2);


}
