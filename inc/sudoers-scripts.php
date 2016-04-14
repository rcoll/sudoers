<?php 

/**
 * Enqueue scripts and styles.
 */
function sudoers_scripts() {
	global $page, $numpages;

	wp_enqueue_style( 'sudoers-style', get_stylesheet_uri() );

	wp_enqueue_script( 'sudoers-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'sudoers-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script( 'sudoers', get_template_directory_uri() . '/js/sudoers.js', array(), '20151215' );

	wp_localize_script( 'sudoers', 'sudoers', array( 
		'is_home'         => is_home(), 
		'is_front_page'   => is_front_page(), 
		'is_single'       => is_single(), 
		'is_singular'     => is_singular(), 
		'is_page'         => is_page(), 
		'is_category'     => is_category(), 
		'is_tag'          => is_tag(), 
		'is_archive'      => is_archive(), 
		'is_404'          => is_404(), 
		'is_search'       => is_search(), 
		'is_tax'          => is_tax(), 
		'is_author'       => is_author(), 
		'is_paged'        => is_paged(), 
		'is_slideshow'    => ( is_single() && $numpages > 1 ) ? 1 : 0,
		'is_video_single' => ( is_single() && in_category( SUDOERS_VIDEO_CATEGORY ) ) ? 1 : 0,
		'is_video_cat'    => ( is_category( SUDOERS_VIDEO_CATEGORY ) ) ? 1 : 0, 
		'post_page'       => $page, 
		'post_numpages'   => $numpages, 
		'debug_js'        => false, 
		'environment'     => get_environment(), 
	));

	wp_enqueue_script( 'sudoers' );
}
add_action( 'wp_enqueue_scripts', 'sudoers_scripts' );

// omit