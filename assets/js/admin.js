jQuery( document ).ready( function( $ ) {
	var correiosSelect = $( '#woocommerce_correios_corporate_service' ),
		correios_val = correiosSelect.val(),
		display_date = $( '#woocommerce_correios_display_date' ),
		correiosAddDays = $( '.form-table:eq(0) tr:eq(7)' ),
		correiosLogin = $( '.form-table:eq(1) tr:eq(1)' ),
		correiosPassword = $( '.form-table:eq(1) tr:eq(2)' ),
		correiosEsedex = $( '.form-table:eq(1) tr:eq(7)' );

	correiosAddDays.hide();
	correiosLogin.hide();
	correiosPassword.hide();
	correiosEsedex.hide();

	function addtionalDaysDisplay() {
		if ( display_date.is( ':checked' ) ) {
			correiosAddDays.show();
		} else {
			correiosAddDays.hide();
		}
	}
	addtionalDaysDisplay();

	display_date.on( 'click', function() {
		addtionalDaysDisplay();
	} );

	function correiosActive( correios ) {
		if ( 'corporate' === correios ) {
			correiosLogin.show();
			correiosPassword.show();
			correiosEsedex.show();
		} else {
			correiosLogin.hide();
			correiosPassword.hide();
			correiosEsedex.hide();
		}
	}
	correiosActive( correios_val );

	correiosSelect.on( 'change', function() {
		correiosActive( $( this ).val() );
	} );
});
