<?php
/**
 * Plugin Name: Chatty AI Chatbot
 * Plugin URI: https://chatty.personaliai.com
 * Description: Adds the Chatty AI chatbot widget to your WordPress site. No code editing required — just paste your Bot ID.
 * Version: 1.1.0
 * Author: PersonaliAI
 * Author URI: https://personaliai.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chatty-widget
 * Requires at least: 5.8
 * Requires PHP: 7.2
 * Update URI: https://chatty.personaliai.com
 *
 * @package Chatty_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'CHATTY_WIDGET_VERSION', '1.1.0' );
define( 'CHATTY_WIDGET_OPTION', 'chatty_widget_settings' );
define( 'CHATTY_WIDGET_SCRIPT_URL', 'https://chatty.personaliai.com/widget.js' );
define( 'CHATTY_WIDGET_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-chatty-widget-plugin.php';

new Chatty_Widget_Plugin();
