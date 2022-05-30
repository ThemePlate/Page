<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Page\CommonInterface;
use ThemePlate\Page\SubMenuPage;

trait TestCommon {
	protected array $default = array(
		'page_title'  => 'Tester',
		'parent_slug' => 'options-general.php',
		'capability'  => 'moderate_comments',
		'menu_title'  => 'Test',
		'menu_slug'   => 'tester',
		'position'    => 2,
		'config'      => array(),
	);

	abstract protected function get_tested_instance( array $args ): CommonInterface;

	protected function generate_data_for_register_settings( string $page_title, string $menu_title, string $menu_slug, string $option_group_name ): array {
		$config = array();

		if ( '' !== $menu_title ) {
			$config['menu_title'] = $menu_title;
		}

		if ( '' !== $menu_slug ) {
			$config['menu_slug'] = $menu_slug;
		}

		$parent_slug = '';

		if ( $this->get_tested_instance( $this->default ) instanceof SubMenuPage ) {
			$parent_slug = $this->default['parent_slug'];
		}

		return array( compact( 'page_title', 'parent_slug', 'config' ), $option_group_name );
	}

	public function for_correctly_fired_hooks_and_assigned_variables(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'with a space in page title' => $this->generate_data_for_register_settings(
				'Plugin Settings',
				'',
				'',
				'plugin-settings',
			),
			'with multiple space in page title' => $this->generate_data_for_register_settings(
				'Print or Download',
				'',
				'',
				'print-or-download',
			),
			'with custom menu title' => $this->generate_data_for_register_settings(
				'Print or Download',
				'Custom Menu',
				'',
				'custom-menu',
			),
			'with custom menu slug' => $this->generate_data_for_register_settings(
				'Print or Download',
				'Custom Menu',
				'my-custom_option',
				'my-custom_option',
			),
			'with crazy menu slug' => $this->generate_data_for_register_settings(
				'Print or Download',
				'Custom Menu',
				' e!xt@ a_1-2$ %',
				'ext-a_1-2',
			),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	public function for_notices_method_echoing_a_message(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'with nothing on request' => array(
				null,
				null,
			),
			'with a page only on request' => array(
				$this->default['menu_slug'],
				null,
			),
			'with updated only on request' => array(
				null,
				'true',
			),
			'with both on request' => array(
				$this->default['menu_slug'],
				'true',
			),
			'with page but not updated' => array(
				$this->default['menu_slug'],
				'false',
			),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	public function for_save_filters_options(): array {
		$empty_like_values = array(
			'',
			false,
			null,
			0,
			array(),
		);

		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'with nothing passed' => array(
				null,
				array(),
			),
			'with sequential empty-like' => array(
				$empty_like_values,
				$empty_like_values,
			),
			'with associative empty-like' => array(
				array(
					'test0' => $empty_like_values[0],
					'test1' => $empty_like_values[1],
					'test2' => $empty_like_values[2],
					'test3' => $empty_like_values[3],
					'test4' => $empty_like_values[4],
				),
				array(
					'test0' => $empty_like_values[0],
					'test1' => $empty_like_values[1],
					'test2' => $empty_like_values[2],
					'test3' => $empty_like_values[3],
					'test4' => $empty_like_values[4],
				),
			),
			'with empty-like 2nd level' => array(
				array(
					'test1' => 'test',
					'test2' => $empty_like_values,
					'test3' => array(
						'test4' => 'test0',
						'test5' => $empty_like_values,
					),
				),
				array(
					'test1' => 'test',
					'test2' => array(),
					'test3' => array(
						'test4' => 'test0',
					),
				),
			),
			'with empty-like deep level' => array(
				array(
					'test1' => 'test',
					'test2' => $empty_like_values,
					'test3' => array(
						'test4' => 'test0',
						'test5' => $empty_like_values,
						'test6' => array(
							'test7' => 'test8',
							'test9' => $empty_like_values,
						),
					),
				),
				array(
					'test1' => 'test',
					'test2' => array(),
					'test3' => array(
						'test4' => 'test0',
						'test6' => array(
							'test7' => 'test8',
						),
					),
				),
			),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}
}
