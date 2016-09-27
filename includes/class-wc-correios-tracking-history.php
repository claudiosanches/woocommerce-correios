<?php
/**
 * Correios Tracking History.
 *
 * @package WooCommerce_Correios/Classes/Tracking
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios tracking history class.
 */
class WC_Correios_Tracking_History {

	/**
	 * Tracking webservice URL.
	 *
	 * @var string
	 */
	private $_webservice_url = 'https://webservice.correios.com.br/service/rastro/Rastro.wsdl';

	/**
	 * Initialize actions.
	 */
	public function __construct() {
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'view' ), 1 );
	}

	/**
	 * Get the tracking history webservice URL.
	 *
	 * @return string
	 */
	protected function get_tracking_history_webservice_url() {
		return apply_filters( 'woocommerce_correios_tracking_webservice_url', $this->_webservice_url );
	}

	/**
	 * Get user data.
	 *
	 * @return array
	 */
	protected function get_user_data() {
		$user_data = apply_filters( 'woocommerce_correios_tracking_user_data', array( 'login' => 'ECT', 'password' => 'SRO' ) );

		return $user_data;
	}

	/**
	 * Logger.
	 *
	 * @param string $data Data to log.
	 */
	protected function logger( $data ) {
		if ( apply_filters( 'woocommerce_correios_enable_tracking_debug', false ) ) {
			$logger = new WC_Logger();
			$logger->add( 'correios-tracking-history', $data );
		}
	}

	/**
	 * Access API Correios.
	 *
	 * @param  string $tracking_code Tracking code.
	 *
	 * @return array
	 */
	protected function get_tracking_history( $tracking_code ) {
		include_once dirname( __FILE__ ) . '/class-wc-correios-soap-client.php';

		$this->logger( sprintf( 'Fetching tracking history for "%s" on Correios Webservices...', $tracking_code ) );

		$events    = null;
		$user_data = $this->get_user_data();
		$args      = apply_filters( 'woocommerce_correios_tracking_history_webservice_args', array(
			'usuario'   => $user_data['login'],
			'senha'     => $user_data['password'],
			'tipo'      => 'L', // L - List of objects, F - Object Range.
			'resultado' => 'T', // T - Returns all the object's events, U - Returns only last event object.
			'lingua'    => '101',
			'objetos'   => $tracking_code,
		) );

		try {
			$soap     = new WC_Correios_Soap_Client( $this->get_tracking_history_webservice_url() );
			$response = $soap->buscaEventos( $args );

			if ( isset( $response->return->objeto->evento ) ) {
				$events = (array) $response->return->objeto->evento;
			}
		} catch ( Exception $e ) {
			$this->logger( sprintf( 'An error occurred while trying to fetch the tracking history for "%s": %s', $tracking_code, $e->getMessage() ) );
		}

		if ( ! is_null( $events ) ) {
			$this->logger( sprintf( 'Tracking history for "%s" found successfully: %s', $tracking_code, print_r( $events, true ) ) );
		}

		return apply_filters( 'woocommerce_correios_tracking_response', $events, $tracking_code );
	}

	/**
	 * Display the order tracking code in order details and the tracking history.
	 *
	 * @param WC_Order $order Order data.
	 */
	public function view( $order ) {
		$events        = false;
		$tracking_code = get_post_meta( $order->id, '_correios_tracking_code', true );

		// Check if exist a tracking code for the order.
		if ( ! $tracking_code ) {
			return;
		}

		// Try to connect to Correios Webservices and get the tracking history.
		if ( apply_filters( 'woocommerce_correios_enable_tracking_history', false ) ) {
			$events = $this->get_tracking_history( $tracking_code );
		}

		// Display the right template for show the tracking code or tracking history.
		if ( is_array( $events ) ) {
			wc_get_template(
				'myaccount/tracking-history-table.php',
				array(
					'events' => $events,
					'code'   => $tracking_code,
				),
				'',
				WC_Correios::get_templates_path()
			);
		} else {
			wc_get_template(
				'myaccount/tracking-code.php',
				array(
					'code' => $tracking_code,
				),
				'',
				WC_Correios::get_templates_path()
			);
		}
	}
}

new WC_Correios_Tracking_History();
