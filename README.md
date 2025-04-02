# ThemePlate Page

## Usage

```php
use ThemePlate\Page\MenuPage;
use ThemePlate\Page\SubMenuPage;

// One-liner
( new MenuPage( 'Theme Options' ) )->setup();
( new SubMenuPage( 'Plugin Settings' ) )->parent( 'plugins.php' )->setup();
```

### Available config
```php
$args = array(
	'menu_title' => 'Site Reports',
	'icon_url'   => 'dashicons-printer',
	'position'   => 2,
);

( new MenuPage( 'Available Reports' ) )->config( $args )->setup();

// or

( new MenuPage( 'Available Reports' ) )
	->title( 'Site Reports' )
	->icon( 'dashicons-printer' )
	->position( 2 )
	// ->capability( 'moderate_comments' )
	// ->slug( 'site-reports' )
	->setup();
```

```php
$args = array(
	// Used as the settings group name
	'menu_slug'  => 'site-reports/print-download',
	'capability' => 'moderate_comments',
);

( new SubMenuPage( 'Print or Download' ) )->config( $args )->parent( 'site-reports' )->setup();

// or

( new SubMenuPage( 'Print or Download' ) )
	->slug( 'site-reports/print-download' )
	->capability( 'moderate_comments' )
	->parent( 'site-reports' )
	// ->position( 2 )
	// ->title( 'Print / Download' )
	->setup();
```
