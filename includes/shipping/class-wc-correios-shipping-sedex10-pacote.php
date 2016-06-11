<?php
/**
 * Correios SEDEX 10 Pacote shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEDEX 10 Pacote shipping method class.
 */
class WC_Correios_Shipping_SEDEX_10_Pacote extends WC_Correios_Shipping {

	/**
	 * Initialize SEDEX 10 Pacote.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-sedex10-pacote';
		$this->method_title = __( 'SEDEX 10 Pacote', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-10';

		parent::__construct( $instance_id );
	}

	/**
	 * Get Correios service code.
	 *
	 * 40886 - SEDEX 10 Pacote.
	 *
	 * @return string
	 */
	public function get_code() {
		$code = '40886';

		return apply_filters( 'woocommerce_correios_shipping_method_code', $code, $this->id, $this->instance_id );
	}
}
