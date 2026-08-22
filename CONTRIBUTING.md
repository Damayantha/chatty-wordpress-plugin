# Contributing to the Chatty WordPress Plugin

Thanks for considering a contribution — patches, bug reports, and
compatibility fixes against real themes/plugins are all welcome.

## Development setup

There's no build step — this is a plain WordPress plugin. To work on it
against a real WordPress install:

```bash
git clone https://github.com/PersonaliAI/chatty-wordpress-plugin.git
cd chatty-wordpress-plugin
ln -s "$(pwd)/chatty-widget" /path/to/wordpress/wp-content/plugins/chatty-widget
```

Then activate **Chatty AI Chatbot** from the WordPress admin's Plugins page
and set a Bot ID under **Settings → Chatty Widget**.

Requires PHP 7.2+ and WordPress 5.8+ (matches the plugin header's own
`Requires PHP` / `Requires at least`).

## Project structure

```
chatty-widget/
  chatty-widget.php   Main plugin file — settings page, sanitization,
                       page-targeting logic, and the enqueue that outputs
                       widget.js with the configured data-* attributes
  readme.txt           WordPress.org plugin-directory-format readme
                       (changelog, FAQ, description shown on wordpress.org)
  uninstall.php        Deletes the plugin's single option on uninstall
```

Everything lives in one option, `chatty_widget_settings`, read through
`get_settings()` and written through `sanitize_settings()` — if you add a
new setting, both of those plus `uninstall.php`'s cleanup (nothing extra
needed there since it deletes the whole option) are the places to touch.

## WordPress Coding Standards

This repo follows [WPCS](https://github.com/WordPress/WordPress-Coding-Standards) —
tabs for indentation, spaces inside parentheses (`if ( $x )`, not `if($x)`),
and everything user-supplied sanitized on the way in and escaped on the way
out (`esc_attr()`, `esc_html()`, `esc_url()`, etc.). CI runs `phpcs` against
the `WordPress-Extra` ruleset on every push — check `.github/workflows/ci.yml`
if you want to run it locally first:

```bash
composer require --dev squizlabs/php_codesniffer wp-coding-standards/wpcs dealerdirect/phpcodesniffer-composer-installer
vendor/bin/phpcs --standard=WordPress-Extra chatty-widget/
```

## Pull requests

- Keep PRs scoped to one change.
- Explain *why*, not just *what* — especially for anything touching
  sanitization or the settings schema.
- CI must pass before merge.

## Reporting bugs

Open an issue with: the plugin version, WordPress version, theme name, and
a minimal repro (which settings you configured and what happened vs. what
you expected).
