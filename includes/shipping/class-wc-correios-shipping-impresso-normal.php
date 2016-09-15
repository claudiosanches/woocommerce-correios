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
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
	 *
	 * @var float
	 */
	protected $additional_cost_per_kg = 3.70;

	/**
	 * Weight limit for this shipping method.
	 *
	 * Value based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
	 *
	 * @var float
	 */
	protected $shipping_method_weight_limit = 20000.000;

	/**
	 * Initialize Impresso Normal.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-impresso-normal';
		$this->method_title = __( 'Impresso Normal', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/impresso-normal';

		parent::__construct( $instance_id );
	}

	/**
	 * Get costs.
	 * Costs based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters( 'woocommerce_correios_impresso_normal_costs', array(
			'20'  => 0.95,
			'50'  => 1.45,
			'100' => 1.95,
			'150' => 2.35,
			'200' => 2.75,
			'250' => 3.20,
			'300' => 3.65,
			'350' => 4.05,
			'400' => 4.50,
			'450' => 4.95,
			'500' => 5.40,
			'550' => 5.75,
			'600' => 6.15,
			'650' => 6.55,
			'700' => 6.90,
			'750' => 7.25,
			'800' => 7.65,
			'850' => 8.05,
			'900' => 8.50,
			'950' => 8.85,
			'1000' => 9.25,
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

		// Get the package weight and validate.
		$weight = $this->get_package_weight( $package );
		if ( false === $weight ) {
			return 0;
		}

		if ( $weight <= $this->shipping_method_weight_limit ) {
			if ( $weight > 1000 ) {
				// Get the additional kgs over 1 kg.
				$additional_weight_kgs = intval( $weight / 1000 );

				// To ensure it will get the maximum price for 1 kg.
				$additional_weight_gs = 1000;
			} else {
				// Get the cart weight itself.
				$additional_weight_gs = $weight;
			}

			foreach ( $this->get_costs() as $cost_weights => $costs ) {
				if ( $additional_weight_gs <= $cost_weights ) {
					$cost = $costs;

					if ( $additional_weight_kgs > 0 ) {
						$cost += $additional_weight_kgs * $this->additional_cost_per_kg;
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
