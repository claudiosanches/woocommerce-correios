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
	 * Service code.
	 * 40886 - SEDEX 10 Pacote.
	 *
	 * @var string
	 */
	protected $code = '40886';

	/**
	 * Initialize SEDEX 10 Pacote.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-sedex10-pacote';
		$this->method_title = __( 'SEDEX 10 Pacote', 'woocommerce-correios' );
		$this->more_link    = 'https://www.correios.com.br/enviar-e-receber/encomendas';

		parent::__construct( $instance_id );
	}

	/**
	 * Get the declared value from the package.
	 *
	 * @param  array $package Cart package.
	 * @return float
	 */
	protected function get_declared_value( $package ) {
		if ( 20.50 >= $package['contents_cost'] ) {
			return 0;
		}

		if ( 3000 < $package['contents_cost'] ) {
			return 3000;
		}

		return $package['contents_cost'];
	}
}
