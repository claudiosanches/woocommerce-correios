<?php
/**
 * Correios
 *
 * @package WooCommerce_Correios/Classes
 * @since   3.6.0
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugins main class.
 */
class WC_Correios {

	/**
	 * Initialize the plugin public actions.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ), -1 );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Integration' ) ) {
			add_action( 'before_woocommerce_init', array( __CLASS__, 'setup_hpos_compatibility' ) );

			self::includes();

			// Run updates.
			self::update();

			if ( is_admin() ) {
				self::admin_includes();
			}

			add_filter( 'woocommerce_integrations', array( __CLASS__, 'include_integrations' ) );
			add_filter( 'woocommerce_shipping_methods', array( __CLASS__, 'include_methods' ) );
			add_filter( 'woocommerce_email_classes', array( __CLASS__, 'include_emails' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( self::get_main_file() ), array( __CLASS__, 'plugin_action_links' ) );
		} else {
			add_action( 'admin_notices', array( __CLASS__, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public static function load_plugin_textdomain() {
		// Try to use the plugins own translation, only available for pt_BR.
		$locale = apply_filters( 'plugin_locale', determine_locale(), 'woocommerce-correios' );

		if ( 'pt_BR' === $locale ) {
			unload_textdomain( 'woocommerce-correios' );
			load_textdomain(
				'woocommerce-correios',
				self::get_plugin_path() . '/languages/woocommerce-correios-' . $locale . '.mo'
			);
		}

		// Load regular translation from WordPress.
		load_plugin_textdomain(
			'woocommerce-correios',
			false,
			dirname( plugin_basename( self::get_main_file() ) ) . '/languages'
		);
	}

	/**
	 * Setup WooCommerce HPOS compatibility.
	 */
	public static function setup_hpos_compatibility() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '7.1', '<' ) ) {
			return;
		}

		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-correios/woocommerce-correios.php', true );
		}
	}

	/**
	 * Includes.
	 */
	private static function includes() {
		include_once __DIR__ . '/wc-correios-functions.php';
		include_once __DIR__ . '/class-wc-correios-install.php';
		include_once __DIR__ . '/class-wc-correios-package.php';
		include_once __DIR__ . '/class-wc-correios-webservice.php';
		include_once __DIR__ . '/class-wc-correios-webservice-international.php';
		include_once __DIR__ . '/class-wc-correios-cws-connect.php';
		include_once __DIR__ . '/class-wc-correios-cws-calculate.php';
		include_once __DIR__ . '/class-wc-correios-autofill-addresses.php';
		include_once __DIR__ . '/class-wc-correios-tracking-history.php';
		include_once __DIR__ . '/class-wc-correios-rest-api.php';
		include_once __DIR__ . '/class-wc-correios-orders.php';
		include_once __DIR__ . '/class-wc-correios-cart.php';

		// Integration.
		include_once __DIR__ . '/integrations/class-wc-correios-integration.php';

		// Shipping methods.
		include_once __DIR__ . '/abstracts/class-wc-correios-shipping.php';
		include_once __DIR__ . '/abstracts/class-wc-correios-shipping-carta.php';
		include_once __DIR__ . '/abstracts/class-wc-correios-shipping-impresso.php';
		include_once __DIR__ . '/abstracts/class-wc-correios-shipping-international.php';

		// New API shipping methods.
		include_once __DIR__ . '/shipping/class-wc-correios-shipping-cws.php';
		include_once __DIR__ . '/shipping/class-wc-correios-shipping-cws-international.php';

		// Load deprecated methods.
		foreach ( glob( plugin_dir_path( __FILE__ ) . '/shipping/*.php' ) as $filename ) {
			include_once $filename;
		}
	}

	/**
	 * Admin includes.
	 */
	private static function admin_includes() {
		include_once __DIR__ . '/admin/class-wc-correios-admin-orders.php';
	}

	/**
	 * Updates.
	 */
	private static function update() {
		WC_Correios_Install::maybe_update();
	}

	/**
	 * Include Correios integration to WooCommerce.
	 *
	 * @param array $integrations Integrations list.
	 * @return array
	 */
	public static function include_integrations( $integrations ) {
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
	public static function include_methods( $methods ) {
		$methods['correios-cws']                              = 'WC_Correios_Shipping_Cws';
		$methods['correios-cws-international']                = 'WC_Correios_Shipping_Cws_International';
		$methods['correios-carta-registrada']                 = 'WC_Correios_Shipping_Carta_Registrada';
		$methods['correios-impresso-normal']                  = 'WC_Correios_Shipping_Impresso_Normal';
		$methods['correios-impresso-urgente']                 = 'WC_Correios_Shipping_Impresso_Urgente';
		$methods['correios-pac']                              = 'WC_Correios_Shipping_PAC';
		$methods['correios-sedex']                            = 'WC_Correios_Shipping_SEDEX';
		$methods['correios-sedex10-envelope']                 = 'WC_Correios_Shipping_SEDEX_10_Envelope';
		$methods['correios-sedex10-pacote']                   = 'WC_Correios_Shipping_SEDEX_10_Pacote';
		$methods['correios-sedex12']                          = 'WC_Correios_Shipping_SEDEX_12';
		$methods['correios-sedex-hoje']                       = 'WC_Correios_Shipping_SEDEX_Hoje';
		$methods['correios-esedex']                           = 'WC_Correios_Shipping_ESEDEX';
		$methods['correios-exporta-facil-economico']          = 'WC_Correios_Shipping_Exporta_Facil_Economico';
		$methods['correios-exporta-facil-expresso']           = 'WC_Correios_Shipping_Exporta_Facil_Expresso';
		$methods['correios-exporta-facil-premium']            = 'WC_Correios_Shipping_Exporta_Facil_Premium';
		$methods['correios-exporta-facil-standard']           = 'WC_Correios_Shipping_Exporta_Facil_Standard';
		$methods['correios-documento-economico']              = 'WC_Correios_Shipping_Documento_Economico';
		$methods['correios-documento-internacional-expresso'] = 'WC_Correios_Shipping_Documento_Internacional_Expresso';
		$methods['correios-documento-internacional-premium']  = 'WC_Correios_Shipping_Documento_Internacional_Premium';
		$methods['correios-documento-internacional-standard'] = 'WC_Correios_Shipping_Documento_Internacional_Standard';

		return $methods;
	}

	/**
	 * Include emails.
	 *
	 * @param  array $emails Default emails.
	 *
	 * @return array
	 */
	public static function include_emails( $emails ) {
		if ( ! isset( $emails['WC_Correios_Tracking_Email'] ) ) {
			$emails['WC_Correios_Tracking_Email'] = include __DIR__ . '/emails/class-wc-correios-tracking-email.php';
		}

		return $emails;
	}

	/**
	 * Action links.
	 *
	 * @param  array $links Default plugin links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=correios-integration' ) ) . '">' . __( 'Settings', 'woocommerce-correios' ) . '</a>';
		$plugin_links[] = '<a href="https://apoia.se/claudiosanches?utm_source=plugin-correios" target="_blank" rel="noopener noreferrer">' . __( 'Premium Support', 'woocommerce-correios' ) . '</a>';
		$plugin_links[] = '<a href="https://apoia.se/claudiosanches?utm_source=plugin-correios" target="_blank" rel="noopener noreferrer">' . __( 'Contribute', 'woocommerce-correios' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}

	/**
	 * WooCommerce fallback notice.
	 */
	public static function woocommerce_missing_notice() {
		include_once __DIR__ . '/admin/views/html-admin-missing-dependencies.php';
	}

	/**
	 * Get main file.
	 *
	 * @return string
	 */
	public static function get_main_file() {
		return WC_CORREIOS_PLUGIN_FILE;
	}

	/**
	 * Get plugin path.
	 *
	 * @return string
	 */
	public static function get_plugin_path() {
		return plugin_dir_path( WC_CORREIOS_PLUGIN_FILE );
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
