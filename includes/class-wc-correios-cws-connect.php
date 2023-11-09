<?php
/**
 * Correios Web Services API Connect.
 *
 * @package WooCommerce_Correios/Classes/Webservice/Connect
 * @since   4.0.0
 * @version 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Web Services API Connect class.
 */
class WC_Correios_Cws_Connect {

	/**
	 * Integration ID.
	 *
	 * @var string
	 */
	protected $id = 'correios-cws';

	/**
	 * Shipping method ID.
	 *
	 * @var string
	 */
	protected $method_id = '';

	/**
	 * Shipping zone instance ID.
	 *
	 * @var int
	 */
	protected $instance_id = 0;

	/**
	 * CWS production endpoint.
	 *
	 * @var string
	 */
	protected $cws_production_endpoint = 'https://api.correios.com.br/';

	/**
	 * CWS staging endpoint.
	 *
	 * @var string
	 */
	protected $cws_staging_endpoint = 'https://apihom.correios.com.br/';

	/**
	 * CWS User data.
	 *
	 * @var string
	 */
	protected $user_data = array();

	/**
	 * CWS environment.
	 *
	 * @var string
	 */
	protected $environment = 'staging';

	/**
	 * CWS debug.
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
	 * Initialize connect class.
	 *
	 * @param string $method_id Method ID.
	 * @param int    $instance_id Instance ID.
	 */
	public function __construct( $method_id = 'correios', $instance_id = 0 ) {
		$this->method_id   = $method_id;
		$this->instance_id = $instance_id;
		$this->environment = apply_filters( 'woocommerce_correios_cws_environment', 'production', $this->method_id, $this->instance_id );
		$this->user_data   = apply_filters( 'woocommerce_correios_cws_user_data', array(), $this->method_id, $this->instance_id );
		$this->debug       = apply_filters( 'woocommerce_correios_cws_debug', '', $this->method_id, $this->instance_id );
		$this->log         = wc_get_logger();
	}

	/**
	 * Add log entry.
	 *
	 * @param string $entry Log Entry.
	 * @param mixed  $code Code to parse.
	 */
	protected function add_log( $entry, $code = '' ) {
		if ( 'yes' === $this->debug ) {
			$code = $this->escape_sensitive_data( $code );
			$code = $code ? ' ' . wc_print_r( $code, true ) : '';
			$this->log->add( $this->id, $entry . $code );
		}
	}

	/**
	 * Escape Sensitive data.
	 *
	 * @param array $data Data to escape.
	 * @return array
	 */
	protected function escape_sensitive_data( $data ) {
		// Escape on top level.
		$ids = array(
			'id',
			'cnpj',
		);
		foreach ( $ids as $id ) {
			if ( isset( $data[ $id ] ) ) {
				$data[ $id ] = wc_correios_esc_sensitive_data( $data[ $id ] );
			}
		}

		// Escape data from cartaoPostagem.
		if ( isset( $data['cartaoPostagem'] ) ) {
			$ids = array(
				'numero',
				'contrato',
			);
			foreach ( $ids as $id ) {
				if ( isset( $data['cartaoPostagem'][ $id ] ) ) {
					$data['cartaoPostagem'][ $id ] = wc_correios_esc_sensitive_data( $data['cartaoPostagem'][ $id ] );
				}
			}
		}

		// Hide token from logs.
		if ( isset( $data['token'] ) ) {
			$data['token'] = '...';
		}

		return $data;
	}

	/**
	 * Get CWS username.
	 *
	 * @return string
	 */
	protected function get_username() {
		return isset( $this->user_data['username'] ) ? $this->user_data['username'] : '';
	}

	/**
	 * Get Access Code.
	 *
	 * @return string
	 */
	protected function get_access_code() {
		return isset( $this->user_data['access_code'] ) ? $this->user_data['access_code'] : '';
	}

	/**
	 * Get Posting Card number.
	 *
	 * @return string
	 */
	protected function get_posting_card() {
		return isset( $this->user_data['posting_card'] ) ? $this->user_data['posting_card'] : '';
	}

