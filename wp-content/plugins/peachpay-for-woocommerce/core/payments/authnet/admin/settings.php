<?php
/**
 * PeachPay Authorize.net payment settings.
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * PeachPay Authorize.net admin settings.
 *
 * @param string $current The key of the current payment section tab.
 */
function peachpay_authnet_admin_settings_section( $current ) {
	$class = 'pp-header pp-sub-nav-authnet';
	if ( 'pp-sub-nav-authnet' !== $current ) {
		$class .= ' hide';
	}
	add_settings_field(
		'peachpay_authnet_setting',
		__( 'Authorize.net', 'peachpay-for-woocommerce' ),
		'peachpay_authnet_setting_section',
		'peachpay',
		'peachpay_payment_settings_section',
		array( 'class' => $class )
	);
}
add_action( 'peachpay_admin_add_payment_setting_section', 'peachpay_authnet_admin_settings_section' );

/**
 * Renders the Authorize.net settings sections
 */
function peachpay_authnet_setting_section() {
	?>
	<div class="peachpay-setting-section">
		<?php
		peachpay_field_authnet_connect_setting();
		peachpay_field_authnet_status_setting();

		// Authorize.net PeachPay checkout enable.
		peachpay_admin_input(
			'authnet-enable',
			'peachpay_payment_options',
			'authnet_enable',
			1,
			__( 'Show Authorize.net in the checkout window', 'peachpay-for-woocommerce' ),
			'',
			array(
				'input_type' => 'checkbox',
				'disabled'   => ! peachpay_authnet_connected(),
			)
		);

		?>
			<div class="pp-save-button-section">
				<?php submit_button( 'Save changes', 'pp-button-primary' ); ?>
			</div>
		</div>
	<?php
}

/**
 * Renders the Authorize.net connect status.
 */
function peachpay_field_authnet_status_setting() {
	if ( peachpay_authnet_login_id() && peachpay_authnet_transaction_key() ) {
		if ( peachpay_authnet_connected() ) {
			?>
			<div>
				<p style="font-size: 12px;">
					<span style="color: #2E7D32; font-size: 16px;" class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( "You've successfully connected your Authorize.net account", 'peachpay-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		} else {
			?>
			<div style="border: 1px solid #CCCCCC; padding: 2px; border-radius: 5px;">
				<p style="font-size: 12px;">
					<span style="color: #D32F2F; font-size: 16px;" class="dashicons dashicons-warning"></span>
					<?php esc_html_e( 'Unable to reach your Authorize.Net account. Please double check your API Login ID & Transaction Key to ensure payments can be processed.', 'peachpay-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
	}
}

/**
 * Renders info form for Authorize.net's test mode and production mode with UI.
 */
function peachpay_field_authnet_connect_setting() {
	// Test mode.
	$test_mode = array(
		'test_login_id'        => __( 'Test API Login ID', 'peachpay-for-woocommerce' ),
		'test_transaction_key' => __( 'Test API Transaction Key', 'peachpay-for-woocommerce' ),
		'test_signature_key'   => __( 'Test Signature Key', 'peachpay-for-woocommerce' ),
	);

	// Production mode.
	$production_mode = array(
		'login_id'        => __( 'API Login ID', 'peachpay-for-woocommerce' ),
		'transaction_key' => __( 'API Transaction Key', 'peachpay-for-woocommerce' ),
		'signature_key'   => __( 'Signature Key', 'peachpay-for-woocommerce' ),
	);

	peachpay_authnet_info_form( 'pp-authnet-test-form', $test_mode );
	peachpay_authnet_info_form( 'pp-authnet-production-form', $production_mode );
	?>
	<script>
		if ( document.querySelector('#peachpay_test_mode') ) {
			document.querySelector('#peachpay_test_mode').checked ? document.querySelector('.pp-authnet-production-form').classList.add('hide') : document.querySelector('.pp-authnet-test-form').classList.add('hide');

			document.querySelector('#peachpay_test_mode').addEventListener('change', (event) => {
				if (event.target.checked ) {
					document.querySelector('.pp-authnet-test-form').classList.remove('hide');
					document.querySelector('.pp-authnet-production-form').classList.add('hide');
				} else {
					document.querySelector('.pp-authnet-test-form').classList.add('hide');
					document.querySelector('.pp-authnet-production-form').classList.remove('hide');
				}
			});
		}
	</script>
	<?php
}

/**
 * Renders info form for Authorize.net's test mode and production mode.
 *
 * @param string $form_name Name for the form element used for UI purposes.
 * @param array  $inputs Contains input id and its label.
 */
function peachpay_authnet_info_form( $form_name, $inputs ) {
	?>
	<fieldset class="<?php echo esc_attr( $form_name ); ?>">
	<?php
	foreach ( $inputs as $value => $label ) {
		$input_type = 'login_id' === $value || 'test_login_id' === $value ? 'text' : 'password';
		?>
		<div class="pp-authnet-admin-input">
			<label for="<?php echo esc_attr( 'peachpay_authnet_' . $value ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
			<input
				id="<?php echo esc_attr( 'peachpay_authnet_' . $value ); ?>"
				name="peachpay_payment_options[<?php echo esc_attr( 'peachpay_authnet_' . $value ); ?>]"
				type="<?php echo esc_attr( $input_type ); ?>"
				value="<?php echo esc_attr( peachpay_get_settings_option( 'peachpay_payment_options', 'peachpay_authnet_' . $value ) ); ?>"
				class="pp-text-box"
				style='width: 300px'
				autocomplete="one-time-code"
			>
			<!-- autocomplete="one-time-code" is a hack to prevent Chrome from autofilling these fields with the WordPress credentials, since as of late 2022 Chrome does not respect autocomplete="off" or similar attributes -->
		</div>
		<?php
	}
	?>
	</fieldset>
	<?php
}
