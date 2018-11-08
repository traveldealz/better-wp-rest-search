<?php
/**
 * REST API: WP_REST_Taxonomy_Search_Handler class
 *
 */
/**
 * Plugin class representing a search handler for taxonomies in the REST API.
 *
 */
class WP_REST_Prettylink_Search_Handler extends WP_REST_Search_Handler {
	/**
	 * Constructor.
	 *
	 * @since 3.3.0
	 */
	public function __construct() {
		$this->type = 'prettylink';
		$this->subtypes = [ 'link' ];
	}
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

		$offset = ( $request['page'] - 1 ) * $request['per_page'];

		$query_args = array(
			'perpage'      			=> (int) $request['per_page'],
			'offset'				=> (int) $offset,
		);

		if ( ! empty( $request['search'] ) ) {
			$query_args['search'] = $request['search'];
		}

		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT id FROM `{$wpdb->base_prefix}prli_links` WHERE slug LIKE %s ORDER BY CHAR_LENGTH(slug) LIMIT %d,%d",
			'%' . urlencode( $query_args['search'] ) . '%',
			$query_args['offset'],
			$query_args['perpage']
		);

		$found_ids = array_column( $wpdb->get_results( $query ), 'id' );

		// ToDo
		//$total     = $query->found_posts;
		$total = 20;
		return array(
			self::RESULT_IDS   => $found_ids,
			self::RESULT_TOTAL => $total,
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
		$link = prli_get_link( $id );
		$data = array();
		if ( in_array( WP_REST_Search_Controller::PROP_ID, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_ID ] = (int) $link->id;
		}
		if ( in_array( WP_REST_Search_Controller::PROP_TITLE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TITLE ] = $link->name . ' (Pretty Link)';
		}
		if ( in_array( WP_REST_Search_Controller::PROP_URL, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_URL ] = prli_get_pretty_link_url( $link->id );
		}
		if ( in_array( WP_REST_Search_Controller::PROP_TYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TYPE ] = $this->type;
		}
		if ( in_array( WP_REST_Search_Controller::PROP_SUBTYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_SUBTYPE ] = 'link';
		}
		return $data;
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