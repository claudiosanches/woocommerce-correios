<?php
/**
 * Correios integration.
 *
 * @package WooCommerce_Correios/Classes/Integration
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios integration class.
 */
class WC_Correios_Integration extends WC_Integration {

	/**
	 * Initialize integration actions.
	 */
	public function __construct() {
		$this->id                 = 'correios-integration';
		$this->method_title       = __( 'Correios', 'woocommerce-correios' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enable_tracking = $this->get_option( 'enable_tracking' );
		$this->tracking_debug  = $this->get_option( 'tracking_debug' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_correios_enable_tracking_history', array( $this, 'setup_tracking_history' ), 10 );
		add_filter( 'woocommerce_correios_enable_tracking_debug', array( $this, 'setup_tracking_debug' ), 10 );
	}

	/**
	 * Get tracking log url.
	 *
	 * @return string
	 */
	protected function get_tracking_log_link() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			return ' <a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=correios-tracking-history-' . sanitize_file_name( wp_hash( 'correios-tracking-history' ) ) . '.log' ) ) . '">' . __( 'View logs.', 'woocommerce-correios' ) . '</a>';
		}
	}

	/**
	 * Initialize integration settings fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'general_options' => array(
				'title' => __( 'General Options', 'woocommerce-correios' ),
				'type'  => 'title',
			),
			'tracking' => array(
				'title'       => __( 'Tracking History Table', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Displays a table with informations about the shipping in My Account > View Order page.', 'woocommerce-correios' ),
			),
			'enable_tracking' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Tracking History Table', 'woocommerce-correios' ),
				'default' => 'no',
			),
			'tracking_debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging for Tracking History', 'woocommerce-correios' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), __( 'Tracking History Table', 'woocommerce-correios' ) ) . $this->get_tracking_log_link(),
			),
		);
	}

	/**
	 * Correios options page.
	 */
	public function admin_options() {
		include WC_Correios::get_plugin_path() . 'includes/admin/views/html-admin-integration-settings.php';
	}

	/**
	 * Set up tracking history.
	 *
	 * @return string
	 */
	public function setup_tracking_history() {
		return 'yes' === $this->enable_tracking;
	}

	/**
	 * Set up tracking debug.
	 *
	 * @return string
	 */
	public function setup_tracking_debug() {
		return 'yes' === $this->tracking_debug;
	}
}
