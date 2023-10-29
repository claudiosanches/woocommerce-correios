<?php
/**
 * Correios Webservice API shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   4.0.0
 * @version 4.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Correios Webservice API shipping method class.
 */
class WC_Correios_Shipping_Cws extends WC_Correios_Shipping {

	/**
	 * Shipping class id.
	 *
	 * @var string
	 */
	public $shipping_class_id = '';

	/**
	 * Segments.
	 *
	 * @var string[]
	 */
	protected $segments = array( '3', '6' );

	/**
	 * API type.
	 *
	 * @var string
	 */
	protected $api_type = 'nacional';

	/**
	 * Initialize the Correios shipping method.
	 *
	 * @param int $instance_id Shipping method instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'correios-cws';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Correios (New API)', 'woocommerce-correios' );
		$this->method_description = __( 'Correios shipping method.', 'woocommerce-correios' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
		);

		// Load the form fields.
		$this->init_form_fields();

		$this->enabled           = $this->get_option( 'enabled' );
		$this->title             = $this->get_option( 'title' );
		$this->shipping_class_id = (int) $this->get_option( 'shipping_class_id', '-1' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Load services list only in shipping settings page.
	 *
	 * @return array
	 */
	protected function load_services_list() {
		$default = array(
			'' => __( 'Select a service...', 'woocommerce-correios' ),
		);

		if ( ! function_exists( 'get_current_screen' ) ) {
			return $default;
		}

		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'woocommerce_page_wc-settings' === $screen->id ) {
			$shipping_tab  = isset( $_REQUEST['tab'] ) && 'shipping' === $_REQUEST['tab']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$shipping_zone = isset( $_REQUEST['instance_id'] ) && 0 < intval( $_REQUEST['instance_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $shipping_tab && $shipping_zone ) {
				$connect  = new WC_Correios_Cws_Connect( $this->id, $this->instance_id );
				$segments = apply_filters( 'woocommerce_correios_cws_allowed_segments_ids', $this->segments );
				$list     = $connect->get_available_services( false, $segments );

				return $default + $list;
			}
		}

		return $default;
	}

	/**
	 * Get declaration options.
	 *
	 * @return array<string,string>
	 */
	protected function get_declaration_options() {
		return array(
			''    => __( 'Not declare', 'woocommerce-correios' ),
			'019' => __( '(019) Valor Declarado Nacional Premium e Expresso (use for SEDEX)', 'woocommerce-correios' ),
			'064' => __( '(064) Valor Declarado Nacional Standard (use for PAC)', 'woocommerce-correios' ),
			'065' => __( '(065) Valor Declarado Correios Mini Envios (use for SEDEX Mini)', 'woocommerce-correios' ),
			'075' => __( '(075) Valor Declarado Expresso RFID (SEDEX)', 'woocommerce-correios' ),
			'076' => __( '(076) Valor Declarado Standard RFID (PAC)', 'woocommerce-correios' ),
		);
	}

	/**
	 * Get the declared value from the package.
	 *
	 * @param  array $package Cart package.
	 * @return float
	 */
	protected function get_declared_value( $package ) {
		if ( 24.5 > $package['contents_cost'] ) {
			return 0;
		}

		return $package['contents_cost'];
	}

	/**
	 * Get additional time.
	 *
	 * @param  array $package Package data.
	 *
	 * @return array
	 */
	protected function get_additional_time( $package = array() ) {
		return apply_filters( 'woocommerce_correios_shipping_additional_time', $this->get_option( 'additional_time' ), $package );
	}

	/**
	 * Check if it's possible to calculate the shipping.
	 *
	 * @param  array $package Cart package.
	 * @return bool
	 */
	protected function can_be_calculated( $package ) {
		if ( empty( $package['destination']['postcode'] ) ) {
			return false;
		}

		return 'BR' === $package['destination']['country'];
	}

