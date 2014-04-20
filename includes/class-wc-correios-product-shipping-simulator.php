<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_Correios_Product_Shipping_Simulator {

	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'get_header', array( $this, 'init' ) );
		}
	}

	public function init() {
		if ( is_product() ) {
			add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'simulator' ), 45 );
		}
	}

	public static function simulator() {
		$html = '<div id="wc-correios-simulator">';
		$html .= '<p>' . __( 'Calculate shipping and delivery time estimated to your region.', 'woocommerce-correios' ) . '</p>';
		$html .= '<form method="post" class="cart">';
		$html .= '<input type="text" size="9" class="input-text text" placeholder="00000-000" name="zipcode" />';
		$html .= '<button class="button alt" type="submit">' . __( 'Calculate', 'woocommerce-correios' ) .'</button>';
		$html .= '</form>';
		$html .= '</div>';

		echo $html;
	}
}

return new WC_Correios_Product_Shipping_Simulator;
