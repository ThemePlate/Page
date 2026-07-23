<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page\Interfaces;

interface PageInterface {

	/**
	 * @param array{
	 *     capability?: string,
	 *     menu_title?: string,
	 *     menu_slug?: string,
	 *     position?: int,
	 *     icon_url?: string,
	 *     parent_slug?: string,
	 * } $config
	 */
	public function config( array $config ): self;

	public function capability( string $capability ): self;

	public function title( string $title ): self;

	public function slug( string $slug ): self;

	public function position( int $position ): self;

	public function menu(): void;

}
