<?php
/**
 * Tracking history table.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* translators: 1: date 2: time */
$date_format = sprintf( __( '%1$s \a\t %2$s', 'woocommerce-correios' ), get_option( 'date_format' ), get_option( 'time_format' ) );
?>

<p class="wc-correios-tracking-description"><?php esc_html_e( 'History for the tracking code:', 'woocommerce-correios' ); ?> <strong><?php echo esc_html( $code ); ?></strong></p>

<table class="wc-correios-tracking-table woocommerce-table shop_table shop_table_responsive">
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
			<td data-title="<?php esc_attr_e( 'Date', 'woocommerce-correios' ); ?>"><?php echo esc_html( date_i18n( $date_format, strtotime( $event['dtHrCriado'] ) ) ); ?></td>
			<td data-title="<?php esc_attr_e( 'Location', 'woocommerce-correios' ); ?>">
				<?php echo esc_html( $event['unidade']['tipo'] . ' - ' . $event['unidade']['endereco']['cidade'] . '/' . $event['unidade']['endereco']['uf'] ); ?>
			</td>
			<td data-title="<?php esc_attr_e( 'Status', 'woocommerce-correios' ); ?>">
				<?php echo esc_html( $event['descricao'] ); ?>
				<?php if ( isset( $event['unidadeDestino'] ) ) : ?>
					<br />
					<em>
					<?php
						/* translators: %s: address */
						echo esc_html( sprintf( __( 'In transit to %s', 'woocommerce-correios' ), $event['unidadeDestino']['tipo'] . ' - ' . $event['unidadeDestino']['endereco']['cidade'] . '/' . $event['unidadeDestino']['endereco']['uf'] ) );
					?>
					</em>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
