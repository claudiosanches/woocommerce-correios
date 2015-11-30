<?php
/**
 * Abstract Correios shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Correios shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class WC_Correios_Shipping extends WC_Shipping_Method {

	/**
	 * Initialize the Correios shipping method.
	 */
	public function __construct() {
		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enabled            = $this->get_option( 'enabled' );
		$this->title              = $this->get_option( 'title' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->fee                = $this->get_option( 'fee' );
		$this->debug              = $this->get_option( 'debug' );

		// Method variables.
		$this->availability = 'specific';
		$this->countries    = array( 'BR' );

		// Active logs.
		if ( 'yes' == $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Save admin options.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_view() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			return ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'View logs.', 'woocommerce-correios' ) . '</a>';
		}
	}

	/**
	 * Admin options fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this shipping method', 'woocommerce-correios' ),
				'default' => 'no',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => $this->method_title,
			),
			'behavior_options' => array(
				'title' => __( 'Behavior Options', 'woocommerce-correios' ),
				'type'  => 'title',
			),
			'show_delivery_time' => array(
				'title'       => __( 'Delivery Time', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Show estimated delivery time', 'woocommerce-correios' ),
				'description' => __( 'Display the estimated delivery time in working days.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'additional_time' => array(
				'title'       => __( 'Additional Days', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Additional working days to the estimated delivery.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
			),
			'fee' => array(
				'title'       => __( 'Handling Fee', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '0.00',
			),
			'testing' => array(
				'title' => __( 'Testing', 'woocommerce-correios' ),
				'type'  => 'title',
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-correios' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), $this->method_title ) . $this->get_log_view(),
			),
		);
	}

	/**
	 * Correios options page.
	 */
	public function admin_options() {
		include WC_Correios::get_plugin_path() . 'includes/admin/views/html-admin-shipping-method-settings.php';
	}

	/**
	 * Check if need to use corporate services.
	 *
	 * @return bool
	 */
	protected function is_corporate() {
		return apply_filters( 'woocommerce_correios_use_corporate_method', false, $this->id );
	}

	/**
	 * Get Correios service code.
	 *
	 * @return string
	 */
	protected function get_code() {
		return apply_filters( 'woocommerce_correios_shipping_method_code', $this->code, $this->id );
	}

	/**
	 * Get shipping rate.
	 *
	 * @param  array $package Order package.
	 *
	 * @return SimpleXMLElement
	 */
	protected function get_rate( $package ) {
		$code = $this->get_code();

		$connect  = new WC_Correios_Webservice( $this->id );
		$connect->set_services( array( $code ) );
		$_package = $connect->set_package( $package );
		$connect->set_destination_postcode( $package['destination']['postcode'] );
		$connect->set_debug( $this->debug );

		if ( apply_filters( 'woocommerce_correios_declare_value', false, $this->id ) ) {
			$declared_value = WC()->cart->cart_contents_total;
			$connect->set_declared_value( $declared_value );
		}

		if ( $this->is_corporate() ) {
			$connect->set_login( $this->login );
			$connect->set_password( $this->password );
		}

		$shipping = $connect->get_shipping();

		if ( ! empty( $shipping[ $code ] ) ) {
			return $shipping[ $code ];
		}

		return null;
	}

	/**
	 * Get accepted error codes.
	 *
	 * @return array
	 */
	protected function get_accepted_error_codes() {
		$codes   = apply_filters( 'woocommerce_correios_accepted_error_codes', array( '-33', '-3', '010' ) );
		$codes[] = '0';

		return $codes;
	}

	/**
	 * Get shipping method label.
	 *
	 * @param  int $days Days to deliver.
	 *
	 * @return string
	 */
	protected function get_shipping_method_label( $days ) {
		if ( 'yes' == $this->show_delivery_time ) {
			return wc_correios_get_estimating_delivery( $this->title, $days, $this->additional_time );
		}

		return $this->title;
	}

	/**
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 */
	public function calculate_shipping( $package = array() ) {
		$error    = '';
		$shipping = $this->get_rate( $package );

		if ( ! isset( $shipping->Erro ) ) {
			return;
		}

		$error_number = (string) $shipping->Erro;

		// Exit if have errors.
		if ( ! in_array( $error_number, $this->get_accepted_error_codes() ) ) {
			return;
		}

		// Set the shipping rates.
		$label = $this->get_shipping_method_label( $shipping->PrazoEntrega );
		$cost  = wc_correios_normalize_price( esc_attr( $shipping->Valor ) );
		$fee   = $this->get_fee( str_replace( ',', '.', $this->fee ), $cost );

		// Display Correios errors notices.
		$error_message = wc_correios_get_error_message( $shipping->Erro );
		if ( '' != $error_message ) {
			$notice_type = ( '010' == $error_number ) ? 'notice' : 'error';
			$notice      = '<strong>' . __( 'Correios', 'woocommerce-correios' ) . ':</strong> ' . esc_html( $error_message );
			wc_add_notice( $notice, $notice_type );
		}

		// Create the rate and apply filters.
		$rate = apply_filters( 'woocommerce_correios_' . $this->id . '_rate', array(
			'id'    => $this->title,
			'label' => $label,
			'cost'  => $cost + $fee,
		) );

		// Deprecated filter.
		$rates = apply_filters( 'woocommerce_correios_shipping_methods', array( $rate ), $package );

		// Add rate to WooComemrce.
		$this->add_rate( $rates[0] );
	}
}
