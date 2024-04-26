import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useMemo, useEffect } from '@wordpress/element';
import { SelectControl, PanelBody } from '@wordpress/components';

import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ context, attributes, setAttributes }) {
	const {
		query: { postType, taxQuery },
	} = context;
	const { taxonomy } = attributes;
	const postTypeTaxonomies = useSelect(
		(select) => {
			const { getTaxonomies } = select(coreStore);
			return getTaxonomies({
				type: postType,
				per_page: -1,
			});
		},
		[postType]
	);
	const otherTaxonomies = useMemo(() => {
		if (!taxQuery) {
			return postTypeTaxonomies;
		}
		return postTypeTaxonomies.filter((taxonomy) => {
			return !taxQuery.some((query) => query.taxonomy === taxonomy.slug);
		});
	}, [postTypeTaxonomies, taxQuery]);
	const selectTaxonomyOptions = useMemo(() => {
		if (!otherTaxonomies) {
			return [];
		}

		return otherTaxonomies.map((taxonomy) => {
			return {
				label: taxonomy.name,
				value: taxonomy.slug,
			};
		});
	}, [otherTaxonomies]);
	const terms = useSelect(
		(select) => {
			const { getEntityRecords } = select(coreStore);
			return getEntityRecords('taxonomy', taxonomy, {
				per_page: 10,
				orderby: 'count',
				order: 'desc',
			});
		},
		[taxonomy]
	);
	const template = useMemo(() => {
		return [[
			'core/buttons',
			{
				justifyContent: 'flex-start',
				verticalAlignment: 'center',
			},
			terms ? terms.map((term) => [
				'core/button',
				{ text: term.name, url: term.link },
			]) : [],
		]];
	}, [terms]);
	const innerBlockProps = useInnerBlocksProps(useBlockProps(), {
		template,
		templateLock: 'all',
	});

	useEffect(() => {
		if (!taxonomy && otherTaxonomies && otherTaxonomies.length > 0) {
			setAttributes({ taxonomy: otherTaxonomies[0].slug });
		}
	}, [taxonomy, otherTaxonomies]);

	return (
		<>
			<InspectorControls>
				<PanelBody>
					<SelectControl
						label={__('Taxonomy', 'wi-query-tax-filter')}
						value={taxonomy}
						options={selectTaxonomyOptions}
						onChange={(value) => setAttributes({ taxonomy: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...innerBlockProps} />
		</>
	);
}
