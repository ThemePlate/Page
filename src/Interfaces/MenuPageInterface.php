<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page\Interfaces;

/** `config()` also accepts `icon_url`. */
interface MenuPageInterface {

	public function icon( string $url ): self;

}
