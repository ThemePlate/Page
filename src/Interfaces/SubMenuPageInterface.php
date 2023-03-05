<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page\Interfaces;

interface SubMenuPageInterface {

	public function parent( string $parent ): self;

}
