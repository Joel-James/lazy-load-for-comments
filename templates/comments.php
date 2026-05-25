<?php
/**
 * Classic-theme comments template.
 *
 * Loaded in place of the theme's `comments.php` when lazy-loading is
 * active. The whole template just outputs the placeholder produced by
 * {@see \DuckDev\LazyComments\Front\Renderer::placeholder()} — the
 * actual comments are fetched from the REST API on click or scroll.
 *
 * @package LazyComments
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is built and escaped inside Renderer::placeholder().
echo \DuckDev\LazyComments\Front\Renderer::placeholder();
