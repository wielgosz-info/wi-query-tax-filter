import {
	getContext,
	getElement,
	store,
	withScope,
} from '@wordpress/interactivity';

const { callbacks } = store('wielgosz-info/wi-query-tax-filter', {
	callbacks: {
		onNavigate(event) {
			const { ref } = getElement();
			const navParams = new URLSearchParams(
				event?.destination?.url ||
					event?.target?.location?.href ||
					window.location.href
			);
			const hrefParams = new URLSearchParams(ref.href);
			const queryPrefix = ref.closest('[data-wp-router-region]').dataset
				.wpRouterRegion;
			const taxonomyKey = `${queryPrefix}-qt-taxonomy`;
			const termKey = `${queryPrefix}-qt-term`;

			const isCurrent =
				(navParams.get(taxonomyKey) === hrefParams.get(taxonomyKey) &&
					navParams.get(termKey) === hrefParams.get(termKey)) ||
				event?.target === ref;
			getContext().isCurrent = isCurrent;

			// TODO: updated context is not rendering correctly when using browser back/forward.
		},

		init() {
			const onNavigate = withScope(callbacks.onNavigate);

			// Initial check.
			onNavigate();

			if (window.navigation) {
				window.navigation.addEventListener('navigate', onNavigate);

				return () => {
					window.navigation.removeEventListener(
						'navigate',
						onNavigate
					);
				};
			}
			// Fallback to partial handling via click.
			const { ref } = getElement();
			const block = ref.closest('[data-wp-interactive]');

			block.addEventListener('click', onNavigate, { passive: true });

			return () => {
				block.removeEventListener('click', onNavigate);
			};
		},
	},
});
