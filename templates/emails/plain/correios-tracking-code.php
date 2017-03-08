<?php
/**
 * Tracking code plain email notification.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 3.2.0
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
$order_date = method_exists( $order, 'get_date_created' ) ? $order->get_date_created() : $order->order_date;
printf( __( 'Order date: %s', 'woocommerce-correios' ), date_i18n( wc_date_format(), strtotime( $order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

echo "\n";

if ( function_exists( 'wc_get_email_order_items' ) ) {
	echo wc_get_email_order_items( $order, array(
		'plain_text' => true,
	) );
} else {
	echo $order->email_order_items_table( array(
		'plain_text' => true,
	) );
}

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
