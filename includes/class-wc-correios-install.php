<?php
/**
 * Correios integration with the REST API.
 *
 * @package WooCommerce_Correios/Classes
 * @since   3.0.0
 * @version 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_REST_API class.
 */
class WC_Correios_Install {

	/**
	 * Get version.
	 *
	 * @return string
	 */
	private static function get_version() {
		return get_option( 'woocommerce_correios_version' );
	}

	/**
	 * Update version.
	 */
	private static function update_version() {
		update_option( 'woocommerce_correios_version', WC_CORREIOS_VERSION );
	}

	/**
	 * Maybe update.
	 */
	public static function maybe_update() {
		$version = self::get_version();

		if ( version_compare( $version, '4.2.3', '<' ) ) {
			self::upgrade_to_423();
			self::update_version();
		}
	}

	/**
	 * Upgrade to 4.2.3.
	 */
	private static function upgrade_to_423() {
		// Remove old transients.
		delete_transient( 'correios-cwsstaging-token' );
		delete_transient( 'correios-cwsproduction-token' );

		// Update service list.
		$connect = new WC_Correios_Cws_Connect();
		$connect->get_available_services( true );
	}
}
