<?php

$tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $tests_dir ) {
	$tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $tests_dir . '/includes/functions.php';
require_once $tests_dir . '/includes/bootstrap.php';
require_once dirname( __FILE__ ) . '/sudoers-testcase.php';
require_once dirname( __FILE__ ) . '/sudoers-ajax-testcase.php';

// omit