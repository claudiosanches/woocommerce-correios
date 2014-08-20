<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Correios orders.
 */
class WC_Correios_Orders {

	private $tracking_code;

	/**
	 * Initialize the order actions.
	 */
	public function __construct() {

		if ( is_admin() ) {
			// Add metabox.
			add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

			// Save Metabox.
			add_action( 'save_post', array( $this, 'save_metabox_data' ) );

			$options = get_option( 'woocommerce_correios_settings' );

			if( isset( $options['tracking_orders_table'] ) && 'yes' == $options['tracking_orders_table'] ) {
				
				add_filter( 'manage_edit-shop_order_columns', array( $this, 'column_order_tracking_code' ), 20, 2 );
				add_action( 'manage_shop_order_posts_custom_column', array( $this, 'row_order_tracking_code' ), 10, 2 );

				add_action( 'admin_enqueue_scripts', array( $this, 'scritps' ) );
				
				add_action( 'wp_ajax_wc_correios_save_tracking_code', array( $this, 'ajax_save_tracking_code' ) );
				add_action( 'wp_ajax_nopriv_wc_correios_save_tracking_code', array( $this, 'ajax_save_tracking_code' ) );
			}

		}

		// Show tracking code in order details.
		add_action( 'woocommerce_view_order', array( $this, 'view_order_tracking_code' ), 1 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Tracking code scripts.
	 *
	 * @return void
	 */
	public function scritps() {
		if ( is_admin() ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style( 'wc-correios', plugins_url( 'assets/css/admin' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), '', 'all' );
			wp_enqueue_script( 'wc-correios', plugins_url( 'assets/js/admin' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '', true );
			wp_localize_script(
				'wc-correios',
				'wc_correios',
				array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'error_message' => __( 'Error', 'woocommerce-correios' )
				)
			);
		}
	}

	protected function get_tracking_code( $post ){
		$this->tracking_code = get_post_meta( $post->ID, 'correios_tracking', true );
    	return $this->tracking_code;
    }

    protected function get_html_input( $post ){
    	$this->get_tracking_code( $post );
    	return '<input type="text" id="wc-correios-tracking-' . $post->ID . '" name="correios_tracking" value="' . $this->tracking_code . '" style="width: 100%;" />';
    }

    public function column_order_tracking_code( $columns ) {
    	if ( !is_array( $columns ) ){
    		$columns = array();
    	}

    	if ( $columns && array_key_exists( 'order_actions', $columns ) ) {
	    	foreach( ( array ) $columns as $k => $v ) {
	    		if ( 'order_actions' == $k ) {
	    			unset( $columns[$k] );
	        		$columns['order_tracking_code']  = __( 'Tracking code', 'woocommerce-correios' );
	        		$columns[$k] = $v;
	        		break;
	    		}
	    	}		    		
    	} else {
    		$columns['order_tracking_code']  = __( 'Tracking code', 'woocommerce-correios' );
    	}

        return $columns;
    }

    public function row_order_tracking_code( $column ) {
     	global $post;

     	if( 'order_tracking_code' == $column ) {
     		echo $this->get_html_input( $post );
     		echo '<button href="javascript:;" class="button save" data-id="' . $post->ID . '">' . __( 'Save', 'woocommerce-correios' ) . '</button>';
     	}

    }

	/**
	 * Register tracking code metabox.
	 *
	 * @return void
	 */
	public function register_metabox() {
		add_meta_box(
			'wc_correios',
			'Correios',
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Tracking code metabox content.
	 *
	 * @param  object $post order_shop data.
	 *
	 * @return string       Metabox HTML.
	 */
	public function metabox_content( $post ) {
		// Use nonce for verification.
		wp_nonce_field( basename( __FILE__ ), 'wc_correios_nonce' );

		$html = '<label for="correios_tracking">' . __( 'Tracking code:', 'woocommerce-correios' ) . '</label><br />';
		$html .= $this->get_html_input( $post );

		echo $html;
	}

	/**
	 * Save metabox data.
	 *
	 * @param  int $post_id Current post type ID.
	 *
	 * @return void
	 */
	public function save_metabox_data( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['wc_correios_nonce'] ) || ! wp_verify_nonce( $_POST['wc_correios_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Verify if this is an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return $post_id;
		}

		if ( 'shop_order' != $_POST['post_type'] ) {
			return $post_id;
		}

		if ( isset( $_POST['correios_tracking'] ) ) {
			$this->save_tracking_code( $post_id, $_POST['correios_tracking'] );
		}
	}

	public function ajax_save_tracking_code() {
		if ( isset( $_POST['order_id'] ) && is_numeric( $_POST['order_id'] ) ) {
			$order_id = absint( $_POST['order_id'] );
		}

		if ( isset( $_POST['correios_tracking'] ) && $_POST['correios_tracking'] ) {
			$tracking_code = $_POST['correios_tracking'];
		}

		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] ) {
			$post_type = $_POST['post_type'];
		}

		if ( !$order_id || 'shop_order' != $post_type ) {
			wp_send_json( array( 'error' => __( 'Error to identify the order.', 'woocommerce-correios' ) ) );
		}else{
			if ( !$this->save_tracking_code( $order_id, $tracking_code ) ) {
				$message = 'The tracking code has been sent.';
			} else {
				$message = '';
			}

			wp_send_json( array( 'error' => __( $message, 'woocommerce-correios' ) ) );
		}
		exit;
	}

	/**
	 * Save tracking code.
	 *
	 * @param  object $order         Order data.
	 * @param  string $tracking_code The Correios tracking code.
	 *
	 * @return boolean
	 */
	protected function save_tracking_code( $post_id, $tracking_code ) {
		if( current_user_can( 'manage_woocommerce' ) && is_numeric( $post_id ) && $post_id ){
			$old = get_post_meta( $post_id, 'correios_tracking', true );

			if ( $tracking_code && $tracking_code != $old ) {
				update_post_meta( $post_id, 'correios_tracking', $tracking_code );

				// Gets order data.
				$order = new WC_Order( $post_id );

				// Add order note.
				$order->add_order_note( sprintf( __( 'Added a Correios tracking code: %s', 'woocommerce-correios' ), $tracking_code ) );

				// Send email notification.
				return $this->trigger_email_notification( $order, $tracking_code );
			} elseif ( '' == $tracking_code && $old ) {
				return delete_post_meta( $post_id, 'correios_tracking', $old );
			}		
		}
		return false;
	}


	/**
	 * Trigger email notification.
	 *
	 * @param  object $order         Order data.
	 * @param  string $tracking_code The Correios tracking code.
	 *
	 * @return boolean
	 */
	protected function trigger_email_notification( $order, $tracking_code ) {
		global $woocommerce;

		$mailer       = $woocommerce->mailer();
		$notification = $mailer->emails['WC_Email_Correios_Tracking'];
		return $notification->trigger( $order, $tracking_code );
	}

	/**
	 * Display the order tracking code in order details.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string           Tracking code as link.
	 */
	public function view_order_tracking_code( $order_id ) {
		$tracking_code = get_post_meta( $order_id, 'correios_tracking', true );

		if ( ! empty( $tracking_code ) ) {
			$url = sprintf( '<a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s" target="_blank">%1$s</a>', $tracking_code );
			echo '<div class="woocommerce-info"><strong>' . __( 'Correios', 'woocommerce-correios' ) . ':</strong> ' . sprintf( __( 'Your the tracking code: %s.', 'woocommerce-correios' ), $url ) . '</div>';
		}
	}
}

new WC_Correios_Orders();
