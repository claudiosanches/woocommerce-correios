<?php
/**
 * WooCommerce cart integration
 *
 * @package WooCommerce_Correios/Classes/Cart
 * @since   3.7.0
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cart integration.
 */
class WC_Correios_Cart {

	/**
	 * Init cart actions.
	 */
	public function __construct() {
		add_action( 'woocommerce_after_shipping_rate', array( $this, 'shipping_delivery_forecast' ), 100 );
	}

	/**
	 * Adds delivery forecast after method name.
	 *
	 * @param WC_Shipping_Rate $shipping_method Shipping method data.
	 */
	public function shipping_delivery_forecast( $shipping_method ) {
		$meta_data = $shipping_method->get_meta_data();
		$total     = isset( $meta_data['_delivery_forecast'] ) ? intval( $meta_data['_delivery_forecast'] ) : 0;

		if ( $total ) {
			/* translators: %d: days to delivery */
			echo '<p><small>' . esc_html( sprintf( _n( 'Delivery within %d working day', 'Delivery within %d working days', $total, 'woocommerce-correios' ), $total ) ) . '</small></p>';
		}
	}
}

new WC_Correios_Cart();
