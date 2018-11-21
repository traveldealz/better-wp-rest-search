<?php
/**
 * REST API: WP_REST_Taxonomy_Search_Handler class
 *
 */
/**
 * Plugin class representing a search handler for taxonomies in the REST API.
 *
 */
class WP_REST_Taxonomy_Search_Handler extends WP_REST_Search_Handler {
	/**
	 * Constructor.
	 *
	 * @since 3.3.0
	 */
	public function __construct() {
		$this->type = 'taxonomy';
		$this->subtypes = get_taxonomies( [
			'show_ui' => true,
			'public' => true,
		] );
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
		// Get the taxonomy types to search for the current request.
		$taxonomy_types = $request[ WP_REST_Search_Controller::PROP_SUBTYPE ];
		if ( in_array( WP_REST_Search_Controller::TYPE_ANY, $taxonomy_types, true ) ) {
			$taxonomy_types = $this->subtypes;
		}

		$offset = ( $request['page'] - 1 ) * $request['per_page'];

		$query_args = array(
			'taxonomy'           	=> $taxonomy_types,
			// Replace paged with offset
			//'paged'               	=> (int) $request['page'],
			'number'      			=> (int) $request['per_page'],
			'offset'				=> (int) $offset,
			'fields'              	=> 'ids',
			'hide_empty'			=> false,
		);
		if ( ! empty( $request['search'] ) ) {
			$query_args['search'] = $request['search'];
		}
		$query     = new WP_Term_Query();
		$found_ids = $query->query( $query_args );
		// ToDo
		//$total     = $query->found_posts;
		$total = 10;
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
		$term = get_term( $id );
		$data = array();
		if ( in_array( WP_REST_Search_Controller::PROP_ID, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_ID ] = (int) $term->term_id;
		}
		if ( in_array( WP_REST_Search_Controller::PROP_TITLE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TITLE ] = $term->name . ' (' . get_taxonomy( $term->taxonomy )->labels->singular_name . ')';
		}
		if ( in_array( WP_REST_Search_Controller::PROP_URL, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_URL ] = get_term_link( $term );
		}
		if ( in_array( WP_REST_Search_Controller::PROP_TYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_TYPE ] = $this->type;
		}
		if ( in_array( WP_REST_Search_Controller::PROP_SUBTYPE, $fields, true ) ) {
			$data[ WP_REST_Search_Controller::PROP_SUBTYPE ] = $term->taxonomy;
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
		$term = get_term( $id );
		$links = array();
		/*$item_route = $this->detect_rest_item_route( $term );
		if ( ! empty( $item_route ) ) {
			$links['self'] = array(
				'href'       => rest_url( $item_route ),
				'embeddable' => true,
			);
		}*/
		$links['about'] = array(
			'href' => rest_url( 'wp/v2/taxonomies/' . $term->taxonomy ),
		);
		return $links;
	}
	/**
	 * Attempts to detect the route to access a single item.
	 *
	 * @since 3.3.0
	 *
	 * @param WP_Post $post Post object.
	 * @return string REST route relative to the REST base URI, or empty string if unknown.
	 */
	protected function detect_rest_item_route( $post ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( ! $post_type ) {
			return '';
		}
		// It's currently impossible to detect the REST URL from a custom controller.
		if ( ! empty( $post_type->rest_controller_class ) && 'WP_REST_Posts_Controller' !== $post_type->rest_controller_class ) {
			return '';
		}
		$namespace = 'wp/v2';
		$rest_base = ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;
		return sprintf( '%s/%s/%d', $namespace, $rest_base, $post->ID );
	}
}