<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Page\CommonInterface;
use ThemePlate\Page\SubMenuPage;

class SubMenuPageTest extends AbstractTester {
	protected function get_tested_instance( array $args ): CommonInterface {
		return ( new SubMenuPage( $args['page_title'], '' ) )->config( $args['config'] );
	}

	public function test_deprecated_argument(): void {
		$this->setExpectedDeprecated( SubMenuPage::class . '::__construct' );

		new SubMenuPage( 'Test', 'parent' );
	}

	public function test_menu_does_not_register_load_hook_without_parent(): void {
		$page = ( new SubMenuPage( 'Test' ) )->parent( 'missing-parent' );

		$page->menu();

		$this->assertSame( '', $page->get_hookname() );
		$this->assertFalse( has_action( 'load-', array( $page, 'load' ) ) );
	}


	public function test_parent_is_fluent_and_sanitizes_parent_slug(): void {

		$config = array();
		$page   = new SubMenuPage( 'Test' );

		add_action(
			'themeplate_page_test_load',
			static function ( string $hookname, array $loaded_config ) use ( &$config ): void {
				$config = $loaded_config;
			},
			10,
			2
		);

		$this->assertSame( $page, $page->parent( '<b>options-general.php</b>' ) );
		$page->load();

		$this->assertSame( 'options-general.php', $config['parent_slug'] );

	}
}
