<?php
/**
 * Tracking code.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="wc-correios-tracking" class="woocommerce-info"><strong><?php _e( 'Correios', 'woocommerce-correios' ); ?>:</strong> <?php printf( __( 'Your the tracking code: %s.', 'woocommerce-correios' ), sprintf( '<a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s" target="_blank">%1$s</a>', $code ) ); ?></div>
