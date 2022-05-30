<?php

/**
 * Setup options page
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate\Page;

interface CommonInterface {

	public function setup(): void;

	public function init(): void;

	public function menu(): void;

	public function notices(): void;

	public function create(): void;

	public function save( array $options ): array;

	public function footer(): void;

}
