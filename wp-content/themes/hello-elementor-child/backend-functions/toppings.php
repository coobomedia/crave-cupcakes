<?php

add_submenu_page('sales-report', 'Toppings by Type', 'Toppings by Type', 'manage_options', 'toppings-sheet', 'wct_toppings_page');

function wct_toppings_page()
{

	$args = array(
		'type'                     => 'product',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => 0,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'store_location',
		'pad_counts'               => false

	);

	$locations = get_categories($args);

	$args = array(
		'type'                     => 'product',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => true,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'product_cat',
		'pad_counts'               => false

	);

	$product_cats = get_categories($args);

	$today = date("Y-m-d", time());
	$tomorrow = date("Y-m-d", strtotime(date("Y-m-d", time()) . " +1 days"));

	$from_date = $today;
	$to_date = $tomorrow;


?>
	<h1>Toppings Sheet</h1>

	From
	<input type="date" id="from_date" value="<?php echo $today; ?>" />
	To
	<input type="date" id="to_date" value="<?php echo $tomorrow; ?>" />

	Store:
	<select name="location" id="location">
		<option value="">All Locations</option>
		<?php
		foreach ($locations as $location) { ?>
			<option value="<?php echo $location->slug; ?>"><?php echo $location->name; ?></option>
		<?php } ?>
	</select>

	<!--Product Type:
    <select name="product_type" id="product_type" >
        <option value="">All</option>
        <?php
		foreach ($product_cats as $product_cat) { ?>
        <option value="<?php echo $product_cat->term_id; ?>" ><?php echo $product_cat->name; ?></option>
        <?php } ?>
    </select>-->

	<button id="generate_toppings" class="crave-table-btn">Generate</button>

	<div id="result">

		<?php

		$location = "";
		$category = "";
		$category_name = "All Locations";
		//echo $from_date.": ".$to_date;

		global $wpdb;

		//echo $from_date."<br>";

		$from_date = date("Y-m-d", strtotime($from_date) + (3600 * 24));

		//echo $from_date."<br>";

		//echo $to_date."<br>";

		$to_date = date("Y-m-d", strtotime($to_date) + (3600 * 24));

		//echo $to_date."<br>";



		?>

	</div>

<?php

}

add_action('wp_ajax_wct_get_toppings_report', 'wct_get_get_toppings_callback');
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action('wp_ajax_nopriv_wct_get_toppings_report', 'wct_get_get_toppings_callback');

