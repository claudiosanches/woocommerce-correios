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
		wp_localize_script(
			'woocommerce-correios-simulator',
			'woocommerce_correios_simulator',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'security'      => wp_create_nonce( 'woocommerce_correios_simulator' ),
				'error_message' => __( 'Error while getting the values', 'woocommerce-correios' )
			)
		);
	}

	public static function simulator() {
		global $product;

		if ( $product->needs_shipping() && in_array( $product->product_type, array( 'simple', 'variable' ) ) ) {

			$style = ( 'variable' == $product->product_type ) ? 'display: none' : '';

			$html = '<div id="wc-correios-simulator" style="' . $style . '">';
			$html .= '<strong>' . __( 'Shipping and Delivery Time.', 'woocommerce-correios' ) . '</strong>';
			$html .= '<p>' . __( 'Calculate shipping and delivery time estimated to your region.', 'woocommerce-correios' ) . '</p>';
			$html .= '<form method="post" class="cart">';
			$html .= '<input type="text" size="9" class="input-text text" placeholder="00000-000" id="zipcode" name="zipcode" />';
			$html .= '<button class="button" type="submit">' . __( 'Calculate', 'woocommerce-correios' ) .'</button>';
			$html .= '<br class="clear" />';
			$html .= '<div id="simulator-data"></div>';
			$html .= '</form>';
			$html .= '</div>';

			echo $html;
		}
	}

	protected static function correios_services( $options ) {
		$corporate_service  = isset( $options['corporate_service'] ) ? $options['corporate_service'] : '';
		$service_pac        = isset( $options['service_pac'] ) ? $options['service_pac'] : '';
		$service_sedex      = isset( $options['service_sedex'] ) ? $options['service_sedex'] : '';
		$service_sedex_10   = isset( $options['service_sedex_10'] ) ? $options['service_sedex_10'] : '';
		$service_sedex_hoje = isset( $options['service_sedex_hoje'] ) ? $options['service_sedex_hoje'] : '';
		$service_esedex     = isset( $options['service_esedex'] ) ? $options['service_esedex'] : '';

		$services = array();
		$services['PAC']        = ( 'yes' == $service_pac ) ? '41106' : '';
		$services['SEDEX']      = ( 'yes' == $service_sedex ) ? '40010' : '';
		$services['SEDEX 10']   = ( 'yes' == $service_sedex_10 ) ? '40215' : '';
		$services['SEDEX Hoje'] = ( 'yes' == $service_sedex_hoje ) ? '40290' : '';

		if ( 'corporate' == $corporate_service ) {
			$services['PAC']     = ( 'yes' == $service_pac ) ? '41068' : '';
			$services['SEDEX']   = ( 'yes' == $service_sedex ) ? '40096' : '';
			$services['e-SEDEX'] = ( 'yes' == $service_esedex ) ? '81019' : '';
		}

		return array_filter( $services );
	}

	protected static function calcule_measures( $height, $width, $length, $weight, $qty, $options ) {
		$count   = 0;
		$_height = array();
		$_width  = array();
		$_length = array();
		$_weight = array();

		$_height[ $count ] = $height;
		$_width[ $count ]  = $width;
		$_length[ $count ] = $length;
		$_weight[ $count ] = $weight;

		if ( $qty > 1 ) {
			$n = $count;
			for ( $i = 0; $i < $qty; $i++ ) {
				$height[ $n ] = $_height;
				$width[ $n ]  = $_width;
				$length[ $n ] = $_length;
				$weight[ $n ] = $_weight;
				$n++;
			}
			$count = $n;
		}
	}

	public static function ajax_simulator() {
		check_ajax_referer( 'woocommerce_correios_simulator', 'security' );

		// Validate the data.
		if ( ! isset( $_GET['product_id'] ) || empty( $_GET['product_id'] ) ) {
			echo json_encode( array( 'error' => __( 'Error to identify the product.', 'woocommerce-correios' ), 'content' => '' ) );
			die();
		}

		if ( ! isset( $_GET['zipcode'] ) || empty( $_GET['zipcode'] ) ) {
			echo json_encode( array( 'error' => __( 'Please enter with your zipcode.', 'woocommerce-correios' ), 'content' => '' ) );
			die();
		}

		// Get the product data.
		$product_id = absint( $_GET['product_id'] );
		$product    = get_product( $product_id );

		// Test with the product exist.
		if ( ! $product ) {
			echo json_encode( array( 'error' => __( 'Invalid product!', 'woocommerce-correios' ), 'content' => '' ) );
			die();
		}

		// Set the shipping params.
		$product_price     = $product->get_price();
		$zip_destination   = $_GET['zipcode'];
		$options           = get_option( 'woocommerce_correios_settings' );
		$minimum_height    = isset( $options['minimum_height'] ) ? $options['minimum_height'] : '';
		$minimum_width     = isset( $options['minimum_width'] ) ? $options['minimum_width'] : '';
		$minimum_length    = isset( $options['minimum_length'] ) ? $options['minimum_length'] : '';
		$zip_origin        = isset( $options['zip_origin'] ) ? $options['zip_origin'] : '';
		$display_date      = isset( $options['display_date'] ) ? $options['display_date'] : '';
		$additional_time   = isset( $options['additional_time'] ) ? $options['additional_time'] : '';
		$declare_value     = isset( $options['declare_value'] ) ? $options['declare_value'] : '';
		$corporate_service = isset( $options['corporate_service'] ) ? $options['corporate_service'] : '';
		$login             = isset( $options['login'] ) ? $options['login'] : '';
		$password          = isset( $options['password'] ) ? $options['password'] : '';
		$fee               = isset( $options['fee'] ) ? $options['fee'] : '';
		$debug             = isset( $options['debug'] ) ? $options['debug'] : '';
		$package           = array(
			'contents' => array(
				array(
					'data' => $product,
					'quantity'  => 1
				)
			)
		);

		// Get the shipping.
		$services = array_values( self::correios_services( $options ) );
		$connect  = new WC_Correios_Connect;
		$connect->set_services( $services );
		$_package = $connect->set_package( $package );
		$_package->set_minimum_height( $minimum_height );
		$_package->set_minimum_width( $minimum_width );
		$_package->set_minimum_width( $minimum_length );
		$connect->set_zip_origin( $zip_origin );
		$connect->set_zip_destination( $zip_destination );
		$connect->set_debug( $debug );
		if ( 'declare' == $declare_value ) {
			$declared_value = number_format( $product_price, 2, ',', '' );
			$connect->set_declared_value( $declared_value );
		}
		if ( 'corporate' == $corporate_service ) {
			$connect->set_login( $login );
			$connect->set_password( $password );
		}

		$shipping = $connect->get_shipping();

		if ( ! empty( $shipping ) ) {
			// return $shipping;
		} else {
			// return array();
		}

		die();
	}
}

return new WC_Correios_Product_Shipping_Simulator;
