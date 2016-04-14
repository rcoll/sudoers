<?php

class Test_Sudoers_Utilities extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	function test_tests() {
		$this->assertEquals( 1, 1 );
		$this->assertNotEquals( 1, 2 );
	}

}

// omit