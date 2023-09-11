<?php
/**
 * Correios Exporta Fácil Premium shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exporta Fácil Premium shipping method class.
 */
class WC_Correios_Shipping_Exporta_Facil_Premium extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45195 - Exporta Fácil Premium.
	 *
	 * @var string
	 */
	protected $code = '45195';

	/**
	 * Initialize Exporta Fácil Premium.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-exporta-facil-premium';
		$this->method_title = __( 'Exporta Fácil Premium (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
