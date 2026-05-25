<?php
/**
 * Plugin settings class.
 *
 * Stores all plugin settings as a single option (an object) and
 * registers it with the REST API so the React UI can read/write it.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Settings
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments
 */
class Settings extends Singleton {

	/**
	 * Option key used to store the settings.
	 *
	 * @since 2.0.0
	 */
	const KEY = 'lazy_load_for_comments_settings';

	/**
	 * Legacy option key (plugin v1.x).
	 *
	 * @since 2.0.0
	 */
	const LEGACY_KEY = 'lazy_load_comments';

	/**
	 * Initialize the settings.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		// Migrate the legacy option if needed.
		$this->maybe_migrate();

		// Register the setting with WordPress and the REST API.
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Get the default settings values.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function defaults(): array {
		/**
		 * Filter the default plugin settings.
		 *
		 * Addons can use this to register their own settings.
		 *
		 * @since 2.0.0
		 *
		 * @param array $defaults Default settings.
		 */
		return (array) apply_filters(
			'lazy_load_for_comments_default_settings',
			array(
				// How comments are loaded: 'scroll', 'click' or 'off'.
				'load_method'      => 'scroll',
				// Text shown on the "Load Comments" button (click method).
				'button_text'      => __( 'Load Comments', 'lazy-load-for-comments' ),
				// Button style: 'theme' (inherit) or 'custom' (plugin style).
				'button_style'     => 'theme',
				// Extra CSS classes added to the button.
				'button_class'     => '',
				// Whether to show the loading spinner while fetching.
				'show_loader'      => true,
				// Minimum number of comments required to lazy load.
				'minimum_count'    => 1,
				// Skip lazy loading for search engine bots (better SEO).
				'disable_for_bots' => true,
				// Cache the parsed comments block in a transient for faster REST renders.
				'cache_enabled'    => true,
			)
		);
	}

	/**
	 * Get all plugin settings.
	 *
	 * Missing keys are filled in with their default values.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function all(): array {
		$defaults = $this->defaults();
		$settings = get_option( self::KEY, array() );
		$settings = is_array( $settings ) ? $settings : array();

		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Get a single setting value.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Value returned when the key does not exist.
	 *
	 * @return mixed
	 */
	public function get( string $key, $fallback = null ) {
		$settings = $this->all();

		return array_key_exists( $key, $settings ) ? $settings[ $key ] : $fallback;
	}

	/**
	 * Update a single setting value.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 *
	 * @return bool
	 */
	public function set( string $key, $value ): bool {
		$settings         = $this->all();
		$settings[ $key ] = $value;

		return $this->update( $settings );
	}

	/**
	 * Update the entire settings option.
	 *
	 * @since 2.0.0
	 *
	 * @param array $values Settings values.
	 *
	 * @return bool
	 */
	public function update( array $values ): bool {
		return update_option( self::KEY, $this->sanitize( $values ) );
	}

	/**
	 * Register the setting with WordPress and the REST API.
	 *
	 * Registering it with `show_in_rest` lets the React UI read and
	 * write the option through the `/wp/v2/settings` endpoint.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		register_setting(
			'options',
			self::KEY,
			array(
				'type'              => 'object',
				'description'       => __( 'Lazy Load for Comments settings.', 'lazy-load-for-comments' ),
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => $this->defaults(),
				'show_in_rest'      => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'load_method'      => array(
								'type' => 'string',
								'enum' => array( 'scroll', 'click', 'off' ),
							),
							'button_text'      => array( 'type' => 'string' ),
							'button_style'     => array(
								'type' => 'string',
								'enum' => array( 'theme', 'custom' ),
							),
							'button_class'     => array( 'type' => 'string' ),
							'show_loader'      => array( 'type' => 'boolean' ),
							'minimum_count'    => array( 'type' => 'integer' ),
							'disable_for_bots' => array( 'type' => 'boolean' ),
							'cache_enabled'    => array( 'type' => 'boolean' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Sanitize the settings values before saving.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $values Raw settings values.
	 *
	 * @return array
	 */
	public function sanitize( $values ): array {
		$values   = is_array( $values ) ? $values : array();
		$current  = $this->all();
		$defaults = $this->defaults();
		$clean    = array();

		foreach ( $defaults as $key => $default ) {
			// Keep the current value for any key not in this request.
			$value = array_key_exists( $key, $values ) ? $values[ $key ] : $current[ $key ];

			switch ( $key ) {
				case 'load_method':
					$clean[ $key ] = in_array( $value, array( 'scroll', 'click', 'off' ), true ) ? $value : 'scroll';
					break;
				case 'button_style':
					$clean[ $key ] = in_array( $value, array( 'theme', 'custom' ), true ) ? $value : 'theme';
					break;
				case 'button_text':
					$clean[ $key ] = sanitize_text_field( $value );
					break;
				case 'button_class':
					$classes       = array_filter( array_map( 'sanitize_html_class', preg_split( '/\s+/', (string) $value ) ) );
					$clean[ $key ] = implode( ' ', $classes );
					break;
				case 'minimum_count':
					$clean[ $key ] = max( 1, absint( $value ) );
					break;
				case 'show_loader':
				case 'disable_for_bots':
				case 'cache_enabled':
					$clean[ $key ] = (bool) $value;
					break;
				default:
					$clean[ $key ] = $value;
					break;
			}
		}

		return $clean;
	}

	/**
	 * Migrate the legacy v1.x option to the new settings structure.
	 *
	 * Runs only once: when the new option does not exist yet.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function maybe_migrate(): void {
		// New settings already exist, nothing to do.
		if ( false !== get_option( self::KEY, false ) ) {
			return;
		}

		$settings = $this->defaults();
		$legacy   = get_option( self::LEGACY_KEY, null );

		// Map the legacy integer value to the new load method.
		if ( null !== $legacy ) {
			$map                     = array(
				'2' => 'scroll',
				'1' => 'click',
				'0' => 'off',
			);
			$settings['load_method'] = $map[ (string) $legacy ] ?? 'scroll';
		}

		update_option( self::KEY, $settings );
	}
}
