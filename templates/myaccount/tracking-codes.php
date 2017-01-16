<?php
/**
 * Tracking codes.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="wc-correios-tracking__description"><?php echo esc_html( _n( 'Tracking code:', 'Tracking codes:', count( $codes ), 'woocommerce-correios' ) ); ?></p>

<ul class="wc-correios-tracking__list">
	<?php foreach ( $codes as $code ) : ?>
		<li><a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=<?php echo esc_attr( $code ); ?>" target="_blank"><?php echo esc_html( $code ) ?></a></li>
	<?php endforeach; ?>
</ul>
