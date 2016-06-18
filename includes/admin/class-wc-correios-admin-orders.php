<?php
/**
 * Admin orders actions.
 *
 * @package WooCommerce_Correios/Admin/Orders
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios orders.
 */
class WC_Correios_Admin_Orders {

	/**
	 * Initialize the order actions.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_tracking_code' ) );
	}

	/**
	 * Register tracking code metabox.
	 */
	public function register_metabox() {
		add_meta_box(
			'wc_correios',
			'Correios',
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Tracking code metabox content.
	 *
	 * @param WC_Post $post Post data.
	 */
	public function metabox_content( $post ) {
		echo '<label for="correios_tracking">' . esc_html__( 'Tracking code:', 'woocommerce-correios' ) . '</label><br />';
		echo '<input type="text" id="correios_tracking" name="correios_tracking" value="' . esc_attr( get_post_meta( $post->ID, '_correios_tracking_code', true ) ) . '" style="width: 100%;" />';
	}

	/**
	 * Save tracking code.
	 *
	 * @param int $post_id Current post type ID.
	 */
	public function save_tracking_code( $post_id ) {
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
			return;
		}

		if ( isset( $_POST['correios_tracking'] ) ) {
			$old = get_post_meta( $post_id, '_correios_tracking_code', true );

			$new = sanitize_text_field( wp_unslash( $_POST['correios_tracking'] ) );

			if ( $new && $new != $old ) {
				update_post_meta( $post_id, '_correios_tracking_code', $new );

				// Gets order data.
				$order = wc_get_order( $post_id );

				// Add order note.
				$order->add_order_note( sprintf( __( 'Added a Correios tracking code: %s', 'woocommerce-correios' ), $new ) );

				// Send email notification.
				$this->trigger_email_notification( $order, $new );
			} elseif ( '' == $new && $old ) {
				delete_post_meta( $post_id, '_correios_tracking_code', $old );
			}
		}
	}

	/**
	 * Trigger email notification.
	 *
	 * @param object $order         Order data.
	 * @param string $tracking_code The Correios tracking code.
	 */
	protected function trigger_email_notification( $order, $tracking_code ) {
		$mailer       = WC()->mailer();
		$notification = $mailer->emails['WC_Correios_Tracking_Email'];

		if ( 'yes' === $notification->enabled ) {
			$notification->trigger( $order, $tracking_code );
		}
	}
}

new WC_Correios_Admin_Orders();
