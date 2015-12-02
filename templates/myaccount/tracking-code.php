<?php
/**
 * Tracking code.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="wc-correios-tracking" class="woocommerce-info">
	<strong><?php esc_html_e( 'Correios', 'woocommerce-correios' ); ?>:</strong> <?php esc_html_e( 'Your the tracking code:', 'woocommerce-correios' ); ?> <a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=<?php echo esc_attr( $code ); ?>" target="_blank"><?php echo esc_html( $code ) ?></a>.
</div>
