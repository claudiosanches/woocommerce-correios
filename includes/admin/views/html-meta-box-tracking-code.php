<?php
/**
 * Meta box - Tracking Code
 *
 * @package WooCommerce_Correios/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="correios-tracking-code">
	<?php if ( ! empty( $tracking_codes ) ) : ?>
		<div class="correios-tracking-code__list">
			<strong><?php echo esc_html( _n( 'Tracking code:', 'Tracking codes:', count( $tracking_codes ), 'woocommerce-correios' ) ); ?></strong>
			<ul>
				<?php foreach ( $tracking_codes as $tracking_code ) : ?>
					<li><a href="#" aria-label="<?php esc_attr_e( 'Tracking code', 'woocommerce-correios' ); ?>"><?php echo esc_html( $tracking_code ); ?></a> <a href="#" class="dashicons-dismiss" title="<?php esc_attr_e( 'Remove tracking code', 'woocommerce-correios' ); ?>" aria-label="<?php esc_attr_e( 'Remove tracking code', 'woocommerce-correios' ) ?>" data-code="<?php echo esc_attr( $tracking_code ); ?>"></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<fieldset>
		<label for="add-tracking-code"><?php esc_html_e( 'Add tracking code', 'woocommerce-correios' ); ?></label>
		<input type="text" id="add-tracking-code" name="correios_tracking" value="" />
		<button type="button" class="button-secondary dashicons-plus" aria-label="<?php esc_attr_e( 'Add new tracking code', 'woocommerce-correios' ); ?>"></button>
	</fieldset>
</div>

<script type="text/html" id="tmpl-tracking-code-list">
	<div class="correios-tracking-code__list">
		<# if ( 1 < data.trackingCodes.length ) { #>
			<strong><?php esc_html_e( 'Tracking codes:', 'woocommerce-correios' ); ?></strong>
		<# } else { #>
			<strong><?php esc_html_e( 'Tracking code:', 'woocommerce-correios' ); ?></strong>
		<# } #>
		<ul>
			<# _.each( data.trackingCodes, function( trackingCode ) { #>
				<li><span aria-label="<?php esc_attr_e( 'Tracking code', 'woocommerce-correios' ) ?>">{{trackingCode}}</span> <a href="#" class="dashicons-dismiss" title="<?php esc_attr_e( 'Remove tracking code', 'woocommerce-correios' ) ?>" aria-label="<?php esc_attr_e( 'Remove tracking code', 'woocommerce-correios' ) ?>" data-code="{{trackingCode}}"></a></li>
			<# }); #>
		</ul>
	</div>
</script>
