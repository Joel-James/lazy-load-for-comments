=== Lazy Load for Comments ===
Contributors: joelcj91,duckdev
Tags: lazy load, comments, lazyload comments, page speed, performance
Donate link: https://paypal.me/JoelCJ
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Speed up your posts by lazy-loading the WordPress comments on scroll or click. Works with classic and block themes.

== Description ==

Comments are heavy. A long thread can pull in dozens of avatars, gravatar lookups and reply scripts — all of which the browser downloads before the visitor has even reached them. **Lazy Load for Comments** defers all of that until the visitor either scrolls to the comments area or clicks a button.

The result: fewer HTTP requests, faster Largest Contentful Paint and a happier Lighthouse score, without changing how your comment template looks.

Version 2.0 is a complete rewrite. It uses the WordPress REST API to fetch the rendered comments on demand and works with both **classic themes** (`comments_template()`) and **block themes** (the core Comments block) out of the box.

> #### Features
>
> * Lazy-load comments on scroll, on button click, or turn the plugin off without deactivating it.
> * Works with classic themes and block themes (the core Comments block).
> * Loads comments inline for search engine crawlers, so indexing and SEO are not affected.
> * Per-post cache of the rendered Comments block — REST renders stay fast.
> * Minimum-comment-count threshold so short threads still render inline.
> * Customisable load-button text, style and extra CSS classes.
> * Clean React settings page under *Comments → Lazy Load*.
> * No jQuery dependency. Front-end script is vanilla JS and intentionally small.
> * Translation-ready. Hooks and filters available for further customisation.

**Useful links**

* [Plugin website](https://duckdev.com/product/lazy-load-for-comments/)
* [Documentation](https://docs.duckdev.com/lazy-load-for-comments/)
* [Support forum](https://wordpress.org/support/plugin/lazy-load-for-comments/)

== Installation ==

**From the WordPress admin (recommended)**

1. Go to *Plugins → Add New*, search for **Lazy Load for Comments** and click *Install now*.
2. Activate the plugin.
3. Open *Comments → Lazy Load* and pick the load method that suits you.

**Manual install**

1. Upload the `lazy-load-for-comments` folder to `/wp-content/plugins/`.
2. Activate the plugin from the *Plugins* screen.
3. Open *Comments → Lazy Load* to configure the settings.

For the full configuration guide, see the [official documentation](https://docs.duckdev.com/lazy-load-for-comments/loading-behaviour).

== Frequently Asked Questions ==

= Does it work with block themes? =

Yes. The plugin detects whether the active theme is a block theme and either swaps the classic `comments_template()` output or replaces the rendered `core/comments` block, whichever applies.

= Will it work with my caching plugin? =

Yes. The placeholder rendered server-side is plain HTML, so full-page caches can cache it safely. The actual comments are fetched from a public REST endpoint, which is also cache-friendly.

= How do I change the load method? =

Open *Comments → Lazy Load* and pick one of **On scroll**, **On button click** or **Disabled** on the *Settings* tab.

= How do I customise the load button? =

The button text, the style (inherit your theme's style or use the plugin's built-in style) and extra CSS classes can all be set under *Load Button* on the *Settings* tab.

= Does it support comment plugins like Disqus or Jetpack Comments? =

No — this plugin only handles the **default** WordPress comments. Third-party comment systems (Disqus, Jetpack Comments, etc.) replace the comment area entirely, so there is nothing for this plugin to lazy-load.

= How do I disable lazy loading for a specific post or page? =

Use the `lazy_load_for_comments_can_lazy_load` filter:

`
add_filter( 'lazy_load_for_comments_can_lazy_load', function ( $can ) {
    if ( is_page( 'contact' ) ) {
        return false;
    }
    return $can;
} );
`

= Are comments still indexed by Google? =

Yes. By default the plugin detects search-engine crawlers from the User-Agent and serves them the original (inline) comments markup, so indexing is not affected. You can turn this behaviour off on the *Settings* tab if you'd rather always lazy-load.

= Where can I get help? =

The [official documentation](https://docs.duckdev.com/lazy-load-for-comments/) covers most setups. For everything else, the free [WordPress.org support forum](https://wordpress.org/support/plugin/lazy-load-for-comments/) is the best place to start.

== Screenshots ==

1. Loading behaviour settings.
2. Load button settings.
3. Cache management.

== Changelog ==

= 2.0.0 =

* Complete rewrite using React and the WordPress REST API.
* Added support for block themes (the core Comments block).
* New React settings page under *Comments → Lazy Load*.
* Settings for load method, minimum comment count, button text, button style and loader visibility.
* Per-post cache of the rendered comments block, with a "Clear comments cache" action in the settings.
* Crawlers receive the inline comments by default so SEO is preserved.
* Removed the dependency on jQuery.
* Existing v1.x load-method setting is migrated automatically on upgrade.

= 1.0.10 =

* Added compatibility for the Divi theme builder.

== Upgrade Notice ==

= 2.0.0 =

Major rewrite with React, REST API and block theme support. Your existing load-method setting is migrated automatically — no manual steps required.
