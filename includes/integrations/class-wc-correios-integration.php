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
		$this->id           = 'correios';
		$this->method_title = __( 'Correios', 'woocommerce-freshdesk' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->origin_postcode = $this->get_option( 'origin_postcode' );
		$this->receipt_notice  = $this->get_option( 'receipt_notice' );
		$this->own_hands       = $this->get_option( 'own_hands' );
		$this->declare_value   = $this->get_option( 'declare_value' );
		$this->service_type    = $this->get_option( 'service_type' );
		$this->login           = $this->get_option( 'login' );
		$this->password        = $this->get_option( 'password' );

		// Run actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_correios_use_corporate_method', array( $this, 'use_corporate_method' ) );
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
			'origin_postcode' => array(
				'title'       => __( 'Origin Postcode', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'The postcode of the location your packages are delivered from.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '00000-000',
			),
			'optional_services' => array(
				'title' => __( 'Optional Services', 'woocommerce-correios' ),
				'type'  => 'title',
			),
			'receipt_notice' => array(
				'title'       => __( 'Receipt Notice', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable receipt notice', 'woocommerce-correios' ),
				'description' => __( 'This controls if the sender must receive a receipt notice when a package is delivered.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'own_hands' => array(
				'title'       => __( 'Own Hands', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable own hands', 'woocommerce-correios' ),
				'description' => __( 'This controls if the package must be delivered exclusively to the recipient printed in its label.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'declare_value' => array(
				'title'       => __( 'Declare Value for Insurance', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable declared value', 'woocommerce-correios' ),
				'description' => __( 'This controls if the price of the package must be declared for insurance purposes.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'service_options' => array(
				'title' => __( 'Service Options', 'woocommerce-correios' ),
				'type'  => 'title',
			),
			'service_type' => array(
				'title'       => __( 'Service Type', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Choose between conventional or corporate service.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'conventional',
				'class'       => 'wc-enhanced-select',
				'options'     => array(
					'conventional' => __( 'Conventional', 'woocommerce-correios' ),
					'corporate'    => __( 'Corporate', 'woocommerce-correios' ),
				),
			),
			'login' => array(
				'title'       => __( 'Administrative Code', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Your Correios login, It\'s usually your CNPJ.', 'woocommerce-correios' ),
				'desc_tip'    => true,
			),
			'password' => array(
				'title'       => __( 'Administrative Password', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Your Correios password.', 'woocommerce-correios' ),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * USe corporate method.
	 * Check if corporate method is enabled.
	 *
	 * @param  bool $default Default value.
	 *
	 * @return bool
	 */
	public function use_corporate_method( $default ) {
		return 'corporate' === $this->service_type;
	}
}
