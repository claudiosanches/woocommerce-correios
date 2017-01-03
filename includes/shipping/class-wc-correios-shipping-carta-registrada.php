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
	 * Costs based in 28/06/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/carta
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters( 'woocommerce_correios_carta_registrada_costs', array(
			'20'  => array(
				'R'     => 6.00,
				'AR'    => 10.30,
				'MP'    => 11.50,
				'AR+MP' => 15.80,
			),
			'50'  => array(
				'R'     => 6.65,
				'AR'    => 10.95,
				'MP'    => 12.15,
				'AR+MP' => 16.45,
			),
			'100' => array(
				'R'     => 7.55,
				'AR'    => 11.85,
				'MP'    => 13.05,
				'AR+MP' => 17.35,
			),
			'150' => array(
				'R'     => 8.30,
				'AR'    => 12.60,
				'MP'    => 13.80,
				'AR+MP' => 18.10,
			),
			'200' => array(
				'R'     => 9.00,
				'AR'    => 13.30,
				'MP'    => 14.50,
				'AR+MP' => 18.80,
			),
			'250' => array(
				'R'     => 9.70,
				'AR'    => 14.00,
				'MP'    => 15.20,
				'AR+MP' => 19.50,
			),
			'300' => array(
				'R'     => 10.50,
				'AR'    => 14.80,
				'MP'    => 16.00,
				'AR+MP' => 20.30,
			),
			'350' => array(
				'R'     => 11.25,
				'AR'    => 15.55,
				'MP'    => 16.75,
				'AR+MP' => 21.05,
			),
			'400' => array(
				'R'     => 11.95,
				'AR'    => 16.25,
				'MP'    => 17.45,
				'AR+MP' => 21.75,
			),
			'450' => array(
				'R'     => 12.65,
				'AR'    => 16.95,
				'MP'    => 18.15,
				'AR+MP' => 22.45,
			),
			'500' => array(
				'R'     => 13.40,
				'AR'    => 17.70,
				'MP'    => 18.90,
				'AR+MP' => 23.20,
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
