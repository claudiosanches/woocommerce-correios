/* global WCCorreiosAutofillAddressParams */
/*!
 * Claudio Sanches - Correios for WooCommerce: Autofill Brazilian 2016.
 *
 * Autofill address with postcodes.
 *
 * Version: 3.0.0
 */

jQuery(function ($) {
	/**
	 * Autofill address class.
	 *
	 * @type {Object}
	 */
	const WCCorreiosAutofillAddress = {
		/**
		 * Initialize actions.
		 */
		init() {
			// Initial load.
			this.autofill('billing', true);

			$(document.body).on('blur', '#billing_postcode', function () {
				WCCorreiosAutofillAddress.autofill('billing');
			});
			$(document.body).on('blur', '#shipping_postcode', function () {
				WCCorreiosAutofillAddress.autofill('shipping');
			});
		},

		/**
		 * Block checkout.
		 */
		block() {
			$('form.checkout, form#order_review')
				.addClass('processing')
				.block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6,
					},
				});
		},

		/**
		 * Unblock checkout.
		 */
		unblock() {
			$('form.checkout, form#order_review')
				.removeClass('processing')
				.unblock();
		},

		/**
		 * Autocomplate address.
		 *
		 * @param {string}  field Target.
		 * @param {boolean} copy
		 */
		autofill(field, copy) {
			copy = copy || false;

			// Checks with *_postcode field exist.
			if ($('#' + field + '_postcode').length) {
				// Valid CEP.
				const cep = $('#' + field + '_postcode')
						.val()
						.replace('.', '')
						.replace('-', ''),
					country = $('#' + field + '_country').val(),
					address1 = $('#' + field + '_address_1').val(),
					override =
						'yes' === WCCorreiosAutofillAddressParams.force
							? true
							: 0 === address1.length;

				// Check country is BR.
				if (
					cep !== '' &&
					8 === cep.length &&
					'BR' === country &&
					override
				) {
					WCCorreiosAutofillAddress.block();

					// Gets the address.
					$.ajax({
						type: 'GET',
						url:
							WCCorreiosAutofillAddressParams.url +
							'&postcode=' +
							cep,
						dataType: 'json',
						contentType: 'application/json',
						success(address) {
							if (address.success) {
								WCCorreiosAutofillAddress.fillFields(
									field,
									address.data
								);

								if (copy) {
									const newField =
										'billing' === field
											? 'shipping'
											: 'billing';

									WCCorreiosAutofillAddress.fillFields(
										newField,
										address.data
									);
								}
							}

							WCCorreiosAutofillAddress.unblock();
						},
					});
				}
			}
		},

		/**
		 * Fill fields.
		 *
		 * @param {string} field
		 * @param {Object} data
		 */
		fillFields(field, data) {
			// Address.
			if (data.address) {
				$('#' + field + '_address_1')
					.val(data.address)
					.change();
			}

			// Neighborhood.
			if (data.neighborhood) {
				if ($('#' + field + '_neighborhood').length) {
					$('#' + field + '_neighborhood')
						.val(data.neighborhood)
						.change();
				} else {
					$('#' + field + '_address_2')
						.val(data.neighborhood)
						.change();
				}
			}

			// City.
			$('#' + field + '_city')
				.val(data.city)
				.change();

			// State.
			$('#' + field + '_state')
				.val(data.state)
				.change();
		},
	};

	WCCorreiosAutofillAddress.init();
});
