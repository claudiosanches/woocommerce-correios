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
		$this->id             = 'correios_tracking';
		$this->title          = __( 'Correios Tracking Code', 'woocommerce-correios' );
		$this->enabled        = 'yes';
		$this->description    = __( 'This email is sent when configured a tracking code within an order.', 'woocommerce-correios' );
		$this->heading        = __( 'Your the Correios tracking code', 'woocommerce-correios' );
		$this->subject        = __( '[{blogname}] Your tracking code from order {order_number} at the Correios', 'woocommerce-correios' );
		$this->template_html  = 'emails/correios-tracking-code.php';
		$this->template_plain = 'emails/plain/correios-tracking-code.php';

		// Triggers for this email.
		add_action( 'woocommerce_correios_tracking_code_notification', array( $this, 'trigger' ) );

		// Call parent constructor.
		parent::__construct();

		// Other settings.
		$this->template_base = plugin_dir_path( dirname( __FILE__ ) );
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
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-correios' ), $this->heading ),
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
	 * Trigger email.
	 *
	 * @return void
	 */
	public function trigger( $order_id ) {
		if ( $order_id ) {
			$order           = new WC_Order( $order_id );
			$this->recipient = $order->billing_email;

			$this->find[]    = '{order_number}';
			$this->replace[] = $order->get_order_number();

			$this->find[]    = '{date}';
			$this->replace[] = date_i18n( woocommerce_date_format(), time() );

			$this->find[]    = '{tracking_code}';
			$this->replace[] = get_post_meta( $order_id, 'correios_tracking', true );
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

		woocommerce_get_template( $this->template_html, array(
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false
		), 'views/', $this->template_base );

		return ob_get_clean();
	}

	/**
	 * Get content plain text.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		woocommerce_get_template( $this->template_plain, array(
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true
		), 'views/', $this->template_base );

		return ob_get_clean();
	}
}

return new WC_Email_Correios_Tracking();
