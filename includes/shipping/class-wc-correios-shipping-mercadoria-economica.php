<?php
/**
 * Correios Mercadoria Econômica shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mercadoria Econômica shipping method class.
 */
class WC_Correios_Shipping_Mercadoria_Economica extends WC_Correios_Shipping {

	/**
	 * Initialize Mercadoria Econômica.
	 */
	public function __construct() {
		$this->id           = 'correios_esedex';
		$this->method_title = __( 'Mercadoria Econ&ocirc;mica', 'woocommerce-correios' );
		$this->more_link    = '';

		/**
		 * 128 - Mercadoria Econômica.
		 */
		$this->code = '128';

		parent::__construct();
	}
}
