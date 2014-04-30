/* global woocommerce_correios_simulator */
jQuery( document ).ready( function ( $ ) {

	$( 'body' ).on( 'show_variation', function () {
		$( '#wc-correios-simulator' ).slideDown( 200 );
	});

	$( 'body' ).on( 'reset_image', function () {
		$( '#wc-correios-simulator' ).slideUp( 200 );
	});

	$( '#wc-correios-simulator' ).on( 'click', '.button', function ( e ) {
		e.preventDefault();

		var content = $( '#wc-correios-simulator #simulator-data' ),
			button = $( this );

		button.addClass( 'loading' );
		content.empty();

		$.ajax({
			type:     'GET',
			url:      woocommerce_correios_simulator.ajax_url,
			cache:    false,
			dataType: 'json',
			data:     {
				action:       'wc_correios_simulator',
				security:     woocommerce_correios_simulator.security,
				zipcode:      $( '#wc-correios-simulator #zipcode' ).val(),
				product_id:   $( '.cart input[name="add-to-cart"]' ).val(),
				variation_id: $( '.cart input[name="variation_id"]' ).val(),
				quantity:     $( '.cart input[name="quantity"]' ).val()
			},
			success: function ( data ) {
				button.removeClass( 'loading' );

				if ( 0 < data.error.length ) {
					content.prepend( '<p>' + data.error + '</p>' );
				} else if ( 0 < data.rates.length ) {
					var shipping = '<ul id="shipping-rates">';

					$.each( data.rates, function( key, value ) {
						shipping += '<li>' + value.label + ': ' + value.cost + '</li>';
					});

					shipping += '</ul>';

					content.prepend( shipping );
				} else {
					content.prepend( '<p>' + woocommerce_correios_simulator.error_message + '</p>' );
				}
			},
			error: function () {
				button.removeClass( 'loading' );
				content.prepend( '<p>' + woocommerce_correios_simulator.error_message + '</p>' );
			}
		});

	});
//
});
