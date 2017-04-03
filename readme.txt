=== Lazy Load for Comments ===
Contributors: joelcj91,duckdev
Tags: lazy load, lazy comments, conditional comments, lazyload comments, lazyload wordpress comments, comments
Donate link: https://paypal.me/JoelCJ
Requires at least: 4.0
Tested up to: 4.7
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lazy load default WordPress commenting system on scroll or click. Improve page speed.

== Description ==

Lazy load WordPress default commenting system without any complex configurations. Get rid of unwanted HTTP requests and get your page speed back.


> #### Lazy Load for Comments - Features & Advantages
>
> - Load comments only when required.<br />
> - **Improve page loading speed.**<br />
> - Reduce no. of HTTP requests!<br />
> - Lazy loading comments gravaters.
> - **Translation ready!**<br />
> - No complex configurations (Just one setting).<br />
> - Developer friendly (Hooks available for altering).<br />
> - Follows best WordPress coding standards.<br />
> - Of course, available on [GitHub](https://github.com/joel-james/lazy-load-comments)<br />
>
> [Installation](https://wordpress.org/plugins/lazy-load-for-comments/installation/) | [Screenshots](https://wordpress.org/plugins/lazy-load-for-comments/screenshots/)



== Installation ==


= Installing the plugin - Simple =
1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for **Lazy Load for Comments** and click "*Install now*"
2. Alternatively, download the plugin and upload the contents of `lazy-load-for-comments.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
3. Activate the plugin
4. Go to Discussion settings.
5. Select the lazy load method (Scroll or Click).


= Need more help? =
Feel free to [open a support request](http://wordpress.org/support/plugin/lazy-load-for-comments/).

= Missing something? =
If you would like to have an additional feature for this plugin, [let me know](https://thefoxe.com/contact/)

== Frequently Asked Questions ==

= What is the use of this plugin? =

Plugin name says everything. This plugin prevents the comments from loading automatically when page/post is loaded. Instead, this plugin will lazy load the comments when user scroll down to comments section or clicking on comment button.

= I don't need loader gif image, can I disable it? =

Yes, you can!. Just add following line to your theme's functions.php or in your custom plugin.


`
add_filter( 'llc_enable_loader_element', '__return_false' );
`

= How can I change the button text? =

Just add following line to your theme's functions.php or in your custom plugin.


`
add_filter( 'llc_button_text', function () {
    return 'My Custom Button Text';
});
`

= How can I add a custom class to the button? =

Add following line to your theme's functions.php or in your custom plugin.


`
add_filter( 'llc_button_class', function () {
    return 'custom-class-1 custom-class-2';
});
`

= Can I use something else instead of loader image? =

Yes! There is a filter for this too! Add following line to your theme's functions.php or in your custom plugin.


`
add_filter( 'llc_loader_element_content', function () {
    // Use any html element.
    return '<p class="custom-loader">Loading... Please wait.</p>';
});
`

= I need more details =

Please [open a support request](http://wordpress.org/support/plugin/lazy-load-for-comments/).


== Other Notes ==

= Bug Reports =

Bug reports are always welcome. [Report here](https://thefoxe.com/contact/).


== Screenshots ==

1. **Settings** - Select lazy load method.


== Changelog ==

= 1.0.3 (03/04/2017) =

- Fixed wrong value return in bot checking function.

= 1.0.2 (08/02/2017) =

- Added custom filters.
- Added loader while comments are being loaded.
- Disabled lazy load for Search Engine crawlers.
- Scroll to comment if #comment id found in url.
- 100% translation ready.

= 1.0.0.1 (19/11/2016) =

- Bug fix on PHP v5.3 - Parse error.

= 1.0.0 (18/11/2016) =

- First version.

== Upgrade Notice ==

= 1.0.3 (03/04/2017) =

- Fixed wrong value return in bot checking function.