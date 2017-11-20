<?php
/**
 * Correios SEDEX 12 shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEDEX 12 shipping method class.
 */
class WC_Correios_Shipping_SEDEX_12 extends WC_Correios_Shipping {

	/**
	 * Service code.
	 * 40169 - SEDEX 12.
	 *
	 * @var string
	 */
	protected $code = '40169';

	/**
	 * Initialize SEDEX 12.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-sedex12';
		$this->method_title = __( 'SEDEX 12', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-12';

		parent::__construct( $instance_id );
	}

	/**
	 * Get the declared value from the package.
	 *
	 * @param  array $package Cart package.
	 * @return float
	 */
	protected function get_declared_value( $package ) {
		if ( 18 >= $package['contents_cost'] ) {
			return 0;
		}

		return $package['contents_cost'];
	}
}
