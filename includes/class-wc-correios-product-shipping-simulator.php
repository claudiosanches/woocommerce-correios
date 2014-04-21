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
			add_action( 'wp_enqueue_scripts', array( $this, 'scritps' ) );
		}
	}

	public function scritps() {
		wp_enqueue_style( 'woocommerce-correios-simulator', plugins_url( 'assets/css/simulator.css', plugin_dir_path( __FILE__ ) ), array(), WC_Correios::VERSION, 'all' );
		wp_enqueue_script( 'woocommerce-correios-simulator', plugins_url( 'assets/js/simulator.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_Correios::VERSION, true );
	}

	public static function simulator() {
		global $product;

		if ( $product->needs_shipping() ) {

			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
				$height = wc_get_dimension( str_replace( '.', ',', $product->height ), 'cm' );
				$width  = wc_get_dimension( str_replace( '.', ',', $product->width ), 'cm' );
				$length = wc_get_dimension( str_replace( '.', ',', $product->length ), 'cm' );
				$weight = wc_get_weight( str_replace( '.', ',', $product->weight ), 'kg' );
			} else {
				$height = woocommerce_get_dimension( str_replace( '.', ',', $product->height ), 'cm' );
				$width  = woocommerce_get_dimension( str_replace( '.', ',', $product->width ), 'cm' );
				$length = woocommerce_get_dimension( str_replace( '.', ',', $product->length ), 'cm' );
				$weight = woocommerce_get_weight( str_replace( '.', ',', $product->weight ), 'kg' );
			}

			$html = '<div id="wc-correios-simulator">';
			$html .= '<strong>' . __( 'Shipping and Delivery Time.', 'woocommerce-correios' ) . '</strong>';
			$html .= '<p>' . __( 'Calculate shipping and delivery time estimated to your region.', 'woocommerce-correios' ) . '</p>';
			$html .= '<form method="post" class="cart">';
			$html .= '<input type="text" size="9" class="input-text text" placeholder="00000-000" id="zipcode" name="zipcode" />';
			$html .= '<input type="hidden" name="height" value="' . $height . '" />';
			$html .= '<input type="hidden" name="width" value="' . $width . '" />';
			$html .= '<input type="hidden" name="length" value="' . $length . '" />';
			$html .= '<input type="hidden" name="weight" value="' . $weight . '" />';
			$html .= '<button class="button alt" type="submit">' . __( 'Calculate', 'woocommerce-correios' ) .'</button>';
			$html .= '</form>';
			$html .= '</div>';

			echo $html;
		}
	}
}

return new WC_Correios_Product_Shipping_Simulator;
