/* global ajaxurl, WCCorreiosIntegrationAdminParams */
jQuery( function( $ ) {

	/**
	 * Admin class.
	 *
	 * @type {Object}
	 */
	var WCCorreiosIntegrationAdmin = {

		/**
		 * Initialize actions.
		 */
		init: function() {
			$( document.body ).on( 'click', '#woocommerce_correios-integration_autofill_empty_database', this.empty_database );
		},

		/**
		 * Empty database.
		 *
		 * @return {String}
		 */
		empty_database: function() {
			if ( ! window.confirm( WCCorreiosIntegrationAdminParams.i18n_confirm_message ) ) {
				return;
			}

			$( '#mainform' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'correios_autofill_addresses_empty_database',
					nonce: WCCorreiosIntegrationAdminParams.empty_database_nonce
				},
				success: function( response ) {
					window.alert( response.data.message );
					$( '#mainform' ).unblock();
				}
			});
		}
	};

	WCCorreiosIntegrationAdmin.init();
});
