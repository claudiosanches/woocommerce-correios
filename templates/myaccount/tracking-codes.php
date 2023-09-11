<?php
/**
 * Tracking codes.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="wc-correios-tracking-description"><?php echo esc_html( _n( 'Tracking code:', 'Tracking codes:', count( $codes ), 'woocommerce-correios' ) ); ?></p>

<ul class="wc-correios-tracking-list">
	<?php foreach ( $codes as $code ) : ?>
		<li><a href="<?php echo esc_url( wc_correios_get_tracking_url( $code ) ); ?>" target="_blank"><?php echo esc_html( $code ); ?></a></li>
	<?php endforeach; ?>
</ul>
