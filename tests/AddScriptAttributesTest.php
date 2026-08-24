<?php
/**
 * Tests for Chatty_Widget_Plugin::add_script_attributes() and, via the same
 * fixture, the private get_settings() defaults/merge behaviour it depends on.
 *
 * @package Chatty_Widget
 */

namespace Chatty_Widget\Tests;

use Brain\Monkey\Functions;

/**
 * Class AddScriptAttributesTest
 */
class AddScriptAttributesTest extends TestCase {

	/**
	 * @var \Chatty_Widget_Plugin
	 */
	private $plugin;

	/**
	 * Instantiates the class under test without running its constructor.
	 */
	protected function setUp(): void {
		parent::setUp();

		$reflection   = new \ReflectionClass( \Chatty_Widget_Plugin::class );
		$this->plugin = $reflection->newInstanceWithoutConstructor();

		// esc_attr() in real WP HTML-entity-encodes; a stub that mirrors that
		// (rather than a no-op passthrough) lets us assert the attribute
		// string is actually escaped, not just concatenated.
		Functions\when( 'esc_attr' )->alias(
			static function ( $value ) {
				return htmlspecialchars( (string) $value, ENT_QUOTES );
			}
		);
	}

	/**
	 * Stubs get_option() to return the given raw stored settings (or none),
	 * and wp_parse_args() to behave like the real WP core implementation
	 * (array_merge with default-first precedence, matching WP's documented
	 * semantics for that function).
	 *
	 * @param array $stored Raw value returned by get_option().
	 */
	private function stub_settings( array $stored = array() ) {
		Functions\when( 'get_option' )->justReturn( $stored );
		Functions\when( 'wp_parse_args' )->alias(
			static function ( $args, $defaults = array() ) {
				return array_merge( $defaults, (array) $args );
			}
		);
	}

	/**
	 * A tag for a different script handle must be returned completely
	 * untouched.
	 */
	public function test_ignores_other_script_handles() {
		$tag = '<script src="https://example.com/other.js"></script>';

		$result = $this->plugin->add_script_attributes( $tag, 'some-other-handle' );

		$this->assertSame( $tag, $result );
	}

	/**
	 * If bot_id is empty (shouldn't normally happen since the script is only
	 * enqueued when bot_id is set, but the filter is defensive), the tag
	 * must be returned untouched.
	 */
	public function test_returns_tag_unmodified_when_bot_id_empty() {
		$this->stub_settings( array( 'bot_id' => '' ) );
		$tag = '<script src="https://chatty.personaliai.com/widget.js"></script>';

		$result = $this->plugin->add_script_attributes( $tag, 'chatty-widget' );

		$this->assertSame( $tag, $result );
	}

	/**
	 * With only bot_id configured, defaults fill in position/mobile-fullscreen/
	 * teaser/sound, and no data-color attribute is added.
	 */
	public function test_adds_default_attributes_for_bot_id_only() {
		$this->stub_settings( array( 'bot_id' => 'my-bot-id' ) );
		$tag = '<script src="https://chatty.personaliai.com/widget.js" id="chatty-widget-js"></script>';

		$result = $this->plugin->add_script_attributes( $tag, 'chatty-widget' );

		$this->assertStringContainsString( 'data-id="my-bot-id"', $result );
		$this->assertStringContainsString( 'data-position="right"', $result );
		$this->assertStringContainsString( 'data-mobile-fullscreen="true"', $result );
		$this->assertStringContainsString( 'data-teaser="true"', $result );
		$this->assertStringContainsString( 'data-sound="true"', $result );
		$this->assertStringNotContainsString( 'data-color', $result );
		// Attributes must land before src, not after (script_loader_tag
		// consumers/browsers don't care, but this pins the implementation).
		$this->assertMatchesRegularExpression( '/data-id="my-bot-id".*src=/', $result );
	}

	/**
	 * All optional settings, when configured, appear as data-* attributes
	 * with correct boolean-to-string mapping.
	 */
	public function test_adds_all_configured_attributes() {
		$this->stub_settings(
			array(
				'bot_id'            => 'my-bot-id',
				'color'             => '#F97316',
				'position'          => 'left',
				'mobile_fullscreen' => '0',
				'teaser'            => '0',
				'sound'             => '0',
			)
		);
		$tag = '<script src="https://chatty.personaliai.com/widget.js"></script>';

		$result = $this->plugin->add_script_attributes( $tag, 'chatty-widget' );

		$this->assertStringContainsString( 'data-id="my-bot-id"', $result );
		$this->assertStringContainsString( 'data-color="#F97316"', $result );
		$this->assertStringContainsString( 'data-position="left"', $result );
		$this->assertStringContainsString( 'data-mobile-fullscreen="false"', $result );
		$this->assertStringContainsString( 'data-teaser="false"', $result );
		$this->assertStringContainsString( 'data-sound="false"', $result );
	}

	/**
	 * Values are escaped via esc_attr(), so a bot_id/color containing
	 * characters that would break out of the HTML attribute must come out
	 * neutralized in the resulting tag.
	 */
	public function test_attribute_values_are_escaped() {
		$this->stub_settings( array( 'bot_id' => '"><script>alert(1)</script>' ) );
		$tag = '<script src="https://chatty.personaliai.com/widget.js"></script>';

		$result = $this->plugin->add_script_attributes( $tag, 'chatty-widget' );

		$this->assertStringNotContainsString( '"><script>alert(1)</script>', $result );
		$this->assertStringContainsString( '&quot;&gt;&lt;script&gt;', $result );
	}
}