	/**
	 * Get shipping rate.
	 *
	 * @param  array $package Cart package.
	 *
	 * @return array
	 */
	protected function get_rate( $package ) {
		$calculate = new WC_Correios_Cws_Calculate( $this->id, $this->instance_id );
		$calculate->set_debug( $this->get_option( 'debug' ) );
		$calculate->set_product_code( $this->get_service_code() );
		$calculate->set_package( $package );
		$calculate->set_origin_postcode( $this->get_option( 'origin_postcode' ) );
		$calculate->set_destination_postcode( $package['destination']['postcode'] );
		$calculate->set_destination_country( $package['destination']['country'] );

		if ( '' !== $this->get_option( 'declare_value' ) ) {
			$calculate->set_declared_value_code( $this->get_option( 'declare_value' ) );
			$calculate->set_declared_value( $this->get_declared_value( $package ) );
		}

		$calculate->set_own_hands( 'yes' === $this->get_option( 'own_hands' ) ? 'S' : 'N' );
		$calculate->set_receipt_notice( 'yes' === $this->get_option( 'receipt_notice' ) ? 'S' : 'N' );

		$calculate->set_minimum_height( $this->get_option( 'minimum_height' ) );
		$calculate->set_minimum_width( $this->get_option( 'minimum_width' ) );
		$calculate->set_minimum_length( $this->get_option( 'minimum_length' ) );
		$calculate->set_extra_weight( $this->get_option( 'extra_weight', '0' ) );

		$shipping = $calculate->get_shipping( $this->api_type );

		if ( 'yes' === $this->get_option( 'show_delivery_time' ) && ! empty( $shipping['pcFinal'] ) ) {
			$shipping['prazo'] = $calculate->get_time( $this->api_type );
		}

		return $shipping;
	}

