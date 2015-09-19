<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_SEDEX10 extends WC_Correios_Method_Base {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
  public function __construct() {
    //Initialize method specific variables.
    $this->id                 = 'correios_sedex10';
		$this->method_title       = __( 'SEDEX 10', 'woocommerce-correios' );
		$this->method_description = __( 'SEDEX 10 is a shipping method from Correios, the brazilian most used delivery company. <a href="http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-10">More about SEDEX 10</a>.', 'woocommerce-correios' );
    /**
  	 * 40215 - SEDEX 10 without contract.
  	 */
    $this->code = '40215';

    parent::__construct();
  }

}

return new WC_Correios_SEDEX10();
