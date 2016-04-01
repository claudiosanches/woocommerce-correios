<?php
/**
 * Tracking code HTML email notification.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php echo wptexturize( wpautop( $tracking_message ) ); ?>

<p><?php esc_html_e( 'For your reference, your order details are shown below.', 'woocommerce-correios' ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

<h2><?php echo esc_html( __( 'Order:', 'woocommerce-correios' ) . ' ' . $order->get_order_number() ); ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-correios' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'woocommerce-correios' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-correios' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.5', '>=' ) ) {
				echo $order->email_order_items_table( array(
					'plain_text' => false,
				) );
			} else {
				echo $order->email_order_items_table( true, false, true );
			}
		?>
	</tbody>
	<tfoot>
		<?php if ( $totals = $order->get_order_item_totals() ) :
			$i = 0;
			foreach ( $totals as $total ) :
				$i++;
				?>
				<tr>
					<th scope="row" colspan="2" style="text-align: left; border: 1px solid #eee; <?php echo ( 1 == $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['label']; ?></th>
					<td style="text-align: left; border: 1px solid #eee; <?php echo ( 1 == $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['value']; ?></td>
				</tr>
				<?php
			endforeach;
		endif; ?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

<?php

/**
 * Order meta.
 *
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

/**
 * Customer details.
 *
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text );

/**
 * Email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer.
 */
do_action( 'woocommerce_email_footer' );
