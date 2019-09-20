<?php
/**
 * Uninstall
 *
 * @package WooCommerce_Correios/Uninstaller
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}correios_postcodes" );
