jQuery( document ).ready( function( $ ) {
	$( '#woocommerce_correios_service_type' ).on( 'change', function() {
		var login    = $( '#woocommerce_correios_login' ).closest( 'tr' ),
			password = $( '#woocommerce_correios_password' ).closest( 'tr' );

		if ( 'corporate' === $( this ).val() ) {
			login.show();
			password.show();
		} else {
			login.hide();
			password.hide();
		}
	}).change();
});
