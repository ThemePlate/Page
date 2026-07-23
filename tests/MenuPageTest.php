<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Page\CommonInterface;
use ThemePlate\Page\MenuPage;

class MenuPageTest extends AbstractTester {
	protected function get_tested_instance( array $args ): CommonInterface {
		return ( new MenuPage( $args['page_title'] ) )->config( $args['config'] );
	}

	public function test_deprecated_argument(): void {
		$this->setExpectedDeprecated( MenuPage::class . '::__construct' );

		new MenuPage( 'Test', array( 'icon_url' => 'test' ) );
	}

	public function test_config_ignores_unknown_keys(): void {
		$config = array();
		$page   = ( new MenuPage( 'Test' ) )->config( array( 'unknown' => 'value' ) );

		add_action(
			'themeplate_page_test_load',
			static function ( string $hookname, array $loaded_config ) use ( &$config ): void {
				$config = $loaded_config;
			},
			10,
			2
		);

		wp_set_current_user( 1 );
		$page->menu();
		$page->load();

		$this->assertArrayNotHasKey( 'unknown', $config );
	}

	public function test_config_preserves_zero_slug(): void {
		$page = ( new MenuPage( 'Test' ) )->config( array( 'menu_slug' => '0' ) );

		wp_set_current_user( 1 );
		$page->menu();

		$this->assertSame( 'toplevel_page_0', $page->get_hookname() );
	}


	public function test_icon_is_fluent_and_updates_page_config(): void {

		$config = array();
		$page   = new MenuPage( 'Test' );

		add_action(
			'themeplate_page_test_load',
			static function ( string $hookname, array $loaded_config ) use ( &$config ): void {
				$config = $loaded_config;
			},
			10,
			2
		);

		$this->assertSame( $page, $page->icon( 'dashicons-admin-generic' ) );
		$page->load();

		$this->assertSame( 'dashicons-admin-generic', $config['icon_url'] );

	}
}
