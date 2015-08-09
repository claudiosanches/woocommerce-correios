<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Connect class.
 */
class WC_Correios_Connect {

	/**
	 * Webservice URL.
	 *
	 * @var string
	 */
	private $_webservice = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?';

	/**
	 * Services ID.
	 *
	 * 41106 - PAC without contract.
	 * 40010 - SEDEX without contract.
	 * 40215 - SEDEX 10 without contract.
	 * 40290 - SEDEX Hoje without contract.
	 * 41068 - PAC with contract.
	 * 40096 - SEDEX with contract.
	 * 81019 - e-SEDEX with contract.
	 *
	 * @var array
	 */
	protected $services = array();

	/**
	 * Products package.
	 *
	 * @var array
	 */
	protected $package = array();

	/**
	 * Origin zipcode.
	 *
	 * @var string
	 */
	protected $zip_origin = '';

	/**
	 * Destination zipcode.
	 *
	 * @var string
	 */
	protected $zip_destination = '';

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
	 * Correios username.
	 *
	 * @var string
	 */
	protected $login = '';

	/**
	 * Correios password.
	 *
	 * @var string
	 */
	protected $password = '';

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
	 * Own hand service.
	 *
	 * @var string
	 */
	protected $own_hand = 'N';

	/**
	 * Receipt notice service.
	 *
	 * @var string
	 */
	protected $receipt_notice = 'N';

	/**
	 * Debug mode.
	 *
	 * @var string
	 */
	protected $debug = 'no';

	/**
	 * Initialize the Connect class.
	 *
	 * @param string $debug Debug mode.
	 */
	public function __construct() {
		$this->id  = 'correios';
		$this->log = new WC_Logger();
	}

	/**
	 * Set the services.
	 *
	 * @param array $services
	 */
	public function set_services( $services = array() ) {
		$this->services = $services;
	}

	/**
	 * Set the package.
	 *
	 * @param array $package
	 *
	 * @return WC_Correios_Package
	 */
	public function set_package( $package = array() ) {
		$this->package = new WC_Correios_Package( $package );

		return $this->package;
	}

	/**
	 * Set the origin zipcode.
	 *
	 * @param string $zip_origin
	 */
	public function set_zip_origin( $zip_origin = '' ) {
		$this->zip_origin = $zip_origin;
	}

	/**
	 * Set the destination zipcode.
	 *
	 * @param string $zip_destination
	 */
	public function set_zip_destination( $zip_destination = '' ) {
		$this->zip_destination = $zip_destination;
	}

	/**
	 * Set the package height.
	 *
	 * @param float $height
	 */
	public function set_height( $height = 0 ) {
		$this->height = $height;
	}

	/**
	 * Set the package width.
	 *
	 * @param float $width
	 */
	public function set_width( $width = 0 ) {
		$this->width = $width;
	}

	/**
	 * Set the package diameter.
	 *
	 * @param float $diameter
	 */
	public function set_diameter( $diameter = 0 ) {
		$this->diameter = $diameter;
	}

	/**
	 * Set the package length.
	 *
	 * @param float $length
	 */
	public function set_length( $length = 0 ) {
		$this->length = $length;
	}

	/**
	 * Set the package weight.
	 *
	 * @param float $weight
	 */
	public function set_weight( $weight = 0 ) {
		$this->weight = $weight;
	}

	/**
	 * Set the Correios username.
	 *
	 * @param string $login
	 */
	public function set_login( $login = '' ) {
		$this->login = $login;
	}

	/**
	 * Set the Correios password.
	 *
	 * @param string $password
	 */
	public function set_password( $password = '' ) {
		$this->password = $password;
	}

	/**
	 * Set the declared value.
	 *
	 * @param string $declared_value
	 */
	public function set_declared_value( $declared_value = '0' ) {
		$this->declared_value = $declared_value;
	}

	/**
	 * Set the package format.
	 *
	 * @param string $format
	 */
	public function set_format( $format = '1' ) {
		$this->format = $format;
	}

	/**
	 * Set the Own hand option.
	 *
	 * @param string $own_hand
	 */
	public function set_own_hand( $own_hand = 'N' ) {
		$this->own_hand = $own_hand;
	}

	/**
	 * Set the receipt notice.
	 *
	 * @param string $receipt_notice
	 */
	public function set_receipt_notice( $receipt_notice = 'N' ) {
		$this->receipt_notice = $receipt_notice;
	}

