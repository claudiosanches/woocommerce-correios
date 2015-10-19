<?php
/**
 * Tracking code plain email notification.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 2.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $email_heading . "\n\n";

echo $tracking_message . "\n\n";

echo __( 'Your order details are shown below for your reference:', 'woocommerce-correios' ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

echo sprintf( __( 'Order number: %s', 'woocommerce-correios' ), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Order date: %s', 'woocommerce-correios' ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

echo "\n" . $order->email_order_items_table( true, false, true, '', '', true );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );

echo __( 'Your details', 'woocommerce-correios' ) . "\n\n";

if ( $order->billing_email ) {
	echo __( 'Email:', 'woocommerce-correios' ) . ' ' . $order->billing_email . "\n";
}

if ( $order->billing_phone ) {
	echo __( 'Tel:', 'woocommerce-correios' ) . ' ' . $order->billing_phone . "\n";
}

wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
