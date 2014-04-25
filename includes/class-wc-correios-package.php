<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Package class.
 */
class WC_Correios_Package {

	protected $package = array();
	protected $height = array();
	protected $width = array();
	protected $length = array();
	protected $weight = array();
	protected $minimum_height = 2;
	protected $minimum_width = 11;
	protected $minimum_length = 16;

	/**
	 * __construct function.
	 *
	 * @param array $height Height total.
	 * @param array $width  Width total.
	 * @param array $length Length total.
	 *
	 * @return array
	 */
	public function __construct( $package = array() ) {
		$this->package = $package;

		// Get the package data.
		$data = apply_filters( 'woocommerce_correios_default_package', $this->get_package_data() );
		$this->height = $data['height'];
		$this->width  = $data['width'];
		$this->length = $data['length'];
		$this->weight = $data['weight'];
	}

	public function set_minimum_height( $minimum_height ) {
		$this->minimum_height = $minimum_height;
	}

	public function set_minimum_width( $minimum_width ) {
		$this->minimum_width = $minimum_width;
	}

	public function set_minimum_length( $minimum_length ) {
		$this->minimum_length = $minimum_length;
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

				if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
					$_height = wc_get_dimension( $this->fix_format( $product->height ), 'cm' );
					$_width  = wc_get_dimension( $this->fix_format( $product->width ), 'cm' );
					$_length = wc_get_dimension( $this->fix_format( $product->length ), 'cm' );
					$_weight = wc_get_weight( $this->fix_format( $product->weight ), 'kg' );
				} else {
					$_height = woocommerce_get_dimension( $this->fix_format( $product->height ), 'cm' );
					$_width  = woocommerce_get_dimension( $this->fix_format( $product->width ), 'cm' );
					$_length = woocommerce_get_dimension( $this->fix_format( $product->length ), 'cm' );
					$_weight = woocommerce_get_weight( $this->fix_format( $product->weight ), 'kg' );
				}

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
	 * @return array
	 */
	protected function cubage_total() {
		// Sets the cubage of all products.
		$all   = array();
		$total = '';

		for ( $i = 0; $i < count( $this->height ); $i++ ) {
			$all[ $i ] = $this->height[ $i ] * $this->width[ $i ] * $this->length[ $i ];
		}

		foreach ( $all as $value ) {
			$total += $value;
		}

		return $total;
	}

	/**
	 * Finds the greatest measure.
	 *
	 * @return array
	 */
	protected function find_max_length() {
		// Defines the greatest.
		$find = array(
			'height' => max( $this->height ),
			'width'  => max( $this->width ),
			'length' => max( $this->length ),
		);

		return $find;
	}

	/**
	 * Calculates the square root of the scaling of all products.
	 *
	 * @return float
	 */
	protected function calculate_root() {
		$cubageTotal = $this->cubage_total();
		$find        = $this->find_max_length();
		$root        = 0;

		if ( 0 != $cubageTotal ) {
			// Dividing the value of scaling of all products.
			// With the measured value of greater.
			$division = $cubageTotal / max( $find );
			// Total square root.
			$root = round( sqrt( $division ), 1 );
		}

		return $root;
	}

	/**
	 * Sets the final cubage.
	 *
	 * @return array
	 */
	protected function cubage() {

		$cubage   = array();
		$root     = $this->calculate_root();
		$find     = $this->find_max_length();
		$greatest = array_search( max( $find ), $find );

		switch ( $greatest ) {
			case 'height':
				$cubage = array(
					'height' => max( $this->height ),
					'width'  => $root,
					'length' => $root,
				);
				break;
			case 'width':
				$cubage = array(
					'height' => $root,
					'width'  => max( $this->width ),
					'length' => $root,
				);
				break;
			case 'length':
				$cubage = array(
					'height' => $root,
					'width'  => $root,
					'length' => max( $this->length ),
				);
				break;

			default:
				break;
		}

		return $cubage;
	}

	public function get_data() {
		$cubage = $this->cubage();

		$height = '';
		$length = '';
		$width = '';
		$weight = '';

		return array(
			'height' => $height,
			'length' => $length,
			'width'  => $width,
			'weight' => $weight,
		);
	}
}