	/**
	 * Set the debug mode.
	 *
	 * @param string $debug yes or no.
	 */
	public function set_debug( $debug = 'no' ) {
		$this->debug = $debug;
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
	 * Replace comma by dot.
	 *
	 * @param  mixed $value Value to fix.
	 *
	 * @return mixed
	 */
	public static function fix_currency_format( $value ) {
		$value = str_replace( '.', '', $value );
		$value = str_replace( ',', '.', $value );

		return $value;
	}

	/**
	 * Clean Zipcode.
	 *
	 * @param  string $zip Zipcode.
	 *
	 * @return string      Cleaned zipcode.
	 */
	protected function clean_zipcode( $zip ) {
		$fixed = preg_replace( '([^0-9])', '', $zip );

		return $fixed;
	}

    /**
     * Get fee.
     *
     * @param  mixed $fee
     * @param  mixed $total
     *
     * @return float
     */
    public static function get_fee( $fee, $total ) {
		if ( strstr( $fee, '%' ) ) {
			$fee = ( $total / 100 ) * str_replace( '%', '', $fee );
		}

		return $fee;
	}

	/**
	 * Gets the service name.
	 *
	 * @param  int   $code Correios service ID.
	 *
	 * @return array       Correios service name.
	 */
	public static function get_service_name( $code ) {
		$name = array(
			'41106' => 'PAC',
			'40010' => 'SEDEX',
			'40215' => 'SEDEX 10',
			'40290' => 'SEDEX Hoje',
			'41068' => 'PAC',
			'40096' => 'SEDEX',
			'81019' => 'e-SEDEX',
		);

		if ( ! isset( $name[ $code ] ) ) {
			return '';
		}

		return $name[ $code ];
	}

	/**
	 * Estimating Delivery.
	 *
	 * @param string $label
	 * @param string $date
	 * @param int    $additional_time
	 *
	 * @return string
	 */
	public static function estimating_delivery( $label, $date, $additional_time = 0 ) {
		$name = $label;
		$additional_time = intval( $additional_time );

		if ( $additional_time > 0 ) {
			$date += intval( $additional_time );
		}

		if ( $date > 0 ) {
			$name .= ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days', $date, 'woocommerce-correios' ),  $date ) . ')';
		}

		return $name;
	}

	/**
	 * Get shipping prices.
	 *
	 * @return array
	 */
	public function get_shipping() {
		$values = array();

		// Checks if services and zipcode is empty.
		if (
			! is_array( $this->services )
			|| empty( $this->services )
			|| empty( $this->zip_destination )
			|| empty( $this->zip_origin )
		) {
			return $values;
		}

		if (
			0 == $this->height
			&& 0 == $this->width
			&& 0 == $this->diameter
			&& 0 == $this->length
			&& 0 == $this->weight
			&& ! empty( $this->package )
		) {
			$package = $this->package->get_data();
			$this->height = $package['height'];
			$this->width  = $package['width'];
			$this->length = $package['length'];
			$this->weight = $package['weight'];

			if ( 'yes' == $this->debug ) {
				$this->log->add( 'correios', 'Weight and cubage of the order: ' . print_r( $package, true ) );
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$package = array(
					'weight' => $this->weight,
					'height' => $this->height,
					'width'  => $this->width,
					'length' => $this->length
				);

				$this->log->add( 'correios', 'Weight and cubage of the order: ' . print_r( $package, true ) );
			}
		}

		$args = apply_filters( 'woocommerce_correios_shipping_args', array(
			'nCdServico'          => implode( ',', $this->services ),
			'nCdEmpresa'          => $this->login,
			'sDsSenha'            => $this->password,
			'sCepDestino'         => $this->clean_zipcode( $this->zip_destination ),
			'sCepOrigem'          => $this->clean_zipcode( $this->zip_origin ),
			'nVlAltura'           => $this->float_to_string( $this->height ),
			'nVlLargura'          => $this->float_to_string( $this->width ),
			'nVlDiametro'         => $this->float_to_string( $this->diameter ),
			'nVlComprimento'      => $this->float_to_string( $this->length ),
			'nVlPeso'             => $this->float_to_string( $this->weight ),
			'nCdFormato'          => $this->format,
			'sCdMaoPropria'       => $this->own_hand,
			'nVlValorDeclarado'   => round( number_format( $this->declared_value, 2, '.', '' ) ),
			'sCdAvisoRecebimento' => $this->receipt_notice,
			'StrRetorno'          => 'xml'
		) );

		$url = add_query_arg( $args, apply_filters( 'woocommerce_correios_webservice_url', $this->_webservice ) );

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Requesting the Correios WebServices...' );
		}

		// Gets the WebServices response.
		$response = wp_safe_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'WP_Error: ' . $response->get_error_message() );
			}
		} elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			try {
				$result = self::safe_load_xml( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices invalid XML: ' . $e->getMessage() );
				}
			}

			if ( isset( $result->cServico ) ) {
				foreach ( $result->cServico as $service ) {
					$code = (string) $service->Codigo;

					if ( 'yes' == $this->debug ) {
						$this->log->add( $this->id, 'Correios WebServices response [' . self::get_service_name( $code ) . ']: ' . print_r( $service, true ) );
					}

					$values[ $code ] = $service;
				}
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error accessing the Correios WebServices: ' . print_r( $response, true ) );
			}
		}

		return $values;
	}

	/**
	 * Safe load XML.
	 *
	 * @param  string $source
	 * @param  int    $options
	 *
	 * @return SimpleXMLElement|bool
	 */
	public static function safe_load_xml( $source, $options = 0 ) {
		$old = null;

		if ( function_exists( 'libxml_disable_entity_loader' ) ) {
			$old = libxml_disable_entity_loader( true );
		}

		$dom    = new DOMDocument();
		$return = $dom->loadXML( $source, $options );

		if ( ! is_null( $old ) ) {
			libxml_disable_entity_loader( $old );
		}

		if ( ! $return ) {
			return false;
		}

		if ( isset( $dom->doctype ) ) {
			throw new Exception( 'Unsafe DOCTYPE Detected while XML parsing' );

			return false;
		}

		return simplexml_import_dom( $dom );
	}
}
