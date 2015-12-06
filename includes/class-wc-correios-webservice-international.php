<?php
/**
 * Correios Webservice International.
 *
 * @package WooCommerce_Correios/Classes/Webservice
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Webservice International integration class.
 */
class WC_Correios_Webservice_International {

	/**
	 * Webservice URL.
	 *
	 * @var string
	 */
	private $_webservice = 'http://www2.correios.com.br/sistemas/efi/bb/Consulta.cfm?';

	/**
	 * IDs from Correios services.
	 *
	 * 110 - Mercadoria Expressa (EMS).
	 * 128 - Mercadoria Econômica.
	 * 209 - Leve Internacional.
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
	 * Destination country.
	 *
	 * @var string
	 */
	protected $destination_country = '';

	/**
	 * Origin location.
	 *
	 * @var string
	 */
	protected $origin_location = '';

	/**
	 * Origin state.
	 *
	 * @var string
	 */
	protected $origin_state = '';

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
	 * Set the service.
	 *
	 * @param string $service Correios international service.
	 */
	public function set_service( $services = '' ) {
		$this->service = $services;
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
	 * Set destination country.
	 *
	 * @param string $country Destination country.
	 */
	public function set_destination_country( $country = '' ) {
		$this->destination_country = $country;
	}

	/**
	 * Set origin location.
	 *
	 * @param string $location Origin location.
	 */
	public function set_origin_location( $location = '' ) {
		$this->origin_location = $location;
	}

	/**
	 * Set origin state.
	 *
	 * @param string $state Origin state.
	 */
	public function set_origin_state( $state = '' ) {
		$this->origin_state = $state;
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
		return apply_filters( 'woocommerce_correios_webservice_international_url', $this->_webservice, $this->id );
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
		return ! empty( $this->service ) || ! empty( $this->destination_country );
	}

	/**
	 * Get shipping prices.
	 *
	 * @return SimpleXMLElement
	 */
	public function get_shipping() {
		$shipping = null;

		// Checks if services and postcode is empty.
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

		$args = apply_filters( 'woocommerce_correios_international_shipping_args', array(
			'tipoConsulta' => 'Geral',
			'especif'      => $this->service,
			'uforigem'     => '',
			'localidade'   => '',
			'pais'         => '',
			'altura'       => $this->float_to_string( $this->height ),
			'largura'      => $this->float_to_string( $this->width ),
			'profundidade' => $this->float_to_string( $this->length ),
			'peso'         => $this->float_to_string( $this->weight ),
			'reset'        => 'true',
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

			if ( isset( $result->exporta_facil->tipo_servico ) ) {
				foreach ( $result->exporta_facil->tipo_servico as $service ) {
					if ( 'yes' == $this->debug ) {
						$this->log->add( $this->id, 'Correios WebServices response: ' . print_r( $service, true ) );
					}

					$shipping = $service;
				}
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error accessing the Correios WebServices: ' . print_r( $response, true ) );
			}
		}

		return $shipping;
	}
}
