<?php
/**
 * Correios Web Services API.
 *
 * @package WooCommerce_Correios/Classes/Webservice
 * @since   4.0.0
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Web Services class, integrates with the new Correios API.
 */
class WC_Correios_Cws_Calculate extends WC_Correios_Webservice {

	/**
	 * CWS Product code.
	 *
	 * @var string
	 */
	protected $product_code = '';

	/**
	 * Declared value code.
	 *
	 * @var string
	 */
	protected $declared_value_code = '';

	/**
	 * Set the CWS product code.
	 *
	 * @param string|array $code Product code.
	 */
	public function set_product_code( $code = '' ) {
		$this->product_code = $code;
	}

	/**
	 * Set declared value code.
	 *
	 * @param string $code Code.
	 */
	public function set_declared_value_code( $code ) {
		$this->declared_value_code = $code;
	}

	/**
	 * Get weight.
	 *
	 * @return float
	 */
	public function get_weight() {
		return $this->float_to_string( wc_get_weight( $this->weight + $this->extra_weight, 'g', 'kg' ) );
	}

	/**
	 * Get receipt notice.
	 *
	 * @return bool
	 */
	public function get_receipt_notice() {
		return 'S' === $this->receipt_notice;
	}

	/**
	 * Get own hands.
	 *
	 * @return bool
	 */
	public function get_own_hands() {
		return 'S' === $this->own_hands;
	}

	/**
	 * Get declared value.
	 *
	 * @return bool
	 */
	public function get_declared_value() {
		return $this->declared_value;
	}

	/**
	 * Get product code.
	 *
	 * @return string
	 */
	public function get_product_code() {
		return $this->product_code;
	}

	/**
	 * Get WooCommerce shipping package.
	 *
	 * @return array
	 */
	public function get_package() {
		return $this->package;
	}

	/**
	 * Get height.
	 *
	 * @return float
	 */
	public function get_height() {
		return $this->minimum_height <= $this->height ? $this->height : $this->minimum_height;
	}

	/**
	 * Get width.
	 *
	 * @return float
	 */
	public function get_width() {
		return $this->minimum_width <= $this->width ? $this->width : $this->minimum_width;
	}

	/**
	 * Get diameter.
	 *
	 * @return float
	 */
	public function get_diameter() {
		return $this->diameter;
	}

	/**
	 * Get length.
	 *
	 * @return float
	 */
	public function get_length() {
		return $this->minimum_length <= $this->length ? $this->length : $this->minimum_length;
	}

	/**
	 * Get declared value code.
	 *
	 * @return string
	 */
	public function get_declared_value_code() {
		return $this->declared_value_code;
	}

	/**
	 * Check if is available.
	 *
	 * @return bool
	 */
	protected function is_available() {
		$origin_postcode = $this->get_origin_postcode();

		return ! empty( $this->product_code ) && ! empty( $this->destination_postcode ) && ! empty( $origin_postcode );
	}

	/**
	 * Get shipping prices.
	 *
	 * @return array
	 */
	public function get_shipping() {
		// Checks if product code and postcode are empty.
		if ( ! $this->is_available() ) {
			return array();
		}

		$args = array(
			'cepDestino'         => wc_correios_sanitize_postcode( $this->destination_postcode ),
			'cepOrigem'          => wc_correios_sanitize_postcode( $this->get_origin_postcode() ),
			'psObjeto'           => $this->get_weight(),
			'tpObjeto'           => '2', // Defaul to package.
			'comprimento'        => $this->get_length(),
			'largura'            => $this->get_width(),
			'altura'             => $this->get_height(),
			'servicosAdicionais' => array(),
		);

		// Set receipt notice, optional, and doesn't works with Carta Registrada.
		if ( $this->get_receipt_notice() ) {
			$args['servicosAdicionais'][] = '001';
		}

		// Set own hands, optional, and doesn't work with Correios Mini Envios.
		if ( $this->get_own_hands() ) {
			$args['servicosAdicionais'][] = '002';
		}

		// Set declared value.
		if ( $this->get_declared_value() ) {
			$args['servicosAdicionais'][] = $this->get_declared_value_code();
			$args['vlDeclarado']          = $this->get_declared_value();
		}

		$connect = new WC_Correios_Cws_Connect( $this->id, $this->instance_id );
		return $connect->get_shipping_cost( $args, $this->get_product_code(), $this->get_package() );
	}

	/**
	 * Get shipping time.
	 *
	 * @return array
	 */
	public function get_time() {
		// Checks if product code and postcode are empty.
		if ( ! $this->is_available() ) {
			return array();
		}

		$args = array(
			'cepDestino' => wc_correios_sanitize_postcode( $this->destination_postcode ),
			'cepOrigem'  => wc_correios_sanitize_postcode( $this->get_origin_postcode() ),
		);

		$connect = new WC_Correios_Cws_Connect( $this->id, $this->instance_id );
		return $connect->get_shipping_time( $args, $this->get_product_code(), $this->get_package() );
	}
}
