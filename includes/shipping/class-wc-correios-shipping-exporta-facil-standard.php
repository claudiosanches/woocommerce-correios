<?php
/**
 * Correios Exporta Fácil Standard shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exporta Fácil Standard shipping method class.
 */
class WC_Correios_Shipping_Exporta_Facil_Standard extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45128 - Exporta Fácil Standard.
	 *
	 * @var string
	 */
	protected $code = '45128';

	/**
	 * Initialize Exporta Fácil Standard.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-exporta-facil-standard';
		$this->method_title = __( 'Exporta Fácil Standard (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
