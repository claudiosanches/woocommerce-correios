<?php
/**
 * Tracking codes.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="wc-correios-tracking__description"><?php echo esc_html( _n( 'Tracking code:', 'Tracking codes:', count( $codes ), 'woocommerce-correios' ) ); ?></p>

<table class="wc-correios-tracking__table woocommerce-table shop_table shop_table_responsive">
	<tbody>
		<?php foreach ( $codes as $code ) : ?>
			<tr>
				<th><?php echo esc_html( $code ); ?></th>
				<td>
					<form method="POST" target="_blank" rel="nofollow noopener noreferrer" action="https://www2.correios.com.br/sistemas/rastreamento/resultado.cfm" class="wc-correios-tracking__form">
						<input type="hidden" name="Objetos" value="<?php echo esc_attr( $code ); ?>">
						<input class="wc-correios-tracking__button button" type="submit" value="<?php esc_attr_e( 'Query on Correios', 'woocommerce-correios' ); ?>">
					</form>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
