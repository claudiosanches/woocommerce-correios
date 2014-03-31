<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Shipping_Correios class.
 */
class WC_Shipping_Correios extends WC_Shipping_Method {

	/**
	 * Initialize the Correios shipping method.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->id                 = WC_Correios::get_method_id();
		$this->plugin_slug        = WC_Correios::get_plugin_slug();
		$this->method_title       = __( 'Correios', $this->plugin_slug );
		$this->method_description = __( 'Correios is a brazilian delivery method.', $this->plugin_slug );

		// API.
		$this->webservice = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?';

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		$this->init();
	}

	/**
	 * Initializes the method.
	 *
	 * @return void
	 */
	public function init() {
		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enabled            = $this->get_option( 'enabled' );
		$this->title              = $this->get_option( 'title' );
		$this->declare_value      = $this->get_option( 'declare_value' );
		$this->display_date       = $this->get_option( 'display_date' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->availability       = $this->get_option( 'availability' );
		$this->fee                = $this->get_option( 'fee' );
		$this->zip_origin         = $this->get_option( 'zip_origin' );
		$this->countries          = $this->get_option( 'countries' );
		$this->corporate_service  = $this->get_option( 'corporate_service' );
		$this->login              = $this->get_option( 'login' );
		$this->password           = $this->get_option( 'password' );
		$this->service_pac        = $this->get_option( 'service_pac' );
		$this->service_sedex      = $this->get_option( 'service_sedex' );
		$this->service_sedex_10   = $this->get_option( 'service_sedex_10' );
		$this->service_sedex_hoje = $this->get_option( 'service_sedex_hoje' );
		$this->service_esedex     = $this->get_option( 'service_esedex' );
		$this->minimum_height     = $this->get_option( 'minimum_height' );
		$this->minimum_width      = $this->get_option( 'minimum_width' );
		$this->minimum_length     = $this->get_option( 'minimum_length' );
		$this->debug              = $this->get_option( 'debug' );

		// Active logs.
		if ( 'yes' == $this->debug ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->log = new WC_Logger();
			} else {
				$this->log = $this->woocommerce_method()->logger();
			}
		}
	}

	/**
	 * Backwards compatibility with version prior to 2.1.
	 *
	 * @return object Returns the main instance of WooCommerce class.
	 */
	protected function woocommerce_method() {
		if ( function_exists( 'WC' ) ) {
			return WC();
		} else {
			global $woocommerce;
			return $woocommerce;
		}
	}

