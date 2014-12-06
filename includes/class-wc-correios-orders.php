<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Correios orders.
 */
class WC_Correios_Orders {

	/**
	 * Initialize the order actions.
	 */
	public function __construct() {

		if ( is_admin() ) {
			// Add metabox.
			add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

			// Save Metabox.
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_tracking_code' ) );
		}

		// Show tracking code in order details.
		add_action( 'woocommerce_view_order', array( $this, 'view_order_tracking_code' ), 1 );
                
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
		$html = '<label for="correios_tracking">' . __( 'Tracking code:', 'woocommerce-correios' ) . '</label><br />';
		$html .= '<input type="text" id="correios_tracking" name="correios_tracking" value="' . get_post_meta( $post->ID, 'correios_tracking', true ) . '" style="width: 100%;" />';

		echo $html;
	}

	/**
	 * Save tracking code.
	 *
	 * @param  int $post_id Current post type ID.
	 *
	 * @return void
	 */
	public function save_tracking_code( $post_id ) {
		if ( isset( $_POST['correios_tracking'] ) ) {
			$old = get_post_meta( $post_id, 'correios_tracking', true );

			$new = $_POST['correios_tracking'];

			if ( $new && $new != $old ) {
				update_post_meta( $post_id, 'correios_tracking', $new );

				// Gets order data.
				$order = new WC_Order( $post_id );

				// Add order note.
				$order->add_order_note( sprintf( __( 'Added a Correios tracking code: %s', 'woocommerce-correios' ), $new ) );

				// Send email notification.
				$this->trigger_email_notification( $order, $new );
			} elseif ( '' == $new && $old ) {
				delete_post_meta( $post_id, 'correios_tracking', $old );
			}
		}
	}

	/**
	 * Trigger email notification.
	 *
	 * @param  object $order         Order data.
	 * @param  string $tracking_code The Correios tracking code.
	 *
	 * @return void
	 */
	protected function trigger_email_notification( $order, $tracking_code ) {
		global $woocommerce;

		$mailer       = $woocommerce->mailer();
		$notification = $mailer->emails['WC_Email_Correios_Tracking'];
		$notification->trigger( $order, $tracking_code );
	}

	/**
	 * Display the order tracking code in order details.
         * Display tracking history
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
            
			$xmltracking = $this->tracking_history( $tracking_code );

                        $html = '';
			$html = '<table class="shop_table"><tbody><tr><th>' . __('DATE', 'woocommerce-correios') . '</th><th>' . __('TIME', 'woocommerce-correios') . '</th>';
			$html .= '<th>' . __('POSITION', 'woocommerce-correios') . '</th><th>' . __('PLACE', 'woocommerce-correios') . '</th><th>' . __('CITY', 'woocommerce-correios') . '</th><th>' . __('UF', 'woocommerce-correios') . '</th></tr>';
			
                        /* Reading all objects */
			foreach ( $xmltracking->objeto as $objeto ):
                                /* Reading all events*/    
				foreach( $objeto->evento as $evento ):				
                                    $html .= '<tr><td>' . $evento->data . '</td><td>' . $evento->hora . '</td><td>' . $evento->descricao . '</td><td>' . $evento->local . '</td><td>';
                                    $html .= $evento->cidade . '</td><td>' . $evento->uf . '</td></tr>';
                                    /* Reading all destinations */
				   foreach( $evento->destino as $destino ):				   
  					$html .= '<tr><td></td><td></td><td>Em trânsito para</td>';
					$html .= '<td>' . $destino->local . '</td><td>' . $destino->cidade . '</td><td>' . $destino->uf . '</td></tr>';				
				   endforeach;				   
				endforeach;				
			endforeach;			
			$html .= '</tbody></table>';
			echo $html;			
		}
	}

	/**
	 * Access API Correios.
	 *
	 * @param  string    $tracking_code.
	 *
	 * @return string    History Tracking code.
	 */        
        
        public function tracking_history( $tracking_code ) {
			
                $options           = get_option( 'woocommerce_correios_settings' );	
                $login             = empty( $options['login'] ) ? 'ECT' : $options['login'];
                $password          = empty( $options['password'] ) ? 'SRO' : $options['password'];
            						
                $parametros = array(
                        'Usuario' => $login, 
                        'Senha' => $password,   
                        'Tipo' => 'L',                  /* L - List of objects | F - Object Range */
                        'Resultado' => 'T',             /* T - Returns all the object's events | U - Returns only last event object */
                        'Objetos' => $tracking_code, 
		);            

                $url = "http://websro.correios.com.br/sro_bin/sroii_xml.eventos";
                
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parametros));
                $result = curl_exec($curl); 		
                curl_close($curl);
                $response = simplexml_load_string($result);

                return $response;                                

        }
        
}

new WC_Correios_Orders();
