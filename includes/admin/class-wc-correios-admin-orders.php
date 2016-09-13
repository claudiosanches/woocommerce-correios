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
		add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'resend_tracking_code_email' ) );
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
			wc_correios_update_tracking_code( $post_id, wp_unslash( $_POST['correios_tracking'] ) );
		}
	}

	/**
	 * Include option to resend the tracking code email.
	 *
	 * @param array $emails List of emails.
	 *
	 * @return array
	 */
	public function resend_tracking_code_email( $emails ) {
		$emails[] = 'correios_tracking';

		return $emails;
	}
}

new WC_Correios_Admin_Orders();
