<?php
/**
 * Plugin Name: PersonaliAI Customer Support Chatbot
 * Plugin URI: https://chatty.personaliai.com
 * Description: Adds the PersonaliAI AI customer support chatbot widget (powered by Chatty) to your WordPress site. No code editing required — just paste your Bot ID.
 * Version: 1.3.0
 * Author: PersonaliAI
 * Author URI: https://personaliai.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: personaliai-customer-support-chatbot
 * Requires at least: 5.8
 * Requires PHP: 7.2
 *
 * @package Chatty_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

if ( ! defined( 'CHATTY_WIDGET_VERSION' ) ) {
	define( 'CHATTY_WIDGET_VERSION', '1.3.0' );
}
if ( ! defined( 'CHATTY_WIDGET_OPTION' ) ) {
	define( 'CHATTY_WIDGET_OPTION', 'chatty_widget_settings' );
}
if ( ! defined( 'CHATTY_WIDGET_SCRIPT_URL' ) ) {
	define( 'CHATTY_WIDGET_SCRIPT_URL', 'https://chatty.personaliai.com/widget.js' );
}
if ( ! defined( 'CHATTY_WIDGET_FILE' ) ) {
	define( 'CHATTY_WIDGET_FILE', __FILE__ );
}

require_once __DIR__ . '/includes/class-chatty-widget-plugin.php';

if ( class_exists( 'Chatty_Widget_Plugin' ) ) {
	new Chatty_Widget_Plugin();
}
