<?php

namespace Inpsyde\UsersBlock\Tests;

class TestMain extends \PHPUnit\Framework\TestCase {
	function setUp() {
		parent::setUp();
	}

	/**
	 * @test
	 * @group users-block
	 */
	function itInitialiseVars() {
		$subject = new Inpsyde\UsersBlock\Main();
	}
	/**
	 * @test
	 * @group users-block
	 */
	function itAddsFilters() {
		$subject = new Inpsyde\UsersBlock\Main();
	}
}
