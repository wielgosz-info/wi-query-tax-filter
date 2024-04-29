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

?>

<?php
	if ( isset( $content ) ) {
		$p = new WP_HTML_Tag_Processor( $content );

		if ( $p->next_tag( array(
			'class_name' => 'wp-block-wielgosz-info-wi-query-tax-filter',
		) ) ) {
			$p->set_attribute('data-wp-interactive', 'wielgosz-info/wi-query-tax-filter');
		}

		while ( $p->next_tag( array(
			'tag_name' => 'a',
			'attr_name' => 'href',
		) ) ) {
			// split the href into the slug and the query string
			$href = $p->get_attribute( 'href' );
			$query = parse_url( $href, PHP_URL_QUERY );
			$args = wp_parse_args( $query, array(
				'qt_term' => 'all',
			) );
			$qt_term = $args['qt_term'];

			$p->set_attribute('data-wp-key', 'query-tax-filter-' . $qt_term);
			$p->set_attribute('data-wp-context', wp_json_encode( array(
				'isCurrent' => $qt_term === 'all',
			), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) );
			$p->set_attribute('data-wp-bind--aria-current', 'context.isCurrent');
			$p->set_attribute('data-wp-class--is-active', 'context.isCurrent');
			$p->set_attribute('data-wp-on--click', 'core/query::actions.navigate');
			$p->set_attribute('data-wp-on--mouseenter', 'core/query::actions.prefetch');
			$p->set_attribute('data-wp-watch', 'core/query::callbacks.prefetch');
			$p->set_attribute('data-wp-init', 'callbacks.init');
		}
		$content = $p->get_updated_html();

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content;
		// phpcs:enable
	}
?>
