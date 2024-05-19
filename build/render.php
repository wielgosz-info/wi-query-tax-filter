<?php
/**
 * Query Taxonomy Filter Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block default content.
 * @param   WP_Block $block - The block instance.
 *
 * @package \WI\SonxFSE
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$wi_query_tax_filter_enhanced_pagination = isset( $block->context['enhancedPagination'] ) && $block->context['enhancedPagination'];
$wi_query_tax_filter_query_id            = isset( $block->context['queryId'] ) ? $block->context['queryId'] : null;

if ( $wi_query_tax_filter_enhanced_pagination && null !== $wi_query_tax_filter_query_id && isset( $content ) ) {
	$wi_query_tax_filter_term_key      = sprintf( 'query-%s-qt-term', $wi_query_tax_filter_query_id );
	$wi_query_tax_filter_taxonomy_key  = sprintf( 'query-%s-qt-taxonomy', $wi_query_tax_filter_query_id );
	$wi_query_tax_filter_tag_processor = new WP_HTML_Tag_Processor( $content );

	if ( $wi_query_tax_filter_tag_processor->next_tag(
		array(
			'class_name' => 'wp-block-wielgosz-info-wi-query-tax-filter',
		)
	) ) {
		$wi_query_tax_filter_tag_processor->set_attribute( 'data-wp-interactive', 'wielgosz-info/wi-query-tax-filter' );
	}

	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$wi_query_tax_filter_request_query = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_QUERY );
	} else {
		$wi_query_tax_filter_request_query = '';
	}

	$wi_query_tax_filter_request_args  = wp_parse_args( $wi_query_tax_filter_request_query );
	$wi_query_tax_filter_idx           = 0;

	while ( $wi_query_tax_filter_tag_processor->next_tag(
		array(
			'tag_name'  => 'a',
			'attr_name' => 'href',
		)
	) ) {
		// Split the href into the slug and the query string.
		$wi_query_tax_filter_href    = $wi_query_tax_filter_tag_processor->get_attribute( 'href' );
		$wi_query_tax_filter_query   = wp_parse_url( $wi_query_tax_filter_href, PHP_URL_QUERY );
		$wi_query_tax_filter_args    = wp_parse_args(
			$wi_query_tax_filter_query,
			array(
				$wi_query_tax_filter_term_key => 'all',
			)
		);
		$wi_query_tax_filter_qt_term = $wi_query_tax_filter_args[ $wi_query_tax_filter_term_key ];

		// If qt_term === 'all', we need to remove filtering form URL.
		if ( 'all' === $wi_query_tax_filter_qt_term ) {
			$wi_query_tax_filter_args[ $wi_query_tax_filter_term_key ]     = false;
			$wi_query_tax_filter_args[ $wi_query_tax_filter_taxonomy_key ] = false;
		}

		// Update href to include the current query string.
		$wi_query_tax_filter_merged_args  = array_merge( $wi_query_tax_filter_request_args, $wi_query_tax_filter_args );
		$wi_query_tax_filter_updated_href = add_query_arg( $wi_query_tax_filter_merged_args, $wi_query_tax_filter_href );
		$wi_query_tax_filter_tag_processor->set_attribute( 'href', esc_url( $wi_query_tax_filter_updated_href ) );

		$wi_query_tax_filter_tag_processor->set_attribute( 'data-wp-key', 'query-tax-filter-' . $wi_query_tax_filter_qt_term );
		$wi_query_tax_filter_tag_processor->set_attribute(
			'data-wp-context',
			wp_json_encode(
				array(
					'isCurrent' => 'all' === $wi_query_tax_filter_qt_term,
				),
				JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
			)
		);
		$wi_query_tax_filter_tag_processor->set_attribute( 'data-wp-bind--aria-current', 'context.isCurrent' );
		$wi_query_tax_filter_tag_processor->set_attribute( 'data-wp-class--is-active', 'context.isCurrent' );
		$wi_query_tax_filter_tag_processor->set_attribute( 'data-wp-on--click', 'core/query::actions.navigate' );
		$wi_query_tax_filter_tag_processor->set_attribute( 'data-wp-on--mouseenter', 'core/query::actions.prefetch' );
		$wi_query_tax_filter_tag_processor->set_attribute( 'data-wp-init', 'callbacks.init' );
	}

	$wi_query_tax_filter_content = $wi_query_tax_filter_tag_processor->get_updated_html();

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $wi_query_tax_filter_content;
	// phpcs:enable
}
