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
	public function __construct( $instance_id = 0 ) {
		$this->instance_id        = absint( $instance_id );
		$this->method_description = sprintf( __( '%s is a shipping method from Correios.', 'woocommerce-correios' ), $this->method_title );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Define user set variables.
		$this->enabled            = $this->get_option( 'enabled' );
		$this->title              = $this->get_option( 'title' );
		$this->origin_postcode    = $this->get_option( 'origin_postcode' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->fee                = $this->get_option( 'fee' );
		$this->receipt_notice     = $this->get_option( 'receipt_notice' );
		$this->own_hands          = $this->get_option( 'own_hands' );
		$this->declare_value      = $this->get_option( 'declare_value' );
		$this->service_type       = $this->get_option( 'service_type' );
		$this->login              = $this->get_option( 'login' );
		$this->password           = $this->get_option( 'password' );
		$this->enable_tracking    = $this->get_option( 'enable_tracking' );
		$this->tracking_debug     = $this->get_option( 'tracking_debug' );
		$this->minimum_height     = $this->get_option( 'minimum_height' );
		$this->minimum_width      = $this->get_option( 'minimum_width' );
		$this->minimum_length     = $this->get_option( 'minimum_length' );
		$this->debug              = $this->get_option( 'debug' );

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
	protected function get_log_link() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			return ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'View logs.', 'woocommerce-correios' ) . '</a>';
		}
	}

	/**
	 * Get behavior options.
	 *
	 * @return array
	 */
	protected function get_behavior_options() {
		return array();
	}

	/**
	 * Admin options fields.
	 */
	public function init_form_fields() {
		$fields = array();

		$fields['enabled'] = array(
			'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable this shipping method', 'woocommerce-correios' ),
			'default' => 'yes',
		);
		$fields['title'] = array(
			'title'       => __( 'Title', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => $this->method_title,
		);
		$fields['behavior_options'] = array(
			'title'   => __( 'Behavior Options', 'woocommerce-correios' ),
			'type'    => 'title',
			'default' => '',
		);
		$fields['origin_postcode'] = array(
			'title'       => __( 'Origin Postcode', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'The postcode of the location your packages are delivered from.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'placeholder' => '00000-000',
			'default'     => '',
		);

		// Add custom behavior options.
		$fields = array_merge( $fields, $this->get_behavior_options() );

		$fields['show_delivery_time'] = array(
			'title'       => __( 'Delivery Time', 'woocommerce-correios' ),
			'type'        => 'checkbox',
			'label'       => __( 'Show estimated delivery time', 'woocommerce-correios' ),
			'description' => __( 'Display the estimated delivery time in working days.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => 'no',
		);
		$fields['additional_time'] = array(
			'title'       => __( 'Additional Days', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'Additional working days to the estimated delivery.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => '0',
			'placeholder' => '0',
		);
		$fields['fee'] = array(
			'title'       => __( 'Handling Fee', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'placeholder' => '0.00',
			'default'     => '',
		);
		$fields['optional_services'] = array(
			'title'   => __( 'Optional Services', 'woocommerce-correios' ),
			'type'    => 'title',
			'default' => '',
		);
		$fields['receipt_notice'] = array(
			'title'       => __( 'Receipt Notice', 'woocommerce-correios' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable receipt notice', 'woocommerce-correios' ),
			'description' => __( 'This controls if the sender must receive a receipt notice when a package is delivered.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => 'no',
		);
		$fields['own_hands'] = array(
			'title'       => __( 'Own Hands', 'woocommerce-correios' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable own hands', 'woocommerce-correios' ),
			'description' => __( 'This controls if the package must be delivered exclusively to the recipient printed in its label.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => 'no',
		);
		$fields['declare_value'] = array(
			'title'       => __( 'Declare Value for Insurance', 'woocommerce-correios' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable declared value', 'woocommerce-correios' ),
			'description' => __( 'This controls if the price of the package must be declared for insurance purposes.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => 'yes',
		);
		$fields['service_options'] = array(
			'title'   => __( 'Service Options', 'woocommerce-correios' ),
			'type'    => 'title',
			'default' => '',
		);
		$fields['service_type'] = array(
			'title'       => __( 'Service Type', 'woocommerce-correios' ),
			'type'        => 'select',
			'description' => __( 'Choose between conventional or corporate service.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => 'conventional',
			'class'       => 'wc-enhanced-select',
			'options'     => array(
				'conventional' => __( 'Conventional', 'woocommerce-correios' ),
				'corporate'    => __( 'Corporate', 'woocommerce-correios' ),
			),
		);
		$fields['login'] = array(
			'title'       => __( 'Administrative Code', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'Your Correios login, It\'s usually your CNPJ.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => '',
		);
		$fields['password'] = array(
			'title'       => __( 'Administrative Password', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'Your Correios password.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => '',
		);
		$fields['package_standard'] = array(
			'title'       => __( 'Package Standard', 'woocommerce-correios' ),
			'type'        => 'title',
			'description' => __( 'Minimum measure for your shipping packages.', 'woocommerce-correios' ),
			'default'     => '',
		);
		$fields['minimum_height'] = array(
			'title'       => __( 'Minimum Height', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'Minimum height of your shipping packages. Correios needs at least 2cm.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => '2',
		);
		$fields['minimum_width'] = array(
			'title'       => __( 'Minimum Width', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'Minimum width of your shipping packages. Correios needs at least 11cm.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => '11',
		);
		$fields['minimum_length'] = array(
			'title'       => __( 'Minimum Length', 'woocommerce-correios' ),
			'type'        => 'text',
			'description' => __( 'Minimum length of your shipping packages. Correios needs at least 16cm.', 'woocommerce-correios' ),
			'desc_tip'    => true,
			'default'     => '16',
		);
		$fields['testing'] = array(
			'title'   => __( 'Testing', 'woocommerce-correios' ),
			'type'    => 'title',
			'default' => '',
		);
		$fields['debug'] = array(
			'title'       => __( 'Debug Log', 'woocommerce-correios' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable logging', 'woocommerce-correios' ),
			'default'     => 'no',
			'description' => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), $this->method_title ) . $this->get_log_link(),
		);

		$this->instance_form_fields = $fields;
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
		return 'corporate' === $this->service_type;
	}

	/**
	 * Get login.
	 *
	 * @return string
	 */
	public function get_login() {
		return $this->is_corporate() ? $this->login : '';
	}

	/**
	 * Get password.
	 *
	 * @return string
	 */
	public function get_password() {
		return $this->is_corporate() ? $this->password : '';
	}

	/**
	 * Get minimum height.
	 *
	 * @param  int $value Default value.
	 *
	 * @return int
	 */
	public function get_minimum_height( $value ) {
		$minimum = ( 2 <= $this->minimum_height ) ? $this->minimum_height : 2;

		return ( $minimum <= $value ) ? $value : $minimum;
	}

	/**
	 * Get minimum width.
	 *
	 * @param  int $value Default value.
	 *
	 * @return int
	 */
	public function get_minimum_width( $value ) {
		$minimum = ( 11 <= $this->minimum_height ) ? $this->minimum_height : 11;

		return ( $minimum <= $value ) ? $value : $minimum;
	}

	/**
	 * Get minimum length.
	 *
	 * @param  int $value Default value.
	 *
	 * @return int
	 */
	public function get_minimum_length( $value ) {
		$minimum = ( 16 <= $this->minimum_height ) ? $this->minimum_height : 16;

		return ( $minimum <= $value ) ? $value : $minimum;
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
		$connect->set_service( $code );
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

		return $shipping;
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
		// Check if valid to be calculeted.
		if ( '' === $package['destination']['postcode'] || 'BR' !== $package['destination']['country'] ) {
			return;
		}

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
			$notice      = '<strong>' . __( $this->title, 'woocommerce-correios' ) . ':</strong> ' . esc_html( $error_message );
			wc_add_notice( $notice, $notice_type );
		}

		// Create the rate and apply filters.
		$rate = apply_filters( 'woocommerce_correios_' . $this->id . '_rate', array(
			'id'    => $this->id . $this->instance_id,
			'label' => $label,
			'cost'  => $cost + $fee,
		) );

		// Deprecated filter.
		$rates = apply_filters( 'woocommerce_correios_shipping_methods', array( $rate ), $package );

		// Add rate to WooComemrce.
		$this->add_rate( $rates[0] );
	}
}
