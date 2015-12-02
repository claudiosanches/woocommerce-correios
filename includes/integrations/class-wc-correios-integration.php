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
		$this->id                 = 'correios';
		$this->method_title       = __( 'Correios', 'woocommerce-correios' );
		$this->method_description = __( 'The following options are valid for all Correios shipping methods.', 'woocommerce-correios' );

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
		$this->minimum_height  = $this->get_option( 'minimum_height' );
		$this->minimum_width   = $this->get_option( 'minimum_width' );
		$this->minimum_length  = $this->get_option( 'minimum_length' );

		// Run actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_correios_use_corporate_method', array( $this, 'use_corporate_method' ), 10 );
		add_filter( 'woocommerce_correios_origin_postcode', array( $this, 'setup_origin_postcode' ), 10 );
		add_filter( 'woocommerce_correios_declare_value', array( $this, 'setup_declared_value' ), 10 );
		add_filter( 'woocommerce_correios_own_hands', array( $this, 'setup_own_hands' ), 10 );
		add_filter( 'woocommerce_correios_receipt_notice', array( $this, 'setup_receipt_notice' ), 10 );
		add_filter( 'woocommerce_correios_login', array( $this, 'setup_login' ), 10 );
		add_filter( 'woocommerce_correios_password', array( $this, 'setup_password' ), 10 );
		add_filter( 'woocommerce_correios_package_height', array( $this, 'normalize_package_height' ), 10 );
		add_filter( 'woocommerce_correios_package_width', array( $this, 'normalize_package_width' ), 10 );
		add_filter( 'woocommerce_correios_package_length', array( $this, 'normalize_package_length' ), 10 );
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
				'default'     => 'yes',
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
			'package_standard' => array(
				'title'       => __( 'Package Standard', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Minimum measure for your shipping packages.', 'woocommerce-correios' ),
				'desc_tip'    => true,
			),
			'minimum_height' => array(
				'title'       => __( 'Minimum Height', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum height of your shipping packages. Correios needs at least 2cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '2',
			),
			'minimum_width' => array(
				'title'       => __( 'Minimum Width', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum width of your shipping packages. Correios needs at least 11cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '11',
			),
			'minimum_length' => array(
				'title'       => __( 'Minimum Length', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum length of your shipping packages. Correios needs at least 16cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '16',
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

	/**
	 * Set up origin postcode.
	 *
	 * @param  string $default Default value.
	 *
	 * @return string
	 */
	public function setup_origin_postcode( $default ) {
		return $this->origin_postcode;
	}

	/**
	 * Set up declared value.
	 *
	 * @param  bool $default Default value.
	 *
	 * @return bool
	 */
	public function setup_declared_value( $default ) {
		return 'yes' === $this->declare_value;
	}

	/**
	 * Set up own hands.
	 *
	 * @param  string $default Default value.
	 *
	 * @return string
	 */
	public function setup_own_hands( $default ) {
		return ( 'yes' === $this->own_hands ) ? 'S' : 'N';
	}

	/**
	 * Set up receipt notice.
	 *
	 * @param  string $default Default value.
	 *
	 * @return string
	 */
	public function setup_receipt_notice( $default ) {
		return ( 'yes' === $this->receipt_notice ) ? 'S' : 'N';
	}

	/**
	 * Set up login.
	 *
	 * @param  string $default Default value.
	 *
	 * @return string
	 */
	public function setup_login( $default ) {
		return ( 'corporate' === $this->service_type ) ? $this->login : '';
	}

	/**
	 * Set up password.
	 *
	 * @param  string $default Default value.
	 *
	 * @return string
	 */
	public function setup_password( $default ) {
		return  ( 'corporate' === $this->service_type ) ? $this->password : '';
	}

	/**
	 * Normalize package height.
	 *
	 * @param  int $value Default value.
	 *
	 * @return int
	 */
	public function normalize_package_height( $value ) {
		$minimum = ( 2 <= $this->minimum_height ) ? $this->minimum_height : 2;

		return ( $minimum <= $value ) ? $value : $minimum;
	}

	/**
	 * Normalize package width.
	 *
	 * @param  int $value Default value.
	 *
	 * @return int
	 */
	public function normalize_package_width( $value ) {
		$minimum = ( 11 <= $this->minimum_height ) ? $this->minimum_height : 11;

		return ( $minimum <= $value ) ? $value : $minimum;
	}

	/**
	 * Normalize package length.
	 *
	 * @param  int $value Default value.
	 *
	 * @return int
	 */
	public function normalize_package_length( $value ) {
		$minimum = ( 16 <= $this->minimum_height ) ? $this->minimum_height : 16;

		return ( $minimum <= $value ) ? $value : $minimum;
	}
}
