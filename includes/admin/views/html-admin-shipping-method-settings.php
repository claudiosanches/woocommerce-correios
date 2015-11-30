<?php
/**
 * Shipping methods admin settings.
 *
 * @package WooCommerce_Correios/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
wp_enqueue_script( 'wc-correios', plugins_url( 'assets/js/admin' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_Correios::VERSION, true );

?>

<h3><?php echo esc_html( $this->method_title ); ?></h3>

<?php echo esc_html( sprintf( __( '%s is a shipping method from Correios, the brazilian most used delivery company.', 'woocommerce-correios' ), $this->method_title ) ); ?>

<?php if ( ! empty( $this->more_link ) ) : ?>
	<a href="<?php echo esc_url( $this->more_link ); ?>"><?php echo esc_html( sprintf( __( 'More about %s.', 'woocommerce-correios' ), $this->method_title ) ); ?></a>
<?php endif; ?>

<?php if ( apply_filters( 'woocommerce_correios_help_message', true ) ) : ?>
	<div class="updated woocommerce-message">
		<p><?php echo esc_html( sprintf( __( 'Help us keep the %s plugin free making a donation or rate &#9733;&#9733;&#9733;&#9733;&#9733; on WordPress.org. Thank you in advance!', 'woocommerce-correios' ), __( 'WooCommerce Correios', 'woocommerce-correios' ) ) ); ?></p>
		<p><a href="http://claudiosmweb.com/doacoes/" target="_blank" class="button button-primary"><?php esc_html_e( 'Make a donation', 'woocommerce-correios' ); ?></a> <a href="https://wordpress.org/support/view/plugin-reviews/woocommerce-correios?filter=5#postform" target="_blank" class="button button-secondary"><?php esc_html_e( 'Make a review', 'woocommerce-correios' ); ?></a></p>
	</div>
<?php endif; ?>

<table class="form-table">
	<?php $this->generate_settings_html(); ?>
</table>
