/* global woocommerce_correios_simulator */
jQuery( document ).ready( function ( $ ) {

	function simulatorClean() {
		$( '#wc-correios-simulator #simulator-data' ).empty();
		$( '#wc-correios-simulator #zipcode' ).val( '' );
	}

	$( 'body' ).on( 'show_variation', function () {
		var ids          = $( '#wc-correios-simulator' ).data( 'product-ids' ).toString().split( ',' ),
			variation_id = $( '.cart input[name="variation_id"]' ).val().toString();

		if ( -1 < $.inArray( variation_id, ids ) ) {
			$( '#wc-correios-simulator' ).slideDown( 200 );
		}

		simulatorClean();
	});

	$( 'body' ).on( 'reset_image', function () {
		$( '#wc-correios-simulator' ).slideUp( 200 );
		simulatorClean();
	});

	$( '#wc-correios-simulator' ).on( 'click', '.button', function ( e ) {
		e.preventDefault();

		var simulator  = $( '#wc-correios-simulator' ),
			content    = $( '#wc-correios-simulator #simulator-data' ),
			button     = $( this ),
			type       = simulator.data( 'product-type' ),
			product_id = $( '.cart input[name="add-to-cart"]' ).val();

		button.addClass( 'loading' );
		content.empty();

		// Support for old templates.
		if ( 'simple' === type && ! product_id ) {
			product_id = simulator.data( 'product-ids' );
		}

		$.ajax({
			type:     'GET',
			url:      woocommerce_correios_simulator.ajax_url,
			cache:    false,
			dataType: 'json',
			data:     {
				action:       'wc_correios_simulator',
				zipcode:      $( '#wc-correios-simulator #zipcode' ).val(),
				product_id:   product_id,
				variation_id: $( '.cart input[name="variation_id"]' ).val(),
				quantity:     $( '.cart input[name="quantity"]' ).val()
			},
			beforeSend: function () {
				button.attr( 'disabled', 'disabled' );
			},
			success: function ( data ) {
				button.removeClass( 'loading' );
				button.removeAttr( 'disabled' );

				if ( 0 < data.error.length ) {
					content.prepend( '<p class="error">' + data.error + '</p>' );
				} else if ( 0 < data.rates.length ) {
					var shipping = '<ul id="shipping-rates">';

					$.each( data.rates, function( key, value ) {
						shipping += '<li>' + value.label + ': ' + value.cost + '</li>';
					});

					shipping += '</ul>';

					content.prepend( shipping );
				} else {
					content.prepend( '<p class="error">' + woocommerce_correios_simulator.error_message + '</p>' );
				}
			},
			error: function () {
				button.removeClass( 'loading' );
				button.removeAttr( 'disabled' );
				content.prepend( '<p class="error">' + woocommerce_correios_simulator.error_message + '</p>' );
			}
		});

	});
//
});
