<?php
/**
 * Correios Documento Internacional Premium shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Documento Internacional Premium shipping method class.
 */
class WC_Correios_Shipping_Documento_Internacional_Premium extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45179 - Documento Internacional Premium.
	 *
	 * @var string
	 */
	protected $code = '45179';

	/**
	 * Initialize Documento Internacional Premium.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-documento-internacional-premium';
		$this->method_title = __( 'Documento Internacional Premium (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
