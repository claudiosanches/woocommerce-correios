<?php
/**
 * Tracking code plain email notification.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '= ' . $email_heading . " =\n\n";

echo wptexturize( $tracking_message ) . "\n\n";

echo __( 'For your reference, your order details are shown below.', 'woocommerce-correios' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

printf( __( 'Order number: %s', 'woocommerce-correios' ), $order->get_order_number() ) . "\n";
printf( __( 'Order date: %s', 'woocommerce-correios' ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.5', '>=' ) ) {
	$order_items = $order->email_order_items_table( array(
		'plain_text' => true,
	) );
} else {
	$order_items = $order->email_order_items_table( true, false, true, '', '', true );
}

echo "\n" . $order_items;

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * Order meta.
 *
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

/**
 * Customer details.
 *
 * @hooked WC_Emails::customer_details() Shows customer details.
 * @hooked WC_Emails::email_address() Shows email address.
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
