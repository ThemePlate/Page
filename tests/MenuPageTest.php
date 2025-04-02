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
}
