<?php
/**
 * Correios integration.
 *
 * @package WooCommerce_Correios/Classes/Integration
 * @since   3.0.0
 * @version 4.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios integration class.
 */
class WC_Correios_Integration extends WC_Integration {

	/**
	 * Integration ID.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Integration title.
	 *
	 * @var string
	 */
	public $method_title = '';

	/**
	 * Tracking status.
	 *
	 * @var string
	 */
	public $tracking_enable = '';

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	public $form_fields = array();

	/**
	 * Initialize integration actions.
	 */
	public function __construct() {
		$this->id           = 'correios-integration';
		$this->method_title = __( 'Correios', 'woocommerce-correios' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Actions.
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );

		// Correios Web Service API actions.
		add_filter( 'woocommerce_correios_cws_is_enabled', array( $this, 'setup_cws_status' ), 10 );
		add_filter( 'woocommerce_correios_cws_environment', array( $this, 'setup_cws_environment' ), 10 );
		add_filter( 'woocommerce_correios_cws_user_data', array( $this, 'setup_cws_user_data' ), 10 );
		add_filter( 'woocommerce_correios_cws_debug', array( $this, 'setup_cws_debug' ), 10 );
		add_action( 'wp_ajax_correios_cws_update_services_list', array( $this, 'ajax_update_services_list' ) );

		// Tracking history actions.
		add_filter( 'woocommerce_correios_enable_tracking_history', array( $this, 'setup_tracking_history' ), 10 );
		add_filter( 'woocommerce_correios_get_tracking_link_correios', array( $this, 'setup_tracking_link_correios' ), 10 );

		// Autofill address actions.
		add_filter( 'woocommerce_correios_enable_autofill_addresses', array( $this, 'setup_autofill_addresses' ), 10 );
		add_filter( 'woocommerce_correios_autofill_addresses_validity_time', array( $this, 'setup_autofill_addresses_validity_time' ), 10 );
		add_filter( 'woocommerce_correios_autofill_addresses_force_autofill', array( $this, 'setup_autofill_addresses_force_autofill' ), 10 );
		add_action( 'wp_ajax_correios_autofill_addresses_empty_database', array( $this, 'ajax_empty_database' ) );
	}

	/**
	 * Get log url.
	 *
	 * @return string
	 */
	protected function get_log_link() {
		return ' <a href="' . esc_url( add_query_arg( 'log_file', wc_get_log_file_name( $this->id ), admin_url( 'admin.php?page=wc-status&tab=logs' ) ) ) . '">' . __( 'View logs.', 'woocommerce-correios' ) . '</a>';
	}

