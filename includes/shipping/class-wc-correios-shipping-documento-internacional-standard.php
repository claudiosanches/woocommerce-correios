<?php
/**
 * Correios Documento Internacional Standard shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Documento Internacional Standard shipping method class.
 */
class WC_Correios_Shipping_Documento_Internacional_Standard extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45039 - Documento Internacional Standard.
	 *
	 * @var string
	 */
	protected $code = '45039';

	/**
	 * Initialize Documento Internacional Standard.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-documento-internacional-standard';
		$this->method_title = __( 'Documento Internacional Standard (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