	/**
	 * Get CWS URL.
	 *
	 * @param string $endpoint Endpoint.
	 * @return string
	 */
	protected function get_cws_url( $endpoint ) {
		$url = 'staging' === $this->environment ? $this->cws_staging_endpoint : $this->cws_production_endpoint;

		return $url . $endpoint;
	}

	/**
	 * GET CWS authentication URL.
	 *
	 * @param string $opt Authentication endpoint options.
	 * @return string
	 */
	protected function get_cws_authentication_url( $opt = 'card' ) {
		$options = array(
			'auth'     => 'token/v1/autentica',
			'contract' => 'token/v1/autentica/contrato',
			'card'     => 'token/v1/autentica/cartaopostagem',
		);

		$endpoint = isset( $options[ $opt ] ) ? $options[ $opt ] : $options['card'];

		return $this->get_cws_url( $endpoint );
	}

	/**
	 * Get errors from Correios response.
	 *
	 * @param array $response HTTP response.
	 * @return array
	 */
	protected function get_error_message( $response ) {
		return array(
			'response' => $response['response'] ?? array(),
			'body'     => $response['body'] ?? array(),
		);
	}

	/**
	 * Safe remote GET request.
	 *
	 * @param string $url   Full URL to request.
	 * @param string $token Token from Correios API.
	 * @param string $type  Request type for error logs.
	 * @return array
	 */
	protected function remote_get( $url, $token, $type ) {
		$response = wp_safe_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Accept'        => 'application/json',
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->add_log( sprintf( 'Failed to fetch %s:', $type ), $response->get_error_message() );
			return array();
		}
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$this->add_log( sprintf( 'Failed to fetch %s:', $type ), $this->get_error_message( $response ) );
			return array();
		}

		return $response;
	}

	/**
	 * Get token expiration date.
	 *
	 * @param array $data Token response data.
	 *
	 * @return int
	 */
	protected function get_token_expiration_date( $data ) {
		$now      = new \DateTime( 'now' );
		$timezone = new \DateTimeZone( 'America/Sao_Paulo' );
		$now->setTimezone( $timezone );

		// Set the transient expiration time to 30 minutes before the token expiration time.
		return strtotime( $data['expiraEm'] ) - $now->format( 'U' ) - ( HOUR_IN_SECONDS / 2 );
	}

	/**
	 * Filter services by segment.
	 *
	 * @param array    $raw_data Raw data.
	 * @param string[] $segments List of segments.
	 * @return array
	 */
	protected function filter_services( $raw_data, $segments = array() ) {
		$data = array();

		foreach ( $raw_data as $item ) {
			if (
				! isset( $item['coSegmento'] )
				|| ( $segments && ! in_array( strval( $item['coSegmento'] ), $segments, true ) )
				|| ! isset( $item['codigo'] )
			) {
				continue;
			}

			$data[ $item['codigo'] ] = sprintf( '(%1$s) %2$s', $item['codigo'], $item['descricao'] );
		}

		return apply_filters( 'woocommerce_correios_cws_services_list', $data );
	}

	/**
	 * Get token transient ID.
	 *
	 * @return string
	 */
	public function get_token_transient_id() {
		return $this->id . '-' . $this->environment . '-token';
	}

	/**
	 * Clean token.
	 *
	 * @return bool
	 */
	public function clean_token() {
		return delete_transient( $this->get_token_transient_id() );
	}

	/**
	 * Get CWS token.
	 *
	 * @return array
	 */
	public function get_token() {
		$transient = $this->get_token_transient_id();

		$data = apply_filters( 'woocommerce_correios_cws_default_token', array() );
		if ( ! empty( $data ) ) {
			$this->add_log( 'Using default token!' );
			return $data;
		}

		$data = get_transient( $transient );
		if ( false !== $data ) {
			$this->add_log( 'Token already saved in transients, returning saved token!' );
			return json_decode( $data, true );
		}

		$this->add_log( 'Generating new CWS token...' );
		$url      = $this->get_cws_authentication_url();
		$response = wp_safe_remote_post(
			$url,
			array(
				'body'    => wp_json_encode( array( 'numero' => $this->get_posting_card() ) ),
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $this->get_username() . ':' . $this->get_access_code() ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->add_log( 'Failed to generate token:', $response->get_error_message() );
			return array();
		}
		if ( ! in_array( wp_remote_retrieve_response_code( $response ), array( 200, 201 ), true ) ) {
			$this->add_log( 'Failed to generate token:', $this->get_error_message( $response ) );
			return array();
		}

		$data       = json_decode( $response['body'], true );
		$expiration = $this->get_token_expiration_date( $data );
		$json       = wp_json_encode( $data );

		// Save token as a transient.
		if ( 0 < $expiration ) {
			set_transient( $transient, $json, $expiration );
		}

		$this->add_log( 'Token generated:', $data );

		return $data;
	}

	/**
	 * Get available services.
	 *
	 * @param bool     $update Update/regenerate list.
	 * @param string[] $segments List of segments.
	 * @return array
	 */
	public function get_available_services( $update = false, $segments = array() ) {
		$option = 'woocommerce_correios_cws_services_list';

		$data = apply_filters( 'woocommerce_correios_cws_default_services', array(), $segments );
		if ( ! empty( $data ) ) {
			return $this->filter_services( $data, $segments );
		}

		if ( false === $update ) {
			$data = get_option( $option, '{}' );
			$data = json_decode( $data, true );

			if ( ! empty( $data ) ) {
				return $this->filter_services( $data, $segments );
			}
		}

		$this->add_log( 'Getting available services list from Correios API...' );

		$token = $this->get_token();
		if ( empty( $token ) || empty( $token['cnpj'] ) ) {
			$this->add_log( 'Missing Token! Aborting...' );
			$this->clean_token();
			return $this->filter_services( $data, $segments );
		}

		$endpoint = array(
			'meucontrato',
			'v1',
			'empresas',
			$token['cnpj'],
			'contratos',
			$token['cartaoPostagem']['contrato'],
			'cartoes',
			$token['cartaoPostagem']['numero'],
			'servicos',
		);

		$url      = $this->get_cws_url( join( '/', $endpoint ) . '?page=0&size=500' );
		$response = $this->remote_get( $url, $token['token'], 'services list' );
		if ( empty( $response['body'] ) ) {
			return array();
		}

		$raw_data = json_decode( $response['body'], true );

		if ( empty( $raw_data['itens'] ) ) {
			$this->add_log( 'No available services found:', $response );
			return array();
		}

		$data = $raw_data['itens'];

		// Save services list.
		update_option( $option, wp_json_encode( $data ) );

		$this->add_log( 'Services list generated:', $data );

		return $this->filter_services( $data, $segments );
	}

	/**
	 * Get shipping cost.
	 *
	 * @param array  $args         List of parameters.
	 * @param array  $product_code Product code.
	 * @param array  $package      WooCommerce shipping package.
	 * @param string $api_type    API type.
	 * @return array
	 */
	public function get_shipping_cost( $args, $product_code, $package, $api_type = 'nacional' ) {
		$data = apply_filters( 'woocommerce_correios_cws_pre_get_shipping_cost', array(), $args, $this->method_id, $this->instance_id, $package );
		if ( ! empty( $data ) ) {
			return $data;
		}

		$this->add_log( sprintf( 'Getting shipping cost for product code: %s', $product_code ) );

		$token = $this->get_token();
		if ( empty( $token ) ) {
			$this->add_log( 'Missing Token! Aborting...' );
			return $data;
		}

		$endpoint = array(
			'preco',
			'v1',
			$api_type,
			$product_code,
		);

		$args['nuContrato'] = $token['cartaoPostagem']['contrato'];
		$args['nuDR']       = $token['cartaoPostagem']['dr'];

		$args = apply_filters( 'woocommerce_correios_cws_get_shipping_cost_args', $args, $this->method_id, $this->instance_id, $package );

		$url      = add_query_arg( $args, $this->get_cws_url( join( '/', $endpoint ) ) );
		$response = $this->remote_get( $url, $token['token'], 'shipping cost' );
		if ( empty( $response['body'] ) ) {
			return array();
		}

		$data = json_decode( $response['body'], true );

		$this->add_log( sprintf( 'Shipping cost calculated for product code %s:', $product_code ), $data );

		return $data;
	}

	/**
	 * Get shipping time.
	 *
	 * @param array  $args         List of paramentes.
	 * @param array  $product_code Product code.
	 * @param array  $package      WooCommerce shipping package.
	 * @param string $api_type    API type.
	 * @return array
	 */
	public function get_shipping_time( $args, $product_code, $package, $api_type = 'nacional' ) {
		$data = apply_filters( 'woocommerce_correios_cws_pre_get_shipping_time', array(), $args, $this->method_id, $this->instance_id, $package );
		if ( ! empty( $data ) ) {
			return $data;
		}

		$this->add_log( sprintf( 'Getting shipping time for product code: %s', $product_code ) );

		$token = $this->get_token();
		if ( empty( $token ) ) {
			$this->add_log( 'Missing Token! Aborting...' );
			return $data;
		}

		$endpoint = array(
			'prazo',
			'v1',
			$api_type,
			$product_code,
		);

		$args = apply_filters( 'woocommerce_correios_cws_get_shipping_time_args', $args, $this->method_id, $this->instance_id, $package );

		$url      = add_query_arg( $args, $this->get_cws_url( join( '/', $endpoint ) ) );
		$response = $this->remote_get( $url, $token['token'], 'shipping time' );
		if ( empty( $response['body'] ) ) {
			return array();
		}

		$data = json_decode( $response['body'], true );

		$this->add_log( sprintf( 'Shipping time calculated for product code %s:', $product_code ), $data );

		return $data;
	}

	/**
	 * Get tracking history.
	 *
	 * @param array $tracking_codes Tracking codes.
	 * @return array
	 */
	public function get_tracking_history( $tracking_codes ) {
		$data = apply_filters( 'woocommerce_correios_cws_pre_get_tracking_history', array(), $tracking_codes );
		if ( ! empty( $data ) ) {
			return $data;
		}

		$this->add_log( sprintf( 'Getting tracking history for: %s', implode( ', ', $tracking_codes ) ) );

		$token = $this->get_token();
		if ( empty( $token ) ) {
			$this->add_log( 'Missing Token! Aborting...' );
			return $data;
		}

		$endpoint = array(
			'srorastro',
			'v1',
			'objetos',
		);

		$args = array(
			'codigosObjetos' => implode( ',', $tracking_codes ),
			'resultado'      => 'T',
		);

		$args     = apply_filters( 'woocommerce_correios_cws_get_tracking_history_args', $args, $tracking_codes );
		$url      = add_query_arg( $args, $this->get_cws_url( join( '/', $endpoint ) ) );
		$response = $this->remote_get( $url, $token['token'], 'tracking history' );
		if ( empty( $response['body'] ) ) {
			return array();
		}

		$data = json_decode( $response['body'], true );

		$this->add_log( sprintf( 'Retrived tracking history for %s:', implode( ', ', $tracking_codes ) ), $data );

		return $data;
	}

	/**
	 * Get address from a postcode.
	 *
	 * @param string $postcode Postcode.
	 * @return array
	 */
	public function get_address_from_postcode( $postcode ) {
		$data = apply_filters( 'woocommerce_correios_cws_pre_get_address_from_postcode', array(), $postcode );
		if ( ! empty( $data ) ) {
			return $data;
		}

		$this->add_log( sprintf( 'Fetching address for: %s', $postcode ) );

		$token = $this->get_token();
		if ( empty( $token ) ) {
			$this->add_log( 'Missing Token! Aborting...' );
			return $data;
		}

		$endpoint = array(
			'cep',
			'v2',
			'enderecos',
			$postcode,
		);

		$url      = $this->get_cws_url( join( '/', $endpoint ) );
		$response = $this->remote_get( $url, $token['token'], 'address from postcode' );
		if ( empty( $response['body'] ) ) {
			return array();
		}

		$data = json_decode( $response['body'], true );

		$this->add_log( sprintf( 'Retrived address for %s:', $postcode ), $data );

		return $data;
	}
}
