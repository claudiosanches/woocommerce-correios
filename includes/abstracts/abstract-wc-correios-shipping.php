<?php
/**
 * Abstract Correios shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Correios shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class WC_Correios_Shipping extends WC_Shipping_Method {

	/**
	 * Initialize the Correios shipping method.
	 */
	public function __construct() {
		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enabled            = $this->get_option( 'enabled' );
		$this->title              = $this->get_option( 'title' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->fee                = $this->get_option( 'fee' );
		$this->debug              = $this->get_option( 'debug' );

		// Method variables.
		$this->availability       = 'specific';
		$this->countries          = array( 'BR' );

		// Active logs.
		if ( 'yes' == $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Save admin options.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_view() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			return ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'View logs.', 'woocommerce-correios' ) . '</a>';
		}
	}

	/**
	 * Admin options fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'            => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable this shipping method', 'woocommerce-correios' ),
				'default'          => 'no',
			),
			'title' => array(
				'title'            => __( 'Title', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => $this->method_title,
			),
			'show_delivery_time' => array(
				'title'            => __( 'Delivery time', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Show estimated delivery time', 'woocommerce-correios' ),
				'description'      => __( 'Display the estimated delivery time in working days.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => 'no',
			),
			'additional_time' => array(
				'title'            => __( 'Additional days', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Additional working days to the estimated delivery.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'default'          => '0',
				'placeholder'      => '0',
			),
			'fee' => array(
				'title'            => __( 'Handling Fee', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'placeholder'      => '0.00',
			),
			'testing' => array(
				'title'            => __( 'Testing', 'woocommerce-correios' ),
				'type'             => 'title',
			),
			'debug' => array(
				'title'            => __( 'Debug Log', 'woocommerce-correios' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable logging', 'woocommerce-correios' ),
				'default'          => 'no',
				'description'      => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), $this->method_title ) . $this->get_log_view(),
			),
		);
	}

	/**
	 * Correios options page.
	 */
	public function admin_options() {
		include WC_Correios::get_plugin_path() . 'includes/admin/views/html-admin-shipping-method-settings.php';
	}

	/**
	 * Check if need to use corporate services.
	 *
	 * @return bool
	 */
	protected function is_corporate() {
		return get_option( 'woocommerce_correios_service_type', 'conventional' ) == 'corporate';
	}
}
