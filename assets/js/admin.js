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

	$( '#woocommerce_correios_service_carta_reg' ).on( 'change', function() {
		var
		  ship_class    = $( '.form-table:eq(1) tr:eq(9)' ),
		  carta_title   = $( 'h4:eq(1)' ),
		  carta_sub     = $( 'h4:eq(1) + p' ),
			carta_values  = $( '.form-table:eq(2) ');

		if ( $( this ).is( ':checked' ) ) {
			ship_class.show();
			carta_title.show();
			carta_sub.show();
			carta_values.show();
		} else {
			ship_class.hide();
			carta_title.hide();
			carta_sub.hide();
			carta_values.hide();
		}
	}).change();

});
