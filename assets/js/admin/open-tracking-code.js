jQuery(function ($) {
	/**
	 * Admin class.
	 *
	 * @type {Object}
	 */
	const WCCorreiosOpenTrackingCode = {
		/**
		 * Initialize actions.
		 */
		init() {
			$(document.body).on(
				'click',
				'.correios-tracking-code a.tracking-code-link',
				this.openTrackingLink
			);
		},

		/**
		 * Open tracking link into Correios.
		 *
		 * @param {Object} evt Current event.
		 */
		openTrackingLink(evt) {
			evt.preventDefault();

			// Remove old form.
			$('#wc-correios-tracking__form').remove();

			const code = $(this).text();
			let form =
				'<form id="wc-correios-tracking__form" method="post" action="https://www2.correios.com.br/sistemas/rastreamento/resultado.cfm" target="_blank" rel="nofollow noopener noreferrer" style="display: none;">';
			form +=
				'<input type="hidden" name="objetos" value="' + code + '" />';
			form += '</form>';

			$('body').prepend(form);

			// Submit form.
			$('#wc-correios-tracking__form').submit();
		},
	};

	WCCorreiosOpenTrackingCode.init();
});