	/**
	 * Admin options fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'            => __( 'Enable/Disable', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable this shipping method', $this->plugin_slug ),
				'default'          => 'no'
			),
			'title' => array(
				'title'            => __( 'Title', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'This controls the title which the user sees during checkout.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => __( 'Correios', $this->plugin_slug )
			),
			'availability' => array(
				'title'            => __( 'Availability', $this->plugin_slug ),
				'type'             => 'select',
				'default'          => 'all',
				'class'            => 'availability',
				'options'          => array(
					'all'          => __( 'All allowed countries', $this->plugin_slug ),
					'specific'     => __( 'Specific Countries', $this->plugin_slug )
				)
			),
			'countries' => array(
				'title'            => __( 'Specific Countries', $this->plugin_slug ),
				'type'             => 'multiselect',
				'class'            => 'chosen_select',
				'css'              => 'width: 450px;',
				'options'          => $this->woocommerce_method()->countries->countries
			),
			'zip_origin' => array(
				'title'            => __( 'Origin Zip Code', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'Zip Code from where the requests are sent.', $this->plugin_slug ),
				'desc_tip'         => true
			),
			'declare_value' => array(
				'title'            => __( 'Declare value', $this->plugin_slug ),
				'type'             => 'select',
				'default'          => 'none',
				'options'          => array(
					'declare'      => __( 'Declare', $this->plugin_slug ),
					'none'         => __( 'None', $this->plugin_slug )
				),
			),
			'display_date' => array(
				'title'            => __( 'Estimated delivery', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', $this->plugin_slug ),
				'description'      => __( 'Display date of estimated delivery.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => 'no'
			),
			'additional_time' => array(
				'title'            => __( 'Additional days', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'Additional days to the estimated delivery.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => '0',
				'placeholder'      => '0'
			),
			'fee' => array(
				'title'            => __( 'Handling Fee', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', $this->plugin_slug ),
				'desc_tip'         => true,
				'placeholder'      => '0.00'
			),
			'services' => array(
				'title'            => __( 'Correios Services', $this->plugin_slug ),
				'type'             => 'title'
			),
			'corporate_service' => array(
				'title'            => __( 'Corporate Service', $this->plugin_slug ),
				'type'             => 'select',
				'description'      => __( 'Choose between conventional or corporate service.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => 'conventional',
				'options'          => array(
					'conventional' => __( 'Conventional', $this->plugin_slug ),
					'corporate'    => __( 'Corporate', $this->plugin_slug )
				),
			),
			'login' => array(
				'title'            => __( 'Administrative Code', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'Your Correios login.', $this->plugin_slug ),
				'desc_tip'         => true
			),
			'password' => array(
				'title'            => __( 'Administrative Password', $this->plugin_slug ),
				'type'             => 'password',
				'description'      => __( 'Your Correios password.', $this->plugin_slug ),
				'desc_tip'         => true
			),
			'service_pac' => array(
				'title'            => __( 'PAC', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', $this->plugin_slug ),
				'description'      => __( 'Shipping via PAC.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => 'no'
			),
			'service_sedex' => array(
				'title'            => __( 'SEDEX', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', $this->plugin_slug ),
				'description'      => __( 'Shipping via SEDEX.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => 'no'
			),
			'service_sedex_10' => array(
				'title'            => __( 'SEDEX 10', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', $this->plugin_slug ),
				'description'      => __( 'Shipping via SEDEX 10.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => 'no'
			),
			'service_sedex_hoje' => array(
				'title'            => __( 'SEDEX Hoje', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', $this->plugin_slug ),
				'description'      => __( 'Shipping via SEDEX Hoje.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => 'no'
			),
			'service_esedex' => array(
				'title'            => __( 'e-SEDEX', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', $this->plugin_slug ),
				'description'      => __( 'Shipping via e-SEDEX.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => 'no'
			),
			'package_standard' => array(
				'title'            => __( 'Package Standard', $this->plugin_slug ),
				'type'             => 'title',
				'description'      => __( 'Sets a minimum measure for the package.', $this->plugin_slug ),
				'desc_tip'         => true,
			),
			'minimum_height' => array(
				'title'            => __( 'Minimum Height', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'Minimum height of the package. Correios needs at least 2 cm.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => '2'
			),
			'minimum_width' => array(
				'title'            => __( 'Minimum Width', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'Minimum width of the package. Correios needs at least 11 cm.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => '11'
			),
			'minimum_length' => array(
				'title'            => __( 'Minimum Length', $this->plugin_slug ),
				'type'             => 'text',
				'description'      => __( 'Minimum length of the package. Correios needs at least 16 cm.', $this->plugin_slug ),
				'desc_tip'         => true,
				'default'          => '16'
			),
			'testing' => array(
				'title'            => __( 'Testing', $this->plugin_slug ),
				'type'             => 'title'
			),
			'debug' => array(
				'title'            => __( 'Debug Log', $this->plugin_slug ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable logging', $this->plugin_slug ),
				'default'          => 'no',
				'description'      => sprintf( __( 'Log Correios events, such as WebServices requests, inside %s.', $this->plugin_slug ), '<code>woocommerce/logs/correios-' . sanitize_file_name( wp_hash( 'correios' ) ) . '.txt</code>' )
			)
		);
	}

	/**
	 * Correios options page.
	 *
	 * @return void
	 */
	public function admin_options() {
		// Call the admin scripts.
		wp_enqueue_script( 'wc-correios', plugins_url( 'assets/js/admin.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '', true );

		echo '<h3>' . $this->method_title . '</h3>';
		echo '<p>' . $this->method_description . '</p>';
		echo '<table class="form-table">';
			$this->generate_settings_html();
		echo '</table>';
	}

	/**
	 * Checks if the method is available.
	 *
	 * @param array $package Order package.
	 *
	 * @return bool
	 */
	public function is_available( $package ) {
		$is_available = true;

		if ( 'no' == $this->enabled ) {
			$is_available = false;
		} else {
			$ship_to_countries = '';

			if ( 'specific' == $this->availability ) {
				$ship_to_countries = $this->countries;
			} elseif ( 'specific' == get_option( 'woocommerce_allowed_countries' ) ) {
				$ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );
			}

			if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
				$is_available = false;
			}
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}

	/**
	 * Replace comma by dot.
	 *
	 * @param  mixed $value Value to fix.
	 *
	 * @return mixed
	 */
	private function fix_format( $value ) {
		$value = str_replace( ',', '.', $value );

		return $value;
	}

	/**
	 * Extracts the weight and dimensions from the order.
	 *
	 * @param array $package
	 *
	 * @return array
	 */
	protected function measures_extract( $package ) {
		$count  = 0;
		$height = array();
		$width  = array();
		$length = array();
		$weight = array();

		// Shipping per item.
		foreach ( $package['contents'] as $item_id => $values ) {
			$product = $values['data'];
			$qty = $values['quantity'];

			if ( $qty > 0 && $product->needs_shipping() ) {

				if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
					$_height = wc_get_dimension( $this->fix_format( $product->height ), 'cm' );
					$_width  = wc_get_dimension( $this->fix_format( $product->width ), 'cm' );
					$_length = wc_get_dimension( $this->fix_format( $product->length ), 'cm' );
					$_weight = wc_get_weight( $this->fix_format( $product->weight ), 'kg' );
				} else {
					$_height = woocommerce_get_dimension( $this->fix_format( $product->height ), 'cm' );
					$_width  = woocommerce_get_dimension( $this->fix_format( $product->width ), 'cm' );
					$_length = woocommerce_get_dimension( $this->fix_format( $product->length ), 'cm' );
					$_weight = woocommerce_get_weight( $this->fix_format( $product->weight ), 'kg' );
				}

				$height[ $count ] = $_height;
				$width[ $count ]  = $_width;
				$length[ $count ] = $_length;
				$weight[ $count ] = $_weight;

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

				$count++;
			}
		}

		return array(
			'height' => array_values( $height ),
			'length' => array_values( $length ),
			'width'  => array_values( $width ),
			'weight' => array_sum( $weight ),
		);
	}

