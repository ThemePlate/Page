<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page\Interfaces;

/** `config()` also accepts `parent_slug`. */
interface SubMenuPageInterface {

	public function parent( string $slug ): self;

}
