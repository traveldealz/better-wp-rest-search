<?php
/**
 * Plugin Name:  Better WP REST Search
 * Plugin URI:
 * Description:  Better WP REST Search for the WordPress Block Editor (Gutenberg)
 * Author:       Johannes Kinast <johannes@travel-dealz.de>
 * Author URI:   https://go-around.de
 * Version:     2.0.0
 */
namespace Better_WP_REST_Search;

use Better_WP_REST_Search\Handler\WP_REST_Taxonomy_Search_Handler;
use Better_WP_REST_Search\Handler\WP_REST_Prettylink_Search_Handler;

function wp_rest_search_handlers( $handlers ) {

	// Add Taxonomy Handler
	require_once __DIR__ . '/handler/class-wp-rest-taxonomy-search-handler.php';
	$handlers[] = new WP_REST_Taxonomy_Search_Handler();

	// Add Pretty Link Handler
	require_once __DIR__ . '/handler/class-wp-rest-prettylink-search-handler.php';
	$handlers[] = new WP_REST_Prettylink_Search_Handler();

	return $handlers;
}
add_filter( 'wp_rest_search_handlers', __NAMESPACE__ . '\wp_rest_search_handlers' );

function change_search_type( $result, $server, $request ) {

	if ( '/wp/v2/search' !== $request->get_route() ) {
		return $result;
	}

	$supported = [
		'tax' => 'taxonomy',
		'ter' => 'taxonomy',
		'pl' => 'prettylink',
	];

	$matches = [];
	preg_match( '/^-(\w{2,3}) (.*)/', $request->get_param( 'search' ), $matches );
	if ( 3 === count( $matches ) && isset( $supported[$matches[1]] ) ) {

		$request->set_param( 'type', $supported[$matches[1]] );
		$request->set_param( 'search', $matches[2] );

	}

	return $result;
}
add_filter( 'rest_pre_dispatch', __NAMESPACE__ . '\change_search_type', 10, 3 );

function search_query_args_posts( $query_args ) {
	// Include all public post status
	unset( $query_args['post_status'] );
	return $query_args;
}
add_filter( 'rest_post_search_query', __NAMESPACE__ . '\search_query_args_posts' );