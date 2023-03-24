<?php
/**
 * PeachPay Extension Admin Trait.
 *
 * @package PeachPay/Admin
 */

defined( 'PEACHPAY_ABSPATH' ) || exit;

/**
 * Used to create a PeachPay admin section for managing multiple tabs.
 */
final class PeachPay_Admin_Section {

	/**
	 * The value of the $_GET["section"] when this section is viewed.
	 *
	 * @var string
	 */
	private $section = '';

	/**
	 * The registered tabs associated with this section(If any at all)
	 *
	 * @var PeachPay_Admin_Tab[]
	 */
	private $tabs = array();

	/**
	 * The intermediate bread crumbs between this section and the root of the PeachPay settings.
	 *
	 * @var array
	 */
	private $bread_crumbs = array();


	/**
	 * Initializes the section page.
	 *
	 * @param string               $section The id of the section.
	 * @param PeachPay_Admin_Tab[] $tabs The tabs to include in this section.
	 * @param array                $bread_crumbs The intermediate bread crumbs between this section and the root of the PeachPay settings.
	 */
	private function __construct( $section, $tabs, $bread_crumbs ) {
		if ( ! $this->is_active() && count( $tabs ) <= 0 ) {
			return;
		}

		$this->section = $section;
		$this->tabs    = $tabs;

		$default_tab_view = $this->get_admin_tab_view( '' );

		$this->bread_crumbs = array_merge(
			$bread_crumbs,
			array(
				array(
					'name' => $this->format_bread_crumb_name( $this->section ),
					'url'  => $this->get_url( $default_tab_view->get_tab() ),
				),
			)
		);

		$this->hooks();
	}

	/**
	 * Setup lifetime hooks for a PeachPay admin section.
	 */
	private function hooks() {
		$admin_section = $this;

		add_action(
			"peachpay_admin_section_$this->section",
			function() use ( $admin_section ) {
				$admin_section->do_admin_page();
			}
		);

		add_action(
			"peachpay_update_options_admin_settings_$this->section",
			function() use ( $admin_section ) {
                // PHPCS:disable WordPress.Security.NonceVerification.Recommended
				$tab_key = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
                // PHPCS:enable

				$admin_tab_view = $admin_section->get_admin_tab_view( $tab_key );
				if ( ! $admin_tab_view ) {
					return;
				}

				$admin_tab_view->process_admin_options();
			}
		);
	}

	/**
	 * Gets the active tab to display.
	 *
	 * @param string $current_tab_key The current tab.
	 */
	private function get_admin_tab_view( $current_tab_key ) {
		if ( '' === $current_tab_key ) {
			return $this->tabs[0];
		}

		foreach ( $this->tabs as $admin_tab_view ) {
			if ( $admin_tab_view->get_tab() === $current_tab_key ) {
				return $admin_tab_view;
			}
		}

		return null;
	}

	/**
	 * Renders the admin section when a tab is being viewed.
	 */
	private function do_admin_page() {
		// PHPCS:disable WordPress.Security.NonceVerification.Recommended
		$tab_key = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
		// PHPCS:enable

		$admin_tab_view = $this->get_admin_tab_view( $tab_key );
		if ( ! $admin_tab_view ) {
			wp_safe_redirect( admin_url( 'admin.php?page=peachpay' ) );
			exit;
		}

		?>
			<div class="peachpay">
			<?php
			$bread_crumbs = array_merge(
				$this->bread_crumbs,
				array(
					array(
						'name' => $admin_tab_view->get_title(),
					),
				)
			);

			require PeachPay::get_plugin_path() . '/core/admin/views/html-primary-navigation.php';
			?>
				<div class="admin-section-wrap">
					<div id="peachpay_settings_container" class="advanced-gateway-settings">
						<h1>
							<?php echo esc_html( $admin_tab_view->get_title() ); ?>
						</h1>
						<p>
							<?php echo esc_html( $admin_tab_view->get_description() ); ?>
						</p>
						<?php
						if ( count( $this->tabs ) > 1 ) {
							$admin_section   = $this;
							$admin_tab_views = $this->tabs;
							require PEACHPAY_ABSPATH . 'core/admin/views/html-secondary-navigation.php';
						}
						?>
						<?php $admin_tab_view->do_admin_view(); ?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Indicates if URL is visiting this section.
	 */
	private function is_active() {
		// PHPCS:disable WordPress.Security.NonceVerification.Recommended
		$page_key    = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		$section_key = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';
		// PHPCS:enable
		return is_admin() && 'peachpay' === $page_key && $this->section === $section_key;
	}

	/**
	 * Formats the section id for display in the breadcrumbs.
	 *
	 * @param string $section The section ID in all lowercase.
	 */
	private static function format_bread_crumb_name( $section ) {
		if ( 'paypal' === $section ) {
			return 'PayPal';
		}
		return ucfirst( $section );
	}

	/**
	 * Gets the URL of a tabbed view in this section.
	 *
	 * @param string $tab_key The tab key to create the URL for.
	 */
	public function get_url( $tab_key = '' ) {
		return admin_url( 'admin.php?page=peachpay&section=' . $this->section . '&tab=' . $tab_key );
	}

	/**
	 * Creates a new PeachPay section page.
	 *
	 * @param string $section The id of the section.
	 * @param array  $tabs The tabs to include in this section.
	 * @param array  $bread_crumbs The intermediate bread crumbs between this section and the root of the PeachPay settings.
	 */
	public static function create( $section, $tabs, $bread_crumbs = array() ) {
		return new self( $section, $tabs, $bread_crumbs );
	}
}
