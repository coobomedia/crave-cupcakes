<?php
/**
 * Helper functions for rendering parts of the view.
 *
 * @package PeachPay
 */

defined( 'PEACHPAY_ABSPATH' ) || exit;

if ( function_exists( 'peachpay_generate_misc_link' ) ) {
	// Since the view templates can be included multiple times, if one of these
	// functions has already been defined, then we return to avoid an error.
	return;
}

/**
 * Returns the key of the tab that should be active in the navigation.
 */
function peachpay_get_current_nav_tab() {
	if ( peachpay_nav_is_peachpay_page() ) {
		// PHPCS:ignore
		return isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'home';
	}
	if ( peachpay_nav_is_analytics_page() ) {
		// PHPCS:ignore
		return isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'payment_methods';
	}
	if ( peachpay_nav_is_gateway_page() ) {
		// PHPCS:ignore
		return '';
	}
	return '';
}

/**
 * Returns true if this is a PeachPay settings page (but not a gateway page).
 */
function peachpay_nav_is_peachpay_page() {
	// PHPCS:ignore
	return isset( $_GET['page'] ) && ( 'peachpay' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) );
}

/**
 * Returns true if this is the PeachPay analytics page.
 */
function peachpay_nav_is_analytics_page() {
	// PHPCS:ignore
	return isset( $_GET['page'] ) && ( 'peachpay_analytics' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) );
}

/**
 * Returns true if this is a PeachPay gateway page.
 */
function peachpay_nav_is_gateway_page() {
	// PHPCS:ignore
	return isset( $_GET['page'] ) && ( 'wc-settings' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) && isset( $_GET['section'] ) && ( 'peachpay' === substr( sanitize_text_field( wp_unslash( $_GET['section'] ) ), 0, 8 ) );
}

/**
 * Generates the PeachPay settings nav bar.
 */
function peachpay_generate_nav_bar() { 	//phpcs:ignore
	if ( peachpay_nav_is_analytics_page() ) {
		?>
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php peachpay_generate_nav_tab( 'peachpay_analytics', 'payment_methods', null, 'Payment methods' ); ?>
			<?php peachpay_generate_nav_tab( 'peachpay_analytics', 'device_breakdown', null, 'Device breakdown' ); ?>
		</nav>
	<?php } else { ?>
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper <?php echo esc_attr( peachpay_nav_is_gateway_page() ? 'no-active-tab' : '' ); ?>">
			<?php peachpay_generate_nav_tab( 'peachpay', 'home', null, 'Home' ); ?>
			<?php peachpay_generate_nav_tab( 'peachpay', 'payment', null, 'Payments' ); ?>
			<?php peachpay_generate_nav_tab( 'peachpay', 'currency', null, 'Currency' ); ?>
			<?php peachpay_generate_nav_tab( 'peachpay', 'field', 'billing', 'Field editor' ); ?>
			<?php peachpay_generate_nav_tab( 'peachpay', 'related_products', null, 'Recommended products' ); ?>
			<?php peachpay_generate_nav_tab( 'peachpay', 'express_checkout', 'branding', 'Express checkout' ); ?>
		</nav>
		<?php
	}
}

/**
 * Generates a styled link on the top right of PeachPay settings header.
 *
 * @param string $link   The url.
 * @param string $icon   The file name of the icon.
 * @param string $title  The text to display on the link.
 */
function peachpay_generate_misc_link( $link, $icon, $title ) {
	?>
	<a class="misc-link <?php echo esc_attr( $icon ); ?>-link" href="<?php echo esc_url( $link ); ?>"<?php echo ( 'https://help.peachpay.app' === $link ? ' target="_blank"' : '' ); ?>>
		<div class="icon <?php echo esc_attr( $icon ); ?>"></div>
		<?php peachpay_generate_nav_tab_title( $title ); ?>
	</a>
	<?php
}

/**
 * Generates a single navigation tab for the given tab and section.
 *
 * @param string $page        The page key.
 * @param string $tab         The tab key.
 * @param string $section     The section key.
 * @param string $title       The text to display on the tab.
 */
