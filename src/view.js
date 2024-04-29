import { getContext, getElement, store, withScope } from '@wordpress/interactivity';

const { callbacks } = store('wielgosz-info/wi-query-tax-filter', {
	callbacks: {
		onNavigate(event) {
			const { ref } = getElement();
			const navParams = new URLSearchParams(event.destination.url);
			const hrefParams = new URLSearchParams(ref.href);

			const isCurrent = navParams.get('qt_taxonomy') === hrefParams.get('qt_taxonomy') && navParams.get('qt_term') === hrefParams.get('qt_term');
			getContext().isCurrent = isCurrent;
		},

		init() {
			if (window.navigation) {
				const onNavigate = withScope(callbacks.onNavigate);
				window.navigation.addEventListener("navigate", onNavigate);

				return () => {
					window.navigation.removeEventListener("navigate", onNavigate);
				}
			}
		}
	}
});
