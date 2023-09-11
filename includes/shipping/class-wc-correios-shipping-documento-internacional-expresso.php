<?php
/**
 * Correios Documento Internacional Expresso shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Documento Internacional Expresso shipping method class.
 */
class WC_Correios_Shipping_Documento_Internacional_Expresso extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45012 - Documento Internacional Expresso.
	 *
	 * @var string
	 */
	protected $code = '45012';

	/**
	 * Initialize Documento Internacional Expresso.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-documento-internacional-expresso';
		$this->method_title = __( 'Documento Internacional Expresso (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
