<?php
namespace Better_WP_REST_Search\Handler;

use WP_REST_Search_Handler;
use WP_REST_Request;

/**
 * REST API: WP_REST_Taxonomy_Search_Handler class
 *
 */
/**
 * Plugin class representing a search handler for taxonomies in the REST API.
 *
 */
class WP_REST_Unsupported_Handler extends WP_REST_Search_Handler {

	public $type = 'unsupported';

	/**
	 * Searches the object type content for a given search request.
	 *
	 *
	 * @param WP_REST_Request $request Full REST request.
	 * @return array Associative array containing an `WP_REST_Search_Handler::RESULT_IDS` containing
	 *               an array of found IDs and `WP_REST_Search_Handler::RESULT_TOTAL` containing the
	 *               total count for the matching search results.
	 */
	public function search_items( WP_REST_Request $request ) {

		return array(
			self::RESULT_IDS   => [],
			self::RESULT_TOTAL => 0,
		);
	}
	/**
	 * Prepares the search result for a given ID.
	 *
	 *
	 * @param int   $id     Item ID.
	 * @param array $fields Fields to include for the item.
	 * @return array Associative array containing all fields for the item.
	 */
	public function prepare_item( $id, array $fields ) {
		return [];
	}
	/**
	 * Prepares links for the search result of a given ID.
	 *
	 *
	 * @param int $id Item ID.
	 * @return array Links for the given item.
	 */
	public function prepare_item_links( $id ) {
		return [];
	}

}