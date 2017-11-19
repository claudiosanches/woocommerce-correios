<?php
/**
 * Admin help message.
 *
 * @package WooCommerce_Correios/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( apply_filters( 'woocommerce_correios_help_message', true ) ) : ?>
	<div class="updated woocommerce-message inline">
		<p>
		<?php
			/* translators: %s: plugin name */
			echo esc_html( sprintf( esc_html__( 'Help us keep the %s plugin free making a donation or rate &#9733;&#9733;&#9733;&#9733;&#9733; on WordPress.org. Thank you in advance!', 'woocommerce-correios' ), __( 'WooCommerce Correios', 'woocommerce-correios' ) ) );
		?>
		</p>
		<p><a href="https://claudiosanches.com/doacoes/" target="_blank" rel="nofollow noopener noreferrer" class="button button-primary"><?php esc_html_e( 'Make a donation', 'woocommerce-correios' ); ?></a> <a href="https://wordpress.org/support/plugin/woocommerce-correios/reviews/?filter=5#new-post" target="_blank" class="button button-secondary"><?php esc_html_e( 'Make a review', 'woocommerce-correios' ); ?></a></p>
	</div>
<?php
endif;
