<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_SEDEXHoje extends WC_Correios_Method_Base {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
  public function __construct() {
    //Initialize method specific variables.
    $this->id                 = 'correios_sedexhoje';
		$this->method_title       = __( 'SEDEX Hoje', 'woocommerce-correios' );
		$this->method_description = __( 'SEDEX Hoje is a shipping method from Correios, the brazilian most used delivery company. <a href="http://www.correios.com.br/para-voce/correios-de-a-a-z/sedex-hoje">More about SEDEX Hoje</a>.', 'woocommerce-correios' );
    /**
  	 * 40290 - SEDEX Hoje without contract.
  	 */
    $this->code = '40290';

    parent::__construct();
  }

}

return new WC_Correios_SEDEXHoje();
