<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_RegisteredLetter extends WC_Correios_Method_Base {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
  public function __construct() {
    //Initialize method specific variables.
    $this->id                 = 'correios_registeredletter';
		$this->method_title       = __( 'Registered Letter', 'woocommerce-correios' );
		$this->method_description = __( 'Registered Letter is a shipping method from Correios, the brazilian most used delivery company. <a href="http://www.correios.com.br/para-voce/correios-de-a-a-z/carta-comercial">More about Registered Letter</a>.', 'woocommerce-correios' );
    /**
  	 * 10014 - Registered Letter
  	 */
    $this->code = '10014';

    parent::__construct();

		$this->form_fields['price_table'] = array(
			'title'       => __( 'Price table', 'woocommerce-correios' ),
			'type'        => 'select',
			'description' => __( 'Allow you to select between Commercial and Non-Commercial price table.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => 'commercial',
			'options'			=> array(
				'commercial' => __( 'Commercial', 'woocommerce-correios' ),
				'noncommercial' => __( 'Non-Commercial', 'woocommerce-correios' ),
			),
		);

  }

}

return new WC_Correios_RegisteredLetter();
