<?php
/**
 * Correios Soap Client.
 *
 * @package WooCommerce_Correios/Classes/Soap
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Correios_Soap_Client class.
 */
class WC_Correios_Soap_Client extends SoapClient {

	/**
	 * SoapClient construct.
	 *
	 * @param mixed $wsdl WSDL URL.
	 */
	public function __construct( $wsdl ) {
		parent::__construct( $wsdl, array(
				'cache_wsdl'     => WSDL_CACHE_NONE,
				'encoding'       => 'UTF-8',
				'exceptions'     => true,
				'stream_context' => $this->get_custom_stream_context(),
			)
		);
	}

	/**
	 * Get a custom stream context to improve performance.
	 *
	 * @return resource Of type stream-context.
	 */
	private function get_custom_stream_context() {
		return stream_context_create( array( 'http' => array( 'protocol_version' => '1.0', 'header' => 'Connection: Close' ) ) );
	}
}
