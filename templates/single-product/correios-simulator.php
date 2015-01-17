<?php
/**
 * Single Product Shipping Simulator.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_Correios/Templates
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="wc-correios-simulator" style="<?php echo esc_attr( $style ); ?>" data-product-ids="<?php echo esc_attr( $ids ); ?>" data-product-type="<?php echo esc_attr( $product->product_type ); ?>">

	<strong><?php echo esc_html( $title ); ?></strong>
	<p><?php echo esc_html( $description ); ?></p>

	<form method="post" class="cart">
		<input type="text" size="9" class="input-text text" placeholder="00000-000" id="zipcode" name="zipcode" />
		<button class="button" type="submit"><?php _e( 'Calculate', 'woocommerce-correios' ); ?></button>
		<br class="clear" />
		<div id="simulator-data"></div>
	</form>

</div>
