=== Plugin Name ===
Contributors: virus-2k
Donate link: http://www.riyuk.de/
Tags: youtube, bitly, facebook, jquery
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 0.1.6

Shortens every Post with Bitly. Youtube scroller integration like Facebook. Requires & Loads jQuery

== Description ==

Currently this Plugin is in Development-Phase.

= You need at least PHP v5.3.0 in order to use this Plugin! =

= Attention JavaScript Users =

This Plugin will *install* jQuery on the Blog.

* If you use another Javascript Plugin you should not use this Plugin.
* If you already have included jQuery on your Blog remove it first if you activate this Plugin
* The latest jQuery Version will be included via Google Code


== Installation ==

1. Upload the `riyuk`-Folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You can active the Youtube Plugin in the Admin menu.

= Extra Feature `Bitly`-Support: =

1. Place `Short URL: <?php echo get_post_meta( get_the_ID(), 'short_url', true ); ?>` in your templates. Only works in `the_post`-While Templates.
1. Point above only works if you filled out your Bitly API-Username/Key in the Admin menu.

== Frequently Asked Questions ==

= FAQ needed? =

*Yes. But not yet.*

== Screenshots ==

Currently no Screenshots are available.

== Changelog ==

= 0.1.6 =
* jQuery now works with compatibility mode.

= 0.1.5 = 
* fixed double Javascript-Code

= 0.1.4 =
* PHP Dependencies active

= 0.1.1 - 0.1.3 =
* SVN fix

= 0.1 =
* Public release

== Upgrade Notice ==

- there's no upgrade notice at this time.

