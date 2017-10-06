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
	 * ID from Correios service.
	 *
	 * @var string|array
	 */
	protected $service = '';

	/**
	 * WooCommerce package containing the products.
	 *
	 * @var array
	 */
	protected $package = null;

	/**
	 * Origin postcode.
	 *
	 * @var string
	 */
	protected $origin_postcode = '';

	/**
	 * Destination postcode.
	 *
	 * @var string
	 */
	protected $destination_postcode = '';

	/**
	 * Login.
	 *
	 * @var string
	 */
	protected $login = '';

	/**
	 * Password.
	 *
	 * @var string
	 */
	protected $password = '';

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
	 * Minimum height.
	 *
	 * @var float
	 */
	protected $minimum_height = 2;

	/**
	 * Minimum width.
	 *
	 * @var float
	 */
	protected $minimum_width = 11;

	/**
	 * Minimum length.
	 *
	 * @var float
	 */
	protected $minimum_length = 16;

	/**
	 * Extra weight.
	 *
	 * @var float
	 */
	protected $extra_weight = 0;

	/**
	 * Declared value.
	 *
	 * @var string
	 */
	protected $declared_value = '0';

	/**
	 * Own hands.
	 *
	 * @var string
	 */
	protected $own_hands = 'N';

	/**
	 * Receipt notice.
	 *
	 * @var string
	 */
	protected $receipt_notice = 'N';

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
	 * Logger.
	 *
	 * @var WC_Logger
	 */
	protected $log = null;

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
	 * Set the service
	 *
	 * @param string|array $service Service.
	 */
	public function set_service( $service = '' ) {
		if ( is_array( $service ) ) {
			$this->service = implode( ',', $service );
		} else {
			$this->service = $service;
		}
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

			$this->set_height( $data['height'] );
			$this->set_width( $data['width'] );
			$this->set_length( $data['length'] );
			$this->set_weight( $data['weight'] );
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
	 * Set origin postcode.
	 *
	 * @param string $postcode Origin postcode.
	 */
	public function set_origin_postcode( $postcode = '' ) {
		$this->origin_postcode = $postcode;
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
	 * Set login.
	 *
	 * @param string $login User login.
	 */
	public function set_login( $login = '' ) {
		$this->login = $login;
	}

	/**
	 * Set password.
	 *
	 * @param string $password User login.
	 */
	public function set_password( $password = '' ) {
		$this->password = $password;
	}

	/**
	 * Set shipping package height.
	 *
	 * @param float $height Package height.
	 */
	public function set_height( $height = 0 ) {
		$this->height = (float) $height;
	}

	/**
	 * Set shipping package width.
	 *
	 * @param float $width Package width.
	 */
	public function set_width( $width = 0 ) {
		$this->width = (float) $width;
	}

	/**
	 * Set shipping package diameter.
	 *
	 * @param float $diameter Package diameter.
	 */
	public function set_diameter( $diameter = 0 ) {
		$this->diameter = (float) $diameter;
	}

	/**
	 * Set shipping package length.
	 *
	 * @param float $length Package length.
	 */
	public function set_length( $length = 0 ) {
		$this->length = (float) $length;
	}

	/**
	 * Set shipping package weight.
	 *
	 * @param float $weight Package weight.
	 */
	public function set_weight( $weight = 0 ) {
		$this->weight = (float) $weight;
	}

	/**
	 * Set minimum height.
	 *
	 * @param float $minimum_height Package minimum height.
	 */
	public function set_minimum_height( $minimum_height = 2 ) {
		$this->minimum_height = 2 <= $minimum_height ? $minimum_height : 2;
	}

	/**
	 * Set minimum width.
	 *
	 * @param float $minimum_width Package minimum width.
	 */
	public function set_minimum_width( $minimum_width = 11 ) {
		$this->minimum_width = 11 <= $minimum_width ? $minimum_width : 11;
	}

	/**
	 * Set minimum length.
	 *
	 * @param float $minimum_length Package minimum length.
	 */
	public function set_minimum_length( $minimum_length = 16 ) {
		$this->minimum_length = 16 <= $minimum_length ? $minimum_length : 16;
	}

	/**
	 * Set extra weight.
	 *
	 * @param float $extra_weight Package extra weight.
	 */
	public function set_extra_weight( $extra_weight = 0 ) {
		$this->extra_weight = (float) wc_format_decimal( $extra_weight );
	}

	/**
	 * Set declared value.
	 *
	 * @param string $declared_value Declared value.
	 */
	public function set_declared_value( $declared_value = '0' ) {
		$this->declared_value = $declared_value;
	}

	/**
	 * Set own hands.
	 *
	 * @param string $own_hands Use 'N' for no and 'S' for yes.
	 */
	public function set_own_hands( $own_hands = 'N' ) {
		$this->own_hands = $own_hands;
	}

	/**
	 * Set receipt notice.
	 *
	 * @param string $receipt_notice Use 'N' for no and 'S' for yes.
	 */
	public function set_receipt_notice( $receipt_notice = 'N' ) {
		$this->receipt_notice = $receipt_notice;
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
		return apply_filters( 'woocommerce_correios_webservice_url', $this->_webservice, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get origin postcode.
	 *
	 * @return string
	 */
	public function get_origin_postcode() {
		return apply_filters( 'woocommerce_correios_origin_postcode', $this->origin_postcode, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get login.
	 *
	 * @return string
	 */
	public function get_login() {
		return apply_filters( 'woocommerce_correios_login', $this->login, $this->id, $this->instance_id, $this->package );
	}
	/**
	 * Get password.
	 *
	 * @return string
	 */
	public function get_password() {
		return apply_filters( 'woocommerce_correios_password', $this->password, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get height.
	 *
	 * @return float
	 */
	public function get_height() {
		return $this->float_to_string( $this->minimum_height <= $this->height ? $this->height : $this->minimum_height );
	}

	/**
	 * Get width.
	 *
	 * @return float
	 */
	public function get_width() {
		return $this->float_to_string( $this->minimum_width <= $this->width ? $this->width : $this->minimum_width );
	}

	/**
	 * Get diameter.
	 *
	 * @return float
	 */
	public function get_diameter() {
		return $this->float_to_string( $this->diameter );
	}

	/**
	 * Get length.
	 *
	 * @return float
	 */
	public function get_length() {
		return $this->float_to_string( $this->minimum_length <= $this->length ? $this->length : $this->minimum_length );
	}

	/**
	 * Get weight.
	 *
	 * @return float
	 */
	public function get_weight() {
		return $this->float_to_string( $this->weight + $this->extra_weight );
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
	 * Check if is available.
	 *
	 * @return bool
	 */
	protected function is_available() {
		$origin_postcode = $this->get_origin_postcode();

		return ! empty( $this->service ) || ! empty( $this->destination_postcode ) || ! empty( $origin_postcode ) || 0 === $this->get_height();
	}

	/**
	 * Get shipping prices.
	 *
	 * @return SimpleXMLElement|array
	 */
	public function get_shipping() {
		$shipping = null;

		// Checks if service and postcode are empty.
		if ( ! $this->is_available() ) {
			return $shipping;
		}

		$args = apply_filters( 'woocommerce_correios_shipping_args', array(
			'nCdServico'          => $this->service,
			'nCdEmpresa'          => $this->get_login(),
			'sDsSenha'            => $this->get_password(),
			'sCepDestino'         => wc_correios_sanitize_postcode( $this->destination_postcode ),
			'sCepOrigem'          => wc_correios_sanitize_postcode( $this->get_origin_postcode() ),
			'nVlAltura'           => $this->get_height(),
			'nVlLargura'          => $this->get_width(),
			'nVlDiametro'         => $this->get_diameter(),
			'nVlComprimento'      => $this->get_length(),
			'nVlPeso'             => $this->get_weight(),
			'nCdFormato'          => $this->format,
			'sCdMaoPropria'       => $this->own_hands,
			'nVlValorDeclarado'   => round( number_format( $this->declared_value, 2, '.', '' ) ),
			'sCdAvisoRecebimento' => $this->receipt_notice,
			'StrRetorno'          => 'xml',
		), $this->id, $this->instance_id, $this->package );

		$url = add_query_arg( $args, $this->get_webservice_url() );

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, 'Requesting Correios WebServices: ' . $url );
		}

		// Gets the WebServices response.
		$response = wp_safe_remote_get( esc_url_raw( $url ), array( 'timeout' => 30 ) );

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

			if ( isset( $result->cServico ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices response: ' . print_r( $result, true ) );
				}

				$shipping = $result->cServico;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'Error accessing the Correios WebServices: ' . print_r( $response, true ) );
			}
		}

		return $shipping;
	}
}
