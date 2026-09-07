<div align="center">

# Chatty WordPress Plugin

**Add the [Chatty](https://chatty.personaliai.com) AI chat widget to any WordPress site — no code editing required.**

Paste your Bot ID, save, and the widget appears on your site — deferred, non-blocking, and
styled exactly the way you configured it in the Chatty dashboard.

[![CI](https://github.com/PersonaliAI/chatty-wordpress-plugin/actions/workflows/ci.yml/badge.svg)](https://github.com/PersonaliAI/chatty-wordpress-plugin/actions/workflows/ci.yml)
[![Release](https://img.shields.io/github/v/release/PersonaliAI/chatty-wordpress-plugin)](https://github.com/PersonaliAI/chatty-wordpress-plugin/releases/latest)
[![License: GPLv2](https://img.shields.io/badge/license-GPLv2%20or%20later-blue.svg)](LICENSE)
[![WordPress 5.8+](https://img.shields.io/badge/WordPress-5.8%2B-21759B?logo=wordpress&logoColor=white)](#requirements)
[![PHP 7.2+](https://img.shields.io/badge/PHP-7.2%2B-777BB4?logo=php&logoColor=white)](#requirements)
[![Stars](https://img.shields.io/github/stars/PersonaliAI/chatty-wordpress-plugin?style=social)](https://github.com/PersonaliAI/chatty-wordpress-plugin/stargazers)

[Install](#install) · [Settings](#settings) · [Page targeting](#page-targeting) · [Development](#development) · [FAQ](#faq)

</div>

---

## Why this plugin

| | |
|---|---|
| **Zero code editing** | Everything configures from **Settings → Chatty Widget** — no theme files, no shortcodes to place. |
| **Loads deferred** | The widget script is enqueued with the `defer` strategy and only in the footer — it never blocks page render. |
| **Stays in sync automatically** | Design, avatar, welcome message, and behavior all come live from your bot's config in the Chatty dashboard — nothing to duplicate here. |
| **Real page targeting** | Show the widget everywhere, only on a chosen list of pages/URL patterns, or everywhere *except* a chosen list. |
| **Any theme, including block themes** | Enqueued the standard WordPress way (`wp_enqueue_scripts`) — no footer-hook hacks that fight page builders. |

## Install

**From a release zip:**

1. Grab `personaliai-customer-support-chatbot.zip` from the [latest release](https://github.com/PersonaliAI/chatty-wordpress-plugin/releases/latest)
2. In your WordPress admin: **Plugins → Add New → Upload Plugin**
3. Choose the zip, click **Install Now**, then **Activate**
4. Go to **Settings → Chatty Widget** and paste your Bot ID — find it in your [Chatty dashboard](https://chatty.personaliai.com/dashboard) → your bot → **Embed & Integrate**
5. Save — the widget now appears on your site

**From source**, for development against a local WordPress install — see [Development](#development).

## Settings

All under **Settings → Chatty Widget**:

| Setting | What it does |
|---|---|
| **Bot ID** (required) | Which Chatty bot to load. Everything else (theme, welcome message, avatar) comes from the bot's own dashboard config. |
| **Accent Color** | Optional override. Leave blank to use whatever's set in the dashboard. |
| **Launcher Position** | Bottom right or bottom left. |
| **Mobile Fullscreen** | Open fullscreen on mobile instead of a floating panel. |
| **Proactive Greeting Bubble** | Show a teaser bubble a few seconds after page load. |
| **Notification Sound** | Chime when a new reply arrives. |
| **Display Mode** | `All Pages`, `Include Only`, or `Exclude` — see [Page targeting](#page-targeting). |
| **Hide for Admins** | Don't show the widget to logged-in administrators (handy so it doesn't get in your own way while you work). |

## Page targeting

Set **Display Mode** to `Include Only` or `Exclude`, then list page IDs and/or URL patterns
(comma-separated) in **Pages to Include/Exclude**:

```
12, 45, /shop/*, /about
```

- A bare number is matched against the page/post ID of the page being viewed.
- Anything else is matched as a URL pattern against the current path — `*` matches any
  sequence of characters, so `/shop/*` matches every URL under `/shop/`.

`Include Only` shows the widget **only** where it matches; `Exclude` shows it **everywhere
except** where it matches.

## Requirements

- WordPress 5.8+
- PHP 7.2+
- A [Chatty](https://chatty.personaliai.com) account with a bot set up

## Development

The plugin has no build step — it's plain PHP. To work on it against a real WordPress site:

```bash
git clone https://github.com/PersonaliAI/chatty-wordpress-plugin.git
cd chatty-wordpress-plugin
ln -s "$(pwd)/personaliai-customer-support-chatbot" /path/to/wordpress/wp-content/plugins/personaliai-customer-support-chatbot
```

Activate **PersonaliAI Customer Support Chatbot** from the Plugins page and set a Bot ID under
**Settings → Chatty Widget**.

```
personaliai-customer-support-chatbot/
  personaliai-customer-support-chatbot.php   Main plugin file — settings page, sanitization, page-targeting
                                             logic, and the enqueue that outputs widget.js with the
                                             configured data-* attributes
  readme.txt                                 WordPress.org plugin-directory-format readme
  uninstall.php                              Deletes the plugin's single option on uninstall
```

See [CONTRIBUTING.md](CONTRIBUTING.md) for coding standards, the CI setup, and how to run
`phpcs` locally before opening a PR.

## FAQ

**Where do I find my Bot ID?** In your Chatty dashboard, select your bot, then open the
"Embed & Integrate" tab.

**Does this slow down my site?** No — the script loads deferred, and the full chat interface
only loads when a visitor actually opens it.

**Does this work with WooCommerce?** Yes — WooCommerce runs on WordPress, so the widget
appears on your store pages automatically once activated (use page targeting if you want it
on some pages only).

**I entered my Bot ID but don't see the widget.** Make sure you clicked "Save Changes," then
check the site's front end (not wp-admin) — the launcher appears in a bottom corner. If
you're running a caching plugin, clear its cache after changing settings.

More in [`personaliai-customer-support-chatbot/readme.txt`](personaliai-customer-support-chatbot/readme.txt), which is also what renders on
the WordPress.org plugin directory page.

## License

GPLv2 or later — see [LICENSE](LICENSE).
