<?php
/**
 * Correios Documento Econômico shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Documento Econômico shipping method class.
 */
class WC_Correios_Shipping_Documento_Economico extends WC_Correios_Shipping_International {

	/**
	 * Service code.
	 * 45020 - Documento Econômico.
	 *
	 * @var string
	 */
	protected $code = '45020';

	/**
	 * Initialize Documento Econômico.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-documento-economico';
		$this->method_title = __( 'Documento Econômico (Legacy)', 'woocommerce-correios' );
		$this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
