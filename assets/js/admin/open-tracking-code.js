jQuery( function( $ ) {

	/**
	 * Admin class.
	 *
	 * @type {Object}
	 */
	var WCCorreiosOpenTrackingCode = {

		/**
		 * Initialize actions.
		 */
		init: function() {
			$( document.body )
				.on( 'click', '.correios-tracking-code a', this.openTrackingLink );
		},

		/**
		 * Open tracking link into Correios.
		 *
		 * @param {Object} evt Current event.
		 */
		openTrackingLink: function( evt ) {
			evt.preventDefault();

			// Remove old form.
			$( '#wc-correios-tracking__form' ).remove();

			var code  = $( this ).text();
			var form = '<form id="wc-correios-tracking__form" method="post" action="http://www2.correios.com.br/sistemas/rastreamento/resultado_semcontent.cfm" target="_blank" rel="nofollow noopener noreferrer" style="display: none;">';
			form += '<input type="hidden" name="objetos" value="' + code + '" />';
			form += '</form>';

			$( 'body' ).prepend( form );

			// Submit form.
			$( '#wc-correios-tracking__form' ).submit();
		}
	};

	WCCorreiosOpenTrackingCode.init();
});
