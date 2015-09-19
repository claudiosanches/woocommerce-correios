<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_SEDEX extends WC_Correios_Method_Base {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
  public function __construct() {
    //Initialize method specific variables.
    $this->id                 = 'correios_sedex';
		$this->method_title       = __( 'SEDEX', 'woocommerce-correios' );
		$this->method_description = __( 'SEDEX is a shipping method from Correios, the brazilian most used delivery company. <a href="http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex">More about SEDEX</a>.', 'woocommerce-correios' );
    /**
  	 * 40010 - SEDEX without contract.
  	 * 40096 - SEDEX with contract.
  	 */
    $this->code = WC_Correios::is_corporate() ? '40096' : '40010';

    parent::__construct();
  }

}

return new WC_Correios_SEDEX();
