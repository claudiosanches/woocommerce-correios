<?php
/**
 * Correios SEDEX 12 shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEDEX 12 shipping method class.
 */
class WC_Correios_Shipping_SEDEX_12 extends WC_Correios_Shipping {

	/**
	 * Initialize SEDEX 12.
	 */
	public function __construct() {
		$this->id           = 'correios-sedex12';
		$this->method_title = __( 'SEDEX 12', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-12';

		/**
		 * XXXXX - SEDEX 12 without contract.
		 */
		$this->code = '';

		parent::__construct();
	}
}
