<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page;

use _WP_Editors;
use ThemePlate\Core\Helper\Box;

abstract class BasePage implements CommonInterface {

	protected array $defaults = array(
		'capability' => 'manage_options',
		'menu_title' => '',
		'menu_slug'  => '',
		'position'   => null,
	);
	protected array $config;
	protected string $title;


	protected function initialize( string $title, array $config ) {

		$this->title = $title;

		if ( empty( $config['menu_title'] ) ) {
			$config['menu_title'] = $this->title;
		}

		if ( empty( $config['menu_slug'] ) ) {
			$config['menu_slug'] = $config['menu_title'];
		}

		$config['menu_slug'] = sanitize_title( $config['menu_slug'] );

		$this->config = array_merge( $this->defaults, $config );

	}


	public function setup(): void {

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'footer' ) );

	}


	public function init(): void {

		$option = $this->config['menu_slug'];

		register_setting( $option, $option, array( $this, 'save' ) );

	}


	public function notices(): void {

		if ( ! isset( $_REQUEST['page'], $_REQUEST['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		if ( $_REQUEST['page'] === $this->config['menu_slug'] && 'true' === $_REQUEST['settings-updated'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			echo '<div id="themeplate-message" class="updated"><p><strong>Settings updated.</strong></p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

	}


	public function create(): void {

		$page = $this->config['menu_slug'];

		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post">
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<?php if ( has_action( $page . '_content' ) || has_action( 'themeplate_settings_' . $page . '_after_title' ) ) : ?>
							<div id="post-body-content">
								<div id="after_title-sortables" class="meta-box-sortables">
									<?php do_action( 'themeplate_settings_' . $page . '_after_title' ); ?>
								</div>

								<?php do_action( $page . '_content' ); ?>
							</div>
						<?php endif; ?>

						<div id="postbox-container-1" class="postbox-container">
							<div id="submitdiv" class="postbox">
								<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

								<div id="major-publishing-actions">
									<?php settings_fields( $page ); ?>

									<?php if ( current_user_can( apply_filters( 'option_page_capability_' . $page, 'manage_options' ) ) ) : ?>
										<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
									<?php else : ?>
										<p><strong>Need a higher level access to save changes.</strong></p>
									<?php endif; ?>
								</div>
							</div>

							<div id="side-sortables" class="meta-box-sortables">
								<?php do_action( 'themeplate_settings_' . $page . '_side' ); ?>
							</div>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<div id="normal-sortables" class="meta-box-sortables">
								<?php do_action( 'themeplate_settings_' . $page . '_normal' ); ?>
							</div>

							<div id="advanced-sortables" class="meta-box-sortables">
								<?php do_action( 'themeplate_settings_' . $page . '_advanced' ); ?>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>

		<?php

	}


	public function save( ?array $options ): ?array {

		return Box::prepare_save( $options );

	}


	public function footer(): void {

		require_once ABSPATH . 'wp-includes/class-wp-editor.php';
		_WP_Editors::wp_link_dialog();

	}

}
