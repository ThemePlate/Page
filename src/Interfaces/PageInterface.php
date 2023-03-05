<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page\Interfaces;

interface PageInterface {

	public function capability( string $capability ): self;

	public function position( int $position ): self;

	public function menu(): void;

}
