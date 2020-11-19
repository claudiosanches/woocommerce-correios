<?php
/**
 * Correios Impresso Normal shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.1.0
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Impresso Normal shipping method class.
 */
class WC_Correios_Shipping_Impresso_Normal extends WC_Correios_Shipping_Impresso {

	/**
	 * Additional cost per kg or fraction.
	 *
	 * Cost based in 05/02/2020 from:
	 * https://www.correios.com.br/enviar-e-receber/marketing-direto/impressos/impresso-normal
	 *
	 * @var float
	 */
	protected $additional_cost_per_kg = 4.85;

	/**
	 * Weight limit for this shipping method.
	 *
	 * Value based in 19/11/2020 from:
	 * https://www.correios.com.br/enviar-e-receber/marketing-direto/impressos/impresso-normal
	 *
	 * @var float
	 */
	protected $shipping_method_weight_limit = 10000.000;

	/**
	 * Initialize Impresso Normal.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-impresso-normal';
		$this->method_title = __( 'Impresso Normal', 'woocommerce-correios' );
		$this->more_link    = 'https://www.correios.com.br/a-a-z/impresso-normal';

		parent::__construct( $instance_id );
	}

	/**
	 * Get additional costs per kg or fraction.
	 *
	 * Cost based in 01/02/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-nacionais/impresso-normal
	 *
	 * @return float
	 */
	protected function get_additional_costs_per_kg() {
		return apply_filters( 'woocommerce_correios_impresso_additional_cost_per_kg',
			$this->additional_cost_per_kg, $this->id, $this->instance_id );
	}

	/**
	 * Get costs.
	 * Costs based in 01/02/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-nacionais/impresso-normal
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters( 'woocommerce_correios_impresso_normal_costs', array(
			'20'  => 1.30,
			'50'  => 1.95,
			'100' => 2.50,
			'150' => 3.05,
			'200' => 3.65,
			'250' => 4.20,
			'300' => 4.75,
			'350' => 5.25,
			'400' => 5.90,
			'450' => 6.50,
			'500' => 7.10,
			'550' => 7.50,
			'600' => 8.10,
			'650' => 8.60,
			'700' => 9.00,
			'750' => 9.50,
			'800' => 9.95,
			'850' => 10.55,
			'900' => 11.15,
			'950' => 11.60,
			'1000' => 12.05,
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
		$additional_weight_kgs = 0;
		$additional_weight_gs  = 0;

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, 'Calculating cost for Impresso Normal' );
		}

		if ( '' === $this->shipping_class ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'Error: No shipping class has been configured!' );
			}

			return 0;
		}

		// Get the package weight, validate and add extra weight.
		$weight = $this->get_package_weight( $package );
		if ( false === $weight ) {
			return 0;
		}

		$weight += wc_format_decimal( $this->extra_weight );

		if ( $weight <= $this->shipping_method_weight_limit ) {
			if ( $weight > 10000 ) {
				return 0;
			} elseif ( $weight > 1000 ) {
				// Get the additional kgs over 1 kg.
				$additional_weight_kgs = intval( $weight / 1000 );

				// To ensure it will get the maximum price for 1 kg.
				$additional_weight_gs = 1000;
			} else {
				// Get the cart weight itself.
				$additional_weight_gs = $weight;
			}

			$additional_costs_per_kg = $this->get_additional_costs_per_kg();

			foreach ( $this->get_costs() as $cost_weights => $costs ) {
				if ( $additional_weight_gs <= $cost_weights ) {
					$cost = $costs;

					if ( $additional_weight_kgs > 0 ) {
						$cost += $additional_weight_kgs * $additional_costs_per_kg;
					}

					if ( $weight > $this->reasonable_registry_weight_limit || 'yes' === $this->own_hands || 'RN' === $this->registry_type ) {
						$cost += $this->national_registry_cost;
					} else {
						$cost += $this->reasonable_registry_cost;
					}

					if ( 'yes' === $this->receipt_notice ) {
						$cost += $this->receipt_notice_cost;
					}

					if ( 'yes' === $this->own_hands ) {
						$cost += $this->own_hands_cost;
					}

					break;
				}
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, sprintf( 'Total cost for %sg and %s: %s', $weight, $this->registry_type, $cost ) );
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, sprintf( 'The cart weight of %.3f exceeds the shipping method supported weight limit of %.3f', $weight, $this->shipping_method_weight_limit ) );
			}
		}

		return $cost;
	}
}
