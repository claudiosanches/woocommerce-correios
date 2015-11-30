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
	 * Initialize e-SEDEX.
	 */
	public function __construct() {
		$this->id           = 'correios_esedex';
		$this->method_title = __( 'e-SEDEX', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/e-sedex';

		/**
		 * 81019 - e-SEDEX with contract.
		 */
		$this->code = $this->is_corporate() ? '81019' : '-1';

		parent::__construct();
	}

	/**
	 * Correios options page.
	 */
	public function admin_options() {
		if ( $this->is_corporate() ) {
			parent::admin_options();
		} else {
			$GLOBALS['hide_save_button'] = true;
			echo '<h3>' . esc_html( $this->method_title ) . '</h3>';
			echo '<p>';
			esc_html_e( 'e-Sedex only works on corporate mode. Enable this options by setting "Corporate" in the "Service Type" option in', 'woocommerce-correios' );
			echo ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=correios' ) ) . '">' . esc_html__( 'Correios settings screen.', 'woocommerce-correios' ) . '</a>';
			echo '</p>';
		}
	}
}
