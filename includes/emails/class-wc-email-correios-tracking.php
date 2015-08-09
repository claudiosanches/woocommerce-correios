<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Correios Tracking code email.
 */
class WC_Email_Correios_Tracking extends WC_Email {

	/**
	 * Initialize tracking template.
	 */
	public function __construct() {
		$this->id               = 'correios_tracking';
		$this->title            = __( 'Correios Tracking Code', 'woocommerce-correios' );
		$this->enabled          = 'yes';
		$this->description      = __( 'This email is sent when configured a tracking code within an order.', 'woocommerce-correios' );
		$this->heading          = __( 'Your order has been sent', 'woocommerce-correios' );
		$this->subject          = __( '[{blogname}] Your order {order_number} has been sent by Correios', 'woocommerce-correios' );
		$this->message          = __( 'Hi there. Your recent order on {blogname} has been sent by Correios.', 'woocommerce-correios' )
									. PHP_EOL . PHP_EOL
									. __( 'To track your delivery, use the following the tracking code: {tracking_code}.', 'woocommerce-correios' )
									. PHP_EOL . PHP_EOL
									. __( 'The delivery service is the responsibility of the Correios, but if you have any questions, please contact us.', 'woocommerce-correios' );
		$this->tracking_message = $this->get_option( 'tracking_message', $this->message );
		$this->template_html    = 'emails/correios-tracking-code.php';
		$this->template_plain   = 'emails/plain/correios-tracking-code.php';

		// Call parent constructor.
		parent::__construct();

		$this->template_base = WC_Correios::get_templates_path();
	}

	/**
	 * Initialise settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'subject' => array(
				'title'       => __( 'Subject', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-correios' ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'woocommerce-correios' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-correios' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'tracking_message' => array(
				'title'       => __( 'Email Content', 'woocommerce-correios' ),
				'type'        => 'textarea',
				'description' => sprintf( __( 'This controls the initial content of the email. Leave blank to use the default content: <code>%s</code>.', 'woocommerce-correios' ), $this->message ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce-correios' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce-correios' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'woocommerce-correios' ),
					'html'      => __( 'HTML', 'woocommerce-correios' ),
					'multipart' => __( 'Multipart', 'woocommerce-correios' ),
				)
			)
		);
	}

	/**
	 * Get email tracking message.
	 *
	 * @return string
	 */
	public function get_tracking_message() {
		return apply_filters( 'woocommerce_correios_email_tracking_message', $this->format_string( $this->tracking_message ), $this->object );
	}

	/**
	 * Get tracking code url.
	 *
	 * @param  string $tracking_code
	 *
	 * @return string
	 */
	public function get_tracking_code_url( $tracking_code ) {
		$options = get_option( 'woocommerce_correios_settings', array() );
		$url     = sprintf( '<a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s" target="_blank">%1$s</a>', $tracking_code );

		if ( ! empty( $options['tracking_history'] ) && 'yes' == $options['tracking_history'] ) {
			$url = sprintf( '<a href="%s#wc-correios-tracking">%s</a>', $this->object->get_view_order_url(), $tracking_code );
		}

		return apply_filters( 'woocommerce_correios_email_tracking_core_url', $url, $tracking_code, $this->object, $options );
	}

	/**
	 * Trigger email.
	 *
	 * @param  WC_Order $order         Order data.
	 * @param  string   $tracking_code Tracking code.
	 *
	 * @return void
	 */
	public function trigger( $order, $tracking_code ) {
		if ( is_object( $order ) ) {
			$this->object    = $order;
			$this->recipient = $this->object->billing_email;

			$this->find[]    = '{order_number}';
			$this->replace[] = $this->object->get_order_number();

			$this->find[]    = '{date}';
			$this->replace[] = date_i18n( wc_date_format(), time() );

			$this->find[]    = '{tracking_code}';
			$this->replace[] = $this->get_tracking_code_url( $tracking_code );
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

		wc_get_template( $this->template_html, array(
			'order'            => $this->object,
			'email_heading'    => $this->get_heading(),
			'tracking_message' => $this->get_tracking_message(),
			'sent_to_admin'    => false,
			'plain_text'       => false
		), '', $this->template_base );

		return ob_get_clean();
	}

	/**
	 * Get content plain text.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		wc_get_template( $this->template_plain, array(
			'order'            => $this->object,
			'email_heading'    => $this->get_heading(),
			'tracking_message' => $this->get_tracking_message(),
			'sent_to_admin'    => false,
			'plain_text'       => true
		), '', $this->template_base );

		return ob_get_clean();
	}
}

return new WC_Email_Correios_Tracking();
