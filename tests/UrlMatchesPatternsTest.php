<?php
/**
 * Tests for the private Chatty_Widget_Plugin::url_matches_patterns(), exercised
 * via ReflectionMethod since it has no public entry point of its own.
 *
 * @package Chatty_Widget
 */

namespace Chatty_Widget\Tests;

use Brain\Monkey\Functions;

/**
 * Class UrlMatchesPatternsTest
 */
class UrlMatchesPatternsTest extends TestCase {

	/**
	 * @var \Chatty_Widget_Plugin
	 */
	private $plugin;

	/**
	 * @var array Original $_SERVER, restored in tearDown().
	 */
	private $original_server;

	/**
	 * Instantiates the class under test and stubs the WP core functions
	 * url_matches_patterns() calls to build the current URL/path.
	 */
	protected function setUp(): void {
		parent::setUp();

		$reflection   = new \ReflectionClass( \Chatty_Widget_Plugin::class );
		$this->plugin = $reflection->newInstanceWithoutConstructor();

		$this->original_server = $_SERVER;

		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'wp_unslash' )->returnArg();
		// Real set_url_scheme() replaces a URL's scheme (or, for a
		// scheme-relative "//host/path" input like url_matches_patterns()
		// passes, prepends one) based on is_ssl() — never leaves it
		// scheme-relative. An identity stub here would silently leave the
		// "//" prefix in place and never actually reproduce that behavior,
		// so full-URL patterns (which are written with a real "http://"/
		// "https://" scheme) could never match. Assume non-SSL, matching
		// this suite not setting $_SERVER['HTTPS'].
		Functions\when( 'set_url_scheme' )->alias(
			static function ( $url ) {
				return preg_replace( '#^(?:https?:)?//#i', 'http://', $url );
			}
		);
	}

	/**
	 * Restores the superglobal so other tests aren't affected.
	 */
	protected function tearDown(): void {
		$_SERVER = $this->original_server;
		parent::tearDown();
	}

	/**
	 * Invokes the private method under test.
	 *
	 * @param array $patterns Comma-split, trimmed page IDs/URL patterns.
	 * @return bool
	 */
	private function call_url_matches_patterns( array $patterns ) {
		$method = new \ReflectionMethod( \Chatty_Widget_Plugin::class, 'url_matches_patterns' );
		$method->setAccessible( true );
		return $method->invoke( $this->plugin, $patterns );
	}

	/**
	 * Sets the request path/host used to build the "current URL".
	 *
	 * @param string $uri  REQUEST_URI, e.g. "/about/team".
	 * @param string $host HTTP_HOST, e.g. "example.com".
	 */
	private function set_request( $uri, $host = 'example.com' ) {
		$_SERVER['REQUEST_URI'] = $uri;
		$_SERVER['HTTP_HOST']   = $host;
	}

	/**
	 * Purely-numeric entries (page IDs) are skipped by this method — they're
	 * matched elsewhere via get_queried_object_id(), not against the URL.
	 */
	public function test_numeric_patterns_are_never_url_matches() {
		$this->set_request( '/12' );

		$this->assertFalse( $this->call_url_matches_patterns( array( '12' ) ) );
	}

	/**
	 * Empty entries (e.g. from a trailing comma) are skipped.
	 */
	public function test_empty_patterns_are_skipped() {
		$this->set_request( '/about' );

		$this->assertFalse( $this->call_url_matches_patterns( array( '' ) ) );
	}

	/**
	 * An exact path pattern matches only that exact path.
	 */
	public function test_exact_path_match() {
		$this->set_request( '/about' );

		$this->assertTrue( $this->call_url_matches_patterns( array( '/about' ) ) );
		$this->assertFalse( $this->call_url_matches_patterns( array( '/about-us' ) ) );
	}

	/**
	 * A trailing "*" wildcard matches any suffix.
	 */
	public function test_wildcard_suffix_match() {
		$this->set_request( '/about/team' );

		$this->assertTrue( $this->call_url_matches_patterns( array( '/about*' ) ) );
	}

	/**
	 * Without the wildcard, a prefix-only pattern does not match a deeper path.
	 */
	public function test_prefix_without_wildcard_does_not_match_deeper_path() {
		$this->set_request( '/about/team' );

		$this->assertFalse( $this->call_url_matches_patterns( array( '/about' ) ) );
	}

	/**
	 * Matching is case-insensitive.
	 */
	public function test_match_is_case_insensitive() {
		$this->set_request( '/About' );

		$this->assertTrue( $this->call_url_matches_patterns( array( '/about' ) ) );
	}

	/**
	 * Regex metacharacters in a pattern are treated literally (escaped),
	 * not interpreted as regex syntax, aside from the "*" wildcard.
	 */
	public function test_regex_special_characters_are_escaped() {
		$this->set_request( '/faq' );

		// A literal "." in the pattern must not act as "any character".
		$this->assertFalse( $this->call_url_matches_patterns( array( '/f.q' ) ) );

		$this->set_request( '/f.q' );
		$this->assertTrue( $this->call_url_matches_patterns( array( '/f.q' ) ) );
	}

	/**
	 * No patterns supplied means no match.
	 */
	public function test_no_patterns_means_no_match() {
		$this->set_request( '/anything' );

		$this->assertFalse( $this->call_url_matches_patterns( array() ) );
	}

	/**
	 * A full-URL-style pattern (with host) can also match against the
	 * reconstructed current URL, not just the bare path.
	 */
	public function test_full_url_pattern_matches_against_current_url() {
		$this->set_request( '/pricing', 'shop.example.com' );

		$this->assertTrue(
			$this->call_url_matches_patterns( array( 'http://shop.example.com/pricing' ) )
		);
	}
}
