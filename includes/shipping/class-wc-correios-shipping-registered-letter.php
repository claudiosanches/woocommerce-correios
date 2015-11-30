<?php
/**
 * Correios Registered Letter shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registered Letter shipping method class.
 */
class WC_Correios_Shipping_Registered_Letter extends WC_Correios_Shipping {

	/**
	 * Initialize Registered Letter.
	 */
	public function __construct() {
		$this->id           = 'correios_registered_letter';
		$this->method_title = __( 'Registered Letter', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/carta-comercial';

		/**
		 * 10014 - Registered Letter.
		 */
		$this->code = '10014';

		parent::__construct();

		// Extra options.
		$new_options = array(
			'price_table' => array(
				'title'       => __( 'Price table', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Allow you to select between Commercial and Non-Commercial price table.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'commercial',
				'options'     => array(
					'commercial'    => __( 'Commercial', 'woocommerce-correios' ),
					'noncommercial' => __( 'Non-Commercial', 'woocommerce-correios' ),
				),
			),
		);

		$this->form_fields = array_merge( array_slice( $this->form_fields, 0, 3 ), $new_options, array_slice( $this->form_fields, 3 ) );
	}
}
