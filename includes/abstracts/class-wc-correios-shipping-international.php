<?php
/**
 * Abstract Correios international shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.0.0
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Correios international shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class WC_Correios_Shipping_International extends WC_Correios_Shipping {

	/**
	 * Initialize the Correios shipping method.
	 *
	 * @param int $instance_id Shipping zone instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->instance_id = absint( $instance_id );

		/* translators: %s: method title */
		$this->method_description = sprintf( __( '%s is a international shipping method from Correios.', 'woocommerce-correios' ), $this->method_title );
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
		$this->declare_value      = $this->get_option( 'declare_value' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
		$this->login              = $this->get_option( 'login' );
		$this->password           = $this->get_option( 'password' );
		$this->minimum_height     = $this->get_option( 'minimum_height' );
		$this->minimum_width      = $this->get_option( 'minimum_width' );
		$this->minimum_length     = $this->get_option( 'minimum_length' );
		$this->extra_weight       = $this->get_option( 'extra_weight', '0' );
		$this->fee                = $this->get_option( 'fee' );
		$this->debug              = $this->get_option( 'debug' );

		// Save admin options.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Admin options fields.
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this shipping method', 'woocommerce-correios' ),
				'default' => 'yes',
			),
			'title'              => array(
				'title'       => __( 'Title', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => $this->method_title,
			),
			'behavior_options'   => array(
				'title'   => __( 'Behavior Options', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'origin_postcode'    => array(
				'title'       => __( 'Origin Postcode', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'The postcode of the location your packages are delivered from.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '00000-000',
				'default'     => $this->get_base_postcode(),
			),
			'shipping_class_id'  => array(
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
			'fee'                => array(
				'title'       => __( 'Handling Fee', 'woocommerce-correios' ),
				'type'        => 'price',
				'description' => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '0.00',
				'default'     => '',
			),
			'optional_services'  => array(
				'title'       => __( 'Optional Services', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Use these options to add the value of each service provided by the Correios.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'declare_value'      => array(
				'title'       => __( 'Declare Value for Insurance', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable declared value', 'woocommerce-correios' ),
				'description' => __( 'This controls if the price of the package must be declared for insurance purposes.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'login'              => array(
				'title'       => __( 'User', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Your Correios login. It\'s usually your idCorreios.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'password'           => array(
				'title'       => __( 'Password', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Your Correios password.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'package_standard'   => array(
				'title'       => __( 'Package Standard', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Minimum measure for your shipping packages.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'minimum_height'     => array(
				'title'       => __( 'Minimum Height (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum height of your shipping packages. Correios needs at least 2cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '2',
			),
			'minimum_width'      => array(
				'title'       => __( 'Minimum Width (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum width of your shipping packages. Correios needs at least 11cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '11',
			),
			'minimum_length'     => array(
				'title'       => __( 'Minimum Length (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum length of your shipping packages. Correios needs at least 16cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '16',
			),
			'extra_weight'       => array(
				'title'       => __( 'Extra Weight (g)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Extra weight in grams to add to the package total when quoting shipping costs.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '0',
			),
			'testing'            => array(
				'title'   => __( 'Testing', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'debug'              => array(
				'title'       => __( 'Debug Log', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-correios' ),
				'default'     => 'no',
				/* translators: %s: method title */
				'description' => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), $this->method_title ) . $this->get_log_link(),
			),
		);
	}

	/**
	 * Get Correios service code.
	 *
	 * @return string
	 */
	public function get_code() {
		return apply_filters( 'woocommerce_correios_shipping_method_code', $this->code, $this->id, $this->instance_id );
	}

	/**
	 * Get login.
	 *
	 * @return string
	 */
	public function get_login() {
		return $this->login;
	}

	/**
	 * Get password.
	 *
	 * @return string
	 */
	public function get_password() {
		return $this->password;
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
	 * @param  array $package Order package.
	 *
	 * @return SimpleXMLElement|null
	 */
	protected function get_rate( $package ) {
		$api = new WC_Correios_Webservice_International( $this->id, $this->instance_id );
		$api->set_debug( $this->debug );
		$api->set_service( $this->get_code() );
		$api->set_package( $package );
		$api->set_origin_postcode( $this->origin_postcode );
		$api->set_destination_country( $package['destination']['country'] );

		if ( 'yes' === $this->declare_value ) {
			$api->set_declared_value( $this->get_declared_value( $package ) );
		}

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
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 */
	public function calculate_shipping( $package = array() ) {
		$api = new WC_Correios_Webservice_International( $this->id, $this->instance_id );

		// Check if valid to be calculeted.
		if ( ! in_array( $package['destination']['country'], $api->get_allowed_countries(), true ) ) {
			return;
		}

		// Check for shipping classes.
		if ( ! $this->has_only_selected_shipping_class( $package ) ) {
			return;
		}

		$shipping = $this->get_rate( $package );

		if ( empty( $shipping->pcFinal ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return;
		}

		// Set the shipping rates.
		$label = $this->title;
		if ( 'yes' === $this->show_delivery_time ) {
			$label .= sprintf(
				' (%s a %s dias úteis)',
				sanitize_text_field( (string) $shipping->prazoMinimo ), // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				sanitize_text_field( (string) $shipping->prazoMaximo ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			);
		}
		$cost = sanitize_text_field( (float) $shipping->pcFinal ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		// Exit if don't have price.
		if ( 0 === intval( $cost ) ) {
			return;
		}

		// Apply fees.
		$fee = $this->get_fee( $this->fee, $cost );

		// Create the rate and apply filters.
		$rate = apply_filters(
			'woocommerce_correios_' . $this->id . '_rate',
			array(
				'id'    => $this->id . $this->instance_id,
				'label' => $label,
				'cost'  => (float) $cost + (float) $fee,
			),
			$this->instance_id,
			$package
		);

		// Deprecated filter.
		$rates = apply_filters( 'woocommerce_correios_shipping_methods', array( $rate ), $package );

		// Add rate to WooCommerce.
		$this->add_rate( $rates[0] );
	}
}
