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
	}

	// /**
	//  * Correios options page.
	//  */
	// public function admin_options() {
	// 	if ( $this->is_corporate() ) {
	// 		parent::admin_options();
	// 	} else {
	// 		$GLOBALS['hide_save_button'] = true;
	// 		echo '<h3>' . esc_html( $this->method_title ) . '</h3>';
	// 		echo '<p>';
	// 		esc_html_e( 'e-Sedex only works on corporate mode. Enable this options by setting "Corporate" in the "Service Type" option in', 'woocommerce-correios' );
	// 		echo ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=correios' ) ) . '">' . esc_html__( 'Correios settings screen.', 'woocommerce-correios' ) . '</a>';
	// 		echo '</p>';
	// 	}
	// }
}
