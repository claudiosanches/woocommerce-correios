jQuery( document ).ready( function( $ ) {
	$( '#woocommerce_correios_display_date' ).on( 'change', function() {
		var field = $( this ).closest( 'tr' ).next( 'tr' );

		if ( $( this ).is( ':checked' ) ) {
			field.show();
		} else {
			field.hide();
		}
	}).change();

	$( '#woocommerce_correios_corporate_service' ).on( 'change', function() {
		var login    = $( '.form-table:eq(1) tr:eq(1)' ),
			password = $( '.form-table:eq(1) tr:eq(2)' ),
			eSedex   = $( '.form-table:eq(1) tr:eq(7)' );

		if ( 'corporate' === $( this ).val() ) {
			login.show();
			password.show();
			eSedex.show();
		} else {
			login.hide();
			password.hide();
			eSedex.hide();
		}
	}).change();
});
