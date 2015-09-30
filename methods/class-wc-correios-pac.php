<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_PAC extends WC_Correios_Method_Base {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
  public function __construct() {
    //Initialize method specific variables.
    $this->id                 = 'correios_pac';
    $this->method_title       = __( 'PAC', 'woocommerce-correios' );
		$this->method_description = __( 'PAC is a shipping method from Correios, the brazilian most used delivery company. <a href="http://www.correios.com.br/para-voce/correios-de-a-a-z/pac-encomenda-economica">More about PAC</a>.', 'woocommerce-correios' );
    /**
     * 41106 - PAC without contract.
     * 41068 - PAC with contract.
  	 */
		$this->code = WC_Correios::is_corporate() ? '41068' : '41106';

    parent::__construct();
  }

}

return new WC_Correios_PAC();
