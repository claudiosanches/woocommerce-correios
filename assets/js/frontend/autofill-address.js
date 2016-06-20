/* global WCCorreiosAutofillAddressParams */
/*!
 * WooCommerce Correios Autofill Brazilian 2016.
 *
 * Autofill address with postcodes.
 *
 * Version: 3.0.0
 */

jQuery( function( $ ) {

	/**
	 * Autofill address class.
	 *
	 * @type {Object}
	 */
	var WCCorreiosAutofillAddress = {

		/**
		 * Initialize actions.
		 */
		init: function() {
			// Initial load.
			this.autofill( 'billing', true );

			$( document.body ).on( 'blur', '#billing_postcode', function() {
				WCCorreiosAutofillAddress.autofill( 'billing' );
			});
			$( document.body ).on( 'blur', '#shipping_postcode', function() {
				WCCorreiosAutofillAddress.autofill( 'shipping' );
			});
		},

		/**
		 * Block checkout.
		 */
		block: function() {
			$( 'form.checkout, form#order_review' )
				.addClass( 'processing' )
				.block({
					message: null,
					overlayCSS: {
					background: '#fff',
					opacity: 0.6
					}
				});
		},

		/**
		 * Unblock checkout.
		 */
		unblock: function() {
			$( 'form.checkout, form#order_review' )
				.removeClass( 'processing' )
				.unblock();
		},

		/**
		 * Autocomplate address.
		 *
		 * @param {String} field Target.
		 * @param {Boolean} copy
		 */
		autofill: function( field, copy ) {
			copy = copy || false;

			// Checks with *_postcode field exist.
			if ( $( '#' + field + '_postcode' ).length ) {

				// Valid CEP.
				var cep       = $( '#' + field + '_postcode' ).val().replace( '.', '' ).replace( '-', '' ),
					country   = $( '#' + field + '_country' ).val(),
					address_1 = $( '#' + field + '_address_1' ).val(),
					override  =  ( 'yes' === WCCorreiosAutofillAddressParams.force ) ? true : ( 0 === address_1.length );

				// Check country is BR.
				if ( cep !== '' && 8 === cep.length && 'BR' === country && override ) {

					WCCorreiosAutofillAddress.block();

					// Gets the address.
					$.ajax({
						type: 'GET',
						url: WCCorreiosAutofillAddressParams.url + '&postcode=' + cep,
						dataType: 'json',
						contentType: 'application/json',
						success: function( address ) {
							if ( address.success ) {
								WCCorreiosAutofillAddress.fillFields( field, address.data );

								if ( copy ) {
									var newField = 'billing' === field ? 'shipping' : 'billing';

									WCCorreiosAutofillAddress.fillFields( newField, address.data );
								}
							}

							WCCorreiosAutofillAddress.unblock();
						}
					});
				}
			}
		},

		/**
		 * Fill fields.
		 *
		 * @param {String} field
		 * @param {Object} data
		 */
		fillFields: function( field, data ) {
			// Address.
			$( '#' + field + '_address_1' ).val( data.address ).change();

			// Neighborhood.
			if ( $( '#' + field + '_neighborhood' ).length ) {
				$( '#' + field + '_neighborhood' ).val( data.neighborhood ).change();
			} else {
				$( '#' + field + '_address_2' ).val( data.neighborhood ).change();
			}

			// City.
			$( '#' + field + '_city' ).val( data.city ).change();

			// State.
			$( '#' + field + '_state option:selected' ).attr( 'selected', false ).change();
			$( '#' + field + '_state option[value="' + data.state + '"]' ).attr( 'selected', 'selected' ).change();
			$( '#' + field + '_state' ).trigger( 'liszt:updated' ).trigger( 'chosen:updated' ); // Chosen support.
		}
	};

	WCCorreiosAutofillAddress.init();
});
