<?php
/**
 * Correios Carta Registrada shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Carta Registrada shipping method class.
 */
class WC_Correios_Shipping_Carta_Registrada extends WC_Correios_Shipping {

	/**
	 * Initialize Carta Registrada.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-carta-registrada';
		$this->method_title = __( 'Carta Registrada', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/carta-comercial';

		parent::__construct( $instance_id );
	}

	/**
	 * Get Correios service code.
	 *
	 * 10014 - Carta Registrada.
	 *
	 * @return string
	 */
	public function get_code() {
		$code = '10014';

		return apply_filters( 'woocommerce_correios_shipping_method_code', $code, $this->id, $this->instance_id );
	}
}
