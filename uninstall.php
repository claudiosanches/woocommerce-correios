<?php
/**
 * Uninstall
 *
 * @package WooCommerce_Correios/Uninstaller
 * @since   3.0.0
 * @version 4.1.1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}correios_postcodes" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange

// Clean transients.
delete_transient( 'correios-cwsstaging-token' );
delete_transient( 'correios-cwsproduction-token' );
delete_transient( 'correios-cws-staging-token' );
delete_transient( 'correios-cws-production-token' );
