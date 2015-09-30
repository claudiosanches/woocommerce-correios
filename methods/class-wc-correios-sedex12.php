<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_SEDEX12 extends WC_Correios_Method_Base {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
  public function __construct() {
    //Initialize method specific variables.
    $this->id                 = 'correios_sedex12';
		$this->method_title       = __( 'SEDEX 12', 'woocommerce-correios' );
		$this->method_description = __( 'SEDEX 12 is a shipping method from Correios, the brazilian most used delivery company. <a href="http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-12">More about SEDEX 12</a>.', 'woocommerce-correios' );
    /**
  	 * xxxxx - SEDEX 12 without contract.
  	 */
    $this->code = '-1';

    parent::__construct();
  }

}

return new WC_Correios_SEDEX12();
