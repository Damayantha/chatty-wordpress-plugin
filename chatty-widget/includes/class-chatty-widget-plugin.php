<?php
/**
 * Main plugin class: settings page, sanitization, page-targeting, and the
 * script enqueue that outputs widget.js with the configured data-* attributes.
 *
 * @package Chatty_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Chatty_Widget_Plugin
 */
class Chatty_Widget_Plugin {

	/**
	 * Wires up all WordPress hooks.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_widget_script' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_script_attributes' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( CHATTY_WIDGET_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Registers the Settings → Chatty Widget page.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Chatty Widget Settings', 'chatty-widget' ),
			__( 'Chatty Widget', 'chatty-widget' ),
			'manage_options',
			'chatty-widget',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Adds a "Settings" link on the plugin list page.
	 *
	 * @param array $links Existing action links.
	 * @return array
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=chatty-widget' ) ) . '">' . esc_html__( 'Settings', 'chatty-widget' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Registers the settings, section, and fields.
	 */
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
		add_settings_field( 'display_mode', __( 'Display Mode', 'chatty-widget' ), array( $this, 'field_display_mode' ), 'chatty-widget', 'chatty_widget_main_section' );
		add_settings_field( 'display_pages', __( 'Pages to Include/Exclude', 'chatty-widget' ), array( $this, 'field_display_pages' ), 'chatty-widget', 'chatty_widget_main_section' );
		add_settings_field( 'hide_for_admins', __( 'Hide for Admins', 'chatty-widget' ), array( $this, 'field_hide_for_admins' ), 'chatty-widget', 'chatty_widget_main_section' );
	}

	/**
	 * Sanitizes and validates the settings form submission.
	 *
	 * @param array $input Raw form input.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$output           = array();
		$output['bot_id'] = isset( $input['bot_id'] ) ? sanitize_text_field( $input['bot_id'] ) : '';
		if ( empty( $output['bot_id'] ) ) {
			add_settings_error( 'chatty_widget_settings', 'invalid_bot_id', __( 'Please enter a valid Bot ID.', 'chatty-widget' ), 'error' );
		}

		if ( ! empty( $input['color'] ) && ! preg_match( '/^#[0-9a-fA-F]{6}$/', $input['color'] ) ) {
			add_settings_error( 'chatty_widget_settings', 'invalid_color', __( 'Please enter a valid hex color (e.g., #FF5733).', 'chatty-widget' ), 'error' );
			$output['color'] = '';
		} else {
			$output['color'] = isset( $input['color'] ) ? $input['color'] : '';
		}

		$output['position']          = ( isset( $input['position'] ) && in_array( $input['position'], array( 'right', 'left' ), true ) ) ? $input['position'] : 'right';
		$output['mobile_fullscreen'] = ! empty( $input['mobile_fullscreen'] ) ? '1' : '0';
		$output['teaser']            = ! empty( $input['teaser'] ) ? '1' : '0';
		$output['sound']             = ! empty( $input['sound'] ) ? '1' : '0';
		$output['display_mode']      = ( isset( $input['display_mode'] ) && in_array( $input['display_mode'], array( 'all', 'include', 'exclude' ), true ) ) ? $input['display_mode'] : 'all';
		$output['display_pages']     = isset( $input['display_pages'] ) ? sanitize_textarea_field( $input['display_pages'] ) : '';
		$output['hide_for_admins']   = ! empty( $input['hide_for_admins'] ) ? '1' : '0';
		return $output;
	}

	/**
	 * Reads the plugin's settings, merged with defaults.
	 *
	 * @return array
	 */
	private function get_settings() {
		$defaults = array(
			'bot_id'            => '',
			'color'             => '',
			'position'          => 'right',
			'mobile_fullscreen' => '1',
			'teaser'            => '1',
			'sound'             => '1',
			'display_mode'      => 'all',
			'display_pages'     => '',
			'hide_for_admins'   => '0',
		);
		return wp_parse_args( get_option( CHATTY_WIDGET_OPTION, array() ), $defaults );
	}

	/**
	 * Renders the Bot ID field.
	 */
	public function field_bot_id() {
		$s = $this->get_settings();
		printf(
			'<input type="text" name="%1$s[bot_id]" value="%2$s" class="regular-text" placeholder="e.g. 8e7713d5-af4e-41d2-a1d1-191fab125d18" />',
			esc_attr( CHATTY_WIDGET_OPTION ),
			esc_attr( $s['bot_id'] )
		);
		echo '<p class="description">' . esc_html__( 'Find this in your Chatty dashboard → your bot → Embed & Integrate.', 'chatty-widget' ) . '</p>';
	}

	/**
	 * Renders the Accent Color field.
	 */
	public function field_color() {
		$s = $this->get_settings();
		printf(
			'<input type="text" name="%1$s[color]" value="%2$s" class="regular-text" placeholder="#f97316" />',
			esc_attr( CHATTY_WIDGET_OPTION ),
			esc_attr( $s['color'] )
		);
		echo '<p class="description">' . esc_html__( 'Optional. Leave blank to use the color already set in your Chatty dashboard.', 'chatty-widget' ) . '</p>';
	}

	/**
	 * Renders the Launcher Position field.
	 */
	public function field_position() {
		$s = $this->get_settings();
		?>
		<select name="<?php echo esc_attr( CHATTY_WIDGET_OPTION ); ?>[position]">
			<option value="right" <?php selected( $s['position'], 'right' ); ?>><?php esc_html_e( 'Bottom right', 'chatty-widget' ); ?></option>
			<option value="left" <?php selected( $s['position'], 'left' ); ?>><?php esc_html_e( 'Bottom left', 'chatty-widget' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Renders the Mobile Fullscreen field.
	 */
	public function field_mobile_fullscreen() {
		$s = $this->get_settings();
		printf(
			'<label><input type="checkbox" name="%1$s[mobile_fullscreen]" value="1" %2$s /> %3$s</label>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			checked( $s['mobile_fullscreen'], '1', false ),
			esc_html__( 'Open fullscreen on mobile devices', 'chatty-widget' )
		);
	}

	/**
	 * Renders the Proactive Greeting Bubble field.
	 */
	public function field_teaser() {
		$s = $this->get_settings();
		printf(
			'<label><input type="checkbox" name="%1$s[teaser]" value="1" %2$s /> %3$s</label>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			checked( $s['teaser'], '1', false ),
			esc_html__( 'Show a proactive greeting bubble after a few seconds', 'chatty-widget' )
		);
	}

	/**
	 * Renders the Notification Sound field.
	 */
	public function field_sound() {
		$s = $this->get_settings();
		printf(
			'<label><input type="checkbox" name="%1$s[sound]" value="1" %2$s /> %3$s</label>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			checked( $s['sound'], '1', false ),
			esc_html__( 'Play a chime when a new reply arrives', 'chatty-widget' )
		);
	}

	/**
	 * Renders the Display Mode field.
	 */
	public function field_display_mode() {
		$s = $this->get_settings();
		?>
		<select name="<?php echo esc_attr( CHATTY_WIDGET_OPTION ); ?>[display_mode]">
			<option value="all" <?php selected( $s['display_mode'], 'all' ); ?>><?php esc_html_e( 'All Pages', 'chatty-widget' ); ?></option>
			<option value="include" <?php selected( $s['display_mode'], 'include' ); ?>><?php esc_html_e( 'Include Only', 'chatty-widget' ); ?></option>
			<option value="exclude" <?php selected( $s['display_mode'], 'exclude' ); ?>><?php esc_html_e( 'Exclude', 'chatty-widget' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Renders the Pages to Include/Exclude field.
	 */
	public function field_display_pages() {
		$s = $this->get_settings();
		printf(
			'<textarea name="%1$s[display_pages]" rows="3" class="large-text" placeholder="e.g. 12, 45, /about*">%2$s</textarea>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			esc_textarea( $s['display_pages'] )
		);
		echo '<p class="description">' . esc_html__( 'Comma-separated list of Page IDs or URL patterns. Only applies if mode is Include or Exclude.', 'chatty-widget' ) . '</p>';
	}

	/**
	 * Renders the Hide for Admins field.
	 */
	public function field_hide_for_admins() {
		$s = $this->get_settings();
		printf(
			'<label><input type="checkbox" name="%1$s[hide_for_admins]" value="1" %2$s /> %3$s</label>',
			esc_attr( CHATTY_WIDGET_OPTION ),
			checked( $s['hide_for_admins'], '1', false ),
			esc_html__( 'Hide widget when logged in as administrator', 'chatty-widget' )
		);
	}

	/**
	 * Renders the full Settings → Chatty Widget admin page.
	 */
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

	/**
	 * Checks whether the current request matches any of the given page-ID or
	 * URL-pattern entries.
	 *
	 * @param array $patterns Comma-split, trimmed page IDs and/or URL patterns.
	 * @return bool
	 */
	private function url_matches_patterns( $patterns ) {
		$host         = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$current_url  = set_url_scheme( 'http://' . $host . $request_uri );
		$current_path = $request_uri;
		foreach ( $patterns as $pattern ) {
			if ( empty( $pattern ) || is_numeric( $pattern ) ) {
				continue;
			}
			$regex = str_replace( '\*', '.*', preg_quote( $pattern, '/' ) );
			if ( preg_match( '#^' . $regex . '$#i', $current_path ) || preg_match( '#^' . $regex . '$#i', $current_url ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Enqueues widget.js, honoring display-mode page targeting and the
	 * hide-for-admins setting.
	 */
	public function enqueue_widget_script() {
		$settings = $this->get_settings();
		if ( empty( $settings['bot_id'] ) ) {
			return;
		}

		// Check display mode. get_queried_object_id() (not get_the_ID()) since this runs on
		// every front-end request via wp_enqueue_scripts, including archives/home/404 where
		// there's no post loop set up for get_the_ID() to read from.
		if ( 'include' === $settings['display_mode'] ) {
			$pages = array_map( 'trim', explode( ',', $settings['display_pages'] ) );
			if ( ! in_array( get_queried_object_id(), $pages, false ) && ! $this->url_matches_patterns( $pages ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- $pages are trimmed strings from a textarea; loose comparison intentionally matches "12" against the int get_queried_object_id() returns.
				return;
			}
		} elseif ( 'exclude' === $settings['display_mode'] ) {
			$pages = array_map( 'trim', explode( ',', $settings['display_pages'] ) );
			if ( in_array( get_queried_object_id(), $pages, false ) || $this->url_matches_patterns( $pages ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- $pages are trimmed strings from a textarea; loose comparison intentionally matches "12" against the int get_queried_object_id() returns.
				return;
			}
		}
		// Check admin visibility.
		if ( ! empty( $settings['hide_for_admins'] ) && current_user_can( 'manage_options' ) ) {
			return;
		}

		$script_url = apply_filters( 'chatty_widget_script_url', CHATTY_WIDGET_SCRIPT_URL );
		wp_enqueue_script(
			'chatty-widget',
			$script_url,
			array(),
			CHATTY_WIDGET_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	/**
	 * Adds data-* attributes (bot ID, color, position, etc.) to widget.js's
	 * <script> tag.
	 *
	 * @param string $tag    The `<script>` tag markup.
	 * @param string $handle The script's registered handle.
	 * @return string
	 */
	public function add_script_attributes( $tag, $handle ) {
		if ( 'chatty-widget' !== $handle ) {
			return $tag;
		}

		$s = $this->get_settings();
		if ( empty( $s['bot_id'] ) ) {
			return $tag;
		}

		$attrs = array(
			'data-id' => $s['bot_id'],
		);
		if ( ! empty( $s['color'] ) ) {
			$attrs['data-color'] = $s['color'];
		}
		$attrs['data-position']          = $s['position'];
		$attrs['data-mobile-fullscreen'] = ( '1' === $s['mobile_fullscreen'] ) ? 'true' : 'false';
		$attrs['data-teaser']            = ( '1' === $s['teaser'] ) ? 'true' : 'false';
		$attrs['data-sound']             = ( '1' === $s['sound'] ) ? 'true' : 'false';

		$attr_string = '';
		foreach ( $attrs as $key => $value ) {
			$attr_string .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return str_replace( ' src', $attr_string . ' src', $tag );
	}
}
