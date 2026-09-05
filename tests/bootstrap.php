<?php
/**
 * PHPUnit bootstrap for the Chatty Widget plugin's unit tests.
 *
 * We do NOT load a full WordPress test environment here. The plugin's
 * WP-core-coupled entry points (hooks registration, admin screens, the
 * settings-field renderers that `echo`/`printf` HTML directly) are excluded
 * from this suite; see the test files for what *is* covered and
 * tests/README-COVERAGE.md-equivalent notes in the project report.
 *
 * Instead we:
 *  - autoload Composer deps (PHPUnit, Brain\Monkey, Mockery).
 *  - define the ABSPATH guard so the plugin class file doesn't exit().
 *  - define the option-name constant the class relies on.
 *  - require the class file once so its methods can be exercised directly,
 *    with WordPress core functions mocked per-test via Brain\Monkey.
 *
 * @package Chatty_Widget
 */

require_once __DIR__ . '/../vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

if ( ! defined( 'CHATTY_WIDGET_OPTION' ) ) {
	define( 'CHATTY_WIDGET_OPTION', 'chatty_widget_settings' );
}

if ( ! defined( 'CHATTY_WIDGET_VERSION' ) ) {
	define( 'CHATTY_WIDGET_VERSION', '1.2.0' );
}

if ( ! defined( 'CHATTY_WIDGET_SCRIPT_URL' ) ) {
	define( 'CHATTY_WIDGET_SCRIPT_URL', 'https://chatty.personaliai.com/widget.js' );
}

require_once __DIR__ . '/../chatty-by-personaliai/includes/class-chatty-widget-plugin.php';
