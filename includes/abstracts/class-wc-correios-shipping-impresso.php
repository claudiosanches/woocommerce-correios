<?php
/**
 * Abstract Correios Impresso shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.1.0
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Correios Impresso shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class WC_Correios_Shipping_Impresso extends WC_Correios_Shipping_Carta {

	/**
	 * National Registry cost.
	 *
	 * Cost based in 01/08/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-adicionais-nacionais	 
	 *
	 * @var float
	 */
	protected $national_registry_cost = 5.75;

	/**
	 * Reasonable Registry cost.
	 *
	 * Cost based in 01/08/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 * @var float
	 */
	protected $reasonable_registry_cost = 2.90;

	/**
	 * Receipt Notice cost.
	 *
	 * Cost based in 01/08/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 * @var float
	 */
	protected $receipt_notice_cost = 5.75;

	/**
	 * Own Hands cost.
	 *
	 * Cost based in 01/08/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 * @var float
	 */
	protected $own_hands_cost = 6.80;

	/**
	 * Weight limit for reasonable registry.
	 *
	 * Value based in 01/02/2018 from:
	 * https://www.correios.com.br/precos-e-prazos/servicos-nacionais/impresso-normal
	 *
	 * @var float
	 */
	protected $reasonable_registry_weight_limit = 2000.000;

	/**
	 * Initialize Impresso shipping method.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->instance_id        = absint( $instance_id );
		$this->method_description = sprintf( __( '%s is a shipping method from Correios.', 'woocommerce-correios' ), $this->method_title );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Define user set variables.
		$this->enabled            = $this->get_option( 'enabled' );
		$this->title              = $this->get_option( 'title' );
		$this->shipping_class     = $this->get_option( 'shipping_class' );
		$this->registry_type      = $this->get_option( 'registry_type' );
		$this->show_delivery_time = $this->get_option( 'show_delivery_time' );
		$this->additional_time    = $this->get_option( 'additional_time' );
		$this->extra_weight       = $this->get_option( 'extra_weight', '0' );
		$this->fee                = $this->get_option( 'fee' );
		$this->receipt_notice     = $this->get_option( 'receipt_notice' );
		$this->own_hands          = $this->get_option( 'own_hands' );
		$this->debug              = $this->get_option( 'debug' );

		// Active logs.
		if ( 'yes' === $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Save admin options.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Admin options fields.
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this shipping method', 'woocommerce-correios' ),
				'default' => 'yes',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => $this->method_title,
			),
			'behavior_options' => array(
				'title'   => __( 'Behavior Options', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'shipping_class' => array(
				'title'       => __( 'Shipping Class', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Select for which shipping class this method will be applied.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
				'class'       => 'wc-enhanced-select',
				'options'     => $this->get_shipping_classes_options(),
			),
			'registry_type' => array(
				'title'       => __( 'Registry Type', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Select for which registry type this method will be applied.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
				'class'       => 'wc-enhanced-select',
				'options'     => array(
					''   => __( '-- Select a registry type --', 'woocommerce-correios' ),
					'RN' => __( 'Registro Nacional', 'woocommerce-correios' ),
					'RM' => __( 'Registro Módico', 'woocommerce-correios' ),
				),
				'default'     => 'RM',
			),
			'show_delivery_time' => array(
				'title'       => __( 'Delivery Time', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Show estimated delivery time', 'woocommerce-correios' ),
				'description' => __( 'Display the estimated delivery time in working days.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'additional_time' => array(
				'title'       => __( 'Delivery Days', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Working days to the estimated delivery.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
			),
			'extra_weight' => array(
				'title'       => __( 'Extra Weight (g)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Extra weight in grams to add to the package total when quoting shipping costs.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '0',
			),
			'fee' => array(
				'title'       => __( 'Handling Fee', 'woocommerce-correios' ),
				'type'        => 'price',
				'description' => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '0.00',
				'default'     => '',
			),
			'optional_services' => array(
				'title'       => __( 'Optional Services', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Use these options to add the value of each service provided by the Correios.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'receipt_notice' => array(
				'title'       => __( 'Receipt Notice', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable receipt notice', 'woocommerce-correios' ),
				'description' => __( 'This controls whether to add costs of the receipt notice service.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'own_hands' => array(
				'title'       => __( 'Own Hands', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable own hands', 'woocommerce-correios' ),
				'description' => __( 'This controls whether to add costs of the own hands service', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'testing' => array(
				'title'   => __( 'Testing', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-correios' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), $this->method_title ) . $this->get_log_link(),
			),
		);
	}
}