	/**
	 * Initialize integration settings fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'cws'                      => array(
				'title'       => __( 'Correios Web Services', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Integrates with the new Correios API. Note that "Username", "Access Code" and "Posting Card" are required to make this integration to work properly.', 'woocommerce-correios' ),
			),
			'cws_environment'          => array(
				'title'   => __( 'Environment', 'woocommerce-correios' ),
				'type'    => 'select',
				'label'   => __( 'Select an environment for your integration with Correios API.', 'woocommerce-correios' ),
				'default' => 'production',
				'options' => array(
					'production' => __( 'Production', 'woocommerce-correios' ),
					'staging'    => __( 'Staging', 'woocommerce-correios' ),
				),
			),
			'cws_username'             => array(
				'title'       => __( 'Portal Meu Correio\'s Username', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => __( 'Your Portal Meu Correio\'s username.', 'woocommerce-correios' ),
				'default'     => '',
			),
			'cws_access_code'          => array(
				'title'       => __( 'Access Code', 'woocommerce-correios' ),
				'type'        => 'password',
				/* translators: %s: Correios URL */
				'description' => sprintf( __( 'Your Correios API Access Code. You can generate an access code in %1$s for production or %2$s for staging.', 'woocommerce-correios' ), '<a href="https://cws.correios.com.br" target="_blank">https://cws.correios.com.br</a>', '<a href="https://cwshom.correios.com.br" target="_blank">https://cwshom.correios.com.br</a>' ),
				'default'     => '',
			),
			'cws_posting_card'         => array(
				'title'       => __( 'Posting Card', 'woocommerce-correios' ),
				'type'        => 'text',
				/* translators: 1: Correios URL */
				'description' => sprintf( __( 'Your Correios Posting Card number. The number is 10 digits long and starts with two zeros. To check your Posting Card number, go to: %1$s', 'woocommerce-correios' ), '<a href="https://apps.correios.com.br/correiosfacil/verificacao" target="_blank">https://apps.correios.com.br/correiosfacil/verificacao</a>' ),
				'default'     => '',
			),
			'cws_update_services_list' => array(
				'title'       => __( 'Generate/Update Services List', 'woocommerce-correios' ),
				'type'        => 'button',
				'label'       => __( 'Update Services List', 'woocommerce-correios' ),
				'description' => __( 'Generates a list with all services available for your Correios\'s contract.', 'woocommerce-correios' ),
			),
			'cws_debug'                => array(
				'title'       => __( 'Debug Log', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging for Correios API', 'woocommerce-correios' ),
				'default'     => 'no',
				/* translators: %s: log link */
				'description' => sprintf( __( 'Log %s events, such as Web Services requests.', 'woocommerce-correios' ), __( 'Correios API', 'woocommerce-correios' ) ) . $this->get_log_link(),
			),
			'tracking'                 => array(
				'title'       => __( 'Tracking History Table', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Displays a table with informations about the shipping in My Account > View Order page. Required username and password that can be obtained with the Correios\' commercial area.', 'woocommerce-correios' ),
			),
			'tracking_enable'          => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Tracking History Table', 'woocommerce-correios' ),
				'default' => 'no',
			),
			'tracking_link_correios'   => array(
				'title'       => __( 'Link Correios', 'woocommerce-correios' ),
				'type'        => 'text',
				/* translators: %s: Correios URL */
				'description' => sprintf( __( 'Custom link to display tracking history of objects from Correios. By default uses %s', 'woocommerce-correios' ), '<a href="https://www.linkcorreios.com.br/?id=" target="_blank">https://www.linkcorreios.com.br/?id=<a>' ),
				'default'     => 'https://www.linkcorreios.com.br/?id=',
			),
			'autofill_addresses'       => array(
				'title'       => __( 'Autofill Addresses', 'woocommerce-correios' ),
				'type'        => 'title',
				'description' => __( 'Enable address autofill based on zipcode during checkout.', 'woocommerce-correios' ),
			),
			'autofill_enable'          => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Autofill Addresses', 'woocommerce-correios' ),
				'default' => 'no',
			),
			'autofill_validity'        => array(
				'title'       => __( 'Postcodes Validity', 'woocommerce-correios' ),
				'type'        => 'select',
				'default'     => 'forever',
				'class'       => 'wc-enhanced-select',
				'description' => __( 'Defines how long a postcode will stay saved in the database before a new query.', 'woocommerce-correios' ),
				'options'     => array(
					'1'       => __( '1 month', 'woocommerce-correios' ),
					/* translators: %s number of months */
					'2'       => sprintf( __( '%d months', 'woocommerce-correios' ), 2 ),
					/* translators: %s number of months */
					'3'       => sprintf( __( '%d months', 'woocommerce-correios' ), 3 ),
					/* translators: %s number of months */
					'4'       => sprintf( __( '%d months', 'woocommerce-correios' ), 4 ),
					/* translators: %s number of months */
					'5'       => sprintf( __( '%d months', 'woocommerce-correios' ), 5 ),
					/* translators: %s number of months */
					'6'       => sprintf( __( '%d months', 'woocommerce-correios' ), 6 ),
					/* translators: %s number of months */
					'7'       => sprintf( __( '%d months', 'woocommerce-correios' ), 7 ),
					/* translators: %s number of months */
					'8'       => sprintf( __( '%d months', 'woocommerce-correios' ), 8 ),
					/* translators: %s number of months */
					'9'       => sprintf( __( '%d months', 'woocommerce-correios' ), 9 ),
					/* translators: %s number of months */
					'10'      => sprintf( __( '%d months', 'woocommerce-correios' ), 10 ),
					/* translators: %s number of months */
					'11'      => sprintf( __( '%d months', 'woocommerce-correios' ), 11 ),
					/* translators: %s number of months */
					'12'      => sprintf( __( '%d months', 'woocommerce-correios' ), 12 ),
					'forever' => __( 'Forever', 'woocommerce-correios' ),
				),
			),
			'autofill_force'           => array(
				'title'       => __( 'Force Autofill', 'woocommerce-correios' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Force Autofill', 'woocommerce-correios' ),
				'description' => __( 'When enabled will autofill all addresses after the user finish to fill the postcode, even if the addresses are already filled.', 'woocommerce-correios' ),
				'default'     => 'no',
			),
			'autofill_empty_database'  => array(
				'title'       => __( 'Empty Database', 'woocommerce-correios' ),
				'type'        => 'button',
				'label'       => __( 'Empty Database', 'woocommerce-correios' ),
				'description' => __( 'Delete all the saved postcodes in the database, use this option if you have issues with outdated postcodes.', 'woocommerce-correios' ),
			),
		);
	}

	/**
	 * Correios options page.
	 */
	public function admin_options() {
		echo '<h2>' . esc_html( $this->get_method_title() ) . '</h2>';
		echo wp_kses_post( wpautop( $this->get_method_description() ) );

		echo '<div id="plugin-correios-settings">';
		echo '<div class="box">';
		echo '<div><input type="hidden" name="section" value="' . esc_attr( $this->id ) . '" /></div>';
		echo '<table class="form-table">' . $this->generate_settings_html( $this->get_form_fields(), false ) . '</table>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
		echo '<div class="box">';
		include WC_Correios::get_plugin_path() . 'includes/admin/views/html-admin-support.php';
		echo '</div>';
		echo '</div>';

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( $this->id . '-admin-styles', plugins_url( 'assets/css/admin/settings.css', WC_Correios::get_main_file() ), array(), WC_CORREIOS_VERSION );
		wp_enqueue_script( $this->id . '-admin', plugins_url( 'assets/js/admin/integration' . $suffix . '.js', WC_Correios::get_main_file() ), array( 'jquery', 'jquery-blockui' ), WC_CORREIOS_VERSION, true );
		wp_localize_script(
			$this->id . '-admin',
			'WCCorreiosIntegrationAdminParams',
			array(
				'i18n_confirm_message'      => __( 'Are you sure you want to delete all postcodes from the database?', 'woocommerce-correios' ),
				'update_cws_services_nonce' => wp_create_nonce( 'woocommerce_correios_update_services_nonce' ),
				'empty_database_nonce'      => wp_create_nonce( 'woocommerce_correios_autofill_addresses_nonce' ),
			)
		);
	}

	/**
	 * Generate Button Input HTML.
	 *
	 * @param string $key  Input key.
	 * @param array  $data Input data.
	 * @return string
	 */
	public function generate_button_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'       => '',
			'label'       => '',
			'desc_tip'    => false,
			'description' => '',
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<button class="button-secondary" type="button" id="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['label'] ); ?></button>
					<?php echo $this->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Setup CWS status.
	 *
	 * @return bool
	 */
	public function setup_cws_status() {
		$data = $this->setup_cws_user_data();
		$data = array_filter( $data );

		return ! empty( $data['username'] ) && ! empty( $data['access_code'] ) && ! empty( $data['posting_card'] );
	}

	/**
	 * Setup Correios Web Service Environment.
	 *
	 * @return string
	 */
	public function setup_cws_environment() {
		$env = $this->get_option( 'cws_environment' );
		return $env ? $env : 'staging';
	}

	/**
	 * Setup Correios Web Service Username.
	 *
	 * @return array
	 */
	public function setup_cws_user_data() {
		return array(
			'username'     => $this->get_option( 'cws_username' ),
			'access_code'  => $this->get_option( 'cws_access_code' ),
			'posting_card' => $this->get_option( 'cws_posting_card' ),
		);
	}

	/**
	 * Setup Correios Web Service Debug log.
	 *
	 * @return string
	 */
	public function setup_cws_debug() {
		return $this->get_option( 'cws_debug' );
	}

	/**
	 * Enable tracking history.
	 *
	 * @return bool
	 */
	public function setup_tracking_history() {
		return 'yes' === $this->get_option( 'tracking_enable' );
	}

	/**
	 * Link Correios integration.
	 *
	 * @param string $code Tracking code.
	 * @return string
	 */
	public function setup_tracking_link_correios( $code ) {
		$link = $this->get_option( 'tracking_link_correios', 'https://www.linkcorreios.com.br/?id=' );

		// Set query string.
		if ( 'https://www.linkcorreios.com.br' === untrailingslashit( $link ) ) {
			$link = $link . '?id=';
		}

		return $link . $code;
	}

	/**
	 * Enable autofill addresses.
	 *
	 * @return bool
	 */
	public function setup_autofill_addresses() {
		return 'yes' === $this->get_option( 'autofill_enable' ) && class_exists( 'SoapClient' );
	}

	/**
	 * Set up autofill addresses validity time.
	 *
	 * @return string
	 */
	public function setup_autofill_addresses_validity_time() {
		return $this->get_option( 'autofill_validity' );
	}

	/**
	 * Set up autofill addresses force autofill.
	 *
	 * @return string
	 */
	public function setup_autofill_addresses_force_autofill() {
		return $this->get_option( 'autofill_force' );
	}

	/**
	 * Ajax update services list.
	 */
	public function ajax_update_services_list() {
		if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing parameters!', 'woocommerce-correios' ) ) );
		}

		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'woocommerce_correios_update_services_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce!', 'woocommerce-correios' ) ) );
		}

		$connect = new WC_Correios_Cws_Connect();
		$list    = $connect->get_available_services( true );

		if ( empty( $list ) ) {
			wp_send_json_error( array( 'message' => __( 'Unable to retrieve services list! Review the Portal Meu Correio\'s Username, Access Code and Posting Card information, save the settings and try again.', 'woocommerce-correios' ) ) );
		}

		wp_send_json_success( array( 'message' => __( 'Services list generated successfully!', 'woocommerce-correios' ) ) );
	}

	/**
	 * Ajax empty database.
	 */
	public function ajax_empty_database() {
		global $wpdb;

		if ( ! isset( $_POST['nonce'] ) ) { // WPCS: input var okay, CSRF ok.
			wp_send_json_error( array( 'message' => __( 'Missing parameters!', 'woocommerce-correios' ) ) );
			exit;
		}

		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'woocommerce_correios_autofill_addresses_nonce' ) ) { // WPCS: input var okay, CSRF ok.
			wp_send_json_error( array( 'message' => __( 'Invalid nonce!', 'woocommerce-correios' ) ) );
			exit;
		}

		$table_name = $wpdb->prefix . WC_Correios_Autofill_Addresses::$table;
		$wpdb->query( "DROP TABLE IF EXISTS $table_name;" ); // @codingStandardsIgnoreLine

		WC_Correios_Autofill_Addresses::create_database();

		wp_send_json_success( array( 'message' => __( 'Postcode database emptied successfully!', 'woocommerce-correios' ) ) );
	}
}
