<?php
/**
 * Plugin Name: WooCommerce Correios
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-correios
 * Description: Correios para WooCommerce
 * Author: claudiosanches, rodrigoprior
 * Author URI: http://claudiosmweb.com/
 * Version: 1.7.0
 * License: GPLv2 or later
 * Text Domain: wccorreios
 * Domain Path: /languages/
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
	 * @since 1.7.0
	 *
	 * @var   string
	 */
	const VERSION = '1.7.0';

	/**
	 * Integration id.
	 *
	 * @since 1.7.0
	 *
	 * @var   string
	 */
	protected static $method_id = 'correios';

	/**
	 * Plugin slug.
	 *
	 * @since 1.7.0
	 *
	 * @var   string
	 */
	protected static $plugin_slug = 'woocommerce-correios';

	/**
	 * Instance of this class.
	 *
	 * @since 1.7.0
	 *
	 * @var   object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin public actions.
	 *
	 * @since  1.7.0
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		if ( class_exists( 'SimpleXmlElement' ) ) {
			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Shipping_Method' ) ) {
				// Include the WC_Shipping_Correios class.
				include_once 'includes/class-wc-shipping-correios.php';

				add_filter( 'woocommerce_shipping_methods', array( $this, 'add_method' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		} else {
			add_action( 'admin_notices', array( $this, 'simplexmlelement_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since  1.7.0
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
	 * Return the method id/slug.
	 *
	 * @since  1.7.0
	 *
	 * @return string Gateway id/slug variable.
	 */
	public static function get_method_id() {
		return self::$method_id;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since  1.7.0
	 *
	 * @return string Plugin slug variable.
	 */
	public static function get_plugin_slug() {
		return self::$plugin_slug;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since  1.7.0
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$domain = self::$plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add the shipping method to WooCommerce.
	 *
	 * @version 1.7.0
	 *
	 * @param   array $methods WooCommerce payment methods.
	 *
	 * @return  array          Payment methods with Correios.
	 */
	public function add_method( $methods ) {
		$methods[] = 'WC_Shipping_Correios';

		return $methods;
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @version 1.7.0
	 *
	 * @return  string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Correios depends on the last version of %s to work!', self::$plugin_slug ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', self::$plugin_slug ) . '</a>' ) . '</p></div>';
	}

	/**
	 * SimpleXMLElement fallback notice.
	 *
	 * @version 1.7.0
	 *
	 * @return  string
	 */
	public function simplexmlelement_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Correios depends to %s to work!', self::$plugin_slug ), '<a href="http://php.net/manual/en/book.simplexml.php">' . __( 'SimpleXML', self::$plugin_slug ) . '</a>' ) . '</p></div>';
	}
}

add_action( 'plugins_loaded', array( 'WC_Correios', 'get_instance' ), 0 );

/**
 * Plugin admin.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once 'includes/class-wc-correios-admin.php';

	add_action( 'plugins_loaded', array( 'WC_Correios_Admin', 'get_instance' ) );
}


endif;
