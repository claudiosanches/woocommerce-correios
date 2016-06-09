<?php
/**
 * Correios Mercadoria Expressa shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mercadoria Expressa shipping method class.
 */
class WC_Correios_Shipping_Mercadoria_Expressa extends WC_Correios_International_Shipping {

	/**
	 * Initialize Mercadoria Expressa.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-mercadoria-expressa';
		$this->method_title = __( 'Mercadoria Expressa', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct();
	}

	/**
	 * Get Correios service code.
	 *
	 * 110 - Mercadoria Expressa.
	 *
	 * @return string
	 */
	public function get_code() {
		$code = '110';

		return apply_filters( 'woocommerce_correios_shipping_method_code', $code, $this->id, $this->instance_id );
	}
}
