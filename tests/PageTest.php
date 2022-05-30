<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Page\Page;
use WP_UnitTestCase;

class PageTest extends WP_UnitTestCase {
	public function test_actions(): void {
		$config = array(
			'id'    => 'test',
			'title' => 'Tester',
		);

		$page = new Page( $config );

		$this->assertSame( 10, has_action( 'admin_init', array( $page, 'init' ) ) );
		$this->assertSame( 10, has_action( 'admin_menu', array( $page, 'menu' ) ) );
		$this->assertSame( 10, has_action( 'admin_notices', array( $page, 'notices' ) ) );
		$this->assertSame( 10, has_action( 'admin_print_footer_scripts', array( $page, 'footer' ) ) );
	}
}
