<?php
/**
 * Correios Exporta Fácil Expresso shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exporta Fácil Expresso shipping method class.
 */
class WC_Correios_Shipping_Exporta_Facil_Expresso extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45110 - Exporta Fácil Expresso.
	 *
	 * @var string
	 */
	protected $code = '45110';

	/**
	 * Initialize Exporta Fácil Expresso.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-exporta-facil-expresso';
		$this->method_title = __( 'Exporta Fácil Expresso (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
