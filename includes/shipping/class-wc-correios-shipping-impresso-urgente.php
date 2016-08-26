<?php
/**
 * Correios Impresso Urgente shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Impresso Urgente shipping method class.
 */
class WC_Correios_Shipping_Impresso_Urgente extends WC_Correios_Shipping {
	/**
	 * National Registry cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const NATIONAL_REGISTRY_COST = '4.30';

	/**
	 * Reasonable Registry cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const REASONABLE_REGISTRY_COST = '2.15';

	/**
	 * Receipt Notice cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const RECEIPT_NOTICE_COST = 4.30;

	/**
	 * Own Hands cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const OWN_HANDS_COST = 5.50;

	/**
	 * Weight limit for this shipping method.
	 * Value based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
	 *
	 */
	const SHIPPING_METHOD_WEIGHT_LIMIT = '500.000';

	/**
	 * Initialize Impresso Urgente.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'correios-impresso-urgente';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Impresso Urgente', 'woocommerce-correios' );
		$this->method_description = sprintf( __( '%s is a shipping method from Correios.', 'woocommerce-correios' ), $this->method_title );
		$this->more_link          = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/impresso-normal';
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
		$this->registry_type  = $this->get_option( 'registry_type' );
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
	 * Get registry type options.
	 *
	 * @return array
	 */
	protected function get_registry_types_options() {
		$options          = array(
			''   => __( '-- Select a registry type --', 'woocommerce-correios' ),
			'RN' => __( 'Registro Nacional', 'woocommerce-correios' ),
			'RM' => __( 'Registro Módico', 'woocommerce-correios' ),
		);

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
			'registry_type' => array(
				'title'       => __( 'Registry Type', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Select for which registry type this method will be applied.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
				'class'       => 'wc-enhanced-select',
				'options'     => $this->get_registry_types_options(),
				'default'     => 'RM',
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
	 * Costs based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters( 'woocommerce_correios_impresso_urgente_costs', array(
			'0-20' => array (
				'MIN'  => 0.000,
				'MAX'  => 20.000,
				'COST' => 1.40,
			),
			'21-50' => array (
				'MIN'  => 20.001,
				'MAX'  => 50.000,
				'COST' => 1.95,
			),
			'51-100' => array (
				'MIN'  => 50.001,
				'MAX'  => 100.000,
				'COST' => 2.60,
				),
			'101-150' => array (
				'MIN'  => 100.001,
				'MAX'  => 150.000,
				'COST' => 3.20,
			),
			'151-200' => array (
				'MIN'  => 150.001,
				'MAX'  => 200.000,
				'COST' => 3.75,
			),
			'201-250' => array (
				'MIN'  => 200.001,
				'MAX'  => 250.000,
				'COST' => 4.40,
			),
			'251-300' => array (
				'MIN'  => 250.001,
				'MAX'  => 300.000,
				'COST' => 4.95,
			),
			'301-350' => array (
				'MIN'  => 300.001,
				'MAX'  => 350.000,
				'COST' => 5.55,
			),
			'351-400' => array (
				'MIN'  => 350.001,
				'MAX'  => 400.000,
				'COST' => 6.05,
			),
			'401-450' => array (
				'MIN'  => 400.001,
				'MAX'  => 450.000,
				'COST' => 6.70,
			),
			'451-500' => array (
				'MIN'  => 450.001,
				'MAX'  => 500.000,
				'COST' => 7.25,
			),
		), $this->id, $this->instance_id );
	}

	/**
	 * Get shipping cost.
	 *
	 * @param  array $package Order package.
	 *
	 * @return float
	 */
	protected function get_shipping_cost( $package ) {
		$cost                  = 0;
		$total_weight          = 0;

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Calculating cost for Impresso Urgente' );
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

		if ( $total_weight <= self::SHIPPING_METHOD_WEIGHT_LIMIT ) {
			foreach ( $this->get_costs() as $cost_weights => $costs ) {
				if ( $total_weight >= $costs[ 'MIN' ] && $total_weight <= $costs[ 'MAX' ] ) {
					$cost = $costs[ 'COST' ];

					if ( 'yes' === $this->own_hands || 'RN' === $this->registry_type ) {
						$cost += self::NATIONAL_REGISTRY_COST;
					} else {
						$cost += self::REASONABLE_REGISTRY_COST;
					}

					if ( 'yes' === $this->receipt_notice ) {
						$cost += self::RECEIPT_NOTICE_COST;
					}

					if ( 'yes' === $this->own_hands ) {
						$cost += self::OWN_HANDS_COST;
					}

					break;
				}
			}

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, sprintf( 'Total cost for %sg and %s: %s', $total_weight, $this->registry_type, $cost ) );
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, sprintf( 'The cart weight of %.3f exceeds the shipping method supported weight limit of %.3f', $total_weight, self::SHIPPING_METHOD_WEIGHT_LIMIT ) );
			}
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
