<?php
/**
 * Tests for Chatty_Widget_Plugin::sanitize_settings().
 *
 * @package Chatty_Widget
 */

namespace Chatty_Widget\Tests;

use Brain\Monkey\Functions;

/**
 * Class SanitizeSettingsTest
 */
class SanitizeSettingsTest extends TestCase {

	/**
	 * @var \Chatty_Widget_Plugin
	 */
	private $plugin;

	/**
	 * Instantiates the class under test without running its constructor,
	 * since the constructor registers WP hooks we don't need for this suite.
	 */
	protected function setUp(): void {
		parent::setUp();

		$reflection   = new \ReflectionClass( \Chatty_Widget_Plugin::class );
		$this->plugin = $reflection->newInstanceWithoutConstructor();

		// sanitize_text_field()/sanitize_textarea_field() in real WP strip tags,
		// slashes, and extra whitespace; a trim-based stub is enough to prove our
		// code wires the values through correctly.
		Functions\when( 'sanitize_text_field' )->alias(
			static function ( $value ) {
				return trim( wp_strip_all_tags_stub( $value ) );
			}
		);
		Functions\when( 'sanitize_textarea_field' )->alias(
			static function ( $value ) {
				return trim( $value );
			}
		);
		Functions\when( '__' )->returnArg( 1 );
	}

	/**
	 * A valid, fully-populated submission should pass through untouched
	 * (modulo sanitization) and register no errors.
	 */
	public function test_valid_input_is_preserved() {
		$input = array(
			'bot_id'            => '8e7713d5-af4e-41d2-a1d1-191fab125d18',
			'color'             => '#F97316',
			'position'          => 'left',
			'mobile_fullscreen' => '1',
			'teaser'            => '1',
			'sound'             => '1',
			'display_mode'      => 'include',
			'display_pages'     => '12, 45, /about*',
			'hide_for_admins'   => '1',
		);

		$output = $this->plugin->sanitize_settings( $input );

		$this->assertSame( '8e7713d5-af4e-41d2-a1d1-191fab125d18', $output['bot_id'] );
		$this->assertSame( '#F97316', $output['color'] );
		$this->assertSame( 'left', $output['position'] );
		$this->assertSame( '1', $output['mobile_fullscreen'] );
		$this->assertSame( '1', $output['teaser'] );
		$this->assertSame( '1', $output['sound'] );
		$this->assertSame( 'include', $output['display_mode'] );
		$this->assertSame( '12, 45, /about*', $output['display_pages'] );
		$this->assertSame( '1', $output['hide_for_admins'] );
	}

	/**
	 * An empty bot_id should register a settings error and still return an
	 * empty string for bot_id (the widget script simply won't enqueue).
	 */
	public function test_empty_bot_id_registers_error() {
		Functions\expect( 'add_settings_error' )
			->once()
			->with( 'chatty_widget_settings', 'invalid_bot_id', \Mockery::type( 'string' ), 'error' );

		$output = $this->plugin->sanitize_settings( array( 'bot_id' => '' ) );

		$this->assertSame( '', $output['bot_id'] );
	}

	/**
	 * Missing bot_id key entirely behaves the same as an empty string.
	 */
	public function test_missing_bot_id_key_registers_error() {
		Functions\expect( 'add_settings_error' )->once();

		$output = $this->plugin->sanitize_settings( array() );

		$this->assertSame( '', $output['bot_id'] );
	}

