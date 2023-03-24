<?php
/**
 * PeachPay Admin settings primary navigation HTML view.
 *
 * @var array $bread_crumbs The array of breadcrumbs passed to the breadcrumb view.
 *
 * @package PeachPay
 */

defined( 'PEACHPAY_ABSPATH' ) || exit;

require_once PEACHPAY_ABSPATH . 'core/admin/views/utilities.php';

?>
<div id="peachpay-nav" class="col">
	<div class='peachpay-header'>
		<div class="peachpay-heading">
			<div class="left">
				<?php require PeachPay::get_plugin_path() . '/core/admin/views/html-bread-crumbs.php'; ?>
				<a class="peachpay-logo" href="<?php Peachpay_Admin::admin_settings_url(); ?>"></a>
			</div>
			<div class="misc-link-group">
				<?php peachpay_generate_misc_link( 'https://help.peachpay.app', 'docs-icon', 'Docs' ); ?>
				<?php peachpay_generate_misc_link( '#', 'support-icon', 'Support' ); ?>
				<?php
				if ( peachpay_nav_is_analytics_page() ) {
					peachpay_generate_misc_link( PeachPay_Admin::admin_settings_url( 'peachpay', '', '', '', false ), 'settings-icon', 'Settings' );
				} else {
					peachpay_generate_misc_link( PeachPay_Admin::admin_settings_url( 'peachpay_analytics', '', '', '', false ), 'analytics-icon', 'Analytics' );
				}
				?>
				<?php peachpay_premium_misc_link(); ?>
			</div>
		</div>
		<?php peachpay_generate_nav_bar(); ?>
	</div>
</div>
<?php
