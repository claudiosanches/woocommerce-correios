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
	 * 99998 - Printed Normal.
	 * 99999 - Printed Urgent.
	 *
	 * @var array
	 */
	protected $services = array();

	/**
	 * Additional Services
	 * Last Update: 2015-04-10
	 * Source: http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 * @var array
	 */

	const ADDITIONAL_SERVICE_NATIONAL_REGISTRY              = 1,
		  ADDITIONAL_SERVICE_REASONABLE_REGISTRY            = 2,
		  ADDITIONAL_SERVICE_OWN_HAND                       = 3,
		  ADDITIONAL_SERVICE_RECEIPT_NOTICE_OTHER_SERVICES  = 4,
		  ADDITIONAL_SERVICE_PUT_REMAINDER_REQUEST          = 5,
		  ADDITIONAL_SERVICE_MAXIMUM_DECLARED_VALUE_PACKAGE = 6,
		  ADDITIONAL_SERVICE_MAXIMUM_DECLARED_VALUE_MESSAGE = 7,
		  ADDITIONAL_SERVICE_DECLARED_VALUE_OMISSION_FINE   = 8,
		  ADDITIONAL_SERVICE_LOST_AND_FOUND                 = 9,
		  ADDITIONAL_SERVICE_STORAGE_PER_KG_AND_DAY         = 10,
		  ADDITIONAL_SERVICE_SHIPPING_MODIFICATION          = 11,
		  ADDITIONAL_SERVICE_INDEMNIFICATION                = 12,
		  ADDITIONAL_SERVICE_CPF_RELATED_SERVICES           = 15,
		  ADDITIONAL_SERVICE_RECEIPT_NOTICE_TELEGRAM        = 16,

		  ADDITIONAL_SERVICE_REASONABLE_REGISTRY_WEIGHT_LIMIT = 0.500;

	 protected $additional_services = array(
	 	1  => array( 'label' => 'National Registry', 'price' => 3.60 ),
		2  => array( 'label' => 'Reasonable Registry', 'price' => 1.80 ),
		3  => array( 'label' => 'Own Hand', 'price' => 4.75 ),
		4  => array( 'label' => 'Receipt Notice', 'price' => 3.60 ),
		5  => array( 'label' => 'Put Remainder Request', 'price' => 0.90 ),
		6  => array( 'label' => 'Maximum National Declared Value (Orders)', 'price' => 10000.00 ),
		7  => array( 'label' => 'Maximum National Declared Value (Messages)', 'price' => 500.00 ),
		8  => array( 'label' => 'Declared Value Omission Fine', 'price' => 40.00 ),
		9  => array( 'label' => 'Lost And Found', 'price' => 4.75 ),
		10 => array( 'label' => 'Storage (Per kg Or Fraction, Per Day)', 'price' => 0.90 ),
		11 => array( 'label' => 'Shipping Data Modification Request (Name, Address, Forwarding, Withdrawal) (Via Postal)', 'price' => 2.50 ),
		12 => array( 'label' => 'Indemnification', 'price' => 6.80 ),
		15 => array( 'label' => 'CPF Related Services', 'price' => 5.70 ),
		16 => array( 'label' => 'Delivery Confirmation Request (Telegram)', 'price' => 3.24 ),
	 );

	/**
	 * Services Fixed Prices by Weight
	 *
	 * @var array
	 */
	protected $prices_per_weight = array(
		/**
		 * Impresso Normal (Printed Normal)
		 * Last Update: 2014-02-01
		 * Source: http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
		 */
		99998 => array(
			array( 'from' => 0.000, 'to' => 0.020, 'price' => 0.85 ),
			array( 'from' => 0.021, 'to' => 0.050, 'price' => 1.30 ),
			array( 'from' => 0.051, 'to' => 0.100, 'price' => 1.75 ),
			array( 'from' => 0.101, 'to' => 0.150, 'price' => 2.15 ),
			array( 'from' => 0.151, 'to' => 0.200, 'price' => 2.50 ),
			array( 'from' => 0.201, 'to' => 0.250, 'price' => 2.90 ),
			array( 'from' => 0.251, 'to' => 0.300, 'price' => 3.30 ),
			array( 'from' => 0.301, 'to' => 0.350, 'price' => 3.70 ),
			array( 'from' => 0.351, 'to' => 0.400, 'price' => 4.10 ),
			array( 'from' => 0.401, 'to' => 0.450, 'price' => 4.50 ),
			array( 'from' => 0.451, 'to' => 0.500, 'price' => 4.90 ),
			array( 'from' => 0.501, 'to' => 0.550, 'price' => 5.20 ),
			array( 'from' => 0.551, 'to' => 0.600, 'price' => 5.60 ),
			array( 'from' => 0.601, 'to' => 0.650, 'price' => 5.95 ),
			array( 'from' => 0.651, 'to' => 0.700, 'price' => 6.25 ),
			array( 'from' => 0.701, 'to' => 0.750, 'price' => 6.60 ),
			array( 'from' => 0.751, 'to' => 0.800, 'price' => 6.95 ),
			array( 'from' => 0.801, 'to' => 0.850, 'price' => 7.30 ),
			array( 'from' => 0.851, 'to' => 0.900, 'price' => 7.70 ),
			array( 'from' => 0.901, 'to' => 0.950, 'price' => 8.05 ),
			array( 'from' => 0.951, 'to' => 1.000, 'price' => 8.40 ),
			'additional_per_kg' => 3.35,
			'weight_limit' => 20.000,
			'delivery_days' => 7
		),

		/**
		 * Impresso Urgente (Printed Urgent)
		 * Last Update: 2014-02-01
		 * Source: http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
		 */
		99999 => array(
			array( 'from' => 0.000, 'to' => 0.020, 'price' => 1.25 ),
			array( 'from' => 0.021, 'to' => 0.050, 'price' => 1.75 ),
			array( 'from' => 0.051, 'to' => 0.100, 'price' => 2.35 ),
			array( 'from' => 0.101, 'to' => 0.150, 'price' => 2.90 ),
			array( 'from' => 0.151, 'to' => 0.200, 'price' => 3.40 ),
			array( 'from' => 0.201, 'to' => 0.250, 'price' => 4.00 ),
			array( 'from' => 0.251, 'to' => 0.300, 'price' => 4.50 ),
			array( 'from' => 0.301, 'to' => 0.350, 'price' => 5.05 ),
			array( 'from' => 0.351, 'to' => 0.400, 'price' => 5.50 ),
			array( 'from' => 0.401, 'to' => 0.450, 'price' => 6.10 ),
			array( 'from' => 0.451, 'to' => 0.500, 'price' => 6.60 ),
			'delivery_days' => 3
		)
	);

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
	 * Registry type.
	 *
	 * @var int
	 */
	protected $registry_type = 1;

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
		$this->id = WC_Correios::get_method_id();

		// Logger.
		if ( class_exists( 'WC_Logger' ) ) {
			$this->log = new WC_Logger();
		} else {
			$this->log = $this->woocommerce_method()->logger();
		}
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
	 * Set the registry type.
	 *
	 * @param int $registry_type
	 */
	public function set_registry_type( $registry_type = self::ADDITIONAL_SERVICE_NATIONAL_REGISTRY ) {
		$this->registry_type = $registry_type;
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
	 * Fix number format for SimpleXML.
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
			'99998' => 'Impresso Normal',
			'99999' => 'Impresso Urgente',
		);

		if ( ! isset( $name[ $code ] ) ) {
			return '';
		}

		return $name[ $code ];
	}

	/**
	 * Gets the additional service value.
	 *
	 * @param  int   $code Additional Service ID.
	 *
	 * @return array       Additional Service Value.
	 */
	public function get_additional_service_price( $code ) {
		$result = NULL;

		if (in_array($code, array_keys($this->additional_services))) $result = $this->additional_services[$code]['price'];

		return $result;
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

		$url = add_query_arg( $args, apply_filters( 'woocommerce_correios_webservice_url' , $this->_webservice ) );

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Requesting the Correios WebServices...' );
		}

		// Gets the WebServices response.
		$response = wp_remote_get( $url, array( 'sslverify' => false, 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'WP_Error: ' . $response->get_error_message() );
			}
		} elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$result = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );

			foreach ( $result->cServico as $service ) {
				$code = (string) $service->Codigo;

				//  Obtain the price by using the prices per weight table  //
				if ( in_array ( $code, array_keys( $this->prices_per_weight ) ) && ( !isset( $this->prices_per_weight[$code]['weight_limit'] ) || $this->weight <= $this->prices_per_weight[$code]['weight_limit'] ) ) {
					$freight_price =
					$biggest_price =
					$biggest_weight = NULL;

					foreach ( $this->prices_per_weight[$code] as $k2 => $v2 ) {
						if ( ( $from = @$v2['from'] ) !== NULL && ( $to = @$v2['to'] ) !== NULL && ( $price = @$v2['price'] ) !== NULL ) {
							$biggest_weight = $to;
							$biggest_price = $price;

							if ( $this->weight >= $from && $this->weight <= $to ) {
								$freight_price = $price;
								break;
							}
						}
					}

					if ( $freight_price === NULL ) {
						//  Adds the additional price per kg  //
						if ( ( $additional_per_kg = @$this->prices_per_weight[$code]['additional_per_kg'] ) !== NULL ) {
							$freight_price = $biggest_price;

							if ( $this->weight > $biggest_weight ) $freight_price += ( ceil( $this->weight ) - 1 ) * $additional_per_kg;
						}
						//  ================================  //
					}

					if ( $freight_price !== NULL ) {
						//  Adds the registry price to the final freight price  //
						$freight_price += $this->additional_services[$this->weight <= self::ADDITIONAL_SERVICE_REASONABLE_REGISTRY_WEIGHT_LIMIT ? $this->registry_type : self::ADDITIONAL_SERVICE_NATIONAL_REGISTRY]['price'];

						//  Adds the own hand price to the final freight price  //
						if ( !( 'N' == $this->own_hand ) ) $freight_price += $this->additional_services[self::ADDITIONAL_SERVICE_OWN_HAND]['price'];

						//  Adds the receipt notice price to the final freight price  //
						if ( !( 'N' == $this->receipt_notice ) ) $freight_price += $this->additional_services[self::ADDITIONAL_SERVICE_RECEIPT_NOTICE_OTHER_SERVICES]['price'];

						//  Verify if this freight is more expensive than any one of the other services returned by the web service  //
						$more_expensive = false;
						foreach ( $values as $k3 => $v3 ) {
							if ( $v3->Valor <= $freight_price ) {
								$more_expensive = true;
								break;
							}
						}
						//  =======================================================================================================  //

						//  Adds the service to the function result  //
						if ( !$more_expensive ) {
							$service->Valor = number_format( $freight_price, 2, ',', '' );
							$service->PrazoEntrega = $this->prices_per_weight[$code]['delivery_days'];
							$service->Erro = 0;
							$service->MsgErro = NULL;
						}
						//  =======================================  //
					}
				}
				//  =====================================================  //

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices response [' . self::get_service_name( $code ) . ']: ' . print_r( $service, true ) );
				}

				$values[ $code ] = $service;
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Error accessing the Correios WebServices: ' . print_r( $response, true ) );
			}
		}

		return $values;
	}
}
