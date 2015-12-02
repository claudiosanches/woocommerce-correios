<?php
/**
 * Tracking history table.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<h2 id="wc-correios-tracking"><?php esc_html_e( 'Correios Delivery History', 'woocommerce-correios' ); ?></h2>

<p><?php esc_html_e( 'History for the tracking code:', 'woocommerce-correios' ); ?> <a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=<?php echo esc_attr( $code ); ?>" target="_blank"><?php echo esc_html( $code ) ?></a>.</p>

<table class="shop_table shop_table_responsive">
	<tr>
		<th><?php esc_html_e( 'Date', 'woocommerce-correios' ); ?></th>
		<th><?php esc_html_e( 'Location', 'woocommerce-correios' ); ?></th>
		<th><?php esc_html_e( 'Status', 'woocommerce-correios' ); ?></th>
	</tr>

	<?php foreach ( $events as $event ) : ?>
		<tr>
			<td><?php echo esc_html( $event->data . ' ' . $event->hora ); ?></td>
			<td>
				<?php echo esc_html( $event->local . ' - ' . $event->cidade . '/' . $event->uf ); ?>

				<?php if ( isset( $event->destino ) ) : ?>
					<br />
					<?php echo esc_html( sprintf( __( 'In transit to %s', 'woocommerce-correios' ), $event->destino->local . ' - ' . $event->destino->cidade . '/' . $event->destino->uf ) ); ?>
				<?php endif; ?>
			</td>
			<td>
				<?php echo esc_html( $event->descricao ); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
