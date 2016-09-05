<?php
/**
 * Correios Carta Registrada shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Carta Registrada shipping method class.
 */
class WC_Correios_Shipping_Carta_Registrada extends WC_Correios_Shipping {

	/**
	 * Initialize Carta Registrada.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'correios-carta-registrada';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Carta Registrada', 'woocommerce-correios' );
		$this->method_description = sprintf( __( '%s is a shipping method from Correios.', 'woocommerce-correios' ), $this->method_title );
		$this->more_link          = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/carta-comercial';
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Define user set variables.
		$this->enabled        = $this->get_option( 'enabled' );
		$this->title          = $this->get_option( 'title' );
		$this->shipping_class = $this->get_option( 'shipping_class' );
		$this->fee            = $this->get_option( 'fee' );
		$this->receipt_notice = $this->get_option( 'receipt_notice' );
		$this->own_hands      = $this->get_option( 'own_hands' );
		$this->debug          = $this->get_option( 'debug' );

		// Active logs.
		if ( 'yes' == $this->debug ) {
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
	 * Costs based in 14/12/2015 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/carta
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters( 'woocommerce_correios_carta_registrada_costs', array(
			'20'  => array(
				'R'     => '5.40',
				'AR'    => '9.30',
				'MP'    => '10.40',
				'AR+MP' => '14.30',
			),
			'50'  => array(
				'R'     => '6.00',
				'AR'    => '9.90',
				'MP'    => '11.00',
				'AR+MP' => '14.90',
			),
			'100' => array(
				'R'     => '6.85',
				'AR'    => '10.75',
				'MP'    => '11.85',
				'AR+MP' => '15.75',
			),
			'150' => array(
				'R'     => '7.50',
				'AR'    => '11.40',
				'MP'    => '12.50',
				'AR+MP' => '16.40',
			),
			'200' => array(
				'R'     => '8.15',
				'AR'    => '12.05',
				'MP'    => '13.15',
				'AR+MP' => '17.05',
			),
			'250' => array(
				'R'     => '8.80',
				'AR'    => '12.70',
				'MP'    => '13.80',
				'AR+MP' => '17.70',
			),
			'300' => array(
				'R'     => '9.50',
				'AR'    => '13.40',
				'MP'    => '14.50',
				'AR+MP' => '18.40',
			),
			'350' => array(
				'R'     => '10.15',
				'AR'    => '14.05',
				'MP'    => '15.15',
				'AR+MP' => '19.05',
			),
			'400' => array(
				'R'     => '10.80',
				'AR'    => '14.70',
				'MP'    => '15.80',
				'AR+MP' => '19.70',
			),
			'450' => array(
				'R'     => '11.45',
				'AR'    => '15.35',
				'MP'    => '16.45',
				'AR+MP' => '20.35',
			),
			'500' => array(
				'R'     => '12.10',
				'AR'    => '16.00',
				'MP'    => '17.10',
				'AR+MP' => '21.00',
			),
		), $this->id, $this->instance_id );
	}

	/**
	 * Get type of cost.
	 *
	 * @return string
	 */
	protected function get_type_of_cost() {
		if ( 'yes' === $this->receipt_notice && 'yes' === $this->own_hands ) {
			return 'AR+MP';
		} elseif ( 'yes' === $this->receipt_notice ) {
			return 'AR';
		} elseif ( 'yes' === $this->own_hands ) {
			return 'MP';
		} else {
			return 'R';
		}
	}

	/**
	 * Get shipping cost.
	 *
	 * @param  array $package Order package.
	 *
	 * @return float
	 */
	protected function get_shipping_cost( $package ) {
		$type         = $this->get_type_of_cost();
		$cost         = 0;
		$total_weight = 0;

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Calculating cost for Carta Registrada' );
		}

		if ( '' === $this->shipping_class ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error: No shipping class has been configured!' );
			}

			return 0;
		}

		foreach ( $package['contents'] as $value ) {
			$product = $value['data'];
			$qty     = $value['quantity'];
			$weight  = 0;

			// Check if all or some items in the cart don't supports this shipping method.
			if ( $this->shipping_class !== $product->get_shipping_class() ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'One or all items in the cart do not supports the configured shipping class' );
				}

				return 0;
			}

			if ( $qty > 0 && $product->needs_shipping() ) {
				$weight = wc_get_weight( str_replace( ',', '.', $product->weight ), 'g' );

				if ( $qty > 1 ) {
					$weight *= $qty;
				}
			}

			$total_weight += $weight;
		}

		foreach ( $this->get_costs() as $cost_weight => $costs ) {
			if ( $total_weight <= $cost_weight ) {
				$cost = $costs[ $type ];
				break;
			}
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, sprintf( 'Total cost for %sg%s: %s', $total_weight, 'R' !== $type ? ' and ' . $type : '', $cost ) );
		}

		return $cost;
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
			'label' => $this->title,
			'cost'  => $cost + $fee,
		), $this->instance_id );

		// Add rate to WooCommerce.
		$this->add_rate( $rate );
	}
}
