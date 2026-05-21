=== Lazy Load for Comments ===
Contributors: joelcj91,duckdev
Tags: lazy load, comments, lazyload comments, page speed, performance
Donate link: https://paypal.me/JoelCJ
Requires at least: 5.9
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lazy load the default WordPress comments on scroll or click. Works with both classic and block themes. Improve page speed.

== Description ==

Lazy Load for Comments stops the WordPress comments from loading with the rest of the page. Instead, the comments are loaded only when the visitor scrolls to the comments area or clicks a button. This cuts down HTTP requests (comment avatars, scripts) and improves page load time.

Version 2.0 is a complete rewrite using React and the WordPress REST API. It now works with **both classic themes and modern block themes**.

> #### Features
>
> - Lazy load comments on scroll or on button click.
> - Works with classic themes and block themes (the Comments block).
> - Loads comments normally for search engine bots, so SEO is not affected.
> - Simple React based settings page under the Comments menu.
> - Customisable load button text and CSS classes.
> - Optional minimum comment count before lazy loading kicks in.
> - Developer friendly — hooks available for customisation.
> - Translation ready.

== Installation ==

1. In your WordPress admin panel, go to *Plugins > Add New*, search for **Lazy Load for Comments** and click *Install now*.
2. Alternatively, upload the `lazy-load-for-comments` folder to `/wp-content/plugins/`.
3. Activate the plugin.
4. Go to *Comments > Lazy Load* to configure the settings.

== Frequently Asked Questions ==

= Does it work with block themes? =

Yes. Version 2.0 supports both classic themes (using `comments_template()`) and block themes that render the core Comments block.

= How do I change the load method? =

Go to *Comments > Lazy Load* and choose between "On scroll", "On button click" or "Disabled".

= How do I change the button text or styling? =

The button text and extra CSS classes can be set on the *Load Button* tab of the settings page.

= How can I disable lazy loading for specific posts? =

Use the `lazy_load_for_comments_can_lazy_load` filter:

`
add_filter( 'lazy_load_for_comments_can_lazy_load', function ( $can ) {
    if ( is_page( 'contact' ) ) {
        return false;
    }
    return $can;
} );
`

== Changelog ==

= 2.0.0 =

- Complete rewrite using React and the WordPress REST API.
- Added support for block themes (the core Comments block).
- New React based settings page under the Comments menu.
- Settings for load method, minimum comment count, loader and button.
- Removed the dependency on jQuery.

= 1.0.10 =

- Added support for Divi.

== Upgrade Notice ==

= 2.0.0 =

Major rewrite with React and block theme support. Your existing load method setting is migrated automatically.
