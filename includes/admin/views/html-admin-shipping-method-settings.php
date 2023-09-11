<?php
/**
 * Shipping methods admin settings.
 *
 * @package WooCommerce_Correios/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
wp_enqueue_style( $this->id . '-admin-styles', plugins_url( 'assets/css/admin/settings.css', WC_Correios::get_main_file() ), array(), WC_CORREIOS_VERSION );
wp_enqueue_script( 'wc-correios', plugins_url( 'assets/js/admin/shipping-methods' . $suffix . '.js', WC_Correios::get_main_file() ), array( 'jquery' ), WC_CORREIOS_VERSION, true );

$description = $this->get_method_description();

if ( ! empty( $this->more_link ) ) {
	/* translators: %s: method title */
	$description .= ' <a href="' . esc_url( $this->more_link ) . '">' . esc_html( sprintf( __( 'More about %s.', 'woocommerce-correios' ), $this->method_title ) ) . '</a>';
}

echo wp_kses_post( wpautop( $description ) );

echo '<div id="plugin-correios-settings">';
echo '<div class="box">';
echo $this->get_admin_options_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '</div>';
echo '<div class="box">';
require __DIR__ . '/html-admin-support.php';
echo '</div>';
echo '</div>';
