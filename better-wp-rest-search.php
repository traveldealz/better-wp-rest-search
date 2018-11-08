<?php
/**
 * Plugin Name:  Better WP REST Search
 * Plugin URI:
 * Description:  Better WP REST Search for Gutenberg
 * Author:       Johannes Kinast <johannes@travel-dealz.de>
 * Author URI:   http://
 * Version:     0.0.1
 */

add_filter( 'wp_rest_search_handlers', 'better_wp_rest_search_handlers' );

function better_wp_rest_search_handlers( $handlers ) {

	// Replace WP_REST_Post_Search_Handler
	include_once plugin_dir_path( __FILE__ ) . 'lib/class-wp-rest-better-post-search-handler.php';
	$handlers[0] = new WP_REST_Better_Post_Search_Handler();

	// Add Taxonomy Handler
	include_once plugin_dir_path( __FILE__ ) . 'lib/class-wp-rest-taxonomy-search-handler.php';
	$handlers[] = new WP_REST_Taxonomy_Search_Handler();

	// Add Pretty Link Handler
	include_once plugin_dir_path( __FILE__ ) . 'lib/class-wp-rest-prettylink-search-handler.php';
	$handlers[] = new WP_REST_Prettylink_Search_Handler();

	return $handlers;
}

add_filter( 'rest_pre_dispatch', 'better_wp_rest_search_parameters', 10, 3 );

function better_wp_rest_search_parameters( $result, $server, $request ) {

	if ( '/wp/v2/search' === $request->get_route() ) {

		$supported = [
			'tax' => 'taxonomy',
			'pl' => 'prettylink',
		];

		$matches = [];
		preg_match( '/^-(\w{2,3}) (.*)/', $request->get_param( 'search' ), $matches );

		if ( 3 === count( $matches ) && isset( $supported[$matches[1]] ) ) {

			$request->set_param( 'type', $supported[$matches[1]] );
			$request->set_param( 'search', $matches[2] );

		}

	}

	return $result;

}