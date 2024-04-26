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

<ul <?php echo get_block_wrapper_attributes(); ?>>
	<li><a href="?query-1-page=1" data-wp-key="query-tax-filter-all" data-wp-on--click="core/query::actions.navigate" data-wp-on--mouseenter="core/query::actions.prefetch" data-wp-watch="core/query::callbacks.prefetch">All</a></li>
	<li><a href="?query-1-page=1&tqt_taxonomy=wi-project-category&gt_term=mentoring"  data-wp-key="query-tax-filter-wi-project-category-mentoring" data-wp-on--click="core/query::actions.navigate" data-wp-on--mouseenter="core/query::actions.prefetch" data-wp-watch="core/query::callbacks.prefetch">Mentoring</a></li>
</ul>
