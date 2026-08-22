=== Chatty AI Chatbot ===
Contributors: personaliai
Tags: chatbot, ai, live chat, customer support, chat widget
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.2
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add the Chatty AI chatbot to your WordPress site in seconds — no code editing required.

== Description ==

Chatty is an AI-powered chat widget that answers visitor questions using your own content, captures leads, and books meetings — right from your website.

This plugin adds the Chatty widget to every page of your WordPress site. Just paste your Bot ID from the [Chatty dashboard](https://chatty.personaliai.com/dashboard) and you're done — no theme editing, no code.

= Features =

* Zero code editing — configure everything from Settings → Chatty Widget
* Works with any theme, including block themes
* Customize launcher position, accent color, and behavior
* Automatically stays in sync with your bot's configuration in the Chatty dashboard
* Lightweight — the full chat interface only loads when a visitor opens it
* Works out of the box with WooCommerce (runs on WordPress, no extra setup needed)
* Control exactly where the widget shows: all pages, only specific pages/URL patterns, or everywhere except a chosen list
* Optionally hide the widget for logged-in administrators

= Requirements =

You need a Chatty account and a bot set up at [chatty.personaliai.com](https://chatty.personaliai.com) before installing this plugin.

== Installation ==

1. Upload the `chatty-widget` folder to `/wp-content/plugins/`, or install directly from the WordPress Plugin Directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings → Chatty Widget and paste your Bot ID.
4. Save changes — the widget now appears on your site.

== Frequently Asked Questions ==

= Where do I find my Bot ID? =

In your Chatty dashboard, select your bot, then open the "Embed & Integrate" tab.

= Does this slow down my site? =

No — the widget script loads deferred (non-blocking), and the full chat interface only loads when a visitor actually clicks to open it.

= Does this work with WooCommerce? =

Yes — WooCommerce runs on WordPress, so the widget appears on all your store pages automatically once activated.

= I entered my Bot ID but don't see the widget =

Make sure you clicked "Save Changes" on the settings page, then check your site's front end (not wp-admin) — the launcher button appears in the bottom corner. If you're using a caching plugin, clear its cache after changing settings.

= I only want the widget on certain pages =

Go to Settings → Chatty Widget and set "Display Mode" to "Include Only", then list the page IDs or URL patterns (one comma-separated list, e.g. `12, 45, /about*`) you want it to appear on. Choose "Exclude" to do the opposite — show everywhere except those pages.

== Changelog ==

= 1.1.0 =
* Add page targeting: show the widget on all pages, only specific pages/URL patterns, or everywhere except a chosen list.
* Add an option to hide the widget for logged-in administrators.
* Validate the Bot ID and accent color on save, with a clear admin notice if either is invalid.

= 1.0.0 =
* Initial release.
