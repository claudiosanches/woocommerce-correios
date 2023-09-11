/* global ajaxurl, WCCorreiosIntegrationAdminParams */
jQuery(function ($) {
	/**
	 * Admin class.
	 *
	 * @type {Object}
	 */
	const WCCorreiosIntegrationAdmin = {
		/**
		 * Initialize actions.
		 */
		init() {
			$(document.body).on(
				'click',
				'#woocommerce_correios-integration_cws_update_services_list',
				this.update_services_list
			);
			$(document.body).on(
				'click',
				'#woocommerce_correios-integration_autofill_empty_database',
				this.empty_database
			);
		},

		/**
		 * Update services list.
		 */
		update_services_list() {
			$('#mainform').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'correios_cws_update_services_list',
					nonce: WCCorreiosIntegrationAdminParams.update_cws_services_nonce,
				},
				success(response) {
					window.alert(response.data.message); // eslint-disable-line no-alert
					$('#mainform').unblock();
				},
			});
		},

		/**
		 * Empty database.
		 */
		empty_database() {
			const message =
				WCCorreiosIntegrationAdminParams.i18n_confirm_message;
			const confirm = window.confirm(message); // eslint-disable-line no-alert
			if (!confirm) {
				return;
			}

			$('#mainform').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'correios_autofill_addresses_empty_database',
					nonce: WCCorreiosIntegrationAdminParams.empty_database_nonce,
				},
				success(response) {
					window.alert(response.data.message); // eslint-disable-line no-alert
					$('#mainform').unblock();
				},
			});
		},
	};

	WCCorreiosIntegrationAdmin.init();
});
