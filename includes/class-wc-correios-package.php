<?php
/**
 * Correios Package.
 *
 * @package WooCommerce_Correios/Classes
 * @since   3.0.0
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Package class.
 */
class WC_Correios_Package {

	/**
	 * Order package.
	 *
	 * @var array
	 */
	protected $package = array();

	/**
	 * Sets the package.
	 *
	 * @param  array $package Package to calcule.
	 *
	 * @return array
	 */
	public function __construct( $package = array() ) {
		$this->package = $package;
	}

	/**
	 * Extracts the weight and dimensions from the package.
	 *
	 * @return array
	 */
	protected function get_package_data() {
		$count  = 0;
		$height = array();
		$width  = array();
		$length = array();
		$weight = array();

		// Shipping per item.
		foreach ( $this->package['contents'] as $item_id => $values ) {
			$product = $values['data'];
			$qty     = $values['quantity'];

			if ( $qty > 0 && $product->needs_shipping() ) {

				$_height = wc_get_dimension( (float) $product->get_length(), 'cm' );
				$_width  = wc_get_dimension( (float) $product->get_width(), 'cm' );
				$_length = wc_get_dimension( (float) $product->get_height(), 'cm' );
				$_weight = wc_get_weight( (float) $product->get_weight(), 'kg' );

				$height[ $count ] = $_height;
				$width[ $count ]  = $_width;
				$length[ $count ] = $_length;
				$weight[ $count ] = $_weight;

				if ( $qty > 1 ) {
					$n = $count;
					for ( $i = 0; $i < $qty; $i++ ) {
						$height[ $n ] = $_height;
						$width[ $n ]  = $_width;
						$length[ $n ] = $_length;
						$weight[ $n ] = $_weight;
						$n++;
					}
					$count = $n;
				}

				$count++;
			}
		}

		return array(
			'height' => array_values( $height ),
			'length' => array_values( $length ),
			'width'  => array_values( $width ),
			'weight' => array_sum( $weight ),
		);
	}

	/**
	 * Calculates the cubage of all products.
	 *
	 * @param  array $height Package height.
	 * @param  array $width  Package width.
	 * @param  array $length Package length.
	 *
	 * @return int
	 */
	protected function cubage_total( $height, $width, $length ) {
		// Sets the cubage of all products.
		$total       = 0;
		$total_items = count( $height );

		for ( $i = 0; $i < $total_items; $i++ ) {
			$total += $height[ $i ] * $width[ $i ] * $length[ $i ];
		}

		return $total;
	}

	/**
	 * Get the max values.
	 *
	 * @param  array $height Package height.
	 * @param  array $width  Package width.
	 * @param  array $length Package length.
	 *
	 * @return array
	 */
	protected function get_max_values( $height, $width, $length ) {
		$find = array(
			'height' => max( $height ),
			'width'  => max( $width ),
			'length' => max( $length ),
		);

		return $find;
	}

	/**
	 * Calculates the square root of the scaling of all products.
	 *
	 * @param  array $height     Package height.
	 * @param  array $width      Package width.
	 * @param  array $length     Package length.
	 * @param  array $max_values Package bigger values.
	 *
	 * @return float
	 */
	protected function calculate_root( $height, $width, $length, $max_values ) {
		$cubage_total = $this->cubage_total( $height, $width, $length );
		$root         = 0;
		$biggest      = max( $max_values );

		if ( 0 !== $cubage_total && 0 < $biggest ) {
			// Dividing the value of scaling of all products.
			// With the measured value of greater.
			$division = $cubage_total / $biggest;
			// Total square root.
			$root = round( sqrt( $division ), 1 );
		}

		return $root;
	}

	/**
	 * Sets the final cubage.
	 *
	 * @param  array $height Package height.
	 * @param  array $width  Package width.
	 * @param  array $length Package length.
	 *
	 * @return array
	 */
	protected function get_cubage( $height, $width, $length ) {
		$cubage     = array();
		$max_values = $this->get_max_values( $height, $width, $length );
		$root       = $this->calculate_root( $height, $width, $length, $max_values );
		$greatest   = array_search( max( $max_values ), $max_values, true );

		switch ( $greatest ) {
			case 'height' :
				$cubage = array(
					'height' => max( $height ),
					'width'  => $root,
					'length' => $root,
				);
				break;
			case 'width' :
				$cubage = array(
					'height' => $root,
					'width'  => max( $width ),
					'length' => $root,
				);
				break;
			case 'length' :
				$cubage = array(
					'height' => $root,
					'width'  => $root,
					'length' => max( $length ),
				);
				break;

			default :
				$cubage = array(
					'height' => 0,
					'width'  => 0,
					'length' => 0,
				);
				break;
		}

		return $cubage;
	}

	/**
	 * Get the package data.
	 *
	 * @return array
	 */
	public function get_data() {
		// Get the package data.
		$data = apply_filters( 'woocommerce_correios_default_package', $this->get_package_data() );

		if ( ! empty( $data['height'] ) && ! empty( $data['width'] ) && ! empty( $data['length'] ) ) {
			$cubage = $this->get_cubage( $data['height'], $data['width'], $data['length'] );
		} else {
			$cubage = array(
				'height' => 0,
				'width'  => 0,
				'length' => 0,
			);
		}

		return array(
			'height' => apply_filters( 'woocommerce_correios_package_height', $cubage['height'] ),
			'width'  => apply_filters( 'woocommerce_correios_package_width', $cubage['width'] ),
			'length' => apply_filters( 'woocommerce_correios_package_length', $cubage['length'] ),
			'weight' => apply_filters( 'woocommerce_correios_package_weight', $data['weight'] ),
		);
	}
}
