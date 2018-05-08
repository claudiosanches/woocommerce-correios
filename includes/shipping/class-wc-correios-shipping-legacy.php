<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy Correios shipping method class.
 */
class WC_Correios_Shipping_Legacy extends WC_Shipping_Method {

	/**
	 * Initialize the Correios shipping method.
	 */
	public function __construct() {
		$this->id                 = 'correios';
		$this->method_title       = __( 'Correios', 'woocommerce-correios' );
		$this->method_description = __( 'Correios is a brazilian delivery method.', 'woocommerce-correios' );

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
		$this->fee                = $this->get_option( 'fee' );
		$this->zip_origin         = $this->get_option( 'zip_origin' );
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

		// Method variables.
		$this->availability       = 'specific';
		$this->countries          = array( 'BR' );

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		// Active logs.
		if ( 'yes' == $this->debug ) {
			$this->log = new WC_Logger();
		}
	}

	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_view() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			return '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'System Status &gt; Logs', 'woocommerce-correios' ) . '</a>';
		}

		return '<code>woocommerce/logs/' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>';
	}

	/**
	 * Admin options fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'            => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable this shipping method', 'woocommerce-correios' ),
				'default'          => 'no',
			),
			'title' => array(
				'title'            => __( 'Title', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => __( 'Correios', 'woocommerce-correios' ),
			),
			'zip_origin' => array(
				'title'            => __( 'Origin Zip Code', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Zip Code from where the requests are sent.', 'woocommerce-correios' ),
				'desc_tip'         => true,
			),
			'declare_value' => array(
				'title'            => __( 'Declare value', 'woocommerce-correios' ),
				'type'             => 'select',
				'default'          => 'none',
				'options'          => array(
					'declare'      => __( 'Declare', 'woocommerce-correios' ),
					'none'         => __( 'No', 'woocommerce-correios' ),
				),
			),
			'display_date' => array(
				'title'            => __( 'Estimated delivery', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', 'woocommerce-correios' ),
				'description'      => __( 'Display date of estimated delivery.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'no',
			),
			'additional_time' => array(
				'title'            => __( 'Additional days', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Additional days to the estimated delivery.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => '0',
				'placeholder'      => '0',
			),
			'fee' => array(
				'title'            => __( 'Handling Fee', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'placeholder'      => '0.00',
			),
			'services' => array(
				'title'            => __( 'Correios Services', 'woocommerce-correios' ),
				'type'             => 'title',
			),
			'corporate_service' => array(
				'title'            => __( 'Corporate Service', 'woocommerce-correios' ),
				'type'             => 'select',
				'description'      => __( 'Choose between conventional or corporate service.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'conventional',
				'options'          => array(
					'conventional' => __( 'Conventional', 'woocommerce-correios' ),
					'corporate'    => __( 'Corporate', 'woocommerce-correios' ),
				),
			),
			'login' => array(
				'title'            => __( 'Administrative Code', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Your Correios login.', 'woocommerce-correios' ),
				'desc_tip'         => true,
			),
			'password' => array(
				'title'            => __( 'Administrative Password', 'woocommerce-correios' ),
				'type'             => 'password',
				'description'      => __( 'Your Correios password.', 'woocommerce-correios' ),
				'desc_tip'         => true,
			),
			'service_pac' => array(
				'title'            => __( 'PAC', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', 'woocommerce-correios' ),
				'description'      => __( 'Shipping via PAC.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'no',
			),
			'service_sedex' => array(
				'title'            => __( 'SEDEX', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', 'woocommerce-correios' ),
				'description'      => __( 'Shipping via SEDEX.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'no',
			),
			'service_sedex_10' => array(
				'title'            => __( 'SEDEX 10', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', 'woocommerce-correios' ),
				'description'      => __( 'Shipping via SEDEX 10.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'no',
			),
			'service_sedex_hoje' => array(
				'title'            => __( 'SEDEX Hoje', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', 'woocommerce-correios' ),
				'description'      => __( 'Shipping via SEDEX Hoje.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'no',
			),
			'service_esedex' => array(
				'title'            => __( 'e-SEDEX', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', 'woocommerce-correios' ),
				'description'      => __( 'Shipping via e-SEDEX.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'no',
			),
			'package_standard' => array(
				'title'            => __( 'Package Standard', 'woocommerce-correios' ),
				'type'             => 'title',
				'description'      => __( 'Sets a minimum measure for the package.', 'woocommerce-correios' ),
				'desc_tip'         => true,
			),
			'minimum_height' => array(
				'title'            => __( 'Minimum Height', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Minimum height of the package. Correios needs at least 2 cm.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => '2',
			),
			'minimum_width' => array(
				'title'            => __( 'Minimum Width', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Minimum width of the package. Correios needs at least 11 cm.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => '11',
			),
			'minimum_length' => array(
				'title'            => __( 'Minimum Length', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Minimum length of the package. Correios needs at least 16 cm.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => '16',
			),
			'testing' => array(
				'title'            => __( 'Testing', 'woocommerce-correios' ),
				'type'             => 'title',
			),
			'debug' => array(
				'title'            => __( 'Debug Log', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable logging', 'woocommerce-correios' ),
				'default'          => 'no',
				'description'      => sprintf( __( 'Log Correios events, such as WebServices requests, inside %s.', 'woocommerce-correios' ), $this->get_log_view() ),
			),
		);
	}

	/**
	 * Correios options page.
	 */
	public function admin_options() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Call the admin scripts.
		wp_enqueue_script( 'wc-correios', plugins_url( 'assets/js/admin/shipping-methods' . $suffix . '.js', WC_Correios::get_main_file() ), array( 'jquery' ), '', true );

		echo '<h3>' . esc_html( $this->method_title ) . '</h3>';

		echo '<p>' . esc_html( $this->method_description ) . '</p>';

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6.0', '>=' ) ) {
			echo '<div class="error inline">';
			echo '<p><strong>' . sprintf( __( 'This shipping method this depreciated since %s, and you should configure shipping methods in the new Shipping Zones screen.', 'woocommerce-correios' ), 'WooCommerce 2.6' ) . ' <a href="https://youtu.be/3z9j0NPU_O0">' . __( 'See how to configure.', 'woocommerce-correios' ) . '</a></strong></p>';
			echo '</div>';
		} else {
			include WC_Correios::get_plugin_path() . 'includes/admin/views/html-admin-help-message.php';
		}

		echo '<table class="form-table">';
		$this->generate_settings_html();
		echo '</table>';
	}

	/**
	 * Gets the services IDs.
	 *
	 * @return array
	 */
	protected function correios_services() {
		$services = array();

		$services['PAC'] = ( 'yes' == $this->service_pac ) ? '04510' : '';
		$services['SEDEX'] = ( 'yes' == $this->service_sedex ) ? '04014' : '';
		$services['SEDEX 10'] = ( 'yes' == $this->service_sedex_10 ) ? '40215' : '';
		$services['SEDEX Hoje'] = ( 'yes' == $this->service_sedex_hoje ) ? '40290' : '';

		if ( 'corporate' == $this->corporate_service ) {
			$services['PAC'] = ( 'yes' == $this->service_pac ) ? '04669' : '';
			$services['SEDEX'] = ( 'yes' == $this->service_sedex ) ? '04162' : '';
			$services['e-SEDEX'] = ( 'yes' == $this->service_esedex ) ? '81019' : '';
		}

		return array_filter( $services );
	}

	/**
	 * Gets the price of shipping.
	 *
	 * @param  array $package Order package.
	 *
	 * @return array          Correios Quotes.
	 */
	protected function correios_calculate( $package ) {
		$services = array_values( $this->correios_services() );
		$connect = new WC_Correios_Webservice( $this->id );
		$connect->set_debug( $this->debug );
		$connect->set_service( $services );
		$connect->set_package( $package );
		$connect->set_origin_postcode( $this->zip_origin );
		$connect->set_destination_postcode( $package['destination']['postcode'] );

		if ( 'declare' == $this->declare_value ) {
			$connect->set_declared_value( $package['contents_cost'] );
		}

		if ( 'corporate' == $this->corporate_service ) {
			$connect->set_login( $this->login );
			$connect->set_password( $this->password );
		}

		$connect->set_minimum_height( $this->minimum_height );
		$connect->set_minimum_width( $this->minimum_width );
		$connect->set_minimum_length( $this->minimum_length );

		$shipping = $connect->get_shipping();

		if ( ! empty( $shipping ) ) {
			return $shipping;
		} else {
			// Cart only with virtual products.
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'correios', 'Cart only with virtual products.' );
			}

			return array();
		}
	}

	/**
	 * Gets the service name.
	 *
	 * @param  int $code Correios service ID.
	 *
	 * @return array       Correios service name.
	 */
	protected function get_service_name( $code ) {
		$name = array(
			'04510' => 'PAC',
			'04014' => 'SEDEX',
			'40215' => 'SEDEX 10',
			'40290' => 'SEDEX Hoje',
			'04669' => 'PAC',
			'04162' => 'SEDEX',
			'81019' => 'e-SEDEX',
		);

		return isset( $name[ $code ] ) ? $name[ $code ] : '';
	}

	/**
	 * Get additional time.
	 *
	 * @param  array $package Package data.
	 *
	 * @return array
	 */
	protected function get_additional_time( $package = array() ) {
		return apply_filters( 'woocommerce_correios_shipping_additional_time', $this->additional_time, $package );
	}

	/**
	 * Get accepted error codes.
	 *
	 * @return array
	 */
	protected function get_accepted_error_codes() {
		$codes   = apply_filters( 'woocommerce_correios_accepted_error_codes', array( '-33', '-3', '010', '011' ) );
		$codes[] = '0';

		return $codes;
	}

	/**
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 */
	public function calculate_shipping( $package = array() ) {
		$rates           = array();
		$errors          = array();
		$shipping_values = $this->correios_calculate( $package );

		if ( ! empty( $shipping_values ) ) {
			foreach ( $shipping_values as $shipping ) {
				if ( ! isset( $shipping->Erro ) ) {
					continue;
				}

				$name          = $this->get_service_name( (string) $shipping->Codigo );
				$error_number  = (string) $shipping->Erro;
				$error_message = wc_correios_get_error_message( $error_number );
				$errors[ $error_number ] = array(
					'error'   => $error_message,
					'number'  => $error_number,
				);

				if ( ! in_array( $error_number, $this->get_accepted_error_codes() ) ) {
					continue;
				}

				// Set the shipping rates.
				$label = ( 'yes' == $this->display_date ) ? wc_correios_get_estimating_delivery( $name, (int) $shipping->PrazoEntrega, $this->get_additional_time( $package ) ) : $name;
				$cost  = wc_correios_normalize_price( esc_attr( (string) $shipping->Valor ) );

				// Exit if don't have price.
				if ( 0 === intval( $cost ) ) {
					return;
				}

				$fee = $this->get_fee( str_replace( ',', '.', $this->fee ), $cost );

				array_push(
					$rates,
					array(
						'id'    => $name,
						'label' => $label,
						'cost'  => $cost + $fee,
					)
				);
			}

			// Display correios errors.
			if ( ! empty( $errors ) && is_cart() ) {
				foreach ( $errors as $error ) {
					if ( '' != $error['error'] ) {
						$type = ( '010' == $error['number'] ) ? 'notice' : 'error';
						$message = '<strong>' . __( 'Correios', 'woocommerce-correios' ) . ':</strong> ' . esc_attr( $error['error'] );
						wc_add_notice( $message, $type );
					}
				}
			}

			$rates = apply_filters( 'woocommerce_correios_shipping_methods', $rates, $package );

			// Add rates.
			foreach ( $rates as $rate ) {
				$this->add_rate( $rate );
			}
		}
	}
}
