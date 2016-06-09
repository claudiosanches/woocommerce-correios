<?php
/**
 * Correios SEDEX shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEDEX shipping method class.
 */
class WC_Correios_Shipping_SEDEX extends WC_Correios_Shipping {

	/**
	 * Initialize SEDEX.
	 */
	public function __construct() {
		$this->id           = 'correios-sedex';
		$this->method_title = __( 'SEDEX', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex';

		/**
		 * 40010 - SEDEX without contract.
		 * 40096 - SEDEX with contract.
		 */
		$this->code = $this->is_corporate() ? '40096' : '40010';

		parent::__construct();
	}
}
