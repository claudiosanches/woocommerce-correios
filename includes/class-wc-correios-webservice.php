<?php
/**
 * Correios Webservice.
 *
 * @package WooCommerce_Correios/Classes/Webservice
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Webservice integration class.
 */
class WC_Correios_Webservice {

	/**
	 * Webservice URL.
	 *
	 * @var string
	 */
	private $_webservice = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?';

	/**
	 * IDs from Correios service.
	 *
	 * 41106 - PAC without contract.
	 * 40010 - SEDEX without contract.
	 * 40215 - SEDEX 10 without contract.
	 * 40290 - SEDEX Hoje without contract.
	 * 41068 - PAC with contract.
	 * 40096 - SEDEX with contract.
	 * 81019 - e-SEDEX with contract.
	 *
	 * @var string
	 */
	protected $service = '';

	/**
	 * Products package.
	 *
	 * @var WC_Correios_Package
	 */
	protected $package = null;

	/**
	 * Destination postcode.
	 *
	 * @var string
	 */
	protected $destination_postcode = '';

	/**
	 * Package height.
	 *
	 * @var float
	 */
	protected $height = 0;

	/**
	 * Package width.
	 *
	 * @var float
	 */
	protected $width = 0;

	/**
	 * Package diameter.
	 *
	 * @var float
	 */
	protected $diameter = 0;

	/**
	 * Package length.
	 *
	 * @var float
	 */
	protected $length = 0;

	/**
	 * Package weight.
	 *
	 * @var float
	 */
	protected $weight = 0;

	/**
	 * Declared value.
	 *
	 * @var string
	 */
	protected $declared_value = '0';

	/**
	 * Package format.
	 *
	 * 1 – box/package
	 * 2 – roll/prism
	 * 3 - envelope
	 *
	 * @var string
	 */
	protected $format = '1';

	/**
	 * Debug mode.
	 *
	 * @var string
	 */
	protected $debug = 'no';

	/**
	 * Initialize webservice.
	 *
	 * @param string $id Shipping method ID.
	 */
	public function __construct( $id = 'correios' ) {
		$this->id  = $id;
		$this->log = new WC_Logger();
	}

	/**
	 * Set the service
	 *
	 * @param stsring $service Correios service.
	 */
	public function set_service( $service = '' ) {
		$this->service = $service;
	}

	/**
	 * Set shipping package.
	 *
	 * @param array $package Shipping package.
	 *
	 * @return WC_Correios_Package
	 */
	public function set_package( $package = array() ) {
		$this->package = new WC_Correios_Package( $package );

		return $this->package;
	}

	/**
	 * Set destination postcode.
	 *
	 * @param string $postcode Destination postcode.
	 */
	public function set_destination_postcode( $postcode = '' ) {
		$this->destination_postcode = $postcode;
	}

	/**
	 * Set shipping package height.
	 *
	 * @param float $height Shipping package height.
	 */
	public function set_height( $height = 0 ) {
		$this->height = $height;
	}

	/**
	 * Set shipping package width.
	 *
	 * @param float $width Shipping package width.
	 */
	public function set_width( $width = 0 ) {
		$this->width = $width;
	}

	/**
	 * Set shipping package diameter.
	 *
	 * @param float $diameter Shipping package diameter.
	 */
	public function set_diameter( $diameter = 0 ) {
		$this->diameter = $diameter;
	}

	/**
	 * Set shipping package length.
	 *
	 * @param float $length Shipping package length.
	 */
	public function set_length( $length = 0 ) {
		$this->length = $length;
	}

	/**
	 * Set shipping package weight.
	 *
	 * @param float $weight Shipping package weight.
	 */
	public function set_weight( $weight = 0 ) {
		$this->weight = $weight;
	}

	/**
	 * Set declared value.
	 *
	 * @param string $declared_value Value to declare.
	 */
	public function set_declared_value( $declared_value = '0' ) {
		$this->declared_value = $declared_value;
	}

	/**
	 * Set shipping package format.
	 *
	 * @param string $format Package format.
	 */
	public function set_format( $format = '1' ) {
		$this->format = $format;
	}

