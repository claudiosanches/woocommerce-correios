<?php
/**
 * Correios Leve Internacional shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Leve Internacional shipping method class.
 */
class WC_Correios_Shipping_Leve_Internacional extends WC_Correios_International_Shipping {

	/**
	 * Initialize Leve Internacional.
	 */
	public function __construct() {
		$this->id           = 'correios-leve-internacional';
		$this->method_title = __( 'Leve Internacional', 'woocommerce-correios' );
		$this->more_link    = '';

		/**
		 * 209 - Leve Internacional.
		 */
		$this->code = '209';

		parent::__construct();
	}
}
