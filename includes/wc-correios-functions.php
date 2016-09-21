<?php
/**
 * Correios functions.
 *
 * @package WooCommerce_Correios/Functions
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Safe load XML.
 *
 * @throws Exception Show detected errors while parsing the data.
 *
 * @param  string $source Source XML.
 * @param  int    $options Reading options.
 *
 * @return SimpleXMLElement|bool
 */
function wc_correios_safe_load_xml( $source, $options = 0 ) {
	$old = null;

	if ( function_exists( 'libxml_disable_entity_loader' ) ) {
		$old = libxml_disable_entity_loader( true );
	}

	$dom    = new DOMDocument();
	$return = $dom->loadXML( trim( $source ), $options );

	if ( ! is_null( $old ) ) {
		libxml_disable_entity_loader( $old );
	}

	if ( ! $return ) {
		return false;
	}

	if ( isset( $dom->doctype ) ) {
		throw new Exception( 'Unsafe DOCTYPE Detected while XML parsing' );

		return false;
	}

	return simplexml_import_dom( $dom );
}

/**
 * Sanitize postcode.
 *
 * @param  string $postcode Postcode.
 *
 * @return string
 */
function wc_correios_sanitize_postcode( $postcode ) {
	return preg_replace( '([^0-9])', '', sanitize_text_field( $postcode ) );
}

/**
 * Get estimating delivery description.
 *
 * @param string $name            Shipping name.
 * @param string $days            Estimated days to accomplish delivery.
 * @param int    $additional_days Additional days.
 *
 * @return string
 */
function wc_correios_get_estimating_delivery( $name, $days, $additional_days = 0 ) {
	$total = intval( $days ) + intval( $additional_days );

	if ( $total > 0 ) {
		$name .= ' (' . sprintf( _n( 'Delivery within %d working day', 'Delivery within %d working days', $total, 'woocommerce-correios' ),  $total ) . ')';
	}

	return $name;
}

/**
 * Fix Correios prices.
 *
 * @param  string $value Value to fix.
 *
 * @return string
 */
function wc_correios_normalize_price( $value ) {
	$value = str_replace( '.', '', $value );
	$value = str_replace( ',', '.', $value );

	return $value;
}

/**
 * Get error messages.
 *
 * @param  string $code Error code.
 *
 * @return string
 */
function wc_correios_get_error_message( $code ) {
	$code = (string) $code;

	$messages = apply_filters( 'woocommerce_correios_available_error_messages', array(
		'-33' => __( 'System temporarily down. Please try again later.', 'woocommerce-correios' ),
		'-3'  => __( 'Invalid zip code.', 'woocommerce-correios' ),
		'010' => __( 'Area with delivery temporarily subjected to different periods.', 'woocommerce-correios' ),
	) );

	return isset( $messages[ $code ] ) ? $messages[ $code ] : '';
}

/**
 * Trigger tracking code email notification.
 *
 * @param WC_Order $order         Order data.
 * @param string   $tracking_code The Correios tracking code.
 */
function wc_correios_trigger_tracking_code_email( $order, $tracking_code ) {
	$mailer       = WC()->mailer();
	$notification = $mailer->emails['WC_Correios_Tracking_Email'];

	if ( 'yes' === $notification->enabled ) {
		$notification->trigger( $order, $tracking_code );
	}
}

/**
 * Update tracking code.
 *
 * @param  int    $order_id      Order ID.
 * @param  string $tracking_code Tracking code.
 * @return bool
 */
function wc_correios_update_tracking_code( $order_id, $tracking_code ) {
	$tracking_code = sanitize_text_field( $tracking_code );
	$current       = get_post_meta( $order_id, '_correios_tracking_code', true );

	if ( '' !== $tracking_code && $tracking_code !== $current ) {
		update_post_meta( $order_id, '_correios_tracking_code', $tracking_code );

		// Gets order data.
		$order = wc_get_order( $order_id );

		// Add order note.
		$order->add_order_note( sprintf( __( 'Added a Correios tracking code: %s', 'woocommerce-correios' ), $tracking_code ) );

		// Send email notification.
		wc_correios_trigger_tracking_code_email( $order, $tracking_code );

		return true;
	} elseif ( '' === $tracking_code ) {
		delete_post_meta( $order_id, '_correios_tracking_code' );

		return true;
	}

	return false;
}

/**
 * Get address by postcode.
 *
 * @param string $postcode Postcode.
 *
 * @return stdClass
 */
function wc_correios_get_address_by_postcode( $postcode ) {
	return WC_Correios_Autofill_Addresses::get_address( $postcode );
}
