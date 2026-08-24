<?php
/**
 * Shared base test case: wires up Brain\Monkey around every test so
 * WordPress core functions (esc_attr, sanitize_text_field, get_option, ...)
 * can be stubbed instead of hitting a real WP install.
 *
 * @package Chatty_Widget
 */

namespace Chatty_Widget\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class TestCase
 */
abstract class TestCase extends PHPUnit_TestCase {

	/**
	 * Boots Brain\Monkey's function/action/filter interception.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tears down Brain\Monkey and verifies Mockery expectations.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
