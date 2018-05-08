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
		$this->instance_id        = absint( $instance_id );
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
		$this->origin_state       = $this->get_option( 'origin_state' );
		$this->origin_location    = $this->get_option( 'origin_location' );
		$this->shipping_class_id  = (int) $this->get_option( 'shipping_class_id', '-1' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
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
			'origin_state' => array(
				'title'       => __( 'Origin State', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'The UF of the location your packages are delivered from.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
				'class'       => 'wc-enhanced-select',
				'options'     => WC()->countries->get_states( 'BR' ),
			),
			'origin_location' => array(
				'title'       => __( 'Origin Locale', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'The location of your packages are delivered from.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'C',
				'class'       => 'wc-enhanced-select',
				'options'     => array(
					'C' => __( 'Capital', 'woocommerce-correios' ),
					'I' => __( 'Interior', 'woocommerce-correios' ),
				),
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
	 * Get Correios service code.
	 *
	 * @return string
	 */
	public function get_code() {
		return apply_filters( 'woocommerce_correios_shipping_method_code', $this->code, $this->id, $this->instance_id );
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
		$api->set_destination_country( $package['destination']['country'] );
		$api->set_origin_state( $this->origin_state );
		$api->set_origin_location( $this->origin_location );

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

		if ( empty( $shipping->dados_postais->preco_postal ) ) {
			return;
		}

		// Set the shipping rates.
		$label = $this->title;
		if ( 'yes' === $this->show_delivery_time ) {
			$label .= ' (' . sanitize_text_field( (string) $shipping->dados_postais->prazo_entrega ) . ')';
		}
		$cost = sanitize_text_field( (float) $shipping->dados_postais->preco_postal );

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
