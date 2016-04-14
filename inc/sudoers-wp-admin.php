<?php

/**
 * Add columns for post ID and thumbnail
 *
 * @param array $cols Array of columns
 *
 * @return array Array of columns
 */
function sudoers_edit_post_columns( $cols ) {
	$cols['post_id'] = 'Post ID';
	$cols['thumbnail'] = 'Thumbnail';

	return $cols;
}
add_filter( 'manage_edit-post_columns', 'sudoers_edit_post_columns' );

/**
 * Add data to post ID and thumbnail columns
 *
 * @param string $colname The columns slug/name
 *
 * @global $post
 *
 * @uses absint()
 * @uses get_the_post_thumbnail()
 *
 * @return void
 */
function sudoers_edit_post_columns_data( $colname ) {
	global $post;

	if ( 'post_id' == $colname ) {
		echo sprintf( '<strong>%d</strong>', absint( $post->ID ) );
	}

	if ( 'thumbnail' == $colname ) {
		echo get_the_post_thumbnail( $post->ID, array( 80, 80 ) );
	}
}
add_action( 'manage_posts_custom_column', 'sudoers_edit_post_columns_data' );

// omit