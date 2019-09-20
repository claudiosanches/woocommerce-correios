<?php
/**
 * Plugin Name:          Claudio Sanches - Correios for WooCommerce
 * Plugin URI:           https://github.com/claudiosanches/woocommerce-correios
 * Description:          Adds Correios shipping methods to your WooCommerce store.
 * Author:               Claudio Sanches
 * Author URI:           https://claudiosanches.com
 * Version:              3.8.0
 * License:              GPLv2 or later
 * Text Domain:          woocommerce-correios
 * Domain Path:          /languages
 * WC requires at least: 3.0.0
 * WC tested up to:      3.6.0
 *
 * Claudio Sanches - Correios for WooCommerce is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or any later version.
 *
 * Claudio Sanches - Correios for WooCommerce is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Claudio Sanches - Correios for WooCommerce. If not, see
 * <https://www.gnu.org/licenses/gpl-2.0.txt>.
 *
 * @package WooCommerce_Correios
 */

defined( 'ABSPATH' ) || exit;

define( 'WC_CORREIOS_VERSION', '3.8.0' );
define( 'WC_CORREIOS_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'WC_Correios' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-correios.php';

	add_action( 'plugins_loaded', array( 'WC_Correios', 'init' ) );
}
