jQuery( document ).ready( function( $ ) {
	$( '#woocommerce_correios_display_date' ).on( 'change', function() {
		var correiosAddDays = $( '.form-table:eq(0) tr:eq(7)' );

		if ( $( this ).is( ':checked' ) ) {
			correiosAddDays.show();
		} else {
			correiosAddDays.hide();
		}
	}).change();

	$( '#woocommerce_correios_corporate_service' ).on( 'change', function() {
		var correiosLogin    = $( '.form-table:eq(1) tr:eq(1)' ),
			correiosPassword = $( '.form-table:eq(1) tr:eq(2)' ),
			correiosEsedex   = $( '.form-table:eq(1) tr:eq(7)' );

		if ( 'corporate' === $( this ).val() ) {
			correiosLogin.show();
			correiosPassword.show();
			correiosEsedex.show();
		} else {
			correiosLogin.hide();
			correiosPassword.hide();
			correiosEsedex.hide();
		}
	}).change();
});
