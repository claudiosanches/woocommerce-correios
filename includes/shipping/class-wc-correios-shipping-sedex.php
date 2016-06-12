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
	 * Service code.
	 * 40010 - SEDEX without contract.
	 *
	 * @var string
	 */
	protected $code = '40010';

	/**
	 * Corporate code.
	 * 40096 - SEDEX with contract.
	 *
	 * @var string
	 */
	protected $corporate_code = '40096';

	/**
	 * Initialize SEDEX.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-sedex';
		$this->method_title = __( 'SEDEX', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex';

		parent::__construct( $instance_id );
	}
}
