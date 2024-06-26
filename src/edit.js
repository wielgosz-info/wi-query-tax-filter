import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useMemo, useEffect, useRef } from '@wordpress/element';
import { SelectControl, RangeControl, PanelBody } from '@wordpress/components';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   props               Props.
 * @param {Object}   props.context
 * @param {Object}   props.attributes
 * @param {Function} props.setAttributes
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ context, attributes, setAttributes }) {
	const {
		query: { postType, taxQuery },
		queryId,
	} = context;
	const { taxonomy, number, buttonClassName } = attributes;
	const innerBlocksRef = useRef();
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
		if (!postTypeTaxonomies) {
			return [];
		}
		if (!taxQuery) {
			return postTypeTaxonomies;
		}
		return postTypeTaxonomies.filter((tax) => {
			return !Object.entries(taxQuery).some(([key, value]) => {
				if (tax.slug !== key || !value || !value.length) {
					return false;
				}
				return true;
			});
		});
	}, [postTypeTaxonomies, taxQuery]);
	const selectTaxonomyOptions = useMemo(() => {
		if (!otherTaxonomies) {
			return [];
		}

		return otherTaxonomies.map((tax) => {
			return {
				label: tax.name,
				value: tax.slug,
			};
		});
	}, [otherTaxonomies]);
	const terms = useSelect(
		(select) => {
			const { getEntityRecords } = select(coreStore);
			return getEntityRecords('taxonomy', taxonomy, {
				per_page: number,
				orderby: 'count',
				order: 'desc',
			});
		},
		[taxonomy, number]
	);
	const template = useMemo(() => {
		return [
			[
				'core/buttons',
				{
					justifyContent: 'flex-start',
					verticalAlignment: 'center',
				},
				[
					[
						'core/button',
						{
							text: __('All', 'wi-query-tax-filter'),
							url: `?query-${queryId}-page=1`,
							className: buttonClassName,
						},
					],
					...(terms
						? terms.map((term) => [
								'core/button',
								{
									text: term.name,
									url: `?query-${queryId}-page=1&query-${queryId}-qt-taxonomy=${taxonomy}&query-${queryId}-qt-term=${term.slug}`,
									className: buttonClassName,
								},
							])
						: []),
				],
			],
		];
	}, [terms, taxonomy, queryId, buttonClassName]);
	const innerBlocksProps = useInnerBlocksProps(useBlockProps(), {
		template,
		templateLock: 'all',
	});

	useEffect(() => {
		if (!taxonomy && otherTaxonomies && otherTaxonomies.length > 0) {
			setAttributes({ taxonomy: otherTaxonomies[0].slug });
		}
	}, [taxonomy, otherTaxonomies, setAttributes]);

	useEffect(() => {
		if (innerBlocksRef.current) {
			// Find is-style-* classes applied to the first button.
			const button =
				innerBlocksRef.current.querySelector('.wp-block-button');
			if (button) {
				const buttonClasses = button.className.split(' ');
				const buttonStyleClass = buttonClasses.find((className) =>
					className.startsWith('is-style-')
				);
				if (buttonStyleClass) {
					setAttributes({ buttonClassName: buttonStyleClass });
				}
			}
		}
	}, [setAttributes]);

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
					<RangeControl
						label={__('Number of terms', 'wi-query-tax-filter')}
						value={number}
						onChange={(value) => setAttributes({ number: value })}
						min={1}
						max={20}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...innerBlocksProps} ref={innerBlocksRef} />
		</>
	);
}
