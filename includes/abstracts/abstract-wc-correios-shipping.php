<?php
/**
 * Abstract Correios shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.0.0
 * @version 3.2.0
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
	 * Service code.
	 *
	 * @var string
	 */
	protected $code = '';

	/**
	 * Corporate code.
	 *
	 * @var string
	 */
	protected $corporate_code = '';

	/**
	 * Initialize the Correios shipping method.
	 *
	 * @param int $instance_id Shipping zone instance ID.
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
		$this->shipping_class_id  = (int) $this->get_option( 'shipping_class_id', '-1' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->fee                = $this->get_option( 'fee' );
		$this->receipt_notice     = $this->get_option( 'receipt_notice' );
		$this->own_hands          = $this->get_option( 'own_hands' );
		$this->declare_value      = $this->get_option( 'declare_value' );
		$this->custom_code        = $this->get_option( 'custom_code' );
		$this->service_type       = $this->get_option( 'service_type' );
		$this->login              = $this->get_option( 'login' );
		$this->password           = $this->get_option( 'password' );
		$this->minimum_height     = $this->get_option( 'minimum_height' );
		$this->minimum_width      = $this->get_option( 'minimum_width' );
		$this->minimum_length     = $this->get_option( 'minimum_length' );
		$this->extra_weight       = $this->get_option( 'extra_weight', '0' );
		$this->debug              = $this->get_option( 'debug' );

		// Save admin options.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_link() {
		return ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'View logs.', 'woocommerce-correios' ) . '</a>';
	}

	/**
	 * Get base postcode.
	 *
	 * @since  3.5.1
	 * @return string
	 */
	protected function get_base_postcode() {
		// WooCommerce 3.1.1+.
		if ( method_exists( WC()->countries, 'get_base_postcode' ) ) {
			return WC()->countries->get_base_postcode();
		}

		return '';
	}

	/**
	 * Get shipping classes options.
	 *
	 * @return array
	 */
	protected function get_shipping_classes_options() {
		$shipping_classes = WC()->shipping->get_shipping_classes();
		$options          = array(
			'-1' => __( 'Any Shipping Class', 'woocommerce-correios' ),
			'0'  => __( 'No Shipping Class', 'woocommerce-correios' ),
		);

		if ( ! empty( $shipping_classes ) ) {
			$options += wp_list_pluck( $shipping_classes, 'name', 'term_id' );
		}

		return $options;
	}

	/**
	 * Admin options fields.
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this shipping method', 'woocommerce-correios' ),
				'default' => 'yes',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => $this->method_title,
			),
			'behavior_options' => array(
				'title'   => __( 'Behavior Options', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'origin_postcode' => array(
				'title'       => __( 'Origin Postcode', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'The postcode of the location your packages are delivered from.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '00000-000',
				'default'     => $this->get_base_postcode(),
			),
			'shipping_class_id' => array(
				'title'       => __( 'Shipping Class', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'If necessary, select a shipping class to apply this method.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
				'class'       => 'wc-enhanced-select',
				'options'     => $this->get_shipping_classes_options(),
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
				'type'        => 'price',
				'description' => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '0.00',
				'default'     => '',
			),
			'optional_services' => array(
				'title'       => __( 'Optional Services', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Use these options to add the value of each service provided by the Correios.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'receipt_notice' => array(
				'title'       => __( 'Receipt Notice', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable receipt notice', 'woocommerce-correios' ),
				'description' => __( 'This controls whether to add costs of the receipt notice service.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'own_hands' => array(
				'title'       => __( 'Own Hands', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable own hands', 'woocommerce-correios' ),
				'description' => __( 'This controls whether to add costs of the own hands service', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'declare_value' => array(
				'title'       => __( 'Declare Value for Insurance', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable declared value', 'woocommerce-correios' ),
				'description' => __( 'This controls if the price of the package must be declared for insurance purposes.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'service_options' => array(
				'title'   => __( 'Service Options', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'custom_code' => array(
				'title'       => __( 'Service Code', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Service code, use this for custom codes.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => $this->code,
				'default'     => '',
			),
			'service_type' => array(
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
			),
			'login' => array(
				'title'       => __( 'Administrative Code', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Your Correios login. It\'s usually your CNPJ.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'password' => array(
				'title'       => __( 'Administrative Password', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Your Correios password.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'package_standard' => array(
				'title'       => __( 'Package Standard', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Minimum measure for your shipping packages.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'minimum_height' => array(
				'title'       => __( 'Minimum Height (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum height of your shipping packages. Correios needs at least 2cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '2',
			),
			'minimum_width' => array(
				'title'       => __( 'Minimum Width (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum width of your shipping packages. Correios needs at least 11cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '11',
			),
			'minimum_length' => array(
				'title'       => __( 'Minimum Length (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum length of your shipping packages. Correios needs at least 16cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '16',
			),
			'extra_weight' => array(
				'title'       => __( 'Extra Weight (kg)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Extra weight in kilograms to add to the package total when quoting shipping costs.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '0',
			),
			'testing' => array(
				'title'   => __( 'Testing', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-correios' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), $this->method_title ) . $this->get_log_link(),
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
	 * Validate price field.
	 *
	 * Make sure the data is escaped correctly, etc.
	 * Includes "%" back.
	 *
	 * @param  string $key   Field key.
	 * @param  string $value Posted value/
	 * @return string
	 */
	public function validate_price_field( $key, $value ) {
		$value     = is_null( $value ) ? '' : $value;
		$new_value = '' === $value ? '' : wc_format_decimal( trim( stripslashes( $value ) ) );

		if ( '%' === substr( $value, -1 ) ) {
			$new_value .= '%';
		}

		return $new_value;
	}

	/**
	 * Get Correios service code.
	 *
	 * @return string
	 */
	public function get_code() {
		if ( ! empty( $this->custom_code ) ) {
			$code = $this->custom_code;
		} elseif ( $this->is_corporate() && ! empty( $this->corporate_code ) ) {
			$code = $this->corporate_code;
		} else {
			$code = $this->code;
		}

		return apply_filters( 'woocommerce_correios_shipping_method_code', $code, $this->id, $this->instance_id );
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
	 * Get the declared value from the package.
	 *
	 * @param  array $package Cart package.
	 *
	 * @return float
	 */
	protected function get_declared_value( $package ) {
		return $package['contents_cost'];
	}

	/**
	 * Get shipping rate.
	 *
	 * @param  array $package Cart package.
	 *
	 * @return SimpleXMLElement|null
	 */
	protected function get_rate( $package ) {
		$api = new WC_Correios_Webservice( $this->id, $this->instance_id );
		$api->set_debug( $this->debug );
		$api->set_service( $this->get_code() );
		$api->set_package( $package );
		$api->set_origin_postcode( $this->origin_postcode );
		$api->set_destination_postcode( $package['destination']['postcode'] );

		if ( 'yes' === $this->declare_value ) {
			$api->set_declared_value( $this->get_declared_value( $package ) );
		}

		$api->set_own_hands( 'yes' === $this->own_hands ? 'S' : 'N' );
		$api->set_receipt_notice( 'yes' === $this->receipt_notice ? 'S' : 'N' );

		$api->set_login( $this->get_login() );
		$api->set_password( $this->get_password() );

		$api->set_minimum_height( $this->minimum_height );
		$api->set_minimum_width( $this->minimum_width );
		$api->set_minimum_length( $this->minimum_length );
		$api->set_extra_weight( $this->extra_weight );

		$shipping = $api->get_shipping();

		return $shipping;
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
	 * Get shipping method label.
	 *
	 * @param  int   $days Days to deliver.
	 * @param  array $package Package data.
	 *
	 * @return string
	 */
	protected function get_shipping_method_label( $days, $package ) {
		if ( 'yes' === $this->show_delivery_time ) {
			return wc_correios_get_estimating_delivery( $this->title, $days, $this->get_additional_time( $package ) );
		}

		return $this->title;
	}

	/**
	 * Check if package uses only the selected shipping class.
	 *
	 * @param  array $package Cart package.
	 * @return bool
	 */
	protected function has_only_selected_shipping_class( $package ) {
		$only_selected = true;

		if ( -1 === $this->shipping_class_id ) {
			return $only_selected;
		}

		foreach ( $package['contents'] as $item_id => $values ) {
			$product = $values['data'];
			$qty     = $values['quantity'];

			if ( $qty > 0 && $product->needs_shipping() ) {
				if ( $this->shipping_class_id !== $product->get_shipping_class_id() ) {
					$only_selected = false;
					break;
				}
			}
		}

		return $only_selected;
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

		// Check for shipping classes.
		if ( ! $this->has_only_selected_shipping_class( $package ) ) {
			return;
		}

		$shipping = $this->get_rate( $package );

		if ( ! isset( $shipping->Erro ) ) {
			return;
		}

		$error_number = (string) $shipping->Erro;

		// Exit if have errors.
		if ( ! in_array( $error_number, $this->get_accepted_error_codes(), true ) ) {
			return;
		}

		// Display Correios errors.
		$error_message = wc_correios_get_error_message( $error_number );
		if ( '' !== $error_message && is_cart() ) {
			$notice_type = ( '010' === $error_number ) ? 'notice' : 'error';
			$notice      = '<strong>' . $this->title . ':</strong> ' . esc_html( $error_message );
			wc_add_notice( $notice, $notice_type );
		}

		// Set the shipping rates.
		$label = $this->get_shipping_method_label( (int) $shipping->PrazoEntrega, $package );
		$cost  = wc_correios_normalize_price( esc_attr( (string) $shipping->Valor ) );

		// Exit if don't have price.
		if ( 0 === intval( $cost ) ) {
			return;
		}

		// Apply fees.
		$fee = $this->get_fee( $this->fee, $cost );

		// Create the rate and apply filters.
		$rate = apply_filters( 'woocommerce_correios_' . $this->id . '_rate', array(
			'id'    => $this->id . $this->instance_id,
			'label' => $label,
			'cost'  => (float) $cost + (float) $fee,
		), $this->instance_id, $package );

		// Deprecated filter.
		$rates = apply_filters( 'woocommerce_correios_shipping_methods', array( $rate ), $package );

		// Add rate to WooCommerce.
		$this->add_rate( $rates[0] );
	}
}
