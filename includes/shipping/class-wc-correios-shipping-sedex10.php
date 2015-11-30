<?php
/**
 * Correios SEDEX 10 shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEDEX 10 shipping method class.
 */
class WC_Correios_Shipping_SEDEX_10 extends WC_Correios_Shipping {

	/**
	 * Initialize SEDEX 10.
	 */
	public function __construct() {
		$this->id           = 'correios_sedex10';
		$this->method_title = __( 'SEDEX 10', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-10';

		/**
		 * 40215 - SEDEX 10 without contract.
		 */
		$this->code = '40215';

		parent::__construct();
	}
}
