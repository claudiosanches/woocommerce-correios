<?php
/**
 * Correios e-SEDEX shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * E-SEDEX shipping method class.
 */
class WC_Correios_Shipping_ESEDEX extends WC_Correios_Shipping {

	/**
	 * Initialize e-SEDEX.
	 */
	public function __construct() {
		$this->id           = 'correios_esedex';
		$this->method_title = __( 'e-SEDEX', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/e-sedex';

		/**
		 * 81019 - e-SEDEX with contract.
		 */
		$this->code = $this->is_corporate() ? '81019' : '-1';

		parent::__construct();
	}
}
