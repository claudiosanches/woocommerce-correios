<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_Correios_Product_Shipping_Simulator {

	/**
	 * Shipping simulator actions.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'get_header', array( $this, 'init' ) );
		}
	}

	/**
	 * Initialize the shipping simulator.
	 *
	 * @return void
	 */
	public function init() {
		if ( is_product() ) {
			$options = get_option( 'woocommerce_correios_settings' );

			if ( isset( $options['simulator'] ) && 'yes' == $options['simulator'] ) {
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'simulator' ), 45 );
				add_action( 'wp_enqueue_scripts', array( $this, 'scritps' ) );
			}
		}
	}

	/**
	 * Shipping simulator scripts.
	 *
	 * @return void
	 */
	public function scritps() {
		wp_enqueue_style( 'woocommerce-correios-simulator', plugins_url( 'assets/css/simulator.css', plugin_dir_path( __FILE__ ) ), array(), WC_Correios::VERSION, 'all' );
		wp_enqueue_script( 'woocommerce-correios-simulator', plugins_url( 'assets/js/simulator.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_Correios::VERSION, true );
		wp_localize_script(
			'woocommerce-correios-simulator',
			'woocommerce_correios_simulator',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'error_message' => __( 'It was not possible to simulate the shipping, please try adding the product to cart and proceed to try to get the value.', 'woocommerce-correios' )
			)
		);
	}

	/**
	 * Display the simulator.
	 *
	 * @return string Simulator HTML.
	 */
	public static function simulator() {
		global $product;

		if ( $product->needs_shipping() && $product->is_in_stock() && in_array( $product->product_type, array( 'simple', 'variable' ) ) ) {
			$options = get_option( 'woocommerce_correios_settings' );
			if ( 'variable' == $product->product_type ) {
				$style = 'display: none';
				$ids   = array();

				foreach ( $product->get_available_variations() as $variation ) {
					$_variation = get_product( $variation['variation_id'] );
					$ids[] = ( $_variation->needs_shipping() ) ? $_variation->variation_id : '';
				}

				$ids = implode( ',', array_filter( $ids ) );
			} else {
				$style = '';
				$ids   = $product->id;
			}

			if ( isset( $options['display_date'] ) && 'yes' == $options['display_date'] ) {
				$title       = __( 'Shipping and delivery time', 'woocommerce-correios' );
				$description = __( 'Calculate the shipping and delivery time estimated to your region.', 'woocommerce-correios' );
			} else {
				$title       = __( 'Shipping', 'woocommerce-correios' );
				$description = __( 'Calculate shipping estimated to your region.', 'woocommerce-correios' );
			}

			$html = '<div id="wc-correios-simulator" style="' . $style . '" data-product-ids="' . $ids . '" data-product-type="' . esc_attr( $product->product_type ) . '">';
			$html .= '<strong>' . $title  . '</strong>';
			$html .= '<p>' . $description . '</p>';
			$html .= '<form method="post" class="cart">';
			$html .= '<input type="text" size="9" class="input-text text" placeholder="00000-000" id="zipcode" name="zipcode" />';
			$html .= '<button class="button" type="submit">' . __( 'Calculate', 'woocommerce-correios' ) .'</button>';
			$html .= '<br class="clear" />';
			$html .= '<div id="simulator-data"></div>';
			$html .= '</form>';
			$html .= '</div>';

			echo apply_filters( 'woocommerce_correios_simulator_output_html', $html );
		}
	}

	/**
	 * Get the correios services.
	 *
	 * @param  array $options Plugin options.
	 *
	 * @return array          Correios services.
	 */
	protected static function get_correios_services( $options ) {
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

	/**
	 * Get the price.
	 *
	 * @param  int    $value Shipping price.
	 *
	 * @return string        Formated shipping price.
	 */
	protected static function get_price( $value ) {
		if ( function_exists( 'wc_price' ) ) {
			return sanitize_text_field( wc_price( $value ) );
		} else {
			return sanitize_text_field( woocommerce_price( $value ) );
		}
	}

	/**
	 * Get the shipping rates.
	 *
	 * @param  object $shipping_values Shipping values.
	 * @param  array  $options         Plugin options.
	 * @param  array  $package         Product package.
	 *
	 * @return array                   Shipping rates.
	 */
	protected static function get_the_shipping( $shipping_values, $options, $package ) {
		$_rates = array();

		if ( ! empty( $shipping_values ) ) {
			foreach ( $shipping_values as $code => $shipping ) {
				if ( isset( $shipping->Erro ) && 0 == $shipping->Erro ) {
					$date     = isset( $options['display_date'] ) ? $options['display_date'] : 'no';
					$fee      = isset( $options['fee'] ) ? $options['fee'] : 0;
					$add_time = isset( $options['additional_time'] ) ? $options['additional_time'] : 0;
					$name     = WC_Correios_Connect::get_service_name( $code );
					$label    = ( 'yes' == $date ) ? WC_Correios_Connect::estimating_delivery( $name, $shipping->PrazoEntrega, $add_time ) : $name;
					$cost     = str_replace( ',', '.', esc_attr( $shipping->Valor ) );
					$fee      = WC_Correios_Connect::get_fee( str_replace( ',', '.', $fee ), $cost );

					$_rates[] = array(
						'id'    => $name,
						'label' => $label,
						'cost'  => $cost + $fee
					);
				}
			}
		}

		$_rates = apply_filters( 'woocommerce_correios_shipping_methods', $_rates, $package );

		// Format the cost.
		$rates = array();
		foreach ( $_rates as $rate ) {
			$rates[] = array(
				'id'    => $rate['id'],
				'label' => $rate['label'],
				'cost'  => self::get_price( $rate['cost'] )
			);
		}

		return $rates;
	}

	/**
	 * Simulator ajax response.
	 *
	 * @return string
	 */
	public static function ajax_simulator() {

		// Validate the data.
		if ( ! isset( $_GET['product_id'] ) || empty( $_GET['product_id'] ) ) {
			echo json_encode( array( 'error' => __( 'Error to identify the product.', 'woocommerce-correios' ), 'rates' => '' ) );
			die();
		}

		if ( ! isset( $_GET['zipcode'] ) || empty( $_GET['zipcode'] ) ) {
			echo json_encode( array( 'error' => __( 'Please enter with your zipcode.', 'woocommerce-correios' ), 'rates' => '' ) );
			die();
		}

		// Get the product data.
		$id         = ( isset( $_GET['variation_id'] ) && ! empty( $_GET['variation_id'] ) ) ? $_GET['variation_id'] : $_GET['product_id'];
		$product_id = absint( $id );
		$product    = get_product( $product_id );
		$quantity   = ( isset( $_GET['quantity'] ) && ! empty( $_GET['quantity'] ) ) ? $_GET['quantity'] : 1;

		// Test with the product exist.
		if ( ! $product ) {
			echo json_encode( array( 'error' => __( 'Invalid product!', 'woocommerce-correios' ), 'rates' => '' ) );
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
					'data'     => $product,
					'quantity' => $quantity
				)
			)
		);

		// Get the shipping.
		$services = array_values( self::get_correios_services( $options ) );
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
			$_shipping = self::get_the_shipping( $shipping, $options, $package );
			echo json_encode( array( 'error' => '', 'rates' => $_shipping ) );
			die();
		} else {
			echo array( 'error' => __( 'It was not possible to simulate the shipping, please try adding the product to cart and proceed to try to get the value.', 'woocommerce-correios' ), 'rates' => '' );
			die();
		}

		die();
	}
}

return new WC_Correios_Product_Shipping_Simulator;
