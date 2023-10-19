<?php
/**
 * Correios tracking code email.
 *
 * @package WooCommerce_Correios/Classes/Emails
 * @since   3.0.0
 * @version 4.1.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Tracking code email.
 */
class WC_Correios_Tracking_Email extends WC_Email {

	/**
	 * Message.
	 *
	 * @var string
	 */
	public $message = '';

	/**
	 * Tracking message.
	 *
	 * @var string
	 */
	public $tracking_message = '';

	/**
	 * Initialize tracking template.
	 */
	public function __construct() {
		$this->id             = 'correios_tracking';
		$this->title          = __( 'Correios Tracking Code', 'woocommerce-correios' );
		$this->customer_email = true;
		$this->description    = __( 'This email is sent when configured a tracking code within an order.', 'woocommerce-correios' );
		$this->template_html  = 'emails/correios-tracking-code.php';
		$this->template_plain = 'emails/plain/correios-tracking-code.php';
		$this->placeholders   = array(
			'{order_number}'  => '',
			'{order_date}'    => '',
			'{tracking_code}' => '',
			'{date}'          => '',
		);

		// Call parent constructor.
		parent::__construct();

		$this->template_base = WC_Correios::get_templates_path();
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Your order #{order_number} has been sent by Correios', 'woocommerce-correios' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Your order has been sent', 'woocommerce-correios' );
	}

	/**
	 * Default tracking message content.
	 *
	 * @return string
	 */
	public function get_default_tracking_message() {
		return __( 'Hi there. Your recent order on {site_title} has been sent by Correios.', 'woocommerce-correios' )
			. PHP_EOL . ' ' . PHP_EOL
			. __( 'To track your delivery, use the following the tracking code(s): {tracking_code}', 'woocommerce-correios' )
			. PHP_EOL . ' ' . PHP_EOL
			. __( 'The delivery service is the responsibility of the Correios, but if you have any questions, please contact us.', 'woocommerce-correios' );
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		/* translators: %s: list of placeholders */
		$placeholder_text = sprintf( __( 'Available placeholders: %s', 'woocommerce-correios' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );

		$this->form_fields = array(
			'enabled'          => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-correios' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce-correios' ),
				'default' => 'yes',
			),
			'subject'          => array(
				'title'       => __( 'Subject', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_subject(),
				'default'     => $this->get_default_subject(),
				'desc_tip'    => true,
			),
			'heading'          => array(
				'title'       => __( 'Email heading', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_heading(),
				'default'     => $this->get_default_heading(),
				'desc_tip'    => true,
			),
			'tracking_message' => array(
				'title'       => __( 'Email content', 'woocommerce-correios' ),
				'type'        => 'textarea',
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_tracking_message(),
				'default'     => $this->get_default_tracking_message(),
				'desc_tip'    => true,
			),
			'email_type'       => array(
				'title'       => __( 'Email type', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce-correios' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Get email tracking message.
	 *
	 * @return string
	 */
	public function get_tracking_message() {
		$message = $this->get_option( 'tracking_message', $this->get_default_tracking_message() );

		return apply_filters( 'woocommerce_correios_email_tracking_message', $this->format_string( $message ), $this->object );
	}

	/**
	 * Get tracking code url.
	 *
	 * @param  string $tracking_code Tracking code.
	 *
	 * @return string
	 */
	public function get_tracking_code_url( $tracking_code ) {
		$html = sprintf( '<a href="%s#wc-correios-tracking">%s</a>', $this->object->get_view_order_url(), $tracking_code );

		return apply_filters( 'woocommerce_correios_email_tracking_code_url', $html, $tracking_code, $this->object );
	}

	/**
	 * Get tracking codes HTML.
	 *
	 * @param  array $tracking_codes Tracking codes.
	 *
	 * @return string
	 */
	public function get_tracking_codes( $tracking_codes ) {
		if ( 1 < count( $tracking_codes ) ) {
			$html = '<ul>';
			foreach ( $tracking_codes as $tracking_code ) {
				$html .= '<li>' . $this->get_tracking_code_url( $tracking_code ) . '</li>';
			}
			$html .= '</ul>';

			return $html;
		}

		return $this->get_tracking_code_url( $tracking_codes[0] );
	}

	/**
	 * Trigger email.
	 *
	 * @param  int      $order_id      Order ID.
	 * @param  WC_Order $order         Order data.
	 * @param  string   $tracking_code Tracking code.
	 */
	public function trigger( $order_id, $order = false, $tracking_code = '' ) {
		// Get the order object while resending emails.
		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_object( $order ) ) {
			$this->object    = $order;
			$this->recipient = $this->object->get_billing_email();

			if ( empty( $tracking_code ) ) {
				$tracking_codes = wc_correios_get_tracking_codes( $this->object );
			} else {
				$tracking_codes = array( $tracking_code );
			}

			$this->placeholders['{order_number}']  = $this->object->get_order_number();
			$this->placeholders['{order_date}']    = wc_format_datetime( $this->object->get_date_created() );
			$this->placeholders['{date}']          = date_i18n( wc_date_format(), time() );
			$this->placeholders['{tracking_code}'] = $this->get_tracking_codes( $tracking_codes );
		}

		if ( ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content HTML.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();

		wc_get_template(
			$this->template_html,
			array(
				'order'            => $this->object,
				'email_heading'    => $this->get_heading(),
				'tracking_message' => $this->get_tracking_message(),
				'sent_to_admin'    => false,
				'plain_text'       => false,
				'email'            => $this,
			),
			'',
			$this->template_base
		);

		return ob_get_clean();
	}

	/**
	 * Get content plain text.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		// Format list.
		$message = $this->get_tracking_message();
		$message = str_replace( '<ul>', "\n", $message );
		$message = str_replace( '<li>', "\n - ", $message );
		$message = str_replace( array( '</ul>', '</li>' ), '', $message );

		wc_get_template(
			$this->template_plain,
			array(
				'order'            => $this->object,
				'email_heading'    => $this->get_heading(),
				'tracking_message' => $message,
				'sent_to_admin'    => false,
				'plain_text'       => true,
				'email'            => $this,
			),
			'',
			$this->template_base
		);

		return ob_get_clean();
	}
}

return new WC_Correios_Tracking_Email();
