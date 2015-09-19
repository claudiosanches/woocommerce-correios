<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_ESEDEX extends WC_Correios_Method_Base {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
  public function __construct() {
    //Initialize method specific variables.
    $this->id                 = 'correios_esedex';
		$this->method_title       = __( 'e-SEDEX', 'woocommerce-correios' );
		$this->method_description = __( 'e-SEDEX is a shipping method from Correios, the brazilian most used delivery company. <a href="http://www.correios.com.br/para-voce/correios-de-a-a-z/e-sedex">More about e-SEDEX</a>.', 'woocommerce-correios' );
    /**
  	 * 81019 - e-SEDEX with contract.
  	 */
    $this->code = WC_Correios::is_corporate() ? '81019' : '-1';

    parent::__construct();
  }

}

return new WC_Correios_ESEDEX();
