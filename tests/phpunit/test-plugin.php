<?php
/**
 * Smoke tests for the plugin's identity helpers.
 *
 * Exists to keep `composer test` exercising the autoloader and the
 * bootstrap on every CI run — even before behavioural tests are
 * written. Failing here means the plugin is not loading at all.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

use DuckDev\LazyComments\Plugin;
use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Test_Plugin
 */
class Test_Plugin extends WP_UnitTestCase {

	/**
	 * The plugin constants are defined after bootstrap.
	 */
	public function test_constants_are_defined(): void {
		$this->assertTrue( defined( 'LLC_VERSION' ) );
		$this->assertTrue( defined( 'LLC_FILE' ) );
		$this->assertTrue( defined( 'LLC_DIR' ) );
		$this->assertTrue( defined( 'LLC_URL' ) );
		$this->assertTrue( defined( 'LLC_BASE_NAME' ) );
	}

	/**
	 * The plugin metadata helpers return the expected values.
	 */
	public function test_plugin_metadata(): void {
		$this->assertSame( 'Lazy Load for Comments', Plugin::name() );
		$this->assertSame( LLC_VERSION, Plugin::version() );
		$this->assertSame( 'lazy-load-for-comments', Plugin::SLUG );
		$this->assertSame( 'comments_page_lazy-load-for-comments', Plugin::screen_id() );
	}

	/**
	 * Singletons return the same instance on repeated calls.
	 */
	public function test_singleton_returns_same_instance(): void {
		$first  = \DuckDev\LazyComments\Settings::instance();
		$second = \DuckDev\LazyComments\Settings::instance();

		$this->assertSame( $first, $second );
		$this->assertInstanceOf( Singleton::class, $first );
	}

	/**
	 * The global helper alias resolves to the same Settings instance.
	 */
	public function test_global_helper_alias(): void {
		$this->assertTrue( function_exists( 'lazy_load_for_comments_settings' ) );
		$this->assertSame(
			\DuckDev\LazyComments\Settings::instance(),
			lazy_load_for_comments_settings()
		);
	}
}
