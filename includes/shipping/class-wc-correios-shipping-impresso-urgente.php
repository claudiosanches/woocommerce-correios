<?php
/**
 * Correios Impresso Urgente shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   3.1.0
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Impresso Urgente shipping method class.
 */
class WC_Correios_Shipping_Impresso_Urgente extends WC_Correios_Shipping_Impresso {

	/**
	 * Weight limit for this shipping method.
	 *
	 * Value based in 01/02/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-nacionais/impresso-normal	 
	 *
	 * @var float
	 */
	protected $shipping_method_weight_limit = 500.000;

	/**
	 * Initialize Impresso Urgente.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-impresso-urgente';
		$this->method_title = __( 'Impresso Urgente', 'woocommerce-correios' );
		$this->more_link    = 'https://www.correios.com.br/a-a-z/impresso-normal';

		parent::__construct( $instance_id );
	}

	/**
	 * Get costs.
	 * Costs based in 01/02/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-nacionais/impresso-normal
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters( 'woocommerce_correios_impresso_urgente_costs', array(
			'20'  => 1.55,
			'50'  => 2.15,
			'100' => 2.95,
			'150' => 3.60,
			'200' => 4.20,
			'250' => 4.95,
			'300' => 5.55,
			'350' => 6.20,
			'400' => 6.80,
			'450' => 7.50,
			'500' => 8.10,
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
		$cost = 0;

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, 'Calculating cost for Impresso Urgente' );
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

		$weight += wc_format_decimal($this->extra_weight);

		if ( $weight <= $this->shipping_method_weight_limit ) {
			foreach ( $this->get_costs() as $cost_weights => $costs ) {
				if ( $weight <= $cost_weights ) {
					$cost = $costs;

					if ( 'yes' === $this->own_hands || 'RN' === $this->registry_type ) {
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