	/**
	 * Admin options fields.
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this shipping method', 'woocommerce-correios' ),
				'default' => 'yes',
			),
			'title'              => array(
				'title'       => __( 'Title', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => $this->method_title,
			),
			'product_code'       => array(
				'title'       => __( 'Service', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Select a service. It list all services available for your contract with Correios.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
				'class'       => 'wc-enhanced-select',
				'options'     => array(
					'' => __( 'Select a service...', 'woocommerce-correios' ),
				),
			),
			'behavior_options'   => array(
				'title'   => __( 'Behavior Options', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'origin_postcode'    => array(
				'title'       => __( 'Origin Postcode', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'The postcode of the location your packages are delivered from.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '00000-000',
				'default'     => $this->get_base_postcode(),
			),
			'shipping_class_id'  => array(
				'title'       => __( 'Shipping Class', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'If necessary, select a shipping class to apply this method.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '',
				'class'       => 'wc-enhanced-select',
				'options'     => $this->get_shipping_classes_options(),
			),
			'show_delivery_time' => array(
				'title'       => __( 'Delivery Time', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Show estimated delivery time', 'woocommerce-correios' ),
				'description' => __( 'Display the estimated delivery time in working days.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'additional_time'    => array(
				'title'       => __( 'Additional Days', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Additional working days to the estimated delivery.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
			),
			'fee'                => array(
				'title'       => __( 'Handling Fee', 'woocommerce-correios' ),
				'type'        => 'price',
				'description' => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'placeholder' => '0.00',
				'default'     => '',
			),
			'optional_services'  => array(
				'title'       => __( 'Optional Services', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Use these options to add the value of each service provided by the Correios.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'receipt_notice'     => array(
				'title'       => __( 'Receipt Notice', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable receipt notice', 'woocommerce-correios' ),
				'description' => __( 'This controls whether to add costs of the receipt notice service.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'own_hands'          => array(
				'title'       => __( 'Own Hands', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable own hands', 'woocommerce-correios' ),
				'description' => __( 'This controls whether to add costs of the own hands service.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'declare_value'      => array(
				'title'       => __( 'Declare Value for Insurance', 'woocommerce-correios' ),
				'type'        => 'select',
				'label'       => __( 'Enable declared value', 'woocommerce-correios' ),
				'description' => __( 'This controls how the price of the package must be declared for insurance purposes.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'class'       => 'wc-enhanced-select',
				'default'     => '',
				'options'     => $this->get_declaration_options(),
			),
			'package_standard'   => array(
				'title'       => __( 'Package Standard', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Minimum measure for your shipping packages.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'minimum_height'     => array(
				'title'       => __( 'Minimum Height (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum height of your shipping packages. Correios needs at least 2cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '2',
			),
			'minimum_width'      => array(
				'title'       => __( 'Minimum Width (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum width of your shipping packages. Correios needs at least 11cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '11',
			),
			'minimum_length'     => array(
				'title'       => __( 'Minimum Length (cm)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Minimum length of your shipping packages. Correios needs at least 16cm.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '16',
			),
			'extra_weight'       => array(
				'title'       => __( 'Extra Weight (kg)', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Extra weight in kilograms to add to the package total when quoting shipping costs.', 'woocommerce-correios' ),
				'desc_tip'    => true,
				'default'     => '0',
			),
			'testing'            => array(
				'title'   => __( 'Testing', 'woocommerce-correios' ),
				'type'    => 'title',
				'default' => '',
			),
			'debug'              => array(
				'title'       => __( 'Debug Log', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-correios' ),
				'default'     => 'no',
				/* translators: %s: method title */
				'description' => sprintf( __( 'Log %s events, such as WebServices requests.', 'woocommerce-correios' ), $this->method_title ) . $this->get_log_link(),
			),
		);

		if ( is_admin() ) {
			$this->instance_form_fields['product_code']['options'] = $this->load_services_list();
		}
	}

	/**
	 * Correios options page.
	 */
	public function admin_options() {
		$cws_needs_setup = 1 >= count( $this->instance_form_fields['product_code']['options'] );

		include WC_Correios::get_plugin_path() . 'includes/admin/views/html-admin-shipping-method-settings.php';
	}

	/**
	 * Get Correios service code.
	 *
	 * @return string
	 */
	public function get_product_code() {
		return $this->get_service_code();
	}

	/**
	 * Get Correios service code.
	 *
	 * @return string
	 */
	public function get_service_code() {
		$code = $this->get_option( 'product_code' );

		return apply_filters( 'woocommerce_correios_shipping_service_code', $code, $this->id, $this->instance_id );
	}

	/**
	 * Get Correios service name.
	 *
	 * @return string
	 */
	public function get_service_name() {
		$code    = $this->get_service_code();
		$connect = new WC_Correios_Cws_Connect( $this->id, $this->instance_id );
		$list    = $connect->get_available_services();
		$name    = $list[ $code ] ?? '';

		return apply_filters( 'woocommerce_correios_shipping_service_name', $name, $code, $this->id, $this->instance_id );
	}

	/**
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 */
	public function calculate_shipping( $package = array() ) {
		// Check if the package can be calculated.
		if ( ! $this->can_be_calculated( $package ) ) {
			return;
		}

		// Check for shipping classes.
		if ( ! $this->has_only_selected_shipping_class( $package ) ) {
			return;
		}

		$shipping = $this->get_rate( $package );

		// No valid rate found, just return.
		if ( empty( $shipping['pcFinal'] ) ) {
			return;
		}

		// Set the shipping rates.
		$label = $this->title;
		$cost  = wc_correios_normalize_price( $shipping['pcFinal'] );

		// Exit if don't have price.
		if ( 0 >= intval( $cost ) ) {
			return;
		}

		// Apply fees.
		$fee = $this->get_fee( $this->get_option( 'fee' ), $cost );

		// Display delivery.
		$meta = array();
		if ( ! empty( $shipping['prazo'] ) ) {
			// Uses prazoEntrega for national shipping methods and prazoMaximo for international shipping methods.
			$delivery_time = isset( $shipping['prazo']['prazoEntrega'] ) ? $shipping['prazo']['prazoEntrega'] : $shipping['prazo']['prazoMaximo'];

			$meta = array(
				'_delivery_forecast' => intval( $delivery_time ) + intval( $this->get_additional_time( $package ) ),
			);
		}

		// Create the rate and apply filters.
		$rate = apply_filters(
			'woocommerce_correios_cws_rate',
			array(
				'id'        => $this->id . $this->instance_id,
				'label'     => $label,
				'cost'      => (float) $cost + (float) $fee,
				'meta_data' => $meta,
			),
			$this->id,
			$this->instance_id,
			$package
		);

		// Add rate to WooCommerce.
		$this->add_rate( $rate );
	}
}
