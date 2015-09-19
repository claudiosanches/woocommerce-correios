<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Correios_Shipping class.
 */
class WC_Correios_Method_Base extends WC_Shipping_Method {

  /**
	 * Initialize the shipping method.
	 *
	 * @return void
	 */
	//public function __construct( $method_id = 'correios_base', $method_name = 'Correios Base' ) {
  public function __construct() {
		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

    // Define user set variables.
		$this->enabled            = $this->get_option( 'enabled', 'yes' );
		$this->title              = $this->get_option( 'title' );
		$this->declare_value      = $this->get_option( 'declare_value', 'no' );
    $this->receipt_notice     = $this->get_option( 'receipt_notice', 'no' );
    $this->own_hands          = $this->get_option( 'own_hands', 'no' );
    $this->declare_value      = $this->get_option( 'declare_value', 'no' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time', 'no' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->additional_fee     = $this->get_option( 'additional_fee' );
    $this->debug              = $this->get_option( 'debug', 'no' );

    // Method variables.
    $this->availability       = 'specific';
    $this->countries          = array( 'BR' );

    // Active logs.
    if ( 'yes' == $this->debug ) {
      $this->log = new WC_Logger();
    }

		// Hooks.
		add_filter( 'woocommerce_shipping_methods', array( $this, 'add_method' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }

	/**
	 * Register the shipping method in Woocommerce.
	 *
	 * @param  string $method
	 */
	public function add_method( $methods ) {
		$methods[] = get_called_class();

		return $methods;
	}

  /**
   * Admin options fields.
   *
   * @return void
   */
  public function init_form_fields() {
    $this->form_fields = array(
      'enabled' => array(
        'title'            => __( 'Enable/disable', 'woocommerce-correios' ),
        'type'             => 'checkbox',
        'label'            => __( 'Enable this shipping method', 'woocommerce-correios' ),
        'default'          => 'no'
      ),
      'title' => array(
        'title'            => __( 'Title', 'woocommerce-correios' ),
        'type'             => 'text',
        'description'      => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
        'desc_tip'         => true,
        'default'          => __( $this->method_title, 'woocommerce-correios' )
      ),
      'show_delivery_time' => array(
        'title'            => __( 'Delivery time', 'woocommerce-correios' ),
        'type'             => 'checkbox',
        'label'            => __( 'Show estimated delivery time', 'woocommerce-correios' ),
        'description'      => __( 'Display the estimated delivery time in working days.', 'woocommerce-correios' ),
        'desc_tip'         => true,
        'default'          => 'no'
      ),
      'additional_time' => array(
        'title'            => __( 'Additional days', 'woocommerce-correios' ),
        'type'             => 'text',
        'description'      => __( 'Additional working days to the estimated delivery.', 'woocommerce-correios' ),
        'desc_tip'         => true,
        'placeholder'      => '0'
      ),
      'additional_fee' => array(
				'title'            => __( 'Handling fee', 'woocommerce-correios' ),
				'type'             => 'text',
				'description'      => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'         => true,
				'placeholder'      => '0.00'
			)
    );
  }

}
