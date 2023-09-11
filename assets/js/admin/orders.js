/* global ajaxurl, WCCorreiosAdminOrdersParams */
jQuery(function ($) {
	/**
	 * Admin class.
	 *
	 * @type {Object}
	 */
	const WCCorreiosAdminOrders = {
		/**
		 * Initialize actions.
		 */
		init() {
			$(document.body)
				.on(
					'click',
					'.correios-tracking-code .dashicons-dismiss',
					this.removeTrackingCode
				)
				.on(
					'click',
					'.correios-tracking-code .button-secondary',
					this.addTrackingCode
				);
		},

		/**
		 * Block meta boxes.
		 */
		block() {
			$('#wc-correios').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		},

		/**
		 * Unblock meta boxes.
		 */
		unblock() {
			$('#wc-correios').unblock();
		},

		/**
		 * Add tracking fields.
		 *
		 * @param {Object} trackingCodes
		 */
		addTrackingFields(trackingCodes) {
			const $wrap = $('body #wc-correios .correios-tracking-code');
			const template = wp.template('tracking-code-list');

			$('.correios-tracking-code-list', $wrap).remove();
			$wrap.prepend(template({ trackingCodes }));
		},

		/**
		 * Add tracking code.
		 *
		 * @param {Object} evt Current event.
		 */
		addTrackingCode(evt) {
			evt.preventDefault();

			const $el = $('#add-tracking-code');
			const trackingCode = $el.val();

			if ('' === trackingCode) {
				return;
			}

			const self = WCCorreiosAdminOrders;
			const data = {
				action: 'woocommerce_correios_add_tracking_code',
				security: WCCorreiosAdminOrdersParams.nonces.add,
				order_id: WCCorreiosAdminOrdersParams.order_id,
				tracking_code: trackingCode,
			};

			self.block();

			// Clean input.
			$el.val('');

			// Add tracking code.
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data,
				success(response) {
					self.addTrackingFields(response.data);
					self.unblock();
				},
			});
		},

		/**
		 * Remove tracking fields.
		 *
		 * @param {Object} $el Current element.
		 */
		removeTrackingFields($el) {
			const $wrap = $('body #wc-correios .correios-tracking-code-list');

			if (1 === $('li', $wrap).length) {
				$wrap.remove();
			} else {
				$el.closest('li').remove();
			}
		},

		/**
		 * Remove tracking code.
		 *
		 * @param {Object} evt Current event.
		 */
		removeTrackingCode(evt) {
			evt.preventDefault();

			// Ask if really want remove the tracking code.
			if (
				!window.confirm(WCCorreiosAdminOrdersParams.i18n.removeQuestion) // eslint-disable-line no-alert
			) {
				return;
			}

			const self = WCCorreiosAdminOrders;
			const $el = $(this);
			const data = {
				action: 'woocommerce_correios_remove_tracking_code',
				security: WCCorreiosAdminOrdersParams.nonces.remove,
				order_id: WCCorreiosAdminOrdersParams.order_id,
				tracking_code: $el.data('code'),
			};

			self.block();

			// Remove tracking code.
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data,
				success() {
					self.removeTrackingFields($el);
					self.unblock();
				},
			});
		},
	};

	WCCorreiosAdminOrders.init();
});
