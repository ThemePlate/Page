<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page;

use ThemePlate\Page\Interfaces\MenuPageInterface;

class MenuPage extends BasePage implements MenuPageInterface {

	public function __construct( string $title, array $config = array() ) {

		$this->defaults['icon_url'] = '';

		if ( array() !== $config ) {
			_deprecated_argument( __METHOD__, '2.5.0', 'Use the new ' . esc_html( self::class . '::config()' ) . ' instead.' );
		}

		$this->initialize( $title, $config );

	}


	public function icon( string $url ): self {

		$this->config['icon_url'] = $url;

		return $this;

	}


	public function menu(): void {

		$config = $this->config;

		$this->hookname = add_menu_page(
			// Page Title
			$this->title,
			// Menu Title
			$config['menu_title'],
			// Capability
			$config['capability'],
			// Menu Slug
			$config['menu_slug'],
			// Content Function
			array( $this, 'create' ),
			// Icon URL
			$config['icon_url'],
			// Menu Order
			$config['position']
		);

		add_action( 'load-' . $this->hookname, array( $this, 'load' ) );

	}

}
