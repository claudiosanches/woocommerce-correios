<?php
/**
 * Plugin Name: WooCommerce Correios
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-correios
 * Description: Correios para WooCommerce
 * Author: Claudio Sanches, Thiago Benvenuto
 * Author URI: http://claudiosmweb.com/
 * Version: 3.0.0-beta1
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
		const VERSION = '3.0.0-beta1';

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
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Shipping_Method' ) ) {
				$this->includes();

				if ( is_admin() ) {
					$this->admin_includes();
				}

				add_filter( 'woocommerce_integrations', array( $this, 'include_integrations' ) );
				add_filter( 'woocommerce_shipping_methods', array( $this, 'include_methods' ) );
				add_filter( 'woocommerce_email_classes', array( $this, 'include_emails' ) );

				// Action links.
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
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
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-correios', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Includes.
		 */
		private function includes() {
			include_once 'includes/wc-correios-functions.php';
			include_once 'includes/class-wc-correios-package.php';
			include_once 'includes/class-wc-correios-webservice.php';
			include_once 'includes/class-wc-correios-webservice-international.php';
			include_once 'includes/class-wc-correios-tracking-history.php';

			// Integration.
			include_once 'includes/integrations/class-wc-correios-integration.php';

			// Shipping methods.
			include_once 'includes/abstracts/abstract-wc-correios-shipping.php';
			include_once 'includes/abstracts/abstract-wc-correios-international-shipping.php';
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

			$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=correios' ) ) . '">' . __( 'Settings', 'woocommerce-correios' ) . '</a>';

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Include Correios integration to WooCommerce.
		 *
		 * @param  array $integrations Default integrations.
		 *
		 * @return array
		 */
		public function include_integrations( $integrations ) {
			$integrations[] = 'WC_Correios_Integration';

			return $integrations;
		}

		/**
		 * Include Correios shipping methods to WooCommerce.
		 *
		 * @param  array $methods Default shipping methods.
		 *
		 * @return array
		 */
		public function include_methods( $methods ) {
			// Legacy method.
			// $methods[] = 'WC_Correios_Shipping_Legacy';

			// New methods.
			$methods['correios-pac']                  = 'WC_Correios_Shipping_PAC';
			$methods['correios-sedex']                = 'WC_Correios_Shipping_SEDEX';
			$methods['correios-sedex10-envelope']     = 'WC_Correios_Shipping_SEDEX_10_Envelope';
			$methods['correios-sedex10-pacote']       = 'WC_Correios_Shipping_SEDEX_10_Pacote';
			$methods['correios-sedex12']              = 'WC_Correios_Shipping_SEDEX_12';
			$methods['correios-sedex-hoje']           = 'WC_Correios_Shipping_SEDEX_Hoje';
			$methods['correios-esedex']               = 'WC_Correios_Shipping_ESEDEX';
			$methods['correios-carta-registrada']     = 'WC_Correios_Shipping_Carta_Registrada';
			// $methods['correios-mercadoria-expressa']  = 'WC_Correios_Shipping_Mercadoria_Expressa';
			// $methods['correios-mercadoria-economica'] = 'WC_Correios_Shipping_Mercadoria_Economica';
			// $methods['correios-leve-internacional']   = 'WC_Correios_Shipping_Leve_Internacional';

			return $methods;
		}

		/**
		 * Include emails.
		 *
		 * @param  array $emails Default emails.
		 *
		 * @return array
		 */
		public function include_emails( $emails ) {
			if ( ! isset( $emails['WC_Correios_Tracking_Email'] ) ) {
				$emails['WC_Correios_Tracking_Email'] = include( 'includes/emails/class-wc-correios-tracking-email.php' );
			}

			return $emails;
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			include_once 'includes/admin/views/html-admin-missing-dependencies.php';
		}

		/**
		 * Get main file.
		 *
		 * @return string
		 */
		public static function get_main_file() {
			return __FILE__;
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
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return self::get_plugin_path() . 'templates/';
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Correios', 'get_instance' ) );

endif;
