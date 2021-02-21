<?php
/**
 * Correios PAC shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PAC shipping method class.
 */
class WC_Correios_Shipping_Mini_Envios extends WC_Correios_Shipping {

	/**
	 * Service code.
	 * 04227 - Mini Envios.
	 *
	 * @var string
	 */
	protected $code = '04227';

	/**
	 * Initialize Mini Envios.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-mini-envios';
		$this->method_title = __( 'Mini Envios', 'woocommerce-correios' );
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
		if ( 10.25 >= $package['contents_cost'] ) {
			return 0;
		}
		
		if ( 100 < $package['contents_cost'] ) {
			return 100;
		}

		return $package['contents_cost'];
	}
}
