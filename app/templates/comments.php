<?php
/**
 * Classic theme comments template.
 *
 * Loaded in place of the theme's comments.php when lazy loading is
 * active. It only outputs the lazy load placeholder — the real
 * comments are fetched from the REST API on click or scroll.
 *
 * @package LazyComments
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

echo DuckDev\LazyComments\Front\Comments::instance()->placeholder(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
