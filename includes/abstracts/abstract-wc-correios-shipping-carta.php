<?php
/**
 * Abstract Correios Carta shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.1.0
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Correios Carta shipping method abstract class.
 */
abstract class WC_Correios_Shipping_Carta extends WC_Correios_Shipping {

	/**
	 * Initialize Carta Registrada.
	 *
	 * @param int $instance_id Shipping zone instance.
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
		$this->shipping_class     = $this->get_option( 'shipping_class' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->fee                = $this->get_option( 'fee' );
		$this->receipt_notice     = $this->get_option( 'receipt_notice' );
		$this->own_hands          = $this->get_option( 'own_hands' );
		$this->debug              = $this->get_option( 'debug' );

		// Active logs.
		if ( 'yes' === $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Save admin options.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Get shipping classes options.
	 *
	 * @return array
	 */
	protected function get_shipping_classes_options() {
		$shipping_classes = WC()->shipping->get_shipping_classes();
		$options          = array(
			'' => __( '-- Select a shipping class --', 'woocommerce-correios' ),
		);

		if ( ! empty( $shipping_classes ) ) {
			$options += wp_list_pluck( $shipping_classes, 'name', 'slug' );
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
			'shipping_class' => array(
				'title'       => __( 'Shipping Class', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Select for which shipping class this method will be applied.', 'woocommerce-correios' ),
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
				'title'       => __( 'Delivery Days', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Working days to the estimated delivery.', 'woocommerce-correios' ),
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
	 * Get costs.
	 *
	 * @return array
	 */
	protected function get_costs() {
		return array();
	}

	/**
	 * Get shpping cost.
	 *
	 * @param  array $package Shipping package.
	 * @return float
	 */
	protected function get_shipping_cost( $package ) {
		return 0;
	}

	/**
	 * Get shpping time.
	 *
	 * @param  array $package Shipping package.
	 * @return int
	 */
	protected function get_shipping_time( $package ) {
		return 0;
	}

	/**
	 * Get package weight.
	 *
	 * @param  array $package Shipping package.
	 *
	 * @return float|bool
	 */
	protected function get_package_weight( $package ) {
		$weight = 0;

		foreach ( $package['contents'] as $value ) {
			$product        = $value['data'];
			$qty            = $value['quantity'];
			$product_weight = 0;

			// Check if all or some items in the cart don't supports this shipping method.
			if ( $this->shipping_class !== $product->get_shipping_class() ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( $this->id, 'One or all items in the cart do not supports the configured shipping class' );
				}

				return false;
			}

			if ( $qty > 0 && $product->needs_shipping() ) {
				$product_weight = wc_get_weight( (float) $product->get_weight(), 'g' );

				if ( $qty > 1 ) {
					$product_weight *= $qty;
				}
			}

			$weight += $product_weight;
		}

		return $weight;
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

		// Cost.
		$cost = $this->get_shipping_cost( $package );

		if ( 0 === $cost ) {
			return;
		}

		// Apply fees.
		$fee = $this->get_fee( $this->fee, $cost );

		// Create the rate and apply filters.
		$rate = apply_filters( 'woocommerce_correios_' . $this->id . '_rate', array(
			'id'    => $this->id . $this->instance_id,
			'label' => $this->get_shipping_method_label( (int) $this->get_shipping_time( $package ), $package ),
			'cost'  => (float) $cost + (float) $fee,
		), $this->instance_id, $package );

		// Add rate to WooCommerce.
		$this->add_rate( $rate );
	}
}
