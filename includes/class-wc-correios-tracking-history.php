<?php
/**
 * Correios Tracking History.
 *
 * @package WooCommerce_Correios/Classes/Tracking
 * @since   3.0.0
 * @version 3.2.0
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
	 * @param  array $tracking_code Tracking code.
	 *
	 * @return array
	 */
	protected function get_tracking_history( $tracking_codes ) {
		include_once dirname( __FILE__ ) . '/class-wc-correios-soap-client.php';

		$this->logger( sprintf( 'Fetching tracking history for "%s" on Correios Webservices...', implode( ', ', $tracking_codes ) ) );

		$objects   = null;
		$user_data = $this->get_user_data();
		$args      = apply_filters( 'woocommerce_correios_tracking_history_webservice_args', array(
			'usuario'   => $user_data['login'],
			'senha'     => $user_data['password'],
			'tipo'      => 'L', // L - List of objects, F - Object Range.
			'resultado' => 'T', // T - Returns all the object's events, U - Returns only last event object.
			'lingua'    => '101',
			'objetos'   => implode( '', $tracking_codes ),
		) );

		try {
			$soap     = new WC_Correios_Soap_Client( $this->get_tracking_history_webservice_url() );
			$response = $soap->buscaEventos( $args );

			// Handle Correios multiple formats response.
			if ( ! empty( $response->return->objeto ) ) {
				// Handle multiple objects.
				if ( is_array( $response->return->objeto ) ) {
					$objects = (array) $response->return->objeto;

					// Fix when return only last event for each object.
					$_objects = array();
					foreach ( $objects as $key => $object ) {
						$_objects[ $key ] = $object;

						if ( is_object( $object->evento ) ) {
							$_objects[ $key ]->evento = array( $_objects[ $key ]->evento );
						}
					}
					$objects = $_objects;

				// Handle single object.
				} elseif ( is_object( $response->return->objeto ) ) {
					$objects = array( $response->return->objeto );

					// Fix when return only last event.
					if ( is_object( $objects[0]->evento ) ) {
						$objects[0]->evento = array( $objects[0]->evento );
					}
				}
			}

		} catch ( Exception $e ) {
			$this->logger( sprintf( 'An error occurred while trying to fetch the tracking history for "%s": %s', implode( ', ', $tracking_codes ), $e->getMessage() ) );
		}

		if ( ! is_null( $objects ) ) {
			$this->logger( sprintf( 'Tracking history found successfully: %s', print_r( $objects, true ) ) );
		}

		return apply_filters( 'woocommerce_correios_tracking_objects', $objects, $tracking_codes );
	}

	/**
	 * Display the order tracking code in order details and the tracking history.
	 *
	 * @param WC_Order $order Order data.
	 */
	public function view( $order ) {
		$objects = array();

		$tracking_codes = wc_correios_get_tracking_codes( $order );

		// Check if exist a tracking code for the order.
		if ( empty( $tracking_codes ) ) {
			return;
		}

		// Try to connect to Correios Webservices and get the tracking history.
		if ( apply_filters( 'woocommerce_correios_enable_tracking_history', false ) ) {
			$objects = $this->get_tracking_history( $tracking_codes );
		}

		wc_get_template(
			'myaccount/tracking-title.php',
			array(),
			'',
			WC_Correios::get_templates_path()
		);

		// Display the right template for show the tracking code or tracking history.
		if ( ! empty( $objects ) ) {
			foreach ( $objects as $object ) {
				wc_get_template(
					'myaccount/tracking-history-table.php',
					array(
						'events' => (array) $object->evento,
						'code'   => (string) $object->numero,
					),
					'',
					WC_Correios::get_templates_path()
				);
			}
		} else {
			wc_get_template(
				'myaccount/tracking-codes.php',
				array(
					'codes' => $tracking_codes,
				),
				'',
				WC_Correios::get_templates_path()
			);
		}
	}
}

new WC_Correios_Tracking_History();
