/* global wp, ajaxurl, WCCorreiosAdminOrdersParams */
jQuery( function( $ ) {

	/**
	 * Admin class.
	 *
	 * @type {Object}
	 */
	var WCCorreiosAdminOrders = {

		/**
		 * Initialize actions.
		 */
		init: function() {
			$( document.body )
				.on( 'click', '.correios-tracking-code .dashicons-dismiss', this.removeTrackingCode )
				.on( 'click', '.correios-tracking-code .button-secondary', this.addTrackingCode );
		},

		/**
		 * Block meta boxes.
		 */
		block: function() {
			$( '#wc_correios' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * Unblock meta boxes.
		 */
		unblock: function() {
			$( '#wc_correios' ).unblock();
		},

		/**
		 * Add tracking fields.
		 *
		 * @param {Object} $el Current element.
		 */
		addTrackingFields: function( trackingCodes ) {
			var $wrap = $( 'body #wc_correios .correios-tracking-code' );
			var template = wp.template( 'tracking-code-list' );

			$( '.correios-tracking-code__list', $wrap ).remove();
			$wrap.prepend( template( { 'trackingCodes': trackingCodes } ) );
		},

		/**
		 * Add tracking code.
		 *
		 * @param {Object} evt Current event.
		 */
		addTrackingCode: function( evt ) {
			evt.preventDefault();

			var $el          = $( '#add-tracking-code' );
			var trackingCode = $el.val();

			if ( '' === trackingCode ) {
				return;
			}

			var self = WCCorreiosAdminOrders;
			var data = {
				action: 'woocommerce_correios_add_tracking_code',
				security: WCCorreiosAdminOrdersParams.nonces.add,
				order_id: WCCorreiosAdminOrdersParams.order_id,
				tracking_code: trackingCode
			};

			self.block();

			// Clean input.
			$el.val( '' );

			// Add tracking code.
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: data,
				success: function( response ) {
					self.addTrackingFields( response.data );
					self.unblock();
				}
			});
		},

		/**
		 * Remove tracking fields.
		 *
		 * @param {Object} $el Current element.
		 */
		removeTrackingFields: function( $el ) {
			var $wrap = $( 'body #wc_correios .correios-tracking-code__list' );

			if ( 1 === $( 'li', $wrap ).length ) {
				$wrap.remove();
			} else {
				$el.closest( 'li' ).remove();
			}
		},

		/**
		 * Remove tracking code.
		 *
		 * @param {Object} evt Current event.
		 */
		removeTrackingCode: function( evt ) {
			evt.preventDefault();

			// Ask if really want remove the tracking code.
			if ( ! window.confirm( WCCorreiosAdminOrdersParams.i18n.removeQuestion ) ) {
				return;
			}

			var self = WCCorreiosAdminOrders;
			var $el  = $( this );
			var data = {
				action: 'woocommerce_correios_remove_tracking_code',
				security: WCCorreiosAdminOrdersParams.nonces.remove,
				order_id: WCCorreiosAdminOrdersParams.order_id,
				tracking_code: $el.data( 'code' )
			};

			self.block();

			// Remove tracking code.
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: data,
				success: function() {
					self.removeTrackingFields( $el );
					self.unblock();
				}
			});
		}
	};

	WCCorreiosAdminOrders.init();
});
