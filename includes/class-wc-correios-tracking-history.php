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
	 * Initialize actions.
	 */
	public function __construct() {
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'view' ), 1 );
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
			$connect = new WC_Correios_Cws_Connect();
			$objects = $connect->get_tracking_history( $tracking_codes );
		}

		wc_get_template(
			'myaccount/tracking-title.php',
			array(),
			'',
			WC_Correios::get_templates_path()
		);

		// Display the right template for show the tracking code or tracking history.
		if ( ! empty( $objects['objetos'] ) ) {
			foreach ( $objects['objetos'] as $object ) {
				wc_get_template(
					'myaccount/tracking-history-table.php',
					array(
						'events' => $object['eventos'],
						'code'   => $object['codObjeto'],
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
