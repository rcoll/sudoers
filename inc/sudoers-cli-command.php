<?php

if ( ! class_exists( 'Sudoers_CLI_Command' ) ) {

	/**
	 * CLI command with several useful features
	 *
	 * @package wp-cli
	 * @since 1.0
	 * @see https://github.com/wp-cli/wp-cli
	 */
	class Sudoers_CLI_Command extends WP_CLI_Command {

		/**
		 * Set a single thumbnail ID as the featured image of every post
		 *
		 * ## OPTIONS
		 *
		 * --thumbnail_id=<int>
		 * : The post ID of the thumbnail to use
		 * 
		 * [--dryrun=<bool>]
		 * : Whether this is a dryrun or not
		 * ---
		 * default: true
		 * options:
		 *    - true
		 *    - false
		 *
		 * ## EXAMPLES
		 *
		 *      wp sudoers single-featured-image --thumbnail_id=123456 --dryrun=false
		 *
		 * @subcommand single-featured-image
		 * @synopsis --thumbnail_id=<int> [--dryrun=<true|false>]
		 */
		public function single_featured_image( $args, $assoc_args ) {
			// Get the thumbnail ID from argument
			$thumbnail_id = ( isset( $assoc_args['thumbnail_id'] ) ) ? absint( $assoc_args['thumbnail_id'] ) : false;
			$thumbnail = get_post( $thumbnail_id );

			// Bail if given a non-attachment post ID
			if ( ! $thumbnail || 'attachment' !== $thumbnail->post_type ) {
				WP_CLI::error( 'Invalid thumbnail ID.' );

				exit;
			}

			// Get dryrun parameter
			$is_dryrun = ( isset( $assoc_args['dryrun'] ) && 'false' == $assoc_args['dryrun'] ) ? false : true;

			// Get all posts
			$post_ids = get_posts( array( 
				'posts_per_page' => -1, 
				'post_status' => 'any', 
				'fields' => 'ids', 
			));

			foreach ( $post_ids as $post_id ) {
				if ( $is_dryrun ) {
					WP_CLI::log( "(dryrun) Set thumbnail $thumbnail_id to post $post_id" );
				} else { 
					// Set the post thumbnail
					$status = set_post_thumbnail( $post_id, $thumbnail_id );

					if ( $status ) {
						WP_CLI::log( "Successfully set thumbnail $thumbnail_id to post $post_id" );
					} else {
						WP_CLI::warning( "Error setting thumbnail $thumbnail_id to post $post_id" );
					}
				}
			}
		}

		/**
		 * Set random thumbnails to every post
		 *
		 * ## OPTIONS
		 * 
		 * [--dryrun=<bool>]
		 * : Whether this is a dryrun or not
		 * ---
		 * default: true
		 * options:
		 *    - true
		 *    - false
		 *
		 * ## EXAMPLES
		 *
		 *      wp sudoers random-featured-images --dryrun=false
		 *
		 * @subcommand random-featured-images
		 * @synopsis [--dryrun=<true|false>]
		 */
		public function random_featured_images( $args, $assoc_args ) {
			// Get dryrun parameter
			$is_dryrun = ( isset( $assoc_args['dryrun'] ) && 'false' == $assoc_args['dryrun'] ) ? false : true;

			// Get an array of all post IDs
			$post_ids = get_posts( array(
				'posts_per_page' => -1, 
				'post_status' => 'any', 
				'fields' => 'ids', 
			));

			// Get an array of all attachment IDs
			$thumbnail_ids = get_posts( array( 
				'posts_per_page' => -1, 
				'post_type' => 'attachment', 
				'post_status' => 'inherit', 
				'fields' => 'ids', 
			));

			// Initialize some count variables
			$count_updated = 0;
			$count_failures = 0;

			// Loop through posts
			foreach ( $post_ids as $post_id ) {
				// Get a random attachment ID from our array
				$thumbnail_id = $thumbnail_ids[ rand( 0, count( $thumbnail_ids ) - 1 ) ];

				if ( $is_dryrun ) {
					WP_CLI::log( "(dryrun) Set thumbnail $thumbnail_id to post $post_id" );
					$count_updated++;
				} else { 
					// Set the post thumbnail
					$status = set_post_thumbnail( $post_id, $thumbnail_id );

					if ( $status ) {
						WP_CLI::log( "Successfully set thumbnail $thumbnail_id to post $post_id" );
						$count_updated++;
					} else {
						WP_CLI::warning( "Error setting thumbnail $thumbnail_id to post $post_id" );
						$count_failures++;
					}
				}
			}

			WP_CLI::success( "$count_updated posts updated. $count_failures posts failed." );
		}

		/**
		 * Free memory during loops
		 *
		 * @param int $delay Time in seconds to sleep
		 *
		 * @global $wpdb WordPress database object
		 * @global $wp_object_cache WordPress object cache object
		 *
		 * @return void
		 */
		protected function stop_the_insanity( $delay = 0 ) {
			global $wpdb, $wp_object_cache;
			
			// Free database queries
			$wpdb->queries = array();
			
			// Return if not using object cache
			if ( ! is_object( $wp_object_cache ) )
				return;
			
			// Free object cache data
			$wp_object_cache->group_ops = array();
			$wp_object_cache->stats = array();
			$wp_object_cache->memcache_debug = array();
			$wp_object_cache->cache = array();
			
			// Unclear what this does, but works on wpcom
			if ( is_callable( $wp_object_cache, '__remoteset' ) )
				$wp_object_cache->__remoteset();

			// Sleep if required
			if ( $delay ) {
				sleep( $delay );
			}
		}
	}

	WP_CLI::add_command( 'sudoers', 'Sudoers_CLI_Command' );
}