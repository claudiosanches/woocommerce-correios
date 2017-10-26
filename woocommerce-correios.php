<?php
/**
 * Plugin Name:          WooCommerce Correios
 * Plugin URI:           https://github.com/claudiosanches/woocommerce-correios
 * Description:          Adds Correios shipping methods to your WooCommerce store.
 * Author:               Claudio Sanches
 * Author URI:           https://claudiosanches.com
 * Version:              3.5.1
 * License:              GPLv2 or later
 * Text Domain:          woocommerce-correios
 * Domain Path:          /languages
 * WC requires at least: 3.0.0
 * WC tested up to:      3.2.0
 *
 * WooCommerce Correios is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WooCommerce Correios is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WooCommerce Correios. If not, see
 * <https://www.gnu.org/licenses/gpl-2.0.txt>.
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
		const VERSION = '3.5.1';

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
			add_action( 'init', array( $this, 'load_plugin_textdomain' ), -1 );

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Integration' ) ) {
				$this->includes();

				if ( is_admin() ) {
					$this->admin_includes();
				}

				add_filter( 'woocommerce_integrations', array( $this, 'include_integrations' ) );
				add_filter( 'woocommerce_shipping_methods', array( $this, 'include_methods' ) );
				add_filter( 'woocommerce_email_classes', array( $this, 'include_emails' ) );
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
			if ( null === self::$instance ) {
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
			include_once dirname( __FILE__ ) . '/includes/wc-correios-functions.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-correios-install.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-correios-package.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-correios-webservice.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-correios-webservice-international.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-correios-autofill-addresses.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-correios-tracking-history.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-correios-rest-api.php';

			// Integration.
			include_once dirname( __FILE__ ) . '/includes/integrations/class-wc-correios-integration.php';

			// Shipping methods.
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6.0', '>=' ) ) {
				include_once dirname( __FILE__ ) . '/includes/abstracts/abstract-wc-correios-shipping.php';
				include_once dirname( __FILE__ ) . '/includes/abstracts/abstract-wc-correios-shipping-carta.php';
				include_once dirname( __FILE__ ) . '/includes/abstracts/abstract-wc-correios-shipping-impresso.php';
				include_once dirname( __FILE__ ) . '/includes/abstracts/abstract-wc-correios-shipping-international.php';
				foreach ( glob( plugin_dir_path( __FILE__ ) . '/includes/shipping/*.php' ) as $filename ) {
					include_once $filename;
				}

				// Update settings to 3.0.0 when using WooCommerce 2.6.0.
				WC_Correios_Install::upgrade_300_from_wc_260();
			} else {
				include_once dirname( __FILE__ ) . '/includes/shipping/class-wc-correios-shipping-legacy.php';
			}

			// Update to 3.0.0.
			WC_Correios_Install::upgrade_300();
		}

		/**
		 * Admin includes.
		 */
		private function admin_includes() {
			include_once dirname( __FILE__ ) . '/includes/admin/class-wc-correios-admin-orders.php';
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
			$methods['correios-legacy'] = 'WC_Correios_Shipping_Legacy';

			// New methods.
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6.0', '>=' ) ) {
				$methods['correios-pac']                  = 'WC_Correios_Shipping_PAC';
				$methods['correios-sedex']                = 'WC_Correios_Shipping_SEDEX';
				$methods['correios-sedex10-envelope']     = 'WC_Correios_Shipping_SEDEX_10_Envelope';
				$methods['correios-sedex10-pacote']       = 'WC_Correios_Shipping_SEDEX_10_Pacote';
				$methods['correios-sedex12']              = 'WC_Correios_Shipping_SEDEX_12';
				$methods['correios-sedex-hoje']           = 'WC_Correios_Shipping_SEDEX_Hoje';
				$methods['correios-esedex']               = 'WC_Correios_Shipping_ESEDEX';
				$methods['correios-carta-registrada']     = 'WC_Correios_Shipping_Carta_Registrada';
				$methods['correios-impresso-normal']      = 'WC_Correios_Shipping_Impresso_Normal';
				$methods['correios-impresso-urgente']     = 'WC_Correios_Shipping_Impresso_Urgente';
				$methods['correios-mercadoria-expressa']  = 'WC_Correios_Shipping_Mercadoria_Expressa';
				$methods['correios-mercadoria-economica'] = 'WC_Correios_Shipping_Mercadoria_Economica';
				$methods['correios-leve-internacional']   = 'WC_Correios_Shipping_Leve_Internacional';

				$old_options = get_option( 'woocommerce_correios_settings' );
				if ( empty( $old_options ) ) {
					unset( $methods['correios-legacy'] );
				}
			}

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
				$emails['WC_Correios_Tracking_Email'] = include( dirname( __FILE__ ) . '/includes/emails/class-wc-correios-tracking-email.php' );
			}

			return $emails;
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			include_once dirname( __FILE__ ) . '/includes/admin/views/html-admin-missing-dependencies.php';
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
