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