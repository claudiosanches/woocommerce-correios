<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php if ( isset( $tracking_data->objeto->evento ) ) : ?>

	<h2><?php _e( 'Delivery History', 'woocommerce-correios' ); ?></h2>

	<table class="shop_table shop_table_responsive">
		<tr>
			<th><?php _e( 'Date', 'woocommerce-correios' ); ?></th>
			<th><?php _e( 'Location', 'woocommerce-correios' ); ?></th>
			<th><?php _e( 'Status', 'woocommerce-correios' ); ?></th>
		</tr>

		<?php foreach ( $tracking_data->objeto->evento as $event ): ?>
			<tr>
				<td><?php echo esc_html( $event->data . ' ' . $event->hora ); ?></td>
				<td>
					<?php echo esc_html( $event->local . ' - ' . $event->cidade . '/' . $event->uf ); ?>

					<?php if ( isset( $event->destino ) ) : ?>
						<br />
						<?php printf( __( 'In transit to %s', 'woocommerce-correios' ), esc_html( $event->destino->local . ' - ' . $event->destino->cidade . '/' . $event->destino->uf ) ); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo esc_html( $event->descricao ); ?>
				</td>
			</tr>
		<?php endforeach ?>
	</table>

<?php else : ?>

	<div class="woocommerce-info"><strong><?php _e( 'Correios', 'woocommerce-correios' ); ?>:</strong> <?php printf( __( 'Your the tracking code: %s.', 'woocommerce-correios' ), sprintf( '<a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s" target="_blank">%1$s</a>', $tracking_code ) ); ?></div>

<?php endif;