function peachpay_generate_nav_tab( $page, $tab, $section, $title ) {
	$has_dropdown = array_key_exists( $tab, peachpay_tabs_with_dropdowns() ) && peachpay_get_current_nav_tab() !== $tab;
	?>
	<div
		class="
			<?php peachpay_premium_tooltip_parent( $tab ); ?>
			<?php echo esc_html( $has_dropdown ? ( 'pp-popup-trigger' ) : '' ); ?>
		">
			<a
				class="
					nav-tab
					<?php echo esc_attr( ( peachpay_get_current_nav_tab() === $tab ) ? 'nav-tab-active' : '' ); ?>
				"
				href="<?php esc_url( PeachPay_Admin::admin_settings_url( $page, $tab, $section ) ); ?>"
				data-heap="<?php echo 'nav_tab_view_' . esc_html( $tab ); ?>"
			>
				<?php peachpay_premium_nav_icon( $tab ); ?>
				<?php peachpay_generate_nav_tab_title( $title ); ?>
				<?php peachpay_premium_tooltip( $tab ); ?>
			</a>
			<?php peachpay_render_nav_dropdown( $tab ); ?>
	</div>
	<?php
}

/**
 * Returns an array defining which tabs have dropdowns.
 */
function peachpay_tabs_with_dropdowns() {
	return array(
		'field'            => array(
			'billing'    => 'Billing',
			'shipping'   => 'Shipping',
			'additional' => 'Additional',
		),
		'express_checkout' => array(
			'branding'                => 'Branding',
			'button'                  => 'Checkout button',
			'window'                  => 'Checkout window',
			'product_recommendations' => 'Product recommendations',
			'advanced'                => 'Advanced',
		),
	);
}

/**
 * Renders the navigation dropdown.
 *
 * @param string $tab The tab of the dropdown.
 */
function peachpay_render_nav_dropdown( $tab ) {
	$tabs_with_dropdowns = peachpay_tabs_with_dropdowns();
	if ( peachpay_get_current_nav_tab() === $tab || ! array_key_exists( $tab, $tabs_with_dropdowns ) ) {
		return;
	}
	$sections = $tabs_with_dropdowns[ $tab ];
	?>
	<div class='peachpay-nav-dropdown peachpay-nav-dropdown-<?php echo esc_attr( $tab ); ?> pp-popup pp-popup-below'>
		<?php
		foreach ( $sections as $section => $title ) {
			?>
			<a class='dropdown-tab' href="<?php esc_url( PeachPay_Admin::admin_settings_url( 'peachpay', $tab, $section ) ); ?>">
				<?php peachpay_generate_nav_tab_title( $title ); ?>
			</a>
			<?php
		}
		?>
	</div>
	<?php
}

/**
 * Generates escaped and translated text for the navigation tab title.
 * This is a workaround for esc_html_e only accepting string literals.
 *
 * @param string $title The text to display on the tab.
 */
function peachpay_generate_nav_tab_title( $title ) {
	switch ( $title ) {
		case 'Home':
			echo esc_html_e( 'Home', 'peachpay-for-woocommerce' );
			break;
		case 'Payments':
			echo esc_html_e( 'Payments', 'peachpay-for-woocommerce' );
			break;
		case 'Currency':
			echo esc_html_e( 'Currency', 'peachpay-for-woocommerce' );
			break;
		case 'Field editor':
			echo esc_html_e( 'Field editor', 'peachpay-for-woocommerce' );
			break;
		case 'Recommended products':
			echo esc_html_e( 'Recommended products', 'peachpay-for-woocommerce' );
			break;
		case 'Express checkout':
			echo esc_html_e( 'Express checkout', 'peachpay-for-woocommerce' );
			break;
		case 'Docs':
			echo esc_html_e( 'Docs', 'peachpay-for-woocommerce' );
			break;
		case 'Support':
			echo esc_html_e( 'Support', 'peachpay-for-woocommerce' );
			break;
		case 'Analytics':
			echo esc_html_e( 'Analytics', 'peachpay-for-woocommerce' );
			break;
		case 'Billing':
			echo esc_html_e( 'Billing', 'peachpay-for-woocommerce' );
			break;
		case 'Shipping':
			echo esc_html_e( 'Shipping', 'peachpay-for-woocommerce' );
			break;
		case 'Additional':
			echo esc_html_e( 'Additional', 'peachpay-for-woocommerce' );
			break;
		case 'Branding':
			echo esc_html_e( 'Branding', 'peachpay-for-woocommerce' );
			break;
		case 'Checkout button':
			echo esc_html_e( 'Checkout button', 'peachpay-for-woocommerce' );
			break;
		case 'Checkout window':
			echo esc_html_e( 'Checkout window', 'peachpay-for-woocommerce' );
			break;
		case 'Product recommendations':
			echo esc_html_e( 'Product recommendations', 'peachpay-for-woocommerce' );
			break;
		case 'Advanced':
			echo esc_html_e( 'Advanced', 'peachpay-for-woocommerce' );
			break;
		case 'Settings':
			echo esc_html_e( 'Settings', 'peachpay-for-woocommerce' );
			break;
		case 'Payment methods':
			echo esc_html_e( 'Payment methods', 'peachpay-for-woocommerce' );
			break;
		case 'Device breakdown':
			echo esc_html_e( 'Device breakdown', 'peachpay-for-woocommerce' );
			break;
	}
}

