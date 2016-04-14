<?php

if ( ! function_exists( 'get_environment' ) ) {
	/**
	 * Get the environment identifier
	 *
	 * @uses SUDOERS_LOCAL_URL
	 * @uses SUDOERS_DEV_URL
	 * @uses SUDOERS_STAGE_URL
	 * @uses SUDOERS_PROD_URL
	 * @uses $_SERVER['HTTP_HOST']
	 *
	 * @return string Environment identifier
	 */
	function get_environment() {
		// Local environment
		if ( defined( 'SUDOERS_LOCAL_URL' ) && false !== strpos( $_SERVER['HTTP_HOST'], SUDOERS_LOCAL_URL ) ) {
			return 'local';
		}

		// Development environment
		if ( defined( 'SUDOERS_DEV_URL' ) && false !== strpos( $_SERVER['HTTP_HOST'], SUDOERS_DEV_URL ) ) {
			return 'dev';
		}

		// Staging environment
		if ( defined( 'SUDOERS_STAGE_URL' ) && false !== strpos( $_SERVER['HTTP_HOST'], SUDOERS_STAGE_URL ) ) {
			return 'stage';
		}

		// Production environment
		if ( defined( 'SUDOERS_PROD_URL' ) && false !== strpos( $_SERVER['HTTP_HOST'], SUDOERS_PROD_URL ) ) {
			return 'production';
		}

		// Something is not right ...
		return false;
	}
}

if ( ! function_exists( 'is_dev' ) ) {
	/**
	 * Get whether this is a development environment
	 *
	 * @uses get_environment()
	 *
	 * @return bool True if on a development environment, false on production
	 */
	function is_dev() {
		$env = get_environment();

		if ( $env && ( 'local' == $env || 'stage' == $env || 'dev' == $env ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'is_prod' ) ) {
	/**
	 * Get whether this is a production environment
	 *
	 * @uses get_environment()
	 *
	 * @return bool True if on a production environment, false on development
	 */
	function is_prod() {
		$env = get_environment();

		if ( $env && ( 'production' == $env ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'sudoers_fix_staging_url' ) ) {
	/**
	 * Patch a provided url to give production url instead of development
	 *
	 * @param string $url The provided URL
	 *
	 * @uses SUDOERS_LOCAL_URL
	 * @uses SUDOERS_DEV_URL
	 * @uses SUDOERS_STAGE_URL
	 * @uses SUDOERS_PROD_URL
	 *
	 * @return string The patched URL
	 */
	function sudoers_fix_staging_url( $url ) {
		if ( defined( 'SUDOERS_LOCAL_URL' ) ) {
			$url = str_replace( SUDOERS_LOCAL_URL, SUDOERS_PROD_URL, $url );
		}

		if ( defined( 'SUDOERS_DEV_URL' ) ) {
			$url = str_replace( SUDOERS_DEV_URL, SUDOERS_PROD_URL, $url );
		}

		if ( defined( 'SUDOERS_STAGE_URL' ) ) {
			$url = str_replace( SUDOERS_LOCAL_URL, SUDOERS_PROD_URL, $url );
		}

		return $url;
	}
}

if ( ! function_exists( 'sudoers_rewrite_thumbs' ) ) {
	/**
	 * Rewrite development image URLs to production image URLs, so that copying production
	 * images to development servers is not necessary.
	 *
	 * @param array $atts Image attribute array
	 *
	 * @uses SUDOERS_ENABLE_DEV_IMAGE_REWRITE
	 * @uses sudoers_fix_staging_url()
	 *
	 * @return array Patched image attribute array
	 */
	function sudoers_rewrite_thumbs( $atts ) {
		if ( true === SUDOERS_ENABLE_DEV_IMAGE_REWRITE ) {
			$atts['src'] = sudoers_fix_staging_url( $atts['src'] );
			$atts['srcset'] = sudoers_fix_staging_url( $atts['srcset'] );
		}

		return $atts;
	}
	add_filter( 'wp_get_attachment_image_attributes', 'sudoers_rewrite_thumbs' );
}

if ( ! function_exists( 'sudoers_head_cleanup' ) ) {
	/**
	 * Remove some fluff from the wp_head action
	 *
	 * @uses remove_action()
	 *
	 * @return void
	 */
	function sudoers_head_cleanup() {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		remove_action( 'wp_head', 'wp_generator' );
	}
	add_action( 'wp_head', 'sudoers_head_cleanup' );
}

if ( ! function_exists( 'sudoers_get_adjacent_post' ) ) {
	/**
	 * Get an adjacent post in a single category
	 *
	 * WordPress builtin get_adjacent_post matches all categories that a post is 
	 * in. This matches only a single specified category.
	 *
	 * @param array $args Arguments array
	 *
	 * @global $post WordPress post object
	 * @global $wpdb WordPress database object
	 *
	 * @uses wp_parse_args()
	 * @uses WP_Error
	 * @uses absint()
	 * @uses get_category_by_slug()
	 * @uses $wpdb->prepare()
	 * @uses wp_cache_get()
	 * @uses $wpdb->get_var()
	 * @uses wp_cache_set()
	 * @uses esc_url_raw()
	 * @uses get_permalink()
	 * @uses home_url()
	 * 
	 * @return mixed Result as url/string or post ID
	 */
	function sudoers_get_adjacent_post( $args ) {
		global $post, $wpdb;

		// Parse in default arguments
		$args = wp_parse_args( $args, array( 
			'in_category' => false, 
			'post_id' => false, 
			'return' => 'permalink', 
		));

		// Throw an error if no category is provided
		if ( ! $args['in_category'] ) {
			return new WP_Error( 'In_category not provided.' );
		}

		// Use $post->ID if no post ID provided
		if ( ! $args['post_id'] ) {
			$args['post_id'] = absint( $post->ID );
		}

		// Get the category ID from the slug if passed
		if ( ! is_numeric( $args['in_category'] ) ) {
			$args['in_category'] = get_category_by_slug( absint( $args['in_category'] ) )->term_id;
		}

		// Formulate the query
		$query = $wpdb->prepare( "
			SELECT p.ID FROM $wpdb->posts AS p 
			INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id 
			INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
			AND tt.taxonomy = 'category'
			AND tt.term_id IN (%d) 
			WHERE p.ID < %d 
			AND p.post_type = 'post'
			AND p.post_status = 'publish'
			ORDER BY p.ID DESC LIMIT 1 
		", $args['in_category'], $args['post_id'] );

		// Formulate the cache key
		$cache_key = md5( serialize( $args ) );

		// Try to get the result from cache first
		$result = wp_cache_get( $cache_key, 'sudoers' );

		// No cache present, run the query and cache the result
		if ( ! $result ) {
			$result = $wpdb->get_var( $query );

			wp_cache_set( $cache_key, $result, 'sudoers', 3600 );
		}

		// Return the result in whatever format was requested
		if ( null === $result && 'permalink' == $args['return'] ) {
			return esc_url_raw( home_url() );
		} elseif ( 'id' == $args['return'] ) {
			return esc_url_raw( get_permalink( $result ) );
		} else {
			return absint( $result );
		}
	}
}

// omit