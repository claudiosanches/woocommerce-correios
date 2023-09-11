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

if ( isset( $cws_needs_setup ) && $cws_needs_setup ) {
	?>
	<div class="notice notice-error inline">
		<p>
			<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: settings link labed "here" */
						__( 'This shipping method requires integration with the new Correios API, complete this integration %s. If you are seeing this message even after completing the integration, click on the "Update Services List" button to generate the list of services and be able to use this delivery method.', 'woocommerce-correios' ),
						'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=correios-integration' ) ) . '">' . __( 'here', 'woocommerce-correios' ) . '</a>'
					)
				);
			?>
		</p>
	</div>
	<?php
}

?>

<div id="plugin-correios-settings">
	<div class="box">
		<?php echo $this->get_admin_options_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="box">
		<?php require __DIR__ . '/html-admin-support.php'; ?>
	</div>
</div>
