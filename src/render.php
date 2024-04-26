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

$query_id = isset( $block->context['queryId'] ) ? $block->context['queryId'] : null;

if ( $query_id === null) {
	return;
}

$post_type = $block->context['query']['postType'];
$available_taxonomies = get_object_taxonomies( $post_type, 'names' );

if ( empty( $available_taxonomies ) ) {
	return;
}

if ( isset( $attributes['taxonomy'] ) && in_array( $attributes['taxonomy'], $available_taxonomies ) ) {
	$selected_taxonomy = $attributes['taxonomy'];
} else {
	$selected_taxonomy = $available_taxonomies[0];
}

$inherit_query = isset( $block->context['query']['inherit'] ) ? $block->context['query']['inherit'] : false;
if ( $inherit_query ) {
	global $wp_query;

	$main_taxonomy = isset( $wp_query->tax_query->queries[0] ) ? $wp_query->tax_query->queries[0]['taxonomy'] : null;

	if ( $selected_taxonomy === $main_taxonomy ) {
		// It doesn't make sense to filter the same taxonomy as the main query.
		return;
	}
}

$hide_empty = isset( $attributes['hideEmpty'] ) ? $attributes['hideEmpty'] : true;
$available_terms = get_terms( [
	'taxonomy' => $selected_taxonomy,
	'hide_empty' => $hide_empty,
] );

array_unshift( $available_terms, (object) array(
	'slug' => 'all',
	'name' => __('All', 'wi-query-tax-filter'),
) );

$query_page = sprintf( 'query-%d-page', $query_id );

?>

<ul <?php echo get_block_wrapper_attributes(); ?>>
	<?php foreach ( $available_terms as $term ) :
		$args = array(
			$query_page => 1,
		);

		
		if ( ! $inherit_query ) {
			if ( $term->slug !== 'all' ) {
				$args = array_merge( $args, array(
					'qt_taxonomy' => $selected_taxonomy,
					'qt_term' => $term->slug,
				) );
			} else {
				$args = array_merge( $args, array(
					'qt_taxonomy' => null,
					'qt_term' => null,
				) );
			}
		} else {
			if ( $term->slug !== 'all' ) {
				$args = array_merge( $args, array(
					'taxonomy' => $selected_taxonomy,
					'term' => $term->slug,
				) );
			} else {
				$args = array_merge( $args, array(
					'taxonomy' => null,
					'term' => null,
				) );
			}
		}

		$url = add_query_arg( $args );
	?>
		<li>
			<a
				href="<?php echo esc_url( $url ); ?>"
				data-wp-key="<?php esc_attr_e( sprintf( "query-tax-filter-%s", $term->slug ) ); ?>"
				data-wp-on--click="core/query::actions.navigate"
				data-wp-on--mouseenter="core/query::actions.prefetch"
				data-wp-watch="core/query::callbacks.prefetch"
			>
				<?php echo esc_html( $term->name ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
