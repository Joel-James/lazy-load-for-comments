<?php
/**
 * Reusable singleton base class.
 *
 * Extend this class to make any subclass a singleton: only one instance
 * is ever created, and the optional `init()` hook is called once, right
 * after that instance is built. The implementation prevents cloning and
 * unserialising so the single instance contract cannot be broken.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Utils;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Singleton
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Utils
 */
abstract class Singleton {

	/**
	 * Hide the constructor so subclasses cannot be instantiated directly.
	 *
	 * Use {@see Singleton::instance()} to retrieve the shared instance.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {}

	/**
	 * Prevent cloning so the single instance contract is preserved.
	 *
	 * Marked `final` so subclasses cannot relax the guard. The `: void`
	 * return type is intentionally omitted — PHP 7.4 (our minimum
	 * supported version) rejects return-type declarations on `__clone()`.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	final public function __clone() {}

	/**
	 * Prevent unserialising so the single instance contract is preserved.
	 *
	 * Marked `final` so subclasses cannot relax the guard. The `: void`
	 * return type is intentionally omitted — PHP 7.4 rejects return-type
	 * declarations on `__wakeup()`.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	final public function __wakeup() {}

	/**
	 * Get the single shared instance of the called subclass.
	 *
	 * The first call to this method on a given subclass builds and stores
	 * the instance, then calls the optional `init()` hook on it. Every
	 * subsequent call returns the same object.
	 *
	 * @since 2.0.0
	 *
	 * @return static The single instance of the called subclass.
	 */
	public static function instance() {
		// One bucket per concrete subclass: keyed by class name so multiple
		// singletons (Settings, Core, Admin\Menu, ...) can coexist.
		static $instances = array();

		$class = static::class;

		if ( ! isset( $instances[ $class ] ) ) {
			$instances[ $class ] = new static();

			// Subclasses may declare a protected `init()` method to run
			// one-time set up (registering hooks, loading defaults, etc.).
			if ( method_exists( $instances[ $class ], 'init' ) ) {
				$instances[ $class ]->init();
			}
		}

		return $instances[ $class ];
	}
}
