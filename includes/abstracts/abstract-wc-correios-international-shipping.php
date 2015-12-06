<?php
/**
 * Abstract Correios international shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Correios international shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class WC_Correios_International_Shipping extends WC_Correios_Shipping {

	/**
	 * Get shipping method title.
	 *
	 * @return string
	 */
	public function get_method_title() {
		return sprintf( __( '%s is a international shipping method from Correios, the brazilian most used delivery company.', 'woocommerce-correios' ), $this->method_title );
	}

	/**
	 * Get shipping rate.
	 *
	 * @param  array $package Order package.
	 *
	 * @return SimpleXMLElement
	 */
	protected function get_rate( $package ) {

	}

	/**
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 */
	public function calculate_shipping( $package = array() ) {

	}
}
