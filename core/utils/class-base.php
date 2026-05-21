<?php
/**
 * Singleton base class.
 *
 * Extend this class to make a singleton. The optional `init()` method
 * is called once, right after the single instance is created.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Utils;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Base
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Utils
 */
abstract class Base {

	/**
	 * Protect the class from being instantiated directly.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {}

	/**
	 * Get the single instance of the called class.
	 *
	 * @since 2.0.0
	 *
	 * @return static
	 */
	public static function instance() {
		static $instances = array();

		$class = static::class;

		if ( ! isset( $instances[ $class ] ) ) {
			$instances[ $class ] = new static();

			// Optionally initialize the class.
			if ( method_exists( $instances[ $class ], 'init' ) ) {
				$instances[ $class ]->init();
			}
		}

		return $instances[ $class ];
	}
}
