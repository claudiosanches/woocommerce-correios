<?php
/**
 * Correios SEDEX Hoje shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEDEX Hoje shipping method class.
 */
class WC_Correios_Shipping_SEDEX_Hoje extends WC_Correios_Shipping {

	/**
	 * Initialize SEDEX Hoje.
	 */
	public function __construct() {
		$this->id           = 'correios_sedex_hoje';
		$this->method_title = __( 'SEDEX Hoje', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-hoje';

		/**
		 * 40290 - SEDEX Hoje without contract.
		 */
		$this->code = '40290';

		parent::__construct();
	}
}
