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
	 * Shipping method ID.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Shipping zone instance ID.
	 *
	 * @var int
	 */
	protected $instance_id = 0;

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
	 * WooCommerce package containing the products.
	 *
	 * @var array
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
	 * @param string $id Method ID.
	 * @param int    $instance_id Instance ID.
	 */
	public function __construct( $id = 'correios', $instance_id = 0 ) {
		$this->id           = $id;
		$this->instance_id  = $instance_id;
		$this->log          = new WC_Logger();
	}

	/**
	 * Set the service.
	 *
	 * @param string $service Correios international service.
	 */
	public function set_service( $service = '' ) {
		$this->service = $service;
	}

	/**
	 * Set shipping package.
	 *
	 * @param array $package Shipping package.
	 */
	public function set_package( $package = array() ) {
		$this->package = $package;
		$correios_package = new WC_Correios_Package( $package );

		if ( ! is_null( $correios_package ) ) {
			$data = $correios_package->get_data();

			$this->set_height( wc_get_dimension( $data['height'], 'mm', 'cm' ) );
			$this->set_width( wc_get_dimension( $data['width'], 'mm', 'cm' ) );
			$this->set_length( wc_get_dimension( $data['length'], 'mm', 'cm' ) );
			$this->set_weight( wc_get_weight( $data['weight'], 'g', 'kg' ) );
		}

		if ( 'yes' === $this->debug ) {
			if ( ! empty( $data ) ) {
				$data = array(
					'weight' => $this->get_weight(),
					'height' => $this->get_height(),
					'width'  => $this->get_width(),
					'length' => $this->get_length(),
				);
			}

			$this->log->add( $this->id, 'Weight and cubage of the order: ' . print_r( $data, true ) );
		}
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
		$this->height = (float) $height;
	}

	/**
	 * Set shipping package width.
	 *
	 * @param float $width Shipping package width.
	 */
	public function set_width( $width = 0 ) {
		$this->width = (float) $width;
	}

	/**
	 * Set shipping package length.
	 *
	 * @param float $length Shipping package length.
	 */
	public function set_length( $length = 0 ) {
		$this->length = (float) $length;
	}

	/**
	 * Set shipping package weight.
	 *
	 * @param float $weight Shipping package weight.
	 */
	public function set_weight( $weight = 0 ) {
		$this->weight = (float) $weight;
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
		return apply_filters( 'woocommerce_correios_webservice_international_url', $this->_webservice, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get allowed countries.
	 *
	 * @return string
	 */
	public function get_allowed_countries() {
		return apply_filters( 'woocommerce_correios_international_allowed_countries', array(
			'AD',
			'AE',
			'AF',
			'AG',
			'AI',
			'AL',
			'AM',
			'AN',
			'AO',
			'AR',
			'AS',
			'AT',
			'AU',
			'AW',
			'AZ',
			'BA',
			'BB',
			'BD',
			'BE',
			'BF',
			'BG',
			'BH',
			'BI',
			'BJ',
			'BM',
			'BN',
			'BO',
			'BS',
			'BT',
			'BW',
			'BY',
			'BZ',
			'CA',
			'CC',
			'CD',
			'CF',
			'CG',
			'CH',
			'CI',
			'CK',
			'CL',
			'CM',
			'CN',
			'CO',
			'CR',
			'CU',
			'CV',
			'CX',
			'CY',
			'CZ',
			'DE',
			'DJ',
			'DK',
			'DM',
			'DO',
			'DZ',
			'EC',
			'EE',
			'EG',
			'EH',
			'ER',
			'ES',
			'ET',
			'FI',
			'FJ',
			'FK',
			'FM',
			'FO',
			'FR',
			'GA',
			'GB',
			'GD',
			'GE',
			'GF',
			'GG',
			'GH',
			'GI',
			'GL',
			'GM',
			'GN',
			'GP',
			'GQ',
			'GR',
			'GS',
			'GT',
			'GU',
			'GW',
			'GY',
			'HK',
			'HN',
			'HR',
			'HT',
			'HU',
			'ID',
			'IE',
			'IL',
			'IM',
			'IN',
			'IQ',
			'IR',
			'IS',
			'IT',
			'JE',
			'JM',
			'JO',
			'JP',
			'KE',
			'KG',
			'KH',
			'KI',
			'KM',
			'KN',
			'KP',
			'KR',
			'KW',
			'KY',
			'KZ',
			'LA',
			'LB',
			'LC',
			'LI',
			'LK',
			'LR',
			'LS',
			'LT',
			'LU',
			'LV',
			'LY',
			'MA',
			'MC',
			'MD',
			'ME',
			'MG',
			'MH',
			'MK',
			'ML',
			'MM',
			'MN',
			'MO',
			'MP',
			'MQ',
			'MR',
			'MS',
			'MT',
			'MU',
			'MV',
			'MW',
			'MX',
			'MY',
			'MZ',
			'NA',
			'NC',
			'NE',
			'NF',
			'NG',
			'NI',
			'NL',
			'NO',
			'NP',
			'NR',
			'NU',
			'NZ',
			'OM',
			'PA',
			'PE',
			'PF',
			'PG',
			'PH',
			'PK',
			'PL',
			'PM',
			'PN',
			'PR',
			'PS',
			'PT',
			'PW',
			'PY',
			'QA',
			'RE',
			'RO',
			'RS',
			'RU',
			'RW',
			'SA',
			'SB',
			'SC',
			'SD',
			'SE',
			'SG',
			'SH',
			'SI',
			'SK',
			'SL',
			'SM',
			'SN',
			'SO',
			'SR',
			'ST',
			'SV',
			'SY',
			'SZ',
			'TC',
			'TD',
			'TF',
			'TG',
			'TH',
			'TJ',
			'TK',
			'TM',
			'TN',
			'TO',
			'TP',
			'TR',
			'TT',
			'TV',
			'TW',
			'TZ',
			'UA',
			'UG',
			'US',
			'UY',
			'UZ',
			'VA',
			'VC',
			'VE',
			'VG',
			'VI',
			'VN',
			'VU',
			'WF',
			'WS',
			'XA',
			'XB',
			'XC',
			'XD',
			'XE',
			'XF',
			'XG',
			'YE',
			'YT',
			'ZA',
			'ZM',
			'ZW',
		) );
	}

	/**
	 * Get destination country.
	 *
	 * @return string
	 */
	public function get_destination_country() {
		return $this->destination_country;
	}

	/**
	 * Get origin location.
	 *
	 * @return string
	 */
	public function get_origin_location() {
		$location = 'C' === $this->origin_location ? 'C' : 'I';

		return apply_filters( 'woocommerce_correios_international_origin_location', $this->origin_location, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get origin state.
	 *
	 * @return string
	 */
	public function get_origin_state() {
		return apply_filters( 'woocommerce_correios_international_origin_state', $this->origin_state, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get height.
	 *
	 * @return float
	 */
	public function get_height() {
		return $this->float_to_string( $this->height );
	}

	/**
	 * Get width.
	 *
	 * @return float
	 */
	public function get_width() {
		return $this->float_to_string( $this->width );
	}

	/**
	 * Get length.
	 *
	 * @return float
	 */
	public function get_length() {
		return $this->float_to_string( $this->length );
	}

	/**
	 * Get weight.
	 *
	 * @return float
	 */
	public function get_weight() {
		return $this->float_to_string( $this->weight );
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
		$state = $this->get_origin_state();

		return ! empty( $this->service ) || ! empty( $this->destination_country ) || ! in_array( $this->destination_country, $this->get_allowed_countries() ) || ! empty( $state ) || 0 === $this->get_height();
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

		$args = apply_filters( 'woocommerce_correios_international_shipping_args', array(
			'tipoConsulta' => 'Geral',
			'especif'      => $this->service,
			'uforigem'     => $this->get_origin_state(),
			'localidade'   => $this->get_origin_location(),
			'pais'         => $this->get_destination_country(),
			'altura'       => $this->get_height(),
			'largura'      => $this->get_width(),
			'profundidade' => $this->get_length(),
			'peso'         => $this->get_weight(),
			'reset'        => 'true',
		), $this->id, $this->package );

		$url = add_query_arg( $args, $this->get_webservice_url() );

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, 'Requesting Correios WebServices: ' . $url );
		}

		// Gets the WebServices response.
		$response = wp_safe_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'WP_Error: ' . $response->get_error_message() );
			}
		} elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			try {
				$result = wc_correios_safe_load_xml( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices invalid XML: ' . $e->getMessage() );
				}
			}

			if ( isset( $result->tipo_servico ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices response: ' . print_r( $result, true ) );
				}

				$shipping = $result->tipo_servico;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'Error accessing the Correios WebServices: ' . print_r( $response, true ) );
			}
		}

		return $shipping;
	}
}
