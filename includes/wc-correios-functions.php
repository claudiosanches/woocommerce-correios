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
	$return = $dom->loadXML( $source, $options );

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
	$additional_days = intval( $additional_days );

	if ( $additional_days > 0 ) {
		$days += intval( $additional_days );
	}

	if ( $days > 0 ) {
		$name .= ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days', $days, 'woocommerce-correios' ),  $days ) . ')';
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

	if ( isset( $messages[ $code ] ) ) {
		return $messages[ $code ];
	}

	return '';
}
