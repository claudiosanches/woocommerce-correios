<?php

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
	 * Package minimum height.
	 *
	 * @var integer
	 */
	protected $minimum_height = 2;

	/**
	 * Package minimum width.
	 *
	 * @var integer
	 */
	protected $minimum_width = 11;

	/**
	 * Package minimum length.
	 *
	 * @var integer
	 */
	protected $minimum_length = 16;

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
	 * Set the package minimum height.
	 *
	 * @param int $minimum_height
	 */
	public function set_minimum_height( $minimum_height ) {
		$this->minimum_height = intval( $minimum_height );
	}

	/**
	 * Set the package minimum width.
	 *
	 * @param int $minimum_width
	 */
	public function set_minimum_width( $minimum_width ) {
		$this->minimum_width = intval( $minimum_width );
	}

	/**
	 * Set the package minimum length.
	 *
	 * @param int $minimum_length
	 */
	public function set_minimum_length( $minimum_length ) {
		$this->minimum_length = intval( $minimum_length );
	}

	/**
	 * Replace comma by dot.
	 *
	 * @param  mixed $value Value to fix.
	 *
	 * @return mixed
	 */
	private function fix_format( $value ) {
		$value = str_replace( ',', '.', $value );

		return $value;
	}

	/**
	 * Extracts the weight and dimensions from the package.
	 *
	 * @param array $package
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

				$_height = wc_get_dimension( $this->fix_format( $product->height ), 'cm' );
				$_width  = wc_get_dimension( $this->fix_format( $product->width ), 'cm' );
				$_length = wc_get_dimension( $this->fix_format( $product->length ), 'cm' );
				$_weight = wc_get_weight( $this->fix_format( $product->weight ), 'kg' );

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
	 * @param  array $height
	 * @param  array $width
	 * @param  array $length
	 *
	 * @return int
	 */
	protected function cubage_total( $height, $width, $length ) {
		// Sets the cubage of all products.
		$all   = array();
		$total = 0;

		for ( $i = 0; $i < count( $height ); $i++ ) {
			$all[ $i ] = $height[ $i ] * $width[ $i ] * $length[ $i ];
		}

		foreach ( $all as $value ) {
			$total += $value;
		}

		return $total;
	}

	/**
	 * Get the max values.
	 *
	 * @param  array $height
	 * @param  array $width
	 * @param  array $length
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
	 * @param  array $height
	 * @param  array $width
	 * @param  array $length
	 *
	 * @return float
	 */
	protected function calculate_root( $height, $width, $length, $max_values ) {
		$cubage_total = $this->cubage_total( $height, $width, $length );
		$root        = 0;

		if ( 0 != $cubage_total ) {
			// Dividing the value of scaling of all products.
			// With the measured value of greater.
			$division = $cubage_total / max( $max_values );
			// Total square root.
			$root = round( sqrt( $division ), 1 );
		}

		return $root;
	}

	/**
	 * Sets the final cubage.
	 *
	 * @param  array $height
	 * @param  array $width
	 * @param  array $length
	 *
	 * @return array
	 */
	protected function get_cubage( $height, $width, $length ) {
		$cubage     = array();
		$max_values = $this->get_max_values( $height, $width, $length );
		$root       = $this->calculate_root( $height, $width, $length, $max_values );
		$greatest   = array_search( max( $max_values ), $max_values );

		switch ( $greatest ) {
			case 'height':
				$cubage = array(
					'height' => max( $height ),
					'width'  => $root,
					'length' => $root,
				);
				break;
			case 'width':
				$cubage = array(
					'height' => $root,
					'width'  => max( $width ),
					'length' => $root,
				);
				break;
			case 'length':
				$cubage = array(
					'height' => $root,
					'width'  => $root,
					'length' => max( $length ),
				);
				break;

			default:
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

		$cubage = $this->get_cubage( $data['height'], $data['width'], $data['length'] );

		$height = ( $cubage['height'] < $this->minimum_height ) ? $this->minimum_height : $cubage['height'];
		$width  = ( $cubage['width'] < $this->minimum_width ) ? $this->minimum_width : $cubage['width'];
		$length = ( $cubage['length'] < $this->minimum_length ) ? $this->minimum_length : $cubage['length'];

		return array(
			'height' => $height,
			'length' => $length,
			'width'  => $width,
			'weight' => $data['weight'],
		);
	}
}
