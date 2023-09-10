<?php
/**
 * Correios Exporta Fácil Econômico shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exporta Fácil Econômico shipping method class.
 */
class WC_Correios_Shipping_Exporta_Facil_Economico extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45209 - Exporta Fácil Ecnômico.
	 *
	 * @var string
	 */
	protected $code = '45209';

	/**
	 * Initialize Exporta Fácil Econômico.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-exporta-facil-economico';
		$this->method_title = __( 'Exporta Fácil Econômico (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