function wct_get_get_toppings_callback()
{

	$from_date = $_POST['from_date'];
	$to_date = $_POST['to_date'];
	$location = $_POST['location'];
	$category = $_POST['category'];
	$category_name = $_POST['category_name'];

	//echo $from_date."<br>";
	//echo $to_date."<br>";
	//echo $location."<br>";
	//echo $category."<br>";

	/*$orders = wc_get_orders(array(
	
		'limit'=>-1,
		//'type'=> 'shop_order',
		'date_created'=> $from_date .'...'. $to_date,
		
		'meta_key'      => '_order_location', // Postmeta key field
    	'meta_value'    => $location
		
		)
		
	);*/

	/*$args = array(
			'limit' => -1,
			'type'=> 'shop_order',
			'meta_query' => array(
				array(
					'key' => '_order_location',
					'value' => $location,
					'compare' => '='
				),
			),
		); 
	
	$orders = wc_get_orders( $args );*/


	global $wpdb;

	//echo $from_date."<br>";

	$from_date = date("Y-m-d", strtotime($from_date) + (3600 * 24));

	//echo $from_date."<br>";

	//echo $to_date."<br>";

	$to_date = date("Y-m-d", strtotime($to_date) + (3600 * 24));

	//echo $to_date."<br>";

	$orders = get_orders_by_date_range($from_date, $to_date, $location);

	$new_products = array();

	foreach ($orders as $key => $order) {

		$order_id = $order->ID;



		$order_details = wc_get_order($order_id);
		$order_items = $order_details->get_items();

		$order_customs = get_post_custom($order_id);

		$order_time = $order_customs['_delivery_time'][0];

		$delivery_date = $order_customs['_delivery_date'][0];

		$decor_type1 = $order_customs['_decor_type'][0];

		$color1_1 = $order_customs['_decor_color_1'][0];
		$color2_1 = $order_customs['_decor_color_2'][0];

		$colors1 = explode("|", $color1_1);
		$colors2 = explode("|", $color2_1);

		// Cupcakes: Goldfish | Mini Cupcakes: Elephant |

		$decor_types = explode("|", $decor_type1);

		foreach ($decor_types as $decor_key => $decor_type) {

			if ($decor_type == " ") {
				continue;
			}

			$color1_2 = explode(":", $colors1[$decor_key]);
			$color2_2 = explode(":", $colors2[$decor_key]);

			$color1 = $color1_2[1];
			$color2 = $color2_2[1];

			$decor_type2 = explode(":", $decor_type);

			$decor_type = $decor_type2[1] . " | " . $color1 . " | " . $color2;
			
			if(strlen($decor_type2[1])>=3){

				foreach ($order_items as $order_item) {
	
					$prod_id = $order_item->get_product_id();
					$name = $order_item->get_name();
					//$item = $order_item->data();
					$qty = $order_item->get_quantity();
					$total = $order_item->get_total();
					
					$terms = get_the_terms ( $prod_id, 'product_cat' );
					
					$cat_id = $terms[0]->term_id;
					$cat_name = $terms[0]->name;
					
					$old_qty = $new_products[$decor_type][$order_time]['quantity'];
	
					/*$new_products[$name][$order_time]['product_id'] = $prod_id;
					$new_products[$name][$order_time]['product_name'] = $name;
					$new_products[$name][$order_time]['decor_type'] = $decor_type;
					$new_products[$name][$order_time]['quantity'] = $old_qty+$qty;
					$new_products[$name][$order_time]['total'] = $new_products[$name][$order_time]['total']+$total;*/
	
					$new_products[$decor_type][$order_time]['product_id'] = $prod_id;
					$new_products[$decor_type][$order_time]['product_name'] = $name;
					
					$new_products[$decor_type][$order_time]['cat_id'] = $cat_id;
					$new_products[$decor_type][$order_time]['cat_name'] = $cat_name;
					
					$new_products[$decor_type][$order_time]['delivery_date'] = $delivery_date;
	
					/*if(isset($new_products[$decor_type][$order_time]['decor_type'])){
						$new_products[$decor_type][$order_time]['decor_type'] = (int)$new_products[$decor_type][$order_time]['decor_type'] + (int)$decor_type;
					} else {
						$new_products[$decor_type][$order_time]['decor_type'] = $decor_type;
					}*/
					
					if($cat_id==35){
						$new_products[$decor_type][$order_time]['quantity'] = $old_qty + $qty*12;
					} else {
						$new_products[$decor_type][$order_time]['quantity'] = $old_qty + $qty;
					}
	
					
					
					
					$new_products[$decor_type][$order_time]['total'] = $new_products[$decor_type][$order_time]['total'] + $total;
					
				}
			
			}
		}
	}

	$cat_array = array();

	foreach ($new_products as $new_product) {

		$prod_id = $new_product['product_id'];

		$terms = get_the_terms($prod_id, 'product_cat');

		$term_id = $terms[0]->term_id;
		$term_slug = $terms[0]->name;

		$old_qty = $cat_array[$term_slug]['quantity'];
		$old_total = $cat_array[$term_slug]['total'];
		if ($term_id == $category) {
			$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
			$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
		} elseif ($category == NULL) {
			$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
			$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
		}
	}

	$time_array = array();

	foreach ($new_products as $key => $new_product) {

		foreach ($new_product as $key2 => $one_time) {

			$time_array[$key2] = $one_time['quantity'];
		}
	}

	ksort($time_array);

	// Totals Separated by Time
	$total_quantity = 0;
	$total_amount = 0;

	$total_array = array();

	// Totals Separated by Product

	$total_quantity_product = 0;
	$total_amount_product = 0;

	$total_array_product = array();

?>

	<br class="clear" />

	<div id="print_table">

		<?php

		$from_date = date("Y-m-d", strtotime($from_date) - (3600 * 24));
		$to_date = date("Y-m-d", strtotime($to_date) - (3600 * 24));

		?>

		<h2><?php esc_attr_e('Toppings for ' . $category_name . ' Pre-orders: ' . date("D M d, Y", strtotime($from_date)) . ' to ' . date("D M d, Y", strtotime($to_date)), 'WpAdminStyle'); ?></h2>

		<?php

		/*echo "<pre>"; 
		print_r($new_products);
		echo "</pre>";*/

		?>

		<table class="crave-table">
			<tr>
				<th>Start Date</th>
				<th>Completion Date</th>
				<th><?php esc_attr_e('Topping Name', 'WpAdminStyle'); ?></th>
				<th>Color 1</th>
				<th>Color 2</th>
				<?php foreach ($time_array as $key2 => $new_time) { ?>
					<!--<td><b><?php echo $key2; ?></b></td>-->
				<?php } ?>
				<th>Total</th>
				<th>#Completed</th>
				<th>#Remaining</th>
				<th>Initials</th>
				<th>Action</th>
			</tr>


			<?php foreach ($new_products as $key => $new_product) { ?>
				<?php
				foreach ($new_product as $time_key => $value) {
				}

				$start_date = date("Y-m-d", strtotime($new_product[$time_key]['delivery_date']) - (3600 * 24));
				?>
				<tr>
					<td class="order-date"><?php echo $start_date; ?></td>
					<td class="delivery-date"><?php echo $new_product[$time_key]['delivery_date']; ?></td>
					<?php $cols = explode(" | ", $key); ?>
					<td><span class="topping-name"><?php echo $cols[0]; ?></span></td>
					<td class="color-1"><?php echo $cols[1]; ?></td>
					<td class="color-2"><?php echo $cols[2]; ?></td>
					<?php //ksort($new_product); 
					?>
					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<!--<td><?php echo $new_product[$key2]['quantity']; ?></td>-->
						<?php

						$old_qty = $total_array[$key2]['quantity'];
						$old_total = $total_array[$key2]['total'];
						$total_array[$key2]['quantity'] = $old_qty + $new_product[$key2]['quantity'];
						$total_array[$key2]['total'] = $old_total + $new_product[$key2]['total'];

						$old_qty_product = $total_array_product[$key]['quantity'];
						$old_total_product = $total_array_product[$key]['total'];
						$total_array_product[$key]['quantity'] = $old_qty_product + $new_product[$key2]['quantity'];
						$total_array_product[$key]['total'] = $old_total_product + $new_product[$key2]['total'];

						?>
					<?php } ?>

					<td><span class="total-toppings"><?php echo $total_array_product[$key]['quantity']; ?></span></td>
					<?php

					global $wpdb;
					$topping_name_sql = str_replace('\'', '\\\\\\\'', $cols[0]);

					$results = $wpdb->get_results(
						"SELECT * FROM  toppings_sheet WHERE 
						delivery_date = '" . $new_product[$time_key]['delivery_date'] . "' AND
						topping_name = '" . $topping_name_sql . "' AND
						color_1 = '" . $cols[1] . "' AND
						color_2 = '" . $cols[2] . "'"
					);

					if (!empty($results)) {
						$done_toppings = $results[0]->done_toppings;
						$initials = $results[0]->initials;
					} else {
						$done_toppings = '';
						$initials = '';
					}

					$difference = (int)$total_array_product[$key]['quantity'] - (int)$done_toppings;

					?>

					<td>
						<?php
						/*echo "<pre>";
					print_r($results);
					echo "</pre>";*/
						?>
						<input type="number" class="done-toppings" min="0" max="<?php echo $total_array_product[$key]['quantity']; ?>" value="<?php echo $done_toppings; ?>" stle="width:30px;" />
					</td>
					<td>
						<p class="remaining-toppings"><?php echo $difference; ?></p>
					</td>
					<td><input type="text" class="initials" value="<?php echo $initials; ?>" stle="width:50px;" /></td>
					<td><button class="save-toppings crave-table-btn">Save</button></td>
				</tr>
				<?php
				//$total_quantity += $new_product[$key2]['quantity'];
				//$total_amount += $new_product[$key2]['total'];
				?>
			<?php } ?>

			<?php
			$grand_total = 0;
			?>

			<tr>
				<td><b>Total</b></td>
				<td>

				</td>
				<td>

				</td>

				<td>

				</td>
				<?php foreach ($time_array as $key2 => $new_time) { ?>
					<!--<td><b><?php echo $total_array[$key2]['quantity']; ?><b></td>-->
					<?php

					$grand_total += $total_array[$key2]['quantity'];

					?>
				<?php } ?>


				<td></td>
				<td><b><?php echo $grand_total; ?></b></td>
				<td></td>
				<td>

				</td>
				<td>

				</td>
				<td>

				</td>

			</tr>

		</table>

	</div>
	<br /><br />
	<!-- <button id="print_me">Print</button> -->
	<input type='button' id='btn' class="crave-table-btn" value='Print' onclick='printDiv();'>
	<style>
		.widefat td {
			border: 1px solid black;
		}
	</style>

