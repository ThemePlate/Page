<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use WP_UnitTestCase;

abstract class AbstractTest extends WP_UnitTestCase {
	use TestCommon;

	public function test_firing_setup_actually_add_hooks(): void {
		$page = $this->get_tested_instance( $this->default );

		$page->setup();

		$this->assertSame( 10, has_action( 'admin_init', array( $page, 'init' ) ) );
		$this->assertSame( 10, has_action( 'admin_menu', array( $page, 'menu' ) ) );
		$this->assertSame( 10, has_action( 'admin_notices', array( $page, 'notices' ) ) );
		$this->assertSame( 10, has_action( 'admin_print_footer_scripts', array( $page, 'footer' ) ) );
		$this->assertSame( 10, has_filter( 'default_option_' . $this->default['menu_slug'], '__return_empty_array' ) );
	}

	/**
	 * @dataProvider for_correctly_fired_hooks_and_assigned_variables
	 */
	public function test_init_method_registers_settings( array $parameters, string $option_group_name ) {
		$page = $this->get_tested_instance( $parameters );

		$page->init();

		global $new_allowed_options, $wp_registered_settings;

		$this->assertArrayHasKey( $option_group_name, $new_allowed_options );
		$this->assertTrue( in_array( $option_group_name, $new_allowed_options[ $option_group_name ], true ) );
		$this->assertSame( 10, has_filter( "sanitize_option_{$option_group_name}", array( $page, 'save' ) ) );
		$this->assertArrayHasKey( $option_group_name, $wp_registered_settings );
		$this->assertSame( array( $page, 'save' ), $wp_registered_settings[ $option_group_name ]['sanitize_callback'] );
		$this->assertSame( array(), $wp_registered_settings[ $option_group_name ]['default'] );
	}

	/**
	 * @dataProvider for_correctly_fired_hooks_and_assigned_variables
	 */
	public function test_menu_method_registers_pages( array $parameters, string $option_group_name ) {
		wp_set_current_user( 1 );
		( $this->get_tested_instance( $parameters ) )->menu();

		$parent_slug = $parameters['parent_slug'];
		$menu_slug   = $option_group_name;
		$hookname    = get_plugin_page_hookname( $menu_slug, $parameters['parent_slug'] );

		global $_registered_pages, $_parent_pages;

		$this->assertArrayHasKey( $hookname, $_registered_pages );
		$this->assertTrue( $_registered_pages[ $hookname ] );
		$this->assertArrayHasKey( $menu_slug, $_parent_pages );

		if ( '' === $parent_slug ) {
			$this->assertFalse( $_parent_pages[ $menu_slug ] );
		} else {
			$this->assertSame( $parameters['parent_slug'], $_parent_pages[ $menu_slug ] );
		}
	}

	/**
	 * @dataProvider for_notices_method_echoing_a_message
	 */
	public function test_notices_method_echoing_a_message( ?string $page, ?string $updated ): void {
		global $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $page ) {
			$_REQUEST['page'] = $page;
		}

		if ( $updated ) {
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
	 * @dataProvider for_correctly_fired_hooks_and_assigned_variables
	 */
	public function test_create_method_layouts_pages( array $parameters, string $option_group_name ) {
		add_action( $option_group_name . '_content', '__return_null' );
		ob_start();
		( $this->get_tested_instance( $parameters ) )->create();
		ob_get_clean();

		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_after_title' ) );
		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_side' ) );
		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_normal' ) );
		$this->assertSame( 1, did_action( 'themeplate_settings_' . $option_group_name . '_advanced' ) );
	}

	/**
	 * @dataProvider for_save_filters_options
	 */
	public function test_save_filters_options( ?array $input, ?array $expected ): void {
		$output = ( $this->get_tested_instance( $this->default ) )->save( $input );

		$this->assertSame( $expected, $output );
	}

	public function test_footer_method(): void {
		ob_start();
		( $this->get_tested_instance( $this->default ) )->footer();
		$output = ob_get_clean();

		$this->assertIsString( $output );
	}
}
