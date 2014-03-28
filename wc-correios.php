<?php
/**
 * Plugin Name: WooCommerce Correios
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-correios
 * Description: Correios para WooCommerce
 * Author: claudiosanches, rodrigoprior
 * Author URI: http://claudiosmweb.com/
 * Version: 1.6.2
 * License: GPLv2 or later
 * Text Domain: wccorreios
 * Domain Path: /languages/
 */

/**
 * WooCommerce fallback notice.
 */
function wccorreios_woocommerce_fallback_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Correios depends on %s to work!', 'wccorreios' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
}

/**
 * SimpleXML missing notice.
 */
function wccorreios_extensions_missing_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Correios depends to %s to work!', 'wccorreios' ), '<a href="http://php.net/manual/en/book.simplexml.php">SimpleXML</a>' ) . '</p></div>';
}

/**
 * Load functions.
 */
function wccorreios_shipping_load() {

	if ( ! class_exists( 'WC_Shipping_Method' ) ) {
		add_action( 'admin_notices', 'wccorreios_woocommerce_fallback_notice' );

		return;
	}

	if ( ! class_exists( 'SimpleXmlElement' ) ) {
		add_action( 'admin_notices', 'wccorreios_extensions_missing_notice' );

		return;
	}

	/**
	 * Load textdomain.
	 */
	load_plugin_textdomain( 'wccorreios', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * Add the Correios to shipping methods.
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	function wccorreios_add_method( $methods ) {
		$methods[] = 'WC_Correios';

		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'wccorreios_add_method' );

	// WC_Correios class.
	include_once 'includes/class-wc-correios.php';

	// Metabox.
	include_once 'includes/class-wc-correios-tracking.php';
	$wc_correios_metabox = new WC_Correios_Tracking;
}

add_action( 'plugins_loaded', 'wccorreios_shipping_load', 0 );
