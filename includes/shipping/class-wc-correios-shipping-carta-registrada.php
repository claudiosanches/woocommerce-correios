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
class WC_Correios_Shipping_Carta_Registrada extends WC_Correios_Shipping_Carta {

	/**
	 * Initialize Carta Registrada.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'correios-carta-registrada';
		$this->method_title       = __( 'Carta Registrada', 'woocommerce-correios' );
		$this->more_link          = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/carta-comercial';

		parent::__construct( $instance_id );
	}

	/**
	 * Get costs.
	 * Costs based in 31/10/2017 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/carta
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters( 'woocommerce_correios_carta_registrada_costs', array(
			'20'  => array(
				'R'     => 6.85,
				'AR'    => 11.85,
				'MP'    => 12.75,
				'AR+MP' => 17.75,
			),
			'50'  => array(
				'R'     => 7.55,
				'AR'    => 12.55,
				'MP'    => 13.45,
				'AR+MP' => 18.45,
			),
			'100' => array(
				'R'     => 8.55,
				'AR'    => 13.55,
				'MP'    => 14.45,
				'AR+MP' => 19.45,
			),
			'150' => array(
				'R'     => 9.35,
				'AR'    => 14.35,
				'MP'    => 15.25,
				'AR+MP' => 20.25,
			),
			'200' => array(
				'R'     => 10.10,
				'AR'    => 15.10,
				'MP'    => 16.00,
				'AR+MP' => 21.00,
			),
			'250' => array(
				'R'     => 10.90,
				'AR'    => 15.90,
				'MP'    => 16.80,
				'AR+MP' => 21.80,
			),
			'300' => array(
				'R'     => 11.75,
				'AR'    => 16.75,
				'MP'    => 17.65,
				'AR+MP' => 22.65,
			),
			'350' => array(
				'R'     => 12.55,
				'AR'    => 17.55,
				'MP'    => 18.45,
				'AR+MP' => 23.45,
			),
			'400' => array(
				'R'     => 13.30,
				'AR'    => 18.30,
				'MP'    => 19.20,
				'AR+MP' => 24.20,
			),
			'450' => array(
				'R'     => 14.10,
				'AR'    => 19.10,
				'MP'    => 20.00,
				'AR+MP' => 25.00,
			),
			'500' => array(
				'R'     => 14.90,
				'AR'    => 19.90,
				'MP'    => 20.80,
				'AR+MP' => 25.80,
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
		$type = $this->get_type_of_cost();
		$cost = 0;

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, 'Calculating cost for Carta Registrada' );
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

		$weight += wc_format_decimal($this->extra_weight);

		foreach ( $this->get_costs() as $cost_weight => $costs ) {
			if ( $weight <= $cost_weight ) {
				$cost = $costs[ $type ];
				break;
			}
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, sprintf( 'Total cost for %sg%s: %s', $weight, 'R' !== $type ? ' and ' . $type : '', $cost ) );
		}

		return $cost;
	}
}
