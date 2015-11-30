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
wp_enqueue_script( 'wc-correios', plugins_url( 'assets/js/admin/shipping-methods' . $suffix . '.js', WC_Correios::get_main_file() ), array( 'jquery' ), WC_Correios::VERSION, true );

?>

<h3><?php echo esc_html( $this->method_title ); ?></h3>

<p>
	<?php echo esc_html( sprintf( __( '%s is a shipping method from Correios, the brazilian most used delivery company.', 'woocommerce-correios' ), $this->method_title ) ); ?>

	<?php if ( ! empty( $this->more_link ) ) : ?>
		<a href="<?php echo esc_url( $this->more_link ); ?>"><?php echo esc_html( sprintf( __( 'More about %s.', 'woocommerce-correios' ), $this->method_title ) ); ?></a>
	<?php endif; ?>
</p>

<?php include 'html-admin-help-message.php'; ?>

<table class="form-table">
	<?php $this->generate_settings_html(); ?>
</table>