/**
 * Renders the peachpay premium misc link code.
 */
function peachpay_premium_misc_link() {
	//phpcs:ignore
	if ( isset( $_GET['page'] ) && 'peachpay' !== $_GET['page'] ) {
		return;
	}

	$peachpay_has_premium_capability = peachpay_plugin_has_capability( 'woocommerce_premium', array( 'woocommerce_premium' => get_option( 'peachpay_premium_capability' ) ) );
	$peachpay_premium_config         = peachpay_plugin_get_capability_config( 'woocommerce_premium', array( 'woocommerce_premium' => get_option( 'peachpay_premium_capability' ) ) );

	if ( isset( $peachpay_premium_config['bypass'] ) ) {
		return;
	}

	if ( $peachpay_has_premium_capability ) {
		?>
			<form
				action="<?php echo esc_url_raw( peachpay_api_url( 'prod' ) . 'api/v1/premium/subscriptionPortal' ); ?>"
				method='post'
			>
				<input 
					type='text'
					name='merchant_id'
					value='<?php echo esc_html( peachpay_plugin_merchant_id() ); ?>'
					style='visibility: hidden; position: absolute; top: -1000px; left: -1000px;'
				/>
				<input
					type='text'
					name='return_url'
					value='<?php echo esc_url_raw( Peachpay_Admin::admin_settings_url( 'peachpay', 'payment' ) ); ?>'
					style='visibility: hidden; position: absolute; top: -1000px; left: -1000px;'
				/>
				<button
					type='submit'
					class='button-to-anchor misc-link <?php echo esc_attr( 'crown-icon' ); ?>-link'
				>
					<div class="icon <?php echo esc_attr( 'crown-icon' ); ?>"></div>
					<?php echo esc_html_e( 'Premium Portal', 'peachpay-for-woocommerce' ); ?>
				</button>
			</form>
		<?php
	} else {
		?>
			<button class="pp-button-continue-premium button-to-anchor misc-link <?php echo esc_attr( 'crown-icon' ); ?>-link'">
				<div class="icon <?php echo esc_attr( 'crown-icon' ); ?>"></div>
				<?php echo esc_html_e( 'Upgrade', 'peachpay-for-woocommerce' ); ?>
			</button>
		<?php
		require_once PeachPay::get_plugin_path() . 'core/admin/views/html-premium-modal.php';
	}
}

/**
 * Renders the premium crown icon.
 *
 * @param string $tab .
 */
function peachpay_premium_nav_icon( $tab ) {
	$peachpay_has_premium_capability = peachpay_plugin_has_capability( 'woocommerce_premium', array( 'woocommerce_premium' => get_option( 'peachpay_premium_capability' ) ) );

	if ( ! $peachpay_has_premium_capability && 'payment' !== $tab && 'home' !== $tab ) {
		?>
		<div class="icon crown-icon"></div>
		<?php
	}
}

/**
 * Echos the tooltip parent class.
 *
 * @param string $tab .
 */
function peachpay_premium_tooltip_parent( $tab ) {
	$peachpay_has_premium_capability = peachpay_plugin_has_capability( 'woocommerce_premium', array( 'woocommerce_premium' => get_option( 'peachpay_premium_capability' ) ) );

	if ( ! $peachpay_has_premium_capability && 'payment' !== $tab && 'home' !== $tab ) {
		echo 'pp-w3-tooltip';
	}
}

/**
 * Renders the tooltip popup.
 *
 * @param string $tab .
 */
function peachpay_premium_tooltip( $tab ) {
	$peachpay_has_premium_capability = peachpay_plugin_has_capability( 'woocommerce_premium', array( 'woocommerce_premium' => get_option( 'peachpay_premium_capability' ) ) );

	if ( ! $peachpay_has_premium_capability && 'payment' !== $tab && 'home' !== $tab ) {
		?>
		<div class="pp-w3-tooltip-pop">
			<?php esc_html_e( 'Premium Feature', 'peachpay-for-woocommerce' ); ?>
		</div>
		<?php
	}
}