	/**
	 * Set the debug mode.
	 *
	 * @param string $debug Yes or no.
	 */
	public function set_debug( $debug = 'no' ) {
		$this->debug = $debug;
	}

	/**
	 * Get webservice URL.
	 *
	 * @return string
	 */
	public function get_webservice_url() {
		return apply_filters( 'woocommerce_correios_webservice_url', $this->_webservice, $this->id );
	}

	/**
	 * Get origin postcode.
	 *
	 * @return string
	 */
	public function get_origin_postcode() {
		return apply_filters( 'woocommerce_correios_origin_postcode', '', $this->id );
	}

	/**
	 * Fix number format for XML.
	 *
	 * @param  float $value  Value with dot.
	 *
	 * @return string        Value with comma.
	 */
	protected function float_to_string( $value ) {
		$value = str_replace( '.', ',', $value );

		return $value;
	}

	/**
	 * Check if is setted.
	 *
	 * @return bool
	 */
	protected function is_setted() {
		return ! empty( $this->service ) || ! empty( $this->destination_postcode ) || ! empty( $this->get_origin_postcode() );
	}

	/**
	 * Get shipping prices.
	 *
	 * @return SimpleXMLElement
	 */
	public function get_shipping() {
		$shipping = null;

		// Checks if service and postcode are empty.
		if ( ! $this->is_setted() ) {
			return $values;
		}

		if ( ! is_null( $this->package ) ) {
			$package = $this->package->get_data();
			$this->height = $package['height'];
			$this->width  = $package['width'];
			$this->length = $package['length'];
			$this->weight = $package['weight'];
		}

		if ( 'yes' == $this->debug ) {
			if ( ! empty( $package ) ) {
				$package = array(
					'weight' => $this->weight,
					'height' => $this->height,
					'width'  => $this->width,
					'length' => $this->length,
				);
			}

			$this->log->add( $this->id, 'Weight and cubage of the order: ' . print_r( $package, true ) );
		}

		$args = apply_filters( 'woocommerce_correios_shipping_args', array(
			'nCdServico'          => $this->service,
			'nCdEmpresa'          => apply_filters( 'woocommerce_correios_login', '', $this->id ),
			'sDsSenha'            => apply_filters( 'woocommerce_correios_password', '', $this->id ),
			'sCepDestino'         => wc_correios_sanitize_postcode( $this->destination_postcode ),
			'sCepOrigem'          => wc_correios_sanitize_postcode( $this->get_origin_postcode() ),
			'nVlAltura'           => $this->float_to_string( $this->height ),
			'nVlLargura'          => $this->float_to_string( $this->width ),
			'nVlDiametro'         => $this->float_to_string( $this->diameter ),
			'nVlComprimento'      => $this->float_to_string( $this->length ),
			'nVlPeso'             => $this->float_to_string( $this->weight ),
			'nCdFormato'          => $this->format,
			'sCdMaoPropria'       => apply_filters( 'woocommerce_correios_own_hands', 'N', $this->id ),
			'nVlValorDeclarado'   => round( number_format( $this->declared_value, 2, '.', '' ) ),
			'sCdAvisoRecebimento' => apply_filters( 'woocommerce_correios_receipt_notice', 'N', $this->id ),
			'StrRetorno'          => 'xml',
		), $this->id );

		$url = add_query_arg( $args, $this->get_webservice_url() );

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Requesting Correios WebServices: ' . $url );
		}

		// Gets the WebServices response.
		$response = wp_safe_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'WP_Error: ' . $response->get_error_message() );
			}
		} elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			try {
				$result = wc_correios_safe_load_xml( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices invalid XML: ' . $e->getMessage() );
				}
			}

			if ( isset( $result->cServico ) ) {
				$service = $result->cServico;

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices response: ' . print_r( $service, true ) );
				}

				$shipping = $service;
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error accessing the Correios WebServices: ' . print_r( $response, true ) );
			}
		}

		return $shipping;
	}
}
