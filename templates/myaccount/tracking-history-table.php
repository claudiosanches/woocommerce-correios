<?php
/**
 * Tracking history table.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="wc-correios-tracking__description"><?php esc_html_e( 'History for the tracking code:', 'woocommerce-correios' ); ?> <strong><?php echo esc_html( $code ); ?></strong></p>

<table class="wc-correios-tracking__table woocommerce-table shop_table shop_table_responsive">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Date', 'woocommerce-correios' ); ?></th>
			<th><?php esc_html_e( 'Location', 'woocommerce-correios' ); ?></th>
			<th><?php esc_html_e( 'Status', 'woocommerce-correios' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $events as $event ) : ?>
		<tr>
			<td><?php echo esc_html( $event->data . ' ' . $event->hora ); ?></td>
			<td>
				<?php echo esc_html( $event->local . ' - ' . $event->cidade . '/' . $event->uf ); ?>

				<?php if ( isset( $event->destino ) ) : ?>
					<br />
					<?php
						/* translators: %s: address */
						echo esc_html( sprintf( __( 'In transit to %s', 'woocommerce-correios' ), $event->destino->local . ' - ' . $event->destino->cidade . '/' . $event->destino->uf ) );
					?>
				<?php endif; ?>
			</td>
			<td>
				<?php echo esc_html( $event->descricao ); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<form method="POST" target="_blank" rel="nofollow noopener noreferrer" action="http://www2.correios.com.br/sistemas/rastreamento/resultado_semcontent.cfm" class="wc-correios-tracking__form">
					<input type="hidden" name="Objetos" value="<?php echo esc_attr( $code ); ?>">
					<input class="wc-correios-tracking__button button" type="submit" value="<?php esc_attr_e( 'Query on Correios', 'woocommerce-correios' ); ?>">
				</form>
			</td>
		</tr>
	</tfoot>
</table>
