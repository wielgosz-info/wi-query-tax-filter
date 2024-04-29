import { getContext, getElement, store, withScope } from '@wordpress/interactivity';

const { callbacks } = store('wielgosz-info/wi-query-tax-filter', {
	callbacks: {
		onNavigate(event) {
			const { ref } = getElement();
			const navParams = new URLSearchParams(event?.destination?.url || event?.target?.location?.href || window.location.href);
			const hrefParams = new URLSearchParams(ref.href);

			const isCurrent = (
				navParams.get('qt_taxonomy') === hrefParams.get('qt_taxonomy') && navParams.get('qt_term') === hrefParams.get('qt_term')
			) || event?.target === ref;
			getContext().isCurrent = isCurrent;

			// TODO: updated context is not rendering correctly when using browser back/forward.
		},

		init() {
			const onNavigate = withScope(callbacks.onNavigate);

			// Initial check.
			onNavigate();

			if (window.navigation) {
				window.navigation.addEventListener("navigate", onNavigate);

				return () => {
					window.navigation.removeEventListener("navigate", onNavigate);
				}
			} else {
				// Fallback to partial handling via click.
				const { ref } = getElement();
				const block = ref.closest('[data-wp-interactive]');

				block.addEventListener("click", onNavigate, { passive: true });

				return () => {
					block.removeEventListener("click", onNavigate);
				}
			}
		}
	}
});
