<?php
/**
 * Plugin Name: WooCommerce Correios
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-correios
 * Description: Correios para WooCommerce
 * Author: Claudio Sanches, Thiago Benvenuto
 * Author URI: http://claudiosmweb.com/
 * Version: 3.0.0
 * License: GPLv2 or later
 * Text Domain: woocommerce-correios
 * Domain Path: languages/
 *
 * @package WooCommerce_Correios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Correios' ) ) :

	/**
	 * WooCommerce Correios main class.
	 */
	class WC_Correios {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '3.0.0';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin public actions.
		 */
		private function __construct() {
			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Shipping_Method' ) ) {
				$this->includes();

				if ( is_admin() ) {
					$this->admin_includes();
				}

				add_filter( 'woocommerce_shipping_methods', array( $this, 'include_methods' ) );
				add_action( 'wp_ajax_wc_correios_simulator', array( 'WC_Correios_Product_Shipping_Simulator', 'ajax_simulator' ) );
				add_action( 'wp_ajax_nopriv_wc_correios_simulator', array( 'WC_Correios_Product_Shipping_Simulator', 'ajax_simulator' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

				add_filter( 'woocommerce_shipping_settings', array( $this, 'add_custom_settings' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return plugin_dir_path( __FILE__ ) . 'templates/';
		}

		/**
		 * Get plugin path.
		 *
		 * @return string
		 */
		public static function get_plugin_path() {
			return plugin_dir_path( __FILE__ );
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-correios', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Includes.
		 */
		private function includes() {
			include_once 'includes/class-wc-correios-error.php';
			include_once 'includes/class-wc-correios-package.php';
			include_once 'includes/class-wc-correios-connect.php';
			include_once 'includes/class-wc-correios-product-shipping-simulator.php';
			include_once 'includes/class-wc-correios-emails.php';
			include_once 'includes/class-wc-correios-tracking-history.php';

			// Shipping methods.
			include_once 'includes/abstracts/abstract-wc-correios-shipping.php';
			foreach ( glob( plugin_dir_path( __FILE__ ) . 'includes/shipping/*.php' ) as $filename ) {
				include_once $filename;
			}
		}

		/**
		 * Admin includes.
		 */
		private function admin_includes() {
			include_once 'includes/admin/class-wc-correios-admin-orders.php';
		}

		/**
		 * Action links.
		 *
		 * @param  array $links Plugin action links.
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array();

			$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=wc_correios_shipping' ) ) . '">' . __( 'Settings', 'woocommerce-correios' ) . '</a>';

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Include Correios shipping methods to WooCommerce.
		 *
		 * @param  array $methods Default shipping methods.
		 *
		 * @return array
		 */
		public function include_methods( $methods ) {
			$methods[] = 'WC_Correios_Shipping_PAC';
			$methods[] = 'WC_Correios_Shipping_SEDEX';
			$methods[] = 'WC_Correios_Shipping_SEDEX_10';
			$methods[] = 'WC_Correios_Shipping_SEDEX_Hoje';
			$methods[] = 'WC_Correios_Shipping_ESEDEX';
			$methods[] = 'WC_Correios_Shipping_Registered_Letter';

			return $methods;
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			include 'includes/admin/views/html-admin-missing-dependencies.php';
		}

		/**
		 * Add custom fields in Woocommerce Shipping settings tab.
		 *
		 * @param  array $settings Woocommerce existing settings.
		 *
		 * @return array           New array of settings
		 */
		public function add_custom_settings( $settings ) {
			$new_settings = array(
				array(
					'name'          => __( 'Correios Options', 'woocommerce-correios' ),
					'id'            => 'correios_settings',
					'type'          => 'title',
				),
				array(
					'name'          => __( 'Origin Postcode', 'woocommerce-correios' ),
					'desc'          => __( 'The postcode of the location your packages are delivered from.', 'woocommerce-correios' ),
					'id'            => 'woocommerce_correios_origin_postcode',
					'type'          => 'text',
					'css'           => 'min-width:350px;',
					'std'           => '',  // WC < 2.0.
					'default'       => '',  // WC >= 2.0.
					'placeholder'   => '00000-000',
					'desc_tip'      => true,
				),
				array(
					'title'         => __( 'Optional Services', 'woocommerce-correios' ),
					'desc'          => __( 'Receipt notice', 'woocommerce-correios' ),
					'id'            => 'woocommerce_correios_receipt_notice',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'std'           => 'no', // WC < 2.0.
					'default'       => 'no', // WC >= 2.0.
					'desc_tip'      => __( 'This controls if the sender must receive a receipt notice when a package is delivered.', 'woocommerce-correios' ),
				),
				array(
					'desc'          => __( 'Own hands', 'woocommerce-correios' ),
					'id'            => 'woocommerce_correios_own_hands',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
					'std'           => 'no', // WC < 2.0.
					'default'       => 'no', // WC >= 2.0.
					'desc_tip'      => __( 'This controls if the package must be delivered exclusively to the recipient printed in its label.', 'woocommerce-correios' ),
				),
				array(
					'desc'          => __( 'Declare value for insurance', 'woocommerce-correios' ),
					'id'            => 'woocommerce_correios_declare_value',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
					'std'           => 'no', // WC < 2.0.
					'default'       => 'no', // WC >= 2.0.
					'desc_tip'      => __( 'This controls if the price of the package must be declared for insurance purposes.', 'woocommerce-correios' ),
				),
				array(
					'name'          => __( 'Service Type', 'woocommerce-correios' ),
					'desc'          => __( 'Choose between conventional or corporate service.', 'woocommerce-correios' ),
					'id'            => 'woocommerce_correios_service_type',
					'type'          => 'select',
					'css'           => 'min-width:350px;',
					'std'           => 'conventional',  // WC < 2.0.
					'default'       => 'conventional',  // WC >= 2.0.
					'options'       => array(
						'conventional' => __( 'Conventional', 'woocommerce-correios' ),
						'corporate'    => __( 'Corporate', 'woocommerce-correios' ),
					),
					'desc_tip'      => true,
					'class'         => 'wc-enhanced-select',
				),
				array(
					'name'          => __( 'Administrative Code', 'woocommerce-correios' ),
					'desc'          => __( 'Your Correios login.', 'woocommerce-correios' ),
					'id'            => 'woocommerce_correios_login',
					'type'          => 'text',
					'css'           => 'min-width:350px;',
					'std'           => '',  // WC < 2.0.
					'default'       => '',  // WC >= 2.0.
					'desc_tip'      => true,
				),
				array(
					'name'          => __( 'Administrative Password', 'woocommerce-correios' ),
					'desc'          => __( 'Your Correios password.', 'woocommerce-correios' ),
					'id'            => 'woocommerce_correios_password',
					'type'          => 'password',
					'css'           => 'min-width:350px;',
					'std'           => '',  // WC < 2.0.
					'default'       => '',  // WC >= 2.0.
					'desc_tip'      => true,
				),
				array(
					'id'            => 'correios_settings',
					'type'          => 'sectionend',
				),
			);

			return array_merge( $new_settings, $settings );
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Correios', 'get_instance' ) );

endif;