	/**
	 * @dataProvider valid_hex_color_provider
	 */
	public function test_valid_hex_colors_are_accepted( $color ) {
		$output = $this->plugin->sanitize_settings(
			array(
				'bot_id' => 'bot-1',
				'color'  => $color,
			)
		);

		$this->assertSame( $color, $output['color'] );
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public function valid_hex_color_provider() {
		return array(
			'lowercase'      => array( '#f97316' ),
			'uppercase'      => array( '#F97316' ),
			'mixed case'     => array( '#Ab12Cd' ),
			'all digits'     => array( '#000000' ),
			'all hex digits' => array( '#ffffff' ),
		);
	}

	/**
	 * @dataProvider invalid_hex_color_provider
	 */
	public function test_invalid_hex_colors_are_rejected_and_reset( $color ) {
		Functions\expect( 'add_settings_error' )
			->once()
			->with( 'chatty_widget_settings', 'invalid_color', \Mockery::type( 'string' ), 'error' );

		$output = $this->plugin->sanitize_settings(
			array(
				'bot_id' => 'bot-1',
				'color'  => $color,
			)
		);

		$this->assertSame( '', $output['color'] );
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public function invalid_hex_color_provider() {
		return array(
			'too short'          => array( '#fff' ),
			'no hash'            => array( 'f97316' ),
			'non-hex characters' => array( '#zzzzzz' ),
			'javascript injection' => array( '#" onmouseover="alert(1)' ),
			'too long'           => array( '#f973167' ),
		);
	}

	/**
	 * An empty color is optional and should be accepted silently (no error,
	 * empty string output) rather than treated as invalid.
	 */
	public function test_empty_color_is_accepted_without_error() {
		Functions\expect( 'add_settings_error' )
			->once() // only the (missing) bot_id error, not a color error.
			->with( 'chatty_widget_settings', 'invalid_bot_id', \Mockery::type( 'string' ), 'error' );

		$output = $this->plugin->sanitize_settings( array( 'color' => '' ) );

		$this->assertSame( '', $output['color'] );
	}

	/**
	 * @dataProvider position_provider
	 */
	public function test_position_falls_back_to_right_for_unknown_values( $input_position, $expected ) {
		$output = $this->plugin->sanitize_settings(
			array(
				'bot_id'   => 'bot-1',
				'position' => $input_position,
			)
		);

		$this->assertSame( $expected, $output['position'] );
	}

	/**
	 * @return array<string, array{0: mixed, 1: string}>
	 */
	public function position_provider() {
		return array(
			'right is kept'         => array( 'right', 'right' ),
			'left is kept'          => array( 'left', 'left' ),
			'unknown falls back'    => array( 'top', 'right' ),
			'empty falls back'      => array( '', 'right' ),
			'not set falls back'    => array( null, 'right' ),
			'case sensitive reject' => array( 'Right', 'right' ),
		);
	}

	/**
	 * @dataProvider display_mode_provider
	 */
	public function test_display_mode_falls_back_to_all_for_unknown_values( $input_mode, $expected ) {
		$output = $this->plugin->sanitize_settings(
			array(
				'bot_id'       => 'bot-1',
				'display_mode' => $input_mode,
			)
		);

		$this->assertSame( $expected, $output['display_mode'] );
	}

	/**
	 * @return array<string, array{0: mixed, 1: string}>
	 */
	public function display_mode_provider() {
		return array(
			'all is kept'        => array( 'all', 'all' ),
			'include is kept'    => array( 'include', 'include' ),
			'exclude is kept'    => array( 'exclude', 'exclude' ),
			'unknown falls back' => array( 'bogus', 'all' ),
			'not set falls back' => array( null, 'all' ),
		);
	}

	/**
	 * Checkbox-style fields are boolean-coerced to the strings '1'/'0'
	 * regardless of what truthy/falsy value was submitted.
	 */
	public function test_checkbox_fields_are_coerced_to_1_or_0() {
		$on = $this->plugin->sanitize_settings(
			array(
				'bot_id'            => 'bot-1',
				'mobile_fullscreen' => 'on',
				'teaser'            => '1',
				'sound'             => 'yes',
				'hide_for_admins'   => 'true',
			)
		);
		$this->assertSame( '1', $on['mobile_fullscreen'] );
		$this->assertSame( '1', $on['teaser'] );
		$this->assertSame( '1', $on['sound'] );
		$this->assertSame( '1', $on['hide_for_admins'] );

		$off = $this->plugin->sanitize_settings( array( 'bot_id' => 'bot-1' ) );
		$this->assertSame( '0', $off['mobile_fullscreen'] );
		$this->assertSame( '0', $off['teaser'] );
		$this->assertSame( '0', $off['sound'] );
		$this->assertSame( '0', $off['hide_for_admins'] );
	}

	/**
	 * display_pages is free text passed through sanitize_textarea_field();
	 * confirm it round-trips and defaults to an empty string when absent.
	 */
	public function test_display_pages_defaults_to_empty_string() {
		$output = $this->plugin->sanitize_settings( array( 'bot_id' => 'bot-1' ) );

		$this->assertSame( '', $output['display_pages'] );
	}
}

/**
 * Minimal stand-in for WP's tag-stripping behaviour, used only by the
 * sanitize_text_field stub above so injected markup is visibly neutralized
 * in test expectations without depending on WP core.
 *
 * @param string $value Raw value.
 * @return string
 */
function wp_strip_all_tags_stub( $value ) {
	return is_string( $value ) ? strip_tags( $value ) : $value;
}
