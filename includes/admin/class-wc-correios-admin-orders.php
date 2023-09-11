<?php
/**
 * Admin orders actions.
 *
 * @package WooCommerce_Correios/Admin/Orders
 * @since   3.0.0
 * @version 4.1.1
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
		add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'resend_tracking_code_email' ) );
		add_action( 'wp_ajax_woocommerce_correios_add_tracking_code', array( $this, 'ajax_add_tracking_code' ) );
		add_action( 'wp_ajax_woocommerce_correios_remove_tracking_code', array( $this, 'ajax_remove_tracking_code' ) );

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'tracking_code_orders_list' ), 100, 2 );
		}
	}

	/**
	 * Display tracking code into orders list.
	 *
	 * @param string $column  Current column.
	 * @param int    $post_id Post ID.
	 */
	public function tracking_code_orders_list( $column, $post_id ) {
		if ( 'shipping_address' === $column ) {
			$order = wc_get_order( $post_id );

			if ( ! $order ) {
				return;
			}

			$codes = wc_correios_get_tracking_codes( $order );
			if ( ! empty( $codes ) ) {
				$tracking_codes = array();
				foreach ( $codes as $code ) {
					$tracking_codes[] = '<a href="' . esc_url( wc_correios_get_tracking_url( $code ) ) . '" aria-label="' . esc_attr__( 'Tracking code', 'woocommerce-correios' ) . '" target="_blank">' . esc_html( $code ) . '</a>';
				}

				include __DIR__ . '/views/html-list-table-tracking-code.php';
			}
		}
	}

	/**
	 * Register tracking code metabox.
	 */
	public function register_metabox() {
		$screen = 'shop_order';

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '7.1', '>=' ) ) {
			$hpos_enabled = wc_get_container()->get( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled();
			$screen       = $hpos_enabled ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		}

		add_meta_box(
			'wc-correios',
			'Correios',
			array( $this, 'metabox_content' ),
			$screen,
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
		$order          = ( $post instanceof WP_Post ) ? wc_get_order( $post->ID ) : $post;
		$order_id       = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->ID;
		$tracking_codes = wc_correios_get_tracking_codes( $order_id );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'woocommerce-correios-orders-admin', plugins_url( 'assets/css/admin/orders.css', WC_Correios::get_main_file() ), array(), WC_CORREIOS_VERSION );
		wp_enqueue_script( 'woocommerce-correios-orders-admin', plugins_url( 'assets/js/admin/orders' . $suffix . '.js', WC_Correios::get_main_file() ), array( 'jquery', 'jquery-blockui', 'wp-util' ), WC_CORREIOS_VERSION, true );
		wp_localize_script(
			'woocommerce-correios-orders-admin',
			'WCCorreiosAdminOrdersParams',
			array(
				'order_id' => $order_id,
				'i18n'     => array(
					'removeQuestion' => esc_js( __( 'Are you sure you want to remove this tracking code?', 'woocommerce-correios' ) ),
				),
				'nonces'   => array(
					'add'    => wp_create_nonce( 'woocommerce-correios-add-tracking-code' ),
					'remove' => wp_create_nonce( 'woocommerce-correios-remove-tracking-code' ),
				),
			)
		);

		include_once __DIR__ . '/views/html-meta-box-tracking-code.php';
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

	/**
	 * Ajax - Add tracking code.
	 */
	public function ajax_add_tracking_code() {
		check_ajax_referer( 'woocommerce-correios-add-tracking-code', 'security' );

		$args = array(
			'order_id'      => isset( $_REQUEST['order_id'] ) ? intval( $_REQUEST['order_id'] ) : 0,
			'tracking_code' => isset( $_REQUEST['tracking_code'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tracking_code'] ) ) : '',
		);

		$order = wc_get_order( $args['order_id'] );

		wc_correios_update_tracking_code( $order, $args['tracking_code'] );

		$tracking_codes = wc_correios_get_tracking_codes( $order );

		wp_send_json_success( $tracking_codes );
	}

	/**
	 * Ajax - Remove tracking code.
	 */
	public function ajax_remove_tracking_code() {
		check_ajax_referer( 'woocommerce-correios-remove-tracking-code', 'security' );

		$args = array(
			'order_id'      => isset( $_REQUEST['order_id'] ) ? intval( $_REQUEST['order_id'] ) : 0,
			'tracking_code' => isset( $_REQUEST['tracking_code'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tracking_code'] ) ) : '',
		);

		wc_correios_update_tracking_code( $args['order_id'], $args['tracking_code'], true );

		wp_send_json_success();
	}
}

new WC_Correios_Admin_Orders();
