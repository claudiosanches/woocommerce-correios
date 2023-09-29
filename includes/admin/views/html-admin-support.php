<?php
/**
 * Admin help message view.
 *
 * @package WooCommerce_Correios/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_correios_support_us', true ) ) {
	return;
}

?>
	<div class="support-us postbox">
		<div class="inside">
			<div class="main">
				<h2><?php esc_html_e( 'Support this plugin\'s author', 'woocommerce-correios' ); ?></h2>
				<h3><?php esc_html_e( 'Apoia.se', 'woocommerce-correios' ); ?></h3>
				<p><?php esc_html_e( 'Be a member of Apoia.se and help in the development of many free plugins for WooCommerce, including the Brazilian Market on WooCommerce, depending on the amount of your support you can report bugs and vote on what will be prioritized in monthly releases and updates, in addition to having access to an exclusive support system for supporters that I intend to answer during working days between 12:00 and 19:00.', 'woocommerce-correios' ); ?></p>
				<p><a href="https://apoia.se/claudiosanches?utm_source=plugin-correios" target="_blank" rel="noopener noreferrer" class="button button-primary"><?php esc_html_e( 'Become a member at Apoia.se', 'woocommerce-correios' ); ?></a></p>
				<h3><?php esc_html_e( 'Make a review', 'woocommerce-correios' ); ?></h3>
				<p><?php esc_html_e( 'Help this plugin by rating with &#9733;&#9733;&#9733;&#9733;&#9733; on WordPress.org.', 'woocommerce-correios' ); ?></p>
				<p><a href="https://wordpress.org/support/plugin/woocommerce-correios/reviews/?filter=5#new-post" target="_blank" rel="noopener noreferrer" class="button button-secondary"><?php esc_html_e( 'Make a review', 'woocommerce-correios' ); ?></a></p>
			</div>
		</div>
	</div>
<?php

