<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Correios emails templates.
 */
class WC_Correios_Emails {

	/**
	 * Initialize emails actions.
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'emails' ) );
	}

	/**
	 * Include email templates.
	 *
	 * @param  array $emails
	 *
	 * @return array
	 */
	public function emails( $emails ) {
		if ( ! isset( $emails['WC_Email_Correios_Tracking'] ) ) {
			$emails['WC_Email_Correios_Tracking'] = include( 'emails/class-wc-email-correios-tracking.php' );
		}

		return $emails;
	}

}

new WC_Correios_Emails();
