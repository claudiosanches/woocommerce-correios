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
		if ( ! isset( $_POST['product_id'] ) ) {
			echo json_encode( array( 'error' => __( 'error', 'woocommerce-correios' ) ) );
			die();
		}

		if ( ! isset( $_POST['zipcode'] ) ) {
			echo json_encode( array( 'error' => __( 'error', 'woocommerce-correios' ) ) );
			die();
		}

		// $product_id = absint( $_POST['product_id'] );
		// // $height = sanitize_text_field( $_POST['height'] );
		// // $width = sanitize_text_field( $_POST['width'] );
		// // $length = sanitize_text_field( $_POST['length'] );
		// // $weight = sanitize_text_field( $_POST['weight'] );
		// $zip_destination = $_POST['zipcode'];

		// $options = get_option( 'woocommerce_correios_settings' );

		// $services = self::correios_services( $options );

		// $zip_origin = isset( $options['zip_origin'] ) ? $options['zip_origin'] : '';
		// $display_date = isset( $options['display_date'] ) ? $options['display_date'] : '';
		// $additional_time = isset( $options['additional_time'] ) ? $options['additional_time'] : '';
		// $declare_value = isset( $options['declare_value'] ) ? $options['declare_value'] : '';
		// $corporate_service = isset( $options['corporate_service'] ) ? $options['corporate_service'] : '';
		// $login = isset( $options['login'] ) ? $options['login'] : '';
		// $password = isset( $options['password'] ) ? $options['password'] : '';
		// $fee = isset( $options['fee'] ) ? $options['fee'] : '';

		// $minimum_height = isset( $options['minimum_height'] ) ? $options['minimum_height'] : '';
		// $minimum_width = isset( $options['minimum_width'] ) ? $options['minimum_width'] : '';
		// $minimum_length = isset( $options['minimum_length'] ) ? $options['minimum_length'] : '';

		// $debug = isset( $options['debug'] ) ? $options['debug'] : '';

		// $api = new WC_Correios_API( $debug );
		// $api->set_services( $services );
		// $api->set_zip_origin( $zip_origin );
		// $api->set_zip_destination( $zip_destination );
		// $api->set_height( $height );
		// $api->set_width( $width );
		// $api->set_length( $length );
		// $api->set_weight( $weight );

		// if ( 'declare' == $declare_value ) {
		// 	// $declared_value = number_format( $woocommerce_method()->cart->cart_contents_total, 2, ',', '' );
		// 	// $api->set_declared_value( $declared_value );
		// }

		// if ( 'corporate' == $corporate_service ) {
		// 	$api->set_login( $login );
		// 	$api->set_password( $password );
		// }

		// $response = $api->get_shipping();

		// error_log( print_r( $response, true ) );

		// echo json_encode( $response );

		die();
	}
}

return new WC_Correios_Product_Shipping_Simulator;
