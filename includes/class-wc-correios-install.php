<?php
/**
 * Correios integration with the REST API.
 *
 * @package WooCommerce_Correios/Classes
 * @since   3.0.0
 * @version 3.0.0
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
	 * Upgrade to 3.0.0.
	 */
	public static function upgrade_300() {
		global $wpdb;

		$version = self::get_version();

		if ( empty( $version ) ) {
			$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_correios_tracking_code' WHERE meta_key = 'correios_tracking';" ); // WPCS: db call ok, cache ok.
			self::update_version();
		}
	}

	/**
	 * Upgrade to 3.0.0 while using WooCommerce 2.6.0.
	 */
	public static function upgrade_300_from_wc_260() {
		$old_options = get_option( 'woocommerce_correios_settings' );
		if ( $old_options ) {
			if ( isset( $old_options['tracking_history'] ) ) {
				$integration_options = get_option( 'woocommerce_correios-integration_settings', array(
					'general_options' => '',
					'tracking'        => '',
					'enable_tracking' => 'no',
					'tracking_debug'  => 'no',
				) );

				// Update integration options.
				$integration_options['enable_tracking'] = $old_options['tracking_history'];
				update_option( 'woocommerce_correios-integration_settings', $integration_options );

				// Update the old options.
				unset( $old_options['tracking_history'] );
				update_option( 'woocommerce_correios_settings', $old_options );
			}

			if ( 'no' === $old_options['enabled'] ) {
				delete_option( 'woocommerce_correios_settings' );
			}
		}
	}
}