<?php

	die();
}

add_action('wp_ajax_wct_save_toppers_done', 'wct_save_toppers_done_callback');
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action('wp_ajax_nopriv_wct_save_toppers_done', 'wct_save_toppers_done_callback');

function wct_save_toppers_done_callback()
{

	global $wpdb;

	$delivery_date = $_POST['delivery_date'];
	$topping_name = $_POST['topping_name'];
	$color_1 = $_POST['color_1'];
	$color_2 = $_POST['color_2'];
	$done_toppings = $_POST['done_toppings'];
	$initials = $_POST['initials'];


	$results = $wpdb->get_results("SELECT * FROM  toppings_sheet WHERE 
			delivery_date = '" . $delivery_date . "' AND
			topping_name = '" . addslashes($topping_name) . "' AND
			color_1 = '" . $color_1 . "' AND
			color_2 = '" . $color_2 . "'");

	echo "SELECT * FROM  toppings_sheet WHERE 
			delivery_date = '" . $delivery_date . "' AND
			topping_name = '" . addslashes($topping_name) . "' AND
			color_1 = '" . $color_1 . "' AND
			color_2 = '" . $color_2 . "'";

	echo "<pre>";
	print_r($results);
	echo "</pre>";

	if (!empty($results)) {

		$id = $results[0]->id;

		echo $id;

		$wpdb->query("UPDATE toppings_sheet SET done_toppings = '" . $done_toppings . "', initials='" . $initials . "' WHERE id = '" . $id . "'");
	} else {

		$wpdb->insert("toppings_sheet", array(
			"delivery_date" => $delivery_date,
			"topping_name" => $topping_name,
			"color_1" => $color_1,
			"color_2" => $color_2,
			"done_toppings" => $done_toppings,
			"initials" => $initials,
		));
	}
}


function get_toppings_backend_js()
{
?>
	<script>
		function printDiv() {

			var divToPrint = document.getElementById('print_table');

			var newWin = window.open('', 'Print-Window');

			newWin.document.open();

			newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

			newWin.document.close();

			setTimeout(function() {
				newWin.close();
			}, 10);

		}

		// function printData() {
		// 	/* var divToPrint=document.getElementById("print_table");
		// 	 newWin= window.open("");
		// 	 newWin.document.write(divToPrint.outerHTML);
		// 	 newWin.print();
		// 	 newWin.close();*/

		// 	var printContents = document.getElementById("print_table").innerHTML;
		// 	var originalContents = document.body.innerHTML;

		// 	document.body.innerHTML = printContents + "<style>td {border:1px solid black; }</style>";

		// 	window.print();

		// 	document.body.innerHTML = originalContents;
		// }

		// jQuery('#wpbody-content').on('click', "#print_me", function() {
		// 	//alert("Print");
		// 	printData();
		// })

		jQuery("#generate_toppings").on("click", function() {

			var date_from = jQuery("#from_date").val();
			var date_to = jQuery("#to_date").val();
			var location1 = jQuery("#location").children("option").filter(":selected").val();
			//var category1 = jQuery("#product_type").children("option").filter(":selected").val();
			var category_name1 = jQuery("#location").children("option").filter(":selected").text();

			jQuery.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					'from_date': date_from,
					'to_date': date_to,
					'location': location1,
					'category_name': category_name1,
					'action': 'wct_get_toppings_report' //this is the name of the AJAX method called in WordPress
				},
				success: function(msg) {
					document.getElementById('result').innerHTML = msg;
				},
				error: function() {
					alert("error: Please Re-generate the Report");
				}
			});
		});

		jQuery("#result").on('click', ".save-toppings", function() {

			var delivery_date = jQuery(this).parent().parent().find('.delivery-date').html();
			var topping_name = jQuery(this).parent().parent().find('.topping-name').html();
			var color_1 = jQuery(this).parent().parent().find('.color-1').html();
			var color_2 = jQuery(this).parent().parent().find('.color-2').html();
			var done_toppings = jQuery(this).parent().parent().find('.done-toppings').val();
			var initials = jQuery(this).parent().parent().find('.initials').val();

			//alert(color_1);

			jQuery.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					'delivery_date': delivery_date,
					'topping_name': topping_name,
					'color_1': color_1,
					'color_2': color_2,
					'done_toppings': done_toppings,
					'initials': initials,
					'action': 'wct_save_toppers_done' //this is the name of the AJAX method called in WordPress
				},
				success: function(msg) {

				},
				error: function() {
					alert("error: Please Re-generate the Report");
				}
			});

		});

		jQuery("#result").on("change", ".done-toppings", function() {

			//alert(jQuery(this).val());

			var new_value = jQuery(this).val();
			var old_value = jQuery(this).parent().parent().find(".total-toppings").html();

			jQuery(this).parent().parent().find(".remaining-toppings").html(parseInt(old_value) - parseInt(new_value));

		});
	</script>
<?php
}
add_action('admin_footer', 'get_toppings_backend_js');
