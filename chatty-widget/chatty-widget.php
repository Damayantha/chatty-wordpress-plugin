<?php
/**
 * Plugin Name: Chatty AI Chatbot
 * Plugin URI: https://chatty.personaliai.com
 * Description: Adds the Chatty AI chatbot widget to your WordPress site. No code editing required — just paste your Bot ID.
 * Version: 1.0.0
 * Author: PersonaliAI
 * Author URI: https://personaliai.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chatty-widget
 * Requires at least: 5.8
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'CHATTY_WIDGET_VERSION', '1.0.0' );
define( 'CHATTY_WIDGET_OPTION', 'chatty_widget_settings' );
define( 'CHATTY_WIDGET_SCRIPT_URL', 'https://chatty.personaliai.com/widget.js' );

class Chatty_Widget_Plugin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_footer', array( $this, 'output_widget_script' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
	}

	public function add_settings_page() {
		add_options_page(
			__( 'Chatty Widget Settings', 'chatty-widget' ),
			__( 'Chatty Widget', 'chatty-widget' ),
			'manage_options',
			'chatty-widget',
			array( $this, 'render_settings_page' )
		);
	}

	public function add_settings_link( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=chatty-widget' ) ) . '">' . esc_html__( 'Settings', 'chatty-widget' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function register_settings() {
		register_setting( 'chatty_widget_settings_group', CHATTY_WIDGET_OPTION, array( $this, 'sanitize_settings' ) );

		add_settings_section(
			'chatty_widget_main_section',
			__( 'Widget Configuration', 'chatty-widget' ),
			'__return_false',
			'chatty-widget'
		);

		add_settings_field( 'bot_id', __( 'Bot ID (required)', 'chatty-widget' ), array( $this, 'field_bot_id' ), 'chatty-widget', 'chatty_widget_main_section' );
		add_settings_field( 'color', __( 'Accent Color', 'chatty-widget' ), array( $this, 'field_color' ), 'chatty-widget', 'chatty_widget_main_section' );
		add_settings_field( 'position', __( 'Launcher Position', 'chatty-widget' ), array( $this, 'field_position' ), 'chatty-widget', 'chatty_widget_main_section' );
		add_settings_field( 'mobile_fullscreen', __( 'Mobile Fullscreen', 'chatty-widget' ), array( $this, 'field_mobile_fullscreen' ), 'chatty-widget', 'chatty_widget_main_section' );
		add_settings_field( 'teaser', __( 'Proactive Greeting Bubble', 'chatty-widget' ), array( $this, 'field_teaser' ), 'chatty-widget', 'chatty_widget_main_section' );
		add_settings_field( 'sound', __( 'Notification Sound', 'chatty-widget' ), array( $this, 'field_sound' ), 'chatty-widget', 'chatty_widget_main_section' );
	}

	public function sanitize_settings( $input ) {
		$output                       = array();
		$output['bot_id']             = isset( $input['bot_id'] ) ? sanitize_text_field( $input['bot_id'] ) : '';
		$output['color']              = ( isset( $input['color'] ) && preg_match( '/^#[0-9a-fA-F]{6}$/', $input['color'] ) ) ? $input['color'] : '';
		$output['position']           = ( isset( $input['position'] ) && in_array( $input['position'], array( 'right', 'left' ), true ) ) ? $input['position'] : 'right';
		$output['mobile_fullscreen']  = ! empty( $input['mobile_fullscreen'] ) ? '1' : '0';
		$output['teaser']             = ! empty( $input['teaser'] ) ? '1' : '0';
		$output['sound']              = ! empty( $input['sound'] ) ? '1' : '0';
		return $output;
	}

	private function get_settings() {
		$defaults = array(
			'bot_id'            => '',
			'color'             => '',
			'position'          => 'right',
			'mobile_fullscreen' => '1',
			'teaser'            => '1',
			'sound'             => '1',
		);
		return wp_parse_args( get_option( CHATTY_WIDGET_OPTION, array() ), $defaults );
	}

	public function field_bot_id() {
		$s = $this->get_settings();
		printf(
			'<input type="text" name="%1$s[bot_id]" value="%2$s" class="regular-text" placeholder="e.g. 8e7713d5-af4e-41d2-a1d1-191fab125d18" />',
			esc_attr( CHATTY_WIDGET_OPTION ),
			esc_attr( $s['bot_id'] )
		);
		echo '<p class="description">' . esc_html__( 'Find this in your Chatty dashboard → your bot → Embed & Integrate.', 'chatty-widget' ) . '</p>';
	}

	public function field_color() {
		$s = $this->get_settings();
		printf(
			'<input type="text" name="%1$s[color]" value="%2$s" class="regular-text" placeholder="#f97316" />',
			esc_attr( CHATTY_WIDGET_OPTION ),
			esc_attr( $s['color'] )
		);
		echo '<p class="description">' . esc_html__( 'Optional. Leave blank to use the color already set in your Chatty dashboard.', 'chatty-widget' ) . '</p>';
	}

	public function field_position() {
		$s = $this->get_settings();
		?>
		<select name="<?php echo esc_attr( CHATTY_WIDGET_OPTION ); ?>[position]">
			<option value="right" <?php selected( $s['position'], 'right' ); ?>><?php esc_html_e( 'Bottom right', 'chatty-widget' ); ?></option>
			<option value="left" <?php selected( $s['position'], 'left' ); ?>><?php esc_html_e( 'Bottom left', 'chatty-widget' ); ?></option>
		</select>
		<?php
	}

	public function field_mobile_fullscreen() {
		$s = $this->get_settings();
		printf(
			'<label><input type="checkbox" name="%1$s[mobile_fullscreen]" value="1" %2$s /> %3$s</label>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			checked( $s['mobile_fullscreen'], '1', false ),
			esc_html__( 'Open fullscreen on mobile devices', 'chatty-widget' )
		);
	}

	public function field_teaser() {
		$s = $this->get_settings();
		printf(
			'<label><input type="checkbox" name="%1$s[teaser]" value="1" %2$s /> %3$s</label>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			checked( $s['teaser'], '1', false ),
			esc_html__( 'Show a proactive greeting bubble after a few seconds', 'chatty-widget' )
		);
	}

	public function field_sound() {
		$s = $this->get_settings();
		printf(
			'<label><input type="checkbox" name="%1$s[sound]" value="1" %2$s /> %3$s</label>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			checked( $s['sound'], '1', false ),
			esc_html__( 'Play a chime when a new reply arrives', 'chatty-widget' )
		);
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$s = $this->get_settings();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Chatty Widget Settings', 'chatty-widget' ); ?></h1>

			<?php if ( empty( $s['bot_id'] ) ) : ?>
				<div class="notice notice-warning">
					<p><?php esc_html_e( 'Enter your Bot ID below to activate the widget on your site.', 'chatty-widget' ); ?></p>
				</div>
			<?php else : ?>
				<div class="notice notice-success">
					<p><?php esc_html_e( 'The Chatty widget is active on your site.', 'chatty-widget' ); ?></p>
				</div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'chatty_widget_settings_group' );
				do_settings_sections( 'chatty-widget' );
				submit_button();
				?>
			</form>

			<hr />
			<p>
				<?php
				printf(
					/* translators: %s: link to the Chatty dashboard */
					esc_html__( "Don't have a bot yet? Create one in the %s.", 'chatty-widget' ),
					'<a href="https://chatty.personaliai.com/dashboard" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Chatty dashboard', 'chatty-widget' ) . '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}

	public function output_widget_script() {
		$s = $this->get_settings();
		if ( empty( $s['bot_id'] ) ) {
			return;
		}

		$attrs = array(
			'src'    => CHATTY_WIDGET_SCRIPT_URL,
			'data-id' => $s['bot_id'],
		);
		if ( ! empty( $s['color'] ) ) {
			$attrs['data-color'] = $s['color'];
		}
		$attrs['data-position']          = $s['position'];
		$attrs['data-mobile-fullscreen'] = ( '1' === $s['mobile_fullscreen'] ) ? 'true' : 'false';
		$attrs['data-teaser']            = ( '1' === $s['teaser'] ) ? 'true' : 'false';
		$attrs['data-sound']             = ( '1' === $s['sound'] ) ? 'true' : 'false';

		$html = '<script';
		foreach ( $attrs as $key => $value ) {
			$html .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}
		$html .= ' defer></script>';

		// Every value above already passed through esc_attr() while building $html.
		echo "\n<!-- Chatty AI Chatbot -->\n" . $html . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

new Chatty_Widget_Plugin();
