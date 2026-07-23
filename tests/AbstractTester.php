<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Page\Interfaces\SubMenuPageInterface;
use WP_UnitTestCase;

abstract class AbstractTester extends WP_UnitTestCase {
	use TestCommon;

	/** @var array<string, mixed> */
	protected array $request = array();


	public function set_up(): void {

		parent::set_up();

		$this->request = $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	}


	public function tear_down(): void {

		$_REQUEST = $this->request; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		parent::tear_down();

	}

	public function test_firing_setup_actually_add_hooks(): void {
		$page = $this->get_tested_instance( $this->default );

		$page->setup();

		$this->assertSame( 10, has_filter( 'allowed_options', array( $page, 'maybe_init_option' ) ) );
		$this->assertSame( 10, has_action( 'admin_menu', array( $page, 'menu' ) ) );
	}

	public function test_setup_matches_save_capability(): void {
		$this->default['config']['capability'] = 'edit_posts';
		$page                                  = $this->get_tested_instance( $this->default );

		$page->setup();

		$this->assertSame(
			'edit_posts',
			apply_filters( 'option_page_capability_' . $this->default['menu_slug'], 'manage_options' )
		);
	}


	public function test_setup_does_not_duplicate_capability_filter(): void {

		$page = $this->get_tested_instance( $this->default );

		$page->setup();
		$page->setup();

		global $wp_filter;

		$this->assertCount( 1, $wp_filter[ 'option_page_capability_' . $this->default['menu_slug'] ]->callbacks[10] );

	}


	public function test_create_uses_configured_capability_before_setup(): void {

		$this->default['config']['capability'] = 'edit_posts';
		$page                                  = $this->get_tested_instance( $this->default );
		$user_id                               = self::factory()->user->create( array( 'role' => 'editor' ) );

		$this->assertIsInt( $user_id );
		wp_set_current_user( $user_id );
		ob_start();
		$page->create();
		$output = ob_get_clean();

		$this->assertIsString( $output );
		$this->assertStringContainsString( 'id="submit"', $output );

	}

	/**
	 * @param array<string, array<int, string>> $options
	 * @dataProvider for_maybe_init_option
	 */
	public function test_maybe_init_option( array $options ): void {
		$page = $this->get_tested_instance( $this->default );

		$result = $page->maybe_init_option( $options );

		$this->assertArrayHasKey( $this->default['menu_slug'], $result );

		if ( array_key_exists( $this->default['menu_slug'], $options ) ) {
			$this->assertSame( $options, $result );
		}
	}

	/**
	 * @param array<string, string> $parameters
	 * @dataProvider for_correctly_fired_hooks_and_assigned_variables
	 */
	public function test_menu_method_registers_pages( array $parameters, string $option_group_name ): void {
		$parent_slug = $parameters['parent_slug'];

		unset( $parameters['parent_slug'] );

		$page = $this->get_tested_instance( $parameters );

		if ( '' !== $parent_slug && $page instanceof SubMenuPageInterface ) {
			$page->parent( $parent_slug );
		}

		wp_set_current_user( 1 );
		$page->menu();

		$menu_slug = $option_group_name;
		$hookname  = get_plugin_page_hookname( $menu_slug, $parent_slug );

		$this->assertSame( $hookname, $page->get_hookname() );
		$this->assertSame( 10, has_action( 'load-' . $hookname, array( $page, 'load' ) ) );
		$page->load();

		if ( empty( $parent_slug ) || 'options-general.php' !== $parent_slug ) {
			$this->assertSame( 10, has_action( 'admin_notices', array( $page, 'notices' ) ) );
		}

		$this->assertSame( 1, did_action( 'themeplate_page_' . $menu_slug . '_load' ) );

		global $_registered_pages, $_parent_pages;

		$this->assertArrayHasKey( $hookname, $_registered_pages );
		$this->assertTrue( $_registered_pages[ $hookname ] );
		$this->assertArrayHasKey( $menu_slug, $_parent_pages );

		if ( '' === $parent_slug ) {
			$this->assertFalse( $_parent_pages[ $menu_slug ] );
		} else {
			$this->assertSame( $parent_slug, $_parent_pages[ $menu_slug ] );
		}
	}

	/**
	 * @dataProvider for_notices_method_echoing_a_message
	 */
	public function test_notices_method_echoing_a_message( ?string $page, ?string $updated ): void {
		global $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( null !== $page && '' !== $page ) {
			$_REQUEST['page'] = $page;
		}

		if ( null !== $updated && '' !== $updated ) {
			$_REQUEST['settings-updated'] = $updated;
		}

		ob_start();
		( $this->get_tested_instance( $this->default ) )->notices();
		$output = ob_get_clean();

		$this->assertIsString( $output );

		if ( 'true' === $updated && $page === $this->default['menu_slug'] ) {
			$this->assertNotEmpty( $output );
		} else {
			$this->assertEmpty( $output );
		}

	}

	/**
	 * @param array<string, array<int, string|array<string, mixed>>> $parameters
	 * @dataProvider for_correctly_fired_hooks_and_assigned_variables
	 */
	public function test_create_method_layouts_pages( array $parameters, string $option_group_name ): void {
		add_action( 'themeplate_page_' . $option_group_name . '_content', function (): void {} );
		ob_start();
		( $this->get_tested_instance( $parameters ) )->create();
		ob_get_clean();

		$this->assertSame( 1, did_action( 'themeplate_page_' . $option_group_name . '_content' ) );
		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_after_title' ) );
		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_side' ) );
		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_normal' ) );
		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_advanced' ) );
	}
}
