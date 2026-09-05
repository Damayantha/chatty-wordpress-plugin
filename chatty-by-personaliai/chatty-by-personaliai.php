<?php
/**
 * Plugin Name: Chatty by PersonaliAI
 * Plugin URI: https://chatty.personaliai.com
 * Description: Adds the Chatty AI chatbot widget to your WordPress site. No code editing required — just paste your Bot ID.
 * Version: 1.2.0
 * Author: PersonaliAI
 * Author URI: https://personaliai.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chatty-by-personaliai
 * Requires at least: 5.8
 * Requires PHP: 7.2
 *
 * @package Chatty_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'CHATTY_WIDGET_VERSION', '1.2.0' );
define( 'CHATTY_WIDGET_OPTION', 'chatty_widget_settings' );
define( 'CHATTY_WIDGET_SCRIPT_URL', 'https://chatty.personaliai.com/widget.js' );
define( 'CHATTY_WIDGET_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-chatty-widget-plugin.php';

new Chatty_Widget_Plugin();
