jQuery( document ).ready( function( $ ) {
	$( 'input[id$="_show_delivery_time"]' ).on( 'change', function() {
		var field = $( 'input[id$="_additional_time"]' ).closest( 'tr' );

		if ( $( this ).is( ':checked' ) ) {
			field.show();
		} else {
			field.hide();
		}
	}).change();

	$( 'select[id$="_service_type"]' ).on( 'change', function() {
		var login    = $( 'input[id$="_login"]' ).closest( 'tr' ),
			password = $( 'input[id$="_password"]' ).closest( 'tr' );

		if ( 'corporate' === $( this ).val() ) {
			login.show();
			password.show();
		} else {
			login.hide();
			password.hide();
		}
	}).change();
});
