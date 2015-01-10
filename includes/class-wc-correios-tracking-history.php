<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Correios orders.
 */
class WC_Correios_Tracking_History {

	/**
	 * Initialize the order actions.
	 */
	public function __construct() {
		// Show tracking code in order details.
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'view_order_tracking_code' ), 1 );
	}

	/**
	 * Display the order tracking code in order details.
		 * Display tracking history
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string           Tracking code as link.
	 */
	public function view_order_tracking_code( $order ) {
		$tracking_code = get_post_meta( $order->id, 'correios_tracking', true );

		if ( ! empty( $tracking_code ) ) {
			$tracking_data = $this->get_tracking_history( $tracking_code );

			include_once 'views/html-tracking-table.php';
		}
	}

	/**
	 * Access API Correios.
	 *
	 * @param  string $tracking_code.
	 *
	 * @return SimpleXmlElement|stdClass History Tracking code.
	 */
	public function get_tracking_history( $tracking_code ) {
		$options  = get_option( 'woocommerce_correios_settings', array() );
		$login    = empty( $options['login'] ) ? 'ECT' : $options['login'];
		$password = empty( $options['password'] ) ? 'SRO' : $options['password'];

		$args = apply_filters( 'woocommerce_correios_tracking_args', array(
			'Usuario'   => $login,
			'Senha'     => $password,
			'Tipo'      => 'L', /* L - List of objects | F - Object Range */
			'Resultado' => 'T', /* T - Returns all the object's events | U - Returns only last event object */
			'Objetos'   => $tracking_code,
		) );

		$url = add_query_arg( $args, 'http://websro.correios.com.br/sro_bin/sroii_xml.eventos' );

		$params = array(
			'sslverify' => false,
			'timeout'   => 30
		);

		$response = wp_remote_get( $url, $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$tracking_history = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
		} else {
			$tracking_history = new stdClass();
			$tracking_history->error = true;
		}

		return $tracking_history;
	}
}
