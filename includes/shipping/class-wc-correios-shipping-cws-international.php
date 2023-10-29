<?php
/**
 * Correios International Webservice API shipping method.
 *
 * @package WooCommerce_Correios/Classes/Shipping
 * @since   4.2.0
 * @version 4.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Correios International Webservice API shipping method class.
 */
class WC_Correios_Shipping_Cws_International extends WC_Correios_Shipping_Cws {

	/**
	 * Segments.
	 *
	 * @var string[]
	 */
	protected $segments = array( '5' );

	/**
	 * API type.
	 *
	 * @var string
	 */
	protected $api_type = 'internacional';

	/**
	 * Initialize the Correios International shipping method.
	 *
	 * @param int $instance_id Shipping method instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'correios-cws-international';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Correios International (New API)', 'woocommerce-correios' );
		$this->method_description = __( 'Correios International shipping method.', 'woocommerce-correios' );
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
	 * Get declaration options.
	 *
	 * @return array<string,string>
	 */
	protected function get_declaration_options() {
		return array(
			''    => __( 'Not declare', 'woocommerce-correios' ),
			'030' => __( '(030) Valor Declarado Internacional', 'woocommerce-correios' ),
		);
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

		return 'BR' !== $package['destination']['country'];
	}
}
