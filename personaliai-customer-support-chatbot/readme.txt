=== PersonaliAI Customer Support Chatbot ===
Contributors: tharindudama
Tags: chatbot, ai chatbot, live chat, customer support, chat widget
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add the PersonaliAI AI customer support chatbot to your WordPress site in seconds — trained on your website content, zero code required.

== Description ==

Chatty is an AI customer support and lead generation chat widget that answers visitor questions using your own website content, captures qualified leads, and automates support — 24/7 without coding.

This plugin seamlessly embeds your Chatty AI chatbot onto your WordPress site. Simply paste your Bot ID from your [Chatty dashboard](https://chatty.personaliai.com/dashboard) and save.

= Key Features =

* **Zero Code Required** — Configure everything from Settings → Chatty Widget.
* **Trained on Your Content** — Answers questions based on your website pages, uploaded docs, and custom knowledge base.
* **Native Shadow DOM Widget** — Featherweight, non-blocking script with crisp rendering and zero iframe styling conflicts.
* **Lead Capture & Actions** — Collect visitor names, emails, and phone numbers directly inside the chat.
* **Works with Any Theme** — 100% compatible with classic themes, full-site editing (FSE) block themes, and page builders like Elementor, Divi, and Beaver Builder.
* **WooCommerce Ready** — Automatically works on WooCommerce product pages and checkouts to help answer pre-sale questions.
* **Page-Level Targeting** — Choose to display the chat widget across your whole site, on specific pages/URLs only, or exclude it from certain pages.
* **Admin Visibility Toggle** — Option to hide the widget for logged-in administrators while working on the site.
* **Custom Appearance** — Customize accent color, launcher position (bottom-right / bottom-left), mobile fullscreen behavior, proactive greeting bubbles, and chime sounds.

= Requirements & Third-Party Service =

This plugin requires an active [Chatty](https://chatty.personaliai.com) account and a bot created in your dashboard.

This plugin relies on the Chatty AI cloud service to deliver chatbot responses, process visitor queries, and render the chat widget interface.

* **Service Name**: Chatty AI Platform (provided by PersonaliAI)
* **Service Website**: [https://chatty.personaliai.com](https://chatty.personaliai.com)
* **Widget Delivery**: Enqueues the widget script from `https://chatty.personaliai.com/widget.js`.
* **Data Transmission**: When website visitors interact with the chat widget, their messages and session identifiers are transmitted securely over HTTPS to the Chatty API (`https://api.chatty.personaliai.com`) to generate real-time AI responses using your configured bot and knowledge base.
* **Terms of Service**: [https://chatty.personaliai.com/terms](https://chatty.personaliai.com/terms)
* **Privacy Policy**: [https://chatty.personaliai.com/privacy](https://chatty.personaliai.com/privacy)

== Installation ==

1. In your WordPress admin, go to **Plugins → Add New** and search for `PersonaliAI Customer Support Chatbot` (or upload the `personaliai-customer-support-chatbot.zip` file).
2. Click **Install Now**, then click **Activate**.
3. Go to **Settings → Chatty Widget** in your WordPress admin menu.
4. Paste your **Bot ID** (available under the "Embed & Integrate" tab in your [Chatty Dashboard](https://chatty.personaliai.com/dashboard)).
5. Click **Save Changes** — your AI chatbot is now live on your site!

== Frequently Asked Questions ==

= Where do I find my Bot ID? =

Log in to your [Chatty Dashboard](https://chatty.personaliai.com/dashboard), select your bot, and open the **Embed & Integrate** tab. Your Bot ID is shown right at the top.

= Does this slow down my website? =

No. The widget script is loaded asynchronously with the `defer` strategy in the footer. It does not block page rendering, and the chat conversation window only loads when a visitor clicks the launcher.

= Does this work with WooCommerce? =

Yes. The widget automatically runs across your store pages and helps answer visitor product questions, shipping queries, and return policies.

= Can I hide the widget on specific pages? =

Yes. In **Settings → Chatty Widget**, change the **Display Mode** to **Exclude** and enter the Page IDs or URL patterns (e.g. `15, /checkout/*, /my-account/*`).

= Can I use it on multiple WordPress sites? =

Yes. You can install this plugin on any number of WordPress sites and link them to the same Bot ID or different Bot IDs from your Chatty account.

== Screenshots ==

1. Chatty settings screen in WordPress admin for Bot ID and display options.
2. The live AI chat widget active on a WordPress website.
3. Proactive greeting bubble capturing visitor attention.

== Changelog ==

= 1.3.0 =
* Renamed the plugin to "PersonaliAI Customer Support Chatbot" (slug: personaliai-customer-support-chatbot) to comply with WordPress.org directory naming and trademark guidelines.

= 1.2.0 =
* Renamed the plugin to "Chatty by PersonaliAI" (slug: chatty-by-personaliai) for WordPress.org directory approval — distinguishes it from other "Chatty"-named services.

= 1.1.0 =
* Added page targeting: include or exclude specific pages and URL patterns.
* Added option to hide widget for logged-in administrators.
* Enhanced input validation and admin feedback notices.
* Added service disclosures for WordPress.org directory compliance.

= 1.0.0 =
* Initial release.

