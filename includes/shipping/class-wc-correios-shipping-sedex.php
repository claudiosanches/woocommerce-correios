<?php
/**
 * Correios SEDEX shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEDEX shipping method class.
 */
class WC_Correios_Shipping_SEDEX extends WC_Correios_Shipping {

	/**
	 * Initialize SEDEX.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-sedex';
		$this->method_title = __( 'SEDEX', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex';

		parent::__construct( $instance_id );
	}

	/**
	 * Get Correios service code.
	 *
	 * 40010 - SEDEX without contract.
	 * 40096 - SEDEX with contract.
	 *
	 * @return string
	 */
	public function get_code() {
		$code = $this->is_corporate() ? '40096' : '40010';

		return apply_filters( 'woocommerce_correios_shipping_method_code', $code, $this->id, $this->instance_id );
	}
}
