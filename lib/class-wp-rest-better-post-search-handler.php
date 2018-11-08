<?php

class WP_REST_Better_Post_Search_Handler extends WP_REST_Post_Search_Handler {

/**
 * Searches the object type content for a given search request.
 *
 * @since 3.3.0
 *
 * @param WP_REST_Request $request Full REST request.
 * @return array Associative array containing an `WP_REST_Search_Handler::RESULT_IDS` containing
 *               an array of found IDs and `WP_REST_Search_Handler::RESULT_TOTAL` containing the
 *               total count for the matching search results.
 */
 public function search_items( WP_REST_Request $request ) {
	// Get the post types to search for the current request.
	$post_types = $request[ WP_REST_Search_Controller::PROP_SUBTYPE ];
	if ( in_array( WP_REST_Search_Controller::TYPE_ANY, $post_types, true ) ) {
		$post_types = $this->subtypes;
	}
	$query_args = array(
		'post_type'           => $post_types,
		// Don't set the post_status to publish, take all default post_status
		//'post_status'         => 'publish',
		'paged'               => (int) $request['page'],
		'posts_per_page'      => (int) $request['per_page'],
		'ignore_sticky_posts' => true,
		'fields'              => 'ids',
	);
	if ( ! empty( $request['search'] ) ) {
		$query_args['s'] = $request['search'];
	}
	$query     = new WP_Query();
	$found_ids = $query->query( $query_args );
	$total     = $query->found_posts;
	return array(
		self::RESULT_IDS   => $found_ids,
		self::RESULT_TOTAL => $total,
	);
}
}