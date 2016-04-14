<?php
/**
 * sudoers functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package sudoers
 */

/**
 * Define environment URLs
 */
define( 'SUDOERS_LOCAL_URL', 'sudoers.dev' );
define( 'SUDOERS_DEV_URL', 'dev.sudoers.com' );
define( 'SUDOERS_STAGE_URL', 'stage.sudoers.com' );
define( 'SUDOERS_PROD_URL', 'www.sudoers.com' );

/**
 * Enable/disable rewriting images on dev servers to production URLs
 */
define( 'SUDOERS_ENABLE_DEV_IMAGE_REWRITE', true );

/**
 * Define some category slugs for use in various functions
 */
define( 'SUDOERS_SLIDESHOW_CATEGORY', 'slideshows' );
define( 'SUDOERS_VIDEO_CATEGORY', 'videos' );

/**
 * Load theme dependencies
 */
require get_template_directory() . '/inc/sudoers-utilities.php';
require get_template_directory() . '/inc/sudoers-template-tags.php';
require get_template_directory() . '/inc/sudoers-extras.php';
require get_template_directory() . '/inc/sudoers-customizer.php';
require get_template_directory() . '/inc/sudoers-jetpack.php';
require get_template_directory() . '/inc/sudoers-wp-admin.php';
require get_template_directory() . '/inc/sudoers-sidebars.php';
require get_template_directory() . '/inc/sudoers-scripts.php';

/**
 * Load CLI command if this is a CLI run
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require get_template_directory() . '/inc/sudoers-cli-command.php';
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
if ( ! function_exists( 'sudoers_setup' ) ) {
	function sudoers_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'sudoers', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		));

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
		));

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'sudoers_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		)));

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'header' => esc_html__( 'Header', 'sudoers' ),
			'footer' => esc_html__( 'Footer', 'sudoers' ), 
			'sidebar' => esc_html__( 'Sidebar', 'sudoers' ), 
			'mobile' => esc_html__( 'Mobile', 'sudoers' ), 
		));
	}
	add_action( 'after_setup_theme', 'sudoers_setup' );
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function sudoers_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sudoers_content_width', 640 );
}
add_action( 'after_setup_theme', 'sudoers_content_width', 0 );

// omit