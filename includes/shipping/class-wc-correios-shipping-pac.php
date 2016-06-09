<?php
/**
 * Correios PAC shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PAC shipping method class.
 */
class WC_Correios_Shipping_PAC extends WC_Correios_Shipping {

	/**
	 * Initialize PAC.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );

		$this->id           = 'correios-pac';
		$this->method_title = __( 'PAC', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/pac-encomenda-economica';

		/**
		 * 41106 - PAC without contract.
		 * 41068 - PAC with contract.
		 */
		$this->code = $this->is_corporate() ? '41068' : '41106';
	}
}
