<?php
/**
 * Plugin Name: WDS TLC Transients
 * Plugin URI: http://webdevstudios
 * Description: Set up TLC transients for theme.
 * Version: 1.0.0
 * Author: WebDevStudios
 * Author URI: http://webdevstudios.com
 * License: GPLv2 or later
 */

wds_tlc_load_dependency();

/**
 * Load this plugin's third-party dependency.
 *
 * Check whether it's relative to this plugin's root, or to the wp-content directory, using default Composer vendor
 * directory names.
 *
 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since  2019-01-02
 * @return void
 */
function wds_tlc_load_dependency() {
	$file       = '/vendor/markjaquith/wp-tlc-transients/tlc-transients.php';
	$paths = array_filter( [
		untrailingslashit( __DIR__ ) . $file,
		untrailingslashit( WP_CONTENT_DIR ) . $file,
	], function ( $path ) {
		return is_readable( $path );
	} );

	if ( count( $paths ) ) {
		require_once $paths[0];
	}
}

/**
 * Use in place of `get_posts()`.
 *
 * @param  array   $wp_query_args Array of get_posts arguments
 * @param  array   $tlc_args Array of TLC arguments
 *
 * @return array   Array of post objects
 */
function wds_cache_get_posts( $wp_query_args = array(), $tlc_args = array() ) {

	// Parse args
	$tlc_args = wp_parse_args( $tlc_args,
		array(
			'time'       => 12 * HOUR_IN_SECONDS,
			'callback'   => 'get_posts',
			'cache_bust' => false
		)
	);

	// Run wp_query
	$posts = wds_cache_wp_query( $wp_query_args, $tlc_args );

	// Return posts
	return ! empty( $posts ) ? $posts : array();
}


/**
 * Use in place of `new WP_Query()`.
 *
 * @param  array   $wp_query_args Array of get_posts arguments
 * @param  array   $tlc_args Array of TLC arguments
 *
 * @return WP_Query object
 */
function wds_cache_wp_query( $wp_query_args = array(), $tlc_args = array() ) {

	// Set default values for WP_Query
	$wp_query_args = wp_parse_args( $wp_query_args,

		/**
		 * Setting default WP_Query values after
		 * _wds_get_original_wp_query() clears them.
		 */
		array(
			'offset'              => 0,
			'category'            => '',
			'category_name'       => '',
			'orderby'             => 'post_date',
			'order'               => 'DESC',
			'include'             => '',
			'exclude'             => '',
			'meta_key'            => '',
			'meta_value'          => '',
			'post_type'           => 'post',
			'post_mime_type'      => '',
			'post_parent'         => '',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'suppress_filters'    => true,
		)
	);

	// Set default values for TLC Transients
	$tlc_args = wp_parse_args( $tlc_args,
		array(
			'time'       => 12 * HOUR_IN_SECONDS,
			'callback'   => '_wds_get_original_wp_query',
			'cache_bust' => false,
		)
	);

	// Setting transient key
	$trans_key = md5( $tlc_args['callback'] . serialize( $wp_query_args ) );

	// Cache query
	$tlc = tlc_transient( $trans_key )
		->updates_with( $tlc_args['callback'], array( $wp_query_args ) )
		->expires_in( $tlc_args['time'] );

	// Get cache query
	$query = $tlc->get();

	// Request a new fetch if result is empty or we're busting some cache
	if ( ( empty( $query ) || is_a( $query, 'WP_Query' ) && ! $query->have_posts() ) || $tlc_args['cache_bust'] ) {
		$query = $tlc->fetch_and_cache();
	}

	return $query;
}


/**
 * Wrapper for WP_Query class.
 *
 * note: Don't call directly, use wds_cache_wp_query()
 */
function _wds_get_original_wp_query( $args = array() ) {
	return new WP_Query( $args );
}
