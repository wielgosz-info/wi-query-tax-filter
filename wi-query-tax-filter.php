<?php
/**
 * Plugin Name:       WI Query Taxonomy Filter
 * Description:       Allows to use query vars to filter Query Block posts.
 * Requires at least: 6.5
 * Requires PHP:      8.2
 * Version:           0.1.0
 * Author:            Urszula Wielgosz
 * License:           MIT
 * Text Domain:       wi-query-tax-filter
 *
 * @package WI\QueryTaxFilter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function wi_query_tax_filter_wi_query_tax_filter_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'wi_query_tax_filter_wi_query_tax_filter_block_init' );

/**
 * Sanitizes the taxonomy filter parameters.
 *
 * @param string $name The parameter name.
 */
function wi_query_tax_filter_get_param( $name ) {
	return isset( $_GET[ $name ] ) ? sanitize_text_field( wp_unslash( $_GET[ $name ] ) ) : null;
}

/**
 * Filters the query vars for the Query Loop block.
 *
 * @param array    $query The query vars for the block.
 * @param WP_Block $block The block being rendered.
 * @param int      $page  The current page number.
 *
 * @return array The filtered query vars.
 */
function wi_query_tax_filter_query_loop_block_query_vars( array $query, WP_Block $block, int $page ) {
	if ( isset( $block->context['query'] ) && $block->context['query']['inherit'] ) {
		// If the block is inherited, we don't want to filter the query with separate parameters.
		// We want to use the query "standard" taxonomy filters.
		return $query;
	}

	$query_id = isset( $block->context['queryId'] ) ? $block->context['queryId'] : null;

	// If the block is for different query, queryPage and other filters will be null.
	$query_page = intval( wi_query_tax_filter_get_param( sprintf( 'query-%d-page', $query_id ) ) );
	$taxonomy   = wi_query_tax_filter_get_param( sprintf( 'query-%d-qt-taxonomy', $query_id ) );
	$term       = wi_query_tax_filter_get_param( sprintf( 'query-%d-qt-term', $query_id ) );

	if ( $query_page && $taxonomy && $term ) {
		$query['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term,
			),
		);
	}

	return $query;
}
add_filter( 'query_loop_block_query_vars', 'wi_query_tax_filter_query_loop_block_query_vars', 10, 3 );
