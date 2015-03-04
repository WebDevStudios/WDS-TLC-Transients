<?php
/**
 * Example usage.
 *
 * Instead of calling WP_Query directly, you would write a function
 * like below and call it instead. You can treat the results the same
 * as the results of WP_Query() or get_posts();
 *
 */


/**
 * Perform a WP_Query() and cache it.
 *
 * @param  boolean  $cache_bust  If true, ignores all caches and busts them up.
 * @return object                WP_Query results.
 */
function wds_query_featured_posts( $cache_bust = false ) {

	return wds_cache_wp_query(

		// WP_Query args
		array(
			'posts_per_page' => 3,
			'category'       => 'featured',
		),

		// TLC Transient args
		array(
			'time'       => 12 * HOUR_IN_SECONDS,
			'cache_bust' => $cache_bust,
		)
	);

}


/**
 * Perform get_posts() and cache it.
 *
 * @param  boolean  $cache_bust  If true, ignores all caches and busts them up.
 * @return array                 An array of posts.
 */
function wds_get_featured_posts( $cache_bust = false ) {

	return wds_cache_get_posts(

		// WP_Query args
		array(
			'posts_per_page' => 3,
			'category'       => 'featured',
		),

		// TLC Transient args
		array(
			'time'       => 12 * HOUR_IN_SECONDS,
			'cache_bust' => $cache_bust,
		)
	);

}


/**
 * Flush out transients.
 */
function wds_transient_flusher() {
	wds_query_featured_posts( true );
	wds_get_featured_posts( true );
}
add_action( 'edit_category', 'wds_transient_flusher' );
add_action( 'save_post',     'wds_transient_flusher' );
