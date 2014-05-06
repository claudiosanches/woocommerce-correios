<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Error class.
 */
class WC_Correios_Error {

	/**
	 * Display error messages.
	 *
	 * @param  string $code Correios error code.
	 *
	 * @return string       Error message.
	 */
	public static function get_message( $code ) {
		$code = (string) $code;

		$messages = array(
			'-3'  => __( 'Invalid zip code.', 'woocommerce-correios' ),
			'-33' => __( 'System temporarily down. Please try again later.', 'woocommerce-correios' ),
			'010' => __( 'Area with delivery temporarily subjected to different periods.', 'woocommerce-correios' ),
		);

		if ( isset( $messages[ $code ] ) ) {
			return $messages[ $code ];
		}

		return '';
	}
}
