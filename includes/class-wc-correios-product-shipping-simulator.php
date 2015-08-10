<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_Correios_Product_Shipping_Simulator {

	/**
	 * Simulator is activated.
	 *
	 * @var bool
	 */
	private static $activated = false;

	/**
	 * Shipping simulator actions.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'scritps' ) );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'simulator' ), 45 );
	}

	/**
	 * Shipping simulator scripts.
	 *
	 * @return void
	 */
	public function scritps() {
		if ( is_product() ) {
			$options         = get_option( 'woocommerce_correios_settings' );
			self::$activated = isset( $options['simulator'] ) && 'yes' == $options['simulator'] && 'yes' == $options['enabled'];

			if ( self::$activated ) {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_style( 'woocommerce-correios-simulator', plugins_url( 'assets/css/simulator' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), WC_Correios::VERSION, 'all' );
				wp_enqueue_script( 'woocommerce-correios-simulator', plugins_url( 'assets/js/simulator' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_Correios::VERSION, true );
				wp_localize_script(
					'woocommerce-correios-simulator',
					'woocommerce_correios_simulator',
					array(
						'ajax_url'      => admin_url( 'admin-ajax.php' ),
						'error_message' => __( 'It was not possible to simulate the shipping, please try adding the product to cart and proceed to try to get the value.', 'woocommerce-correios' )
					)
				);
			}
		}
	}

	/**
	 * Display the simulator.
	 *
	 * @return string Simulator HTML.
	 */
	public static function simulator() {
		global $product;

		if ( ! is_product() || ! self::$activated ) {
			return;
		}

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

			wc_get_template( 'single-product/correios-simulator.php', array(
				'product'     => $product,
				'style'       => $style,
				'ids'         => $ids,
				'title'       => $title,
				'description' => $description,
			), '', WC_Correios::get_templates_path() );
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
		$service_carta_reg  = isset( $options['service_carta_reg'] ) ? $options['service_carta_reg'] : '';

		$services = array();
		$services['PAC']              = ( 'yes' == $service_pac ) ? '41106' : '';
		$services['SEDEX']            = ( 'yes' == $service_sedex ) ? '40010' : '';
		$services['SEDEX 10']         = ( 'yes' == $service_sedex_10 ) ? '40215' : '';
		$services['SEDEX Hoje']       = ( 'yes' == $service_sedex_hoje ) ? '40290' : '';
		$services['Carta Registrada'] = ( 'yes' == $service_carta_reg ) ? '10014' : '';

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
		if ( 0 == $value ) {
			return __( '(Free)', 'woocommerce-correios' );
		}

		return sanitize_text_field( wc_price( $value ) );
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
					$cost     = WC_Correios_Connect::fix_currency_format( esc_attr( $shipping->Valor ) );
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
			wp_send_json( array( 'error' => __( 'Error to identify the product.', 'woocommerce-correios' ), 'rates' => '' ) );
		}

		if ( ! isset( $_GET['zipcode'] ) || empty( $_GET['zipcode'] ) ) {
			wp_send_json( array( 'error' => __( 'Please enter with your zipcode.', 'woocommerce-correios' ), 'rates' => '' ) );
		}

		// Get the product data.
		$id         = ( isset( $_GET['variation_id'] ) && ! empty( $_GET['variation_id'] ) ) ? $_GET['variation_id'] : $_GET['product_id'];
		$product_id = absint( $id );
		$product    = get_product( $product_id );
		$quantity   = ( isset( $_GET['quantity'] ) && ! empty( $_GET['quantity'] ) ) ? $_GET['quantity'] : 1;

		// Test with the product exist.
		if ( ! $product ) {
			wp_send_json( array( 'error' => __( 'Invalid product!', 'woocommerce-correios' ), 'rates' => '' ) );
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
			$connect->set_declared_value( $product_price );
		}
		if ( 'corporate' == $corporate_service ) {
			$connect->set_login( $login );
			$connect->set_password( $password );
		}

		$shipping = $connect->get_shipping();
		$shipping_values = array();
		$allow_carta_reg = self::allow_carta_reg( $package );

		foreach ( $shipping as $code => $shipping_item ) {
			$name = WC_Correios_Connect::get_service_name( $code );

			if ( $name == 'Carta Registrada' ) {
				$package_data   = $_package->get_data();
				$shipping_price = self::calc_carta_reg_price( $package_data['weight'] );

				if ( $allow_carta_reg == 0 || $shipping_price == 0 ) {
					continue;
				} else {
					$shipping_item->Valor = $shipping_price;
				}
			}

			$shipping_values[$code] = $shipping_item;
		}

		// Send shipping rates.
		if ( ! empty( $shipping_values ) ) {
			$_shipping = self::get_the_shipping( $shipping_values, $options, $package );
			wp_send_json( array( 'error' => '', 'rates' => $_shipping ) );
		}

		// Error.
		wp_send_json( array( 'error' => __( 'It was not possible to simulate the shipping, please try adding the product to cart and proceed to try to get the value.', 'woocommerce-correios' ), 'rates' => '' ) );
	}


	/**
	* Verify if all products in the package allow Carta Registrada.
	*
	* @return boolean
	*/
	public static function allow_carta_reg( $package ) {
		$options = get_option( 'woocommerce_correios_settings' );
		$items = 0;
		foreach ( $package['contents'] as $item_id => $values ) {
			$product = $values['data'];
			$items++;

			$ship_class = $product->get_shipping_class();

			if ( $product->needs_shipping() ) {
				if ($ship_class != $options['carta_reg_shipping_class'] ) {
					return 0;
				}
			}
		}

		return ($items > 0) ? 1 : 0;
	}

	/**
	* Calculates Carta Registrada based on weight.
	*
	* @return float
	*/
	public static function calc_carta_reg_price( $weight ) {
		$options = get_option( 'woocommerce_correios_settings' );
		if ( $weight > 0 && $weight <= 0.020)
		  return $options['carta_reg_price_0_20'];
		elseif ( $weight > 0.020 && $weight <= 0.050)
		  return $options['carta_reg_price_20_50'];
		elseif ( $weight > 0.050 && $weight <= 0.100)
		  return $options['carta_reg_price_50_100'];
		elseif ( $weight > 0.100 && $weight <= 0.150)
		  return $options['carta_reg_price_100_150'];
		elseif ( $weight > 0.150 && $weight <= 0.200)
		  return $options['carta_reg_price_150_200'];
		elseif ( $weight > 0.200 && $weight <= 0.250)
		  return $options['carta_reg_price_200_250'];
		elseif ( $weight > 0.250 && $weight <= 0.300)
		  return $options['carta_reg_price_250_300'];
		elseif ( $weight > 0.300 && $weight <= 0.350)
		  return $options['carta_reg_price_300_350'];
		elseif ( $weight > 0.350 && $weight <= 0.400)
		  return $options['carta_reg_price_350_400'];
		elseif ( $weight > 0.400 && $weight <= 0.450)
		  return $options['carta_reg_price_400_450'];
		elseif ( $weight > 0.450 && $weight <= 0.500)
		  return $options['carta_reg_price_450_500'];
		else
		  return 0;
	}
}

new WC_Correios_Product_Shipping_Simulator();
