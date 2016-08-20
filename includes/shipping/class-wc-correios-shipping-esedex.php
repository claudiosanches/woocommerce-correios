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
	 * Service code.
	 * 81019 - e-SEDEX.
	 *
	 * @var string
	 */
	protected $code = '81019';

	/**
	 * Initialize e-SEDEX.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-esedex';
		$this->method_title = __( 'e-SEDEX', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/e-sedex';

		parent::__construct( $instance_id );

		$this->instance_form_fields['service_type'] = array(
			'title'       => __( 'Service Type', 'woocommerce-correios' ),
			'type'        => 'select',
			'description' => __( 'Choose between conventional or corporate service.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => 'corporate',
			'class'       => 'wc-enhanced-select',
			'options'     => array(
				'corporate' => __( 'Corporate', 'woocommerce-correios' ),
			),
		);
	}
}
