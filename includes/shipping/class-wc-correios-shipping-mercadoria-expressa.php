<?php
/**
 * Correios Mercadoria Expressa shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mercadoria Expressa shipping method class.
 */
class WC_Correios_Shipping_Mercadoria_Expressa extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 110 - Mercadoria Expressa.
	 *
	 * @var string
	 */
	protected $code = '110';

	/**
	 * Initialize Mercadoria Expressa.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-mercadoria-expressa';
		$this->method_title = __( 'Mercadoria Expressa', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
