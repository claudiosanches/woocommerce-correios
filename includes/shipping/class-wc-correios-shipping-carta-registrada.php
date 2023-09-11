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
		$this->id           = 'correios-carta-registrada';
		$this->method_title = __( 'Carta Registrada', 'woocommerce-correios' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/carta-comercial';

		parent::__construct( $instance_id );
	}

	/**
	 * Get costs.
	 * Costs based in 13/07/2020 from:
	 * http://www.correios.com.br/enviar-e-receber/correspondencia/carta/precos-e-prazos/carta
	 *
	 * @return array
	 */
	protected function get_costs() {
		return apply_filters(
			'woocommerce_correios_carta_registrada_costs',
			array(
				'20'  => array(
					'R'     => 8.40,
					'AR'    => 14.75,
					'MP'    => 15.90,
					'AR+MP' => 22.25,
				),
				'50'  => array(
					'R'     => 9.20,
					'AR'    => 15.55,
					'MP'    => 16.70,
					'AR+MP' => 23.05,
				),
				'100' => array(
					'R'     => 10.30,
					'AR'    => 16.65,
					'MP'    => 17.80,
					'AR+MP' => 24.15,
				),
				'150' => array(
					'R'     => 11.15,
					'AR'    => 17.50,
					'MP'    => 18.65,
					'AR+MP' => 25.00,
				),
				'200' => array(
					'R'     => 12.00,
					'AR'    => 18.35,
					'MP'    => 19.50,
					'AR+MP' => 25.85,
				),
				'250' => array(
					'R'     => 12.90,
					'AR'    => 19.25,
					'MP'    => 20.40,
					'AR+MP' => 26.75,
				),
				'300' => array(
					'R'     => 13.85,
					'AR'    => 20.20,
					'MP'    => 21.35,
					'AR+MP' => 27.70,
				),
				'350' => array(
					'R'     => 14.70,
					'AR'    => 21.05,
					'MP'    => 22.20,
					'AR+MP' => 28.55,
				),
				'400' => array(
					'R'     => 15.60,
					'AR'    => 21.95,
					'MP'    => 23.10,
					'AR+MP' => 29.45,
				),
				'450' => array(
					'R'     => 16.45,
					'AR'    => 22.80,
					'MP'    => 23.95,
					'AR+MP' => 30.30,
				),
				'500' => array(
					'R'     => 17.35,
					'AR'    => 23.70,
					'MP'    => 24.85,
					'AR+MP' => 31.20,
				),
			),
			$this->id,
			$this->instance_id
		);
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

		$weight += wc_format_decimal( $this->extra_weight );

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