	/**
	 * Gets the services IDs.
	 *
	 * @return array
	 */
	protected function correios_services() {
		$services = array();

		$services['PAC'] = ( 'yes' == $this->service_pac ) ? '41106' : '';
		$services['SEDEX'] = ( 'yes' == $this->service_sedex ) ? '40010' : '';
		$services['SEDEX 10'] = ( 'yes' == $this->service_sedex_10 ) ? '40215' : '';
		$services['SEDEX Hoje'] = ( 'yes' == $this->service_sedex_hoje ) ? '40290' : '';

		if ( 'corporate' == $this->corporate_service ) {
			$services['PAC'] = ( 'yes' == $this->service_pac ) ? '41068' : '';
			$services['SEDEX'] = ( 'yes' == $this->service_sedex ) ? '40096' : '';
			$services['e-SEDEX'] = ( 'yes' == $this->service_esedex ) ? '81019' : '';
		}

		return array_filter( $services );
	}

	/**
	 * Estimating Delivery.
	 *
	 * @param string $label
	 * @param string $date
	 *
	 * @return string
	 */
	protected function estimating_delivery( $label, $date ) {
		$msg = $label;

		if ( $this->additional_time > 0 ) {
			$date += (int) $this->additional_time;
		}

		if ( $date > 0 ) {
			$msg .= ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days', $date, $this->plugin_slug ),  $date ) . ')';
		}

		return $msg;
	}

	/**
	 * Gets the price of shipping.
	 *
	 * @param  array $package Order package.
	 *
	 * @return array          Correios Quotes.
	 */
	protected function correios_calculate( $package ) {
		// Proccess measures.
		$measures = apply_filters( 'woocommerce_correios_default_package', $this->measures_extract( $package ) );

		// Checks if the cart is not just virtual goods.
		if ( ! empty( $measures['height'] ) && ! empty( $measures['width'] ) && ! empty( $measures['length'] ) ) {

			// Get the Cubage.
			$cubage = new WC_Correios_Cubage( $measures['height'], $measures['width'], $measures['length'] );
			$totalcubage = $cubage->cubage();

			$services = array_values( $this->correios_services() );
			$zip_destination = $package['destination']['postcode'];

			// Test min values.
			$min_height = $this->minimum_height;
			$min_width  = $this->minimum_width;
			$min_length = $this->minimum_length;

			$height = ( $totalcubage['height'] < $min_height ) ? $min_height : $totalcubage['height'];
			$width  = ( $totalcubage['width'] < $min_width ) ? $min_width : $totalcubage['width'];
			$length = ( $totalcubage['length'] < $min_length ) ? $min_length : $totalcubage['length'];

			if ( 'yes' == $this->debug ) {
				$weight_cubage = array(
					'weight' => $measures['weight'],
					'height' => $height,
					'width'  => $width,
					'length' => $length
				);

				$this->log->add( 'correios', 'Weight and cubage of the order: ' . print_r( $weight_cubage, true ) );
			}

			$api = new WC_Correios_API( $this->debug, $this->log );
			$api->set_services( $services );
			$api->set_zip_origin( $this->zip_origin );
			$api->set_zip_destination( $zip_destination );
			$api->set_height( $height );
			$api->set_width( $width );
			$api->set_length( $length );
			$api->set_weight( $measures['weight'] );

			if ( 'declare' == $this->declare_value ) {
				$declared_value = number_format( $this->woocommerce_method()->cart->cart_contents_total, 2, ',', '' );
				$api->set_declared_value( $declared_value );
			}

			if ( 'corporate' == $this->corporate_service ) {
				$api->set_login( $this->login );
				$api->set_password( $this->password );
			}

			return $api->get_shipping();

		} else {

			// Cart only with virtual products.
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'correios', 'Cart only with virtual products.' );
			}

			return array();
		}
	}

	/**
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 *
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$rates  = array();
		$quotes = $this->correios_calculate( $package );

		if ( $quotes ) {
			foreach ( $quotes as $key => $value ) {
				$name = WC_Correios_API::get_service_name( $key );

				if ( 0 == $value->Erro ) {

					$label = ( 'yes' == $this->display_date ) ? $this->estimating_delivery( $name, $value->PrazoEntrega ) : $name;
					$cust = $this->fix_format( esc_attr( $value->Valor ) );
					$fee = $this->get_fee( $this->fix_format( $this->fee ), $cust );

					array_push(
						$rates,
						array(
							'id'    => $name,
							'label' => $label,
							'cost'  => $cust + $fee,
						)
					);
				}
			}

			$rate = apply_filters( 'woocommerce_correios_shipping_methods', $rates, $package );

			// Register the rate.
			foreach ( $rate as $key => $value )
				$this->add_rate( $value );
		}
	}
}
