<?php
/**
 * Admin options screen.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reviews_url = 'https://wordpress.org/support/view/plugin-reviews/woocommerce-correios?filter=5#postform';

?>

<h3><?php echo $this->method_title; ?></h3>

<?php echo $this->method_description; ?>

<?php if ( apply_filters( 'woocommerce_correios_help_message', true ) ) : ?>
	<div class="updated woocommerce-message">
		<p><?php printf( __( 'Help us keep the %s plugin free making a %s or rate %s on %s. Thank you in advance!', 'woocommerce-correios' ), '<strong>' . __( 'WooCommerce Correios', 'woocommerce-correios' ) . '</strong>', '<a href="http://claudiosmweb.com/doacoes/">' . __( 'donation', 'woocommerce-correios' ) . '</a>', '<a href="' . esc_url( $reviews_url ) . '" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>', '<a href="' . esc_url( $reviews_url ) . '" target="_blank">' . __( 'WordPress.org', 'woocommerce-correios' ) . '</a>' ); ?></p>
	</div>
<?php endif; ?>

<table class="form-table">
	<?php $this->generate_settings_html(); ?>
</table>
