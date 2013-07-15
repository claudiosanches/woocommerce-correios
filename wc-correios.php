<?php
/**
 * Plugin Name: WooCommerce Correios
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-correios
 * Description: Correios para WooCommerce
 * Author: claudiosanches, rodrigoprior
 * Author URI: http://claudiosmweb.com/
 * Version: 1.4
 * License: GPLv2 or later
 * Text Domain: wccorreios
 * Domain Path: /languages/
 */

define( 'WOO_CORREIOS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * WooCommerce fallback notice.
 */
function wccorreios_woocommerce_fallback_notice() {
    $html = '<div class="error">';
        $html .= '<p>' . __( 'WooCommerce Correios depends on <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to work!', 'wccorreios' ) . '</p>';
    $html .= '</div>';

    echo $html;
}

/**
 * SOAP and SimpleXML missing notice.
 */
function wccorreios_extensions_missing_notice() {
    $html = '<div class="error">';
        $html .= '<p>' . __( 'WooCommerce Correios depends to <a href="http://php.net/manual/en/book.soap.php">SOAP</a> or <a href="http://php.net/manual/en/book.simplexml.php">SimpleXML</a> to work!', 'wccorreios' ) . '</p>';
    $html .= '</div>';

    echo $html;
}

/**
 * Load functions.
 */
add_action( 'plugins_loaded', 'wccorreios_shipping_load', 0 );

function wccorreios_shipping_load() {

    if ( ! class_exists( 'WC_Shipping_Method' ) ) {
        add_action( 'admin_notices', 'wccorreios_woocommerce_fallback_notice' );

        return;
    }

    if ( ! extension_loaded( 'soap' ) && ! extension_loaded( 'simplexml' ) ) {
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
    include_once WOO_CORREIOS_PATH . 'includes/class-wc-correios.php';

    // Metabox.
    include_once WOO_CORREIOS_PATH . 'includes/tracking.php';
    $wc_correios_metabox = new WC_Correios_Tracking;

} // function wccorreios_shipping_load.
