<?php
/**
 * PeachPay Stripe Connect
 *
 * @package PeachPay
 */

defined( 'PEACHPAY_ABSPATH' ) || exit;

?>
<div class="row">
	<div class="col-3" style="text-align: center;">
		<div class="payment-logo stripe-primary-bg">
			<img src="<?php echo esc_attr( peachpay_url( 'core/payments/stripe/admin/assets/img/stripe-logo.svg' ) ); ?>" />
		</div>

		<!-- Stripe Connect / Unlink buttons -->
		<?php if ( PeachPay_Stripe_Integration::connected() ) : ?>
			<a class="unlink-payment-button" href="<?php echo esc_url( admin_url( 'admin.php?page=peachpay&tab=payment&unlink_stripe#stripe' ) ); ?>">
				<?php esc_html_e( 'Unlink Stripe', 'peachpay-for-woocommerce' ); ?>
			</a>
		<?php else : ?>
			<a class="connect-payment-button" href="<?php echo esc_url( PeachPay_Stripe_Integration::signup_url() ); ?>">
				<span><?php esc_html_e( 'Connect with Stripe', 'peachpay-for-woocommerce' ); ?></span>
			</a>
		<?php endif; ?>

		<div>
			<?php
			//phpcs:ignore
			echo peachpay_build_video_help_section('https://youtu.be/SrTykTIzwHo', "justify-content: center");
			?>
		</div>
	</div>
	<div class="col-9 flex-col gap-4" style="padding-left: 1rem;">
		<!-- Stripe Status -->
		<?php if ( PeachPay_Stripe_Integration::connected() ) : ?>
			<div class="pp-flex-row pp-gap-4">
				<span class="dashicons dashicons-yes-alt"></span>
				<div class="pp-flex-col pp-gap-4">
					<?php esc_html_e( "You've successfully connected your Stripe account", 'peachpay-for-woocommerce' ); ?>
				</div>
			</div>
		<?php else : ?>
			<p>
				<?php esc_html_e( 'Connect your Stripe account to PeachPay.', 'peachpay-for-woocommerce' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Considering getting Stripe? Stripe is a good choice if you want the biggest selection of global payment methods and “buy now pay later” options.', 'peachpay-for-woocommerce' ); ?>
			</p>
		<?php endif; ?>

		<!-- Stripe advanced details -->
		<?php if ( PeachPay_Stripe_Integration::connected() ) : ?>
		<details style="border: 1px solid #dcdcde; border-radius: 4px; padding: 4px 10px; width: content-width; margin-top: 1rem;">
			<summary>
				<b><?php esc_html_e( 'Advanced Details', 'peachpay-for-woocommerce' ); ?></b>
			</summary>
			<hr>
			<p style="padding: 0 1rem 0; margin: 0;"><b><?php esc_html_e( 'Connect Id:', 'peachpay-for-woocommerce' ); ?></b>
			<?php
			PeachPay_Stripe::dashboard_url(
				PeachPay_Stripe_Integration::mode(),
				PeachPay_Stripe_Integration::connect_id(),
				'activity',
				PeachPay_Stripe_Integration::connect_id()
			);
			?>
			</p>
		</details>
		<?php endif; ?>
	</div>
</div>
