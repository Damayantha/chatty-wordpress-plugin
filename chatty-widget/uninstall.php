<?php
// Fired when the plugin is deleted via the Plugins screen.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'chatty_widget_settings' );
