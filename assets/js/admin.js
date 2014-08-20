jQuery( document ).ready( function( $ ) {
	$( '#woocommerce_correios_display_date' ).on( 'change', function() {
		var field = $( '.form-table:eq(0) tr:eq(7)' );

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

	$( 'td.order_tracking_code .save' ).on( 'click', function(){
		var self = $( this ),
			id   = self.data( 'id' );
		$.ajax({
			type:     'POST',
			url:      wc_correios.ajax_url,
			cache:    false,
			dataType: 'json',
			data:     {
				action 				: 'wc_correios_save_tracking_code',
				security 			: wc_correios.security,
				order_id 			: id,
				post_type			: $('[name="post_type"]').val(),
				correios_tracking 	: $('#wc-correios-tracking-'+id).val()
			},
			beforeSend: function () {
				self.attr( 'disabled', 'disabled' )
					.addClass( 'button-primary' );
			},
			success: function ( data ) {
				self.removeAttr( 'disabled' )
					.removeClass( 'button-primary' );
				if(data.error){
					window.alert( data.error );
				}
			},
			error: function () {
				self.removeAttr( 'disabled' );
				window.alert( wc_correios.error_message );
			}
		});
		return false;
	});
});
