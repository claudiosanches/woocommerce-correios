<?php
/**
 * WooCommerce orders integration
 *
 * @package WooCommerce_Correios/Classes/Orders
 * @since   3.7.0
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Orders integration.
 */
class WC_Correios_Orders {

	/**
	 * Init orders actions.
	 */
	public function __construct() {
		add_filter( 'woocommerce_order_shipping_method', array( $this, 'shipping_method_delivery_forecast' ), 100, 2 );
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'item_display_delivery_forecast' ), 100, 2 );
	}

	/**
	 * Append delivery forecast in shipping method name.
	 *
	 * @param string   $name  Method name.
	 * @param WC_Order $order Order data.
	 * @return string
	 */
	public function shipping_method_delivery_forecast( $name, $order ) {
		$names = array();

		foreach ( $order->get_shipping_methods() as $shipping_method ) {
			$total = (int) $shipping_method->get_meta( '_delivery_forecast' );

			if ( $total ) {
				/* translators: 1: shipping method name 2: days to delivery */
				$names[] = sprintf( _n( '%1$s (delivery within %2$d working day after paid)', '%1$s (delivery within %2$d working days after paid)', $total, 'woocommerce-correios' ), $shipping_method->get_name(), $total );
			} else {
				$names[] = $shipping_method->get_name();
			}
		}

		return implode( ', ', $names );
	}

	/**
	 * Properly display _delivery_forecast name.
	 *
	 * @param  string       $display_key Display key.
	 * @param  WC_Meta_Data $meta        Meta data.
	 * @return string
	 */
	public function item_display_delivery_forecast( $display_key, $meta ) {
		return '_delivery_forecast' === $meta->key ? __( 'Delivery forecast', 'woocommerce-correios' ) : $display_key;
	}
}

new WC_Correios_Orders();
