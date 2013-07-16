<?php
/**
 * WC_Correios class.
 */
class WC_Correios extends WC_Shipping_Method {

    /**
     * __construct function.
     *
     * @return void
     */
    public function __construct() {
        $this->id           = 'correios';
        $this->method_title = __( 'Correios', 'wccorreios' );
        $this->init();
    }

    /**
     * Initializes the method.
     *
     * @return void
     */
    public function init() {
        global $woocommerce;

        // Correios Web Service.
        $this->webservice = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?';

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables.
        $this->enabled            = $this->settings['enabled'];
        $this->title              = $this->settings['title'];
        $this->declare_value      = $this->settings['declare_value'];
        $this->display_date       = $this->settings['display_date'];
        $this->additional_time    = $this->settings['additional_time'];
        $this->availability       = $this->settings['availability'];
        $this->fee                = $this->settings['fee'];
        $this->zip_origin         = $this->settings['zip_origin'];
        $this->countries          = $this->settings['countries'];
        $this->corporate_service  = $this->settings['corporate_service'];
        $this->login              = $this->settings['login'];
        $this->password           = $this->settings['password'];
        $this->service_pac        = $this->settings['service_pac'];
        $this->service_sedex      = $this->settings['service_sedex'];
        $this->service_sedex_10   = $this->settings['service_sedex_10'];
        $this->service_sedex_hoje = $this->settings['service_sedex_hoje'];
        $this->service_esedex     = $this->settings['service_esedex'];
        $this->minimum_height     = $this->settings['minimum_height'];
        $this->minimum_width      = $this->settings['minimum_width'];
        $this->minimum_length     = $this->settings['minimum_length'];
        $this->debug              = $this->settings['debug'];

        // Active logs.
        if ( 'yes' == $this->debug )
            $this->log = $woocommerce->logger();

        // Actions.
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );
    }

    /**
     * Admin options fields.
     *
     * @return void
     */
    public function init_form_fields() {
        global $woocommerce;

        $this->form_fields = array(
            'enabled' => array(
                'title'            => __( 'Enable/Disable', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable this shipping method', 'wccorreios' ),
                'default'          => 'no'
            ),
            'title' => array(
                'title'            => __( 'Title', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'This controls the title which the user sees during checkout.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => __( 'Correios', 'wccorreios' )
            ),
            'availability' => array(
                'title'            => __( 'Availability', 'wccorreios' ),
                'type'             => 'select',
                'default'          => 'all',
                'class'            => 'availability',
                'options'          => array(
                    'all'          => __( 'All allowed countries', 'wccorreios' ),
                    'specific'     => __( 'Specific Countries', 'wccorreios' )
                )
            ),
            'countries' => array(
                'title'            => __( 'Specific Countries', 'wccorreios' ),
                'type'             => 'multiselect',
                'class'            => 'chosen_select',
                'css'              => 'width: 450px;',
                'options'          => $woocommerce->countries->countries
            ),
            'zip_origin' => array(
                'title'            => __( 'Origin Zip Code', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'Zip Code from where the requests are sent.', 'wccorreios' ),
                'desc_tip'         => true
            ),
            'declare_value' => array(
                'title'            => __( 'Declare value', 'wccorreios' ),
                'type'             => 'select',
                'default'          => 'none',
                'options'          => array(
                    'declare'      => __( 'Declare', 'wccorreios' ),
                    'none'         => __( 'None', 'wccorreios' )
                ),
            ),
            'display_date' => array(
                'title'            => __( 'Estimated delivery', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable', 'wccorreios' ),
                'description'      => __( 'Display date of estimated delivery.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => 'no'
            ),
            'additional_time' => array(
                'title'            => __( 'Additional days', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'Additional days to the estimated delivery.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => '0',
                'placeholder'      => '0'
            ),
            'fee' => array(
                'title'            => __( 'Handling Fee', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'wccorreios' ),
                'desc_tip'         => true,
                'placeholder'      => '0.00'
            ),
            'services' => array(
                'title'            => __( 'Correios Services', 'wccorreios' ),
                'type'             => 'title'
            ),
            'corporate_service' => array(
                'title'            => __( 'Corporate Service', 'wccorreios' ),
                'type'             => 'select',
                'description'      => __( 'Choose between conventional or corporate service.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => 'conventional',
                'options'          => array(
                    'conventional' => __( 'Conventional', 'wccorreios' ),
                    'corporate'    => __( 'Corporate', 'wccorreios' )
                ),
            ),
            'login' => array(
                'title'            => __( 'Administrative Code', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'Your Correios login.', 'wccorreios' ),
                'desc_tip'         => true
            ),
            'password' => array(
                'title'            => __( 'Administrative Password', 'wccorreios' ),
                'type'             => 'password',
                'description'      => __( 'Your Correios password.', 'wccorreios' ),
                'desc_tip'         => true
            ),
            'service_pac' => array(
                'title'            => __( 'PAC', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable', 'wccorreios' ),
                'description'      => __( 'Shipping via PAC.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => 'no'
            ),
            'service_sedex' => array(
                'title'            => __( 'SEDEX', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable', 'wccorreios' ),
                'description'      => __( 'Shipping via SEDEX.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => 'no'
            ),
            'service_sedex_10' => array(
                'title'            => __( 'SEDEX 10', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable', 'wccorreios' ),
                'description'      => __( 'Shipping via SEDEX 10.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => 'no'
            ),
            'service_sedex_hoje' => array(
                'title'            => __( 'SEDEX Hoje', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable', 'wccorreios' ),
                'description'      => __( 'Shipping via SEDEX Hoje.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => 'no'
            ),
            'service_esedex' => array(
                'title'            => __( 'e-SEDEX', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable', 'wccorreios' ),
                'description'      => __( 'Shipping via e-SEDEX.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => 'no'
            ),
            'package_standard' => array(
                'title'            => __( 'Package Standard', 'wccorreios' ),
                'type'             => 'title',
                'description'      => __( 'Sets a minimum measure for the package.', 'wccorreios' ),
                'desc_tip'         => true,
            ),
            'minimum_height' => array(
                'title'            => __( 'Minimum Height', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'Minimum height of the package. Correios needs at least 2 cm.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => '2'
            ),
            'minimum_width' => array(
                'title'            => __( 'Minimum Width', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'Minimum width of the package. Correios needs at least 11 cm.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => '11'
            ),
            'minimum_length' => array(
                'title'            => __( 'Minimum Length', 'wccorreios' ),
                'type'             => 'text',
                'description'      => __( 'Minimum length of the package. Correios needs at least 16 cm.', 'wccorreios' ),
                'desc_tip'         => true,
                'default'          => '16'
            ),
            'testing' => array(
                'title'            => __( 'Testing', 'wccorreios' ),
                'type'             => 'title'
            ),
            'debug' => array(
                'title'            => __( 'Debug Log', 'wccorreios' ),
                'type'             => 'checkbox',
                'label'            => __( 'Enable logging', 'wccorreios' ),
                'default'          => 'no',
                'description'      => sprintf( __( 'Log Correios events, such as WebServices requests, inside %s.', 'wccorreios' ), '<code>woocommerce/logs/correios-' . sanitize_file_name( wp_hash( 'correios' ) ) . '.txt</code>' )
            )
        );
    }

    /**
     * Correios options page.
     *
     * @return void
     */
    public function admin_options() {
        // Call the admin scripts.
        wp_enqueue_script( 'wc-correios', WOO_CORREIOS_URL . 'js/admin.js', array( 'jquery' ), '', true );

        echo '<h3>' . $this->method_title . '</h3>';
        echo '<p>' . __( 'Correios is a brazilian delivery method.', 'wccorreios' ) . '</p>';
        echo '<table class="form-table">';
            $this->generate_settings_html();
        echo '</table>';
    }

    /**
     * Checks if the method is available.
     *
     * @param array $package Order package.
     *
     * @return bool
     */
    public function is_available( $package ) {
        global $woocommerce;
        $is_available = true;

        if ( 'no' == $this->enabled ) {
            $is_available = false;
        } else {
            $ship_to_countries = '';

            if ( 'specific' == $this->availability )
                $ship_to_countries = $this->countries;
            elseif ( 'specific' == get_option( 'woocommerce_allowed_countries' ) )
                $ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );

            if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) )
                $is_available = false;
        }

        return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
    }

    /**
     * Replace comma by dot.
     *
     * @param  mixed $value Value to fix.
     *
     * @return mixed
     */
    private function fix_format( $value ) {
        $value = str_replace( ',', '.', $value );

        return $value;
    }

    /**
     * Fix number format for SimpleXML.
     *
     * @param  float $value  Value with dot.
     *
     * @return string        Value with comma.
     */
    private function fix_simplexml_format( $value ) {
        $value = str_replace( '.', ',', $value );

        return $value;
    }

    /**
     * Fix Zip Code format.
     *
     * @param mixed $zip Zip Code.
     *
     * @return int
     */
    protected function fix_zip_code( $zip ) {
        $fixed = preg_replace( '([^0-9])', '', $zip );

        return $fixed;
    }

    /**
     * Gets the weight and dimensions of the order.
     *
     * @param array $package
     *
     * @return array
     */
    protected function order_weight_dimensions( $package ) {
        $count  = 0;
        $height = array();
        $width  = array();
        $length = array();
        $weight = array();

        // Shipping per item.
        foreach ( $package['contents'] as $item_id => $values ) {
            $product = $values['data'];
            $qty = $values['quantity'];

            if ( $qty > 0 && $product->needs_shipping() && $product->has_dimensions() ) {

                $_height = woocommerce_get_dimension( $this->fix_format( $product->height ), 'cm' );
                $_width  = woocommerce_get_dimension( $this->fix_format( $product->width ), 'cm' );
                $_length = woocommerce_get_dimension( $this->fix_format( $product->length ), 'cm' );
                $_weight = woocommerce_get_weight( $this->fix_format( $product->weight ), 'kg' );

                $height[ $count ] = $_height;
                $width[ $count ]  = $_width;
                $length[ $count ] = $_length;
                $weight[ $count ] = $_weight;

                if ( $qty > 1 ) {
                    $n = $count;
                    for ( $i = 0; $i < $qty; $i++ ) {
                        $height[ $n ] = $_height;
                        $width[ $n ]  = $_width;
                        $length[ $n ] = $_length;
                        $weight[ $n ] = $_weight;
                        $n++;
                    }
                    $count = $n;
                }

                $count++;
            }
        }

        return array(
            'height' => array_values( $height ),
            'length' => array_values( $length ),
            'width'  => array_values( $width ),
            'weight' => array_sum( $weight ),
        );
    }

    /**
     * Gets the service name.
     *
     * @return array
     */
    protected function get_service_name( $service ) {
        $name = array(
            '41106' => 'PAC',        // no contract.
            '40010' => 'SEDEX',      // no contract.
            '40215' => 'SEDEX 10',   // no contract.
            '40290' => 'SEDEX Hoje', // no contract.
            '41068' => 'PAC',        // with contract.
            '40096' => 'SEDEX',      // with contract.
            '81019' => 'e-SEDEX',    // with contract.
        );

        return $name[ $service ];
    }

    /**
     * Gets the services IDs.
     *
     * @return array
     */
    protected function correios_services() {
        $services = array();

        $services['PAC'] = ( 'yes' == $this->service_pac ) ? '41106' : '';
        $services['SEDEX'] = ( 'yes' == $this->service_sedex ) ? '40010' : '';
        $services['SEDEX 10'] = ( 'yes' == $this->service_sedex_10 ) ? '40215' : '';
        $services['SEDEX Hoje'] = ( 'yes' == $this->service_sedex_hoje ) ? '40290' : '';

        if ( 'corporate' == $this->corporate_service ) {
            $services['PAC'] = ( 'yes' == $this->service_pac ) ? '41068' : '';
            $services['SEDEX'] = ( 'yes' == $this->service_sedex ) ? '40096' : '';
            $services['e-SEDEX'] = ( 'yes' == $this->service_esedex ) ? '81019' : '';
        }

        return array_filter( $services );
    }

    /**
     * estimating_delivery function.
     *
     * @param string $label
     * @param string $date
     *
     * @return string
     */
    protected function estimating_delivery( $label, $date ) {
        $msg = $label;

        if ( $this->additional_time > 0 )
            $date += (int) $this->additional_time;

        if ( $date > 0 )
            $msg .= ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days', $date, 'wccorreios' ),  $date ) . ')';

        return $msg;
    }

    /**
     * Connection method.
     *
     * @param array  $services        Correios services.
     * @param mixed  $zip_origin      Zip Code of the origin.
     * @param mixed  $zip_destination Zip Code of the destination.
     * @param float  $height          Height total.
     * @param float  $width           Width total.
     * @param float  $diameter        Diamenter total.
     * @param float  $length          Length total.
     * @param float  $weight          Weight total.
     * @param string $login           Correios user.
     * @param string $password        Correios user password.
     * @param float  $declared        Declared value.
     * @param int    $format          Format.
     * @param string $own_hand        Own hand option.
     * @param string $receipt_notice  Notice.
     *
     * @return array                  Quotes.
     */
    protected function correios_connect(
        $services,
        $zip_origin,
        $zip_destination,
        $height,
        $width,
        $diameter,
        $length,
        $weight,
        $login          = null,
        $password       = null,
        $declared       = '0',
        $format         = '1',
        $own_hand       = 'N',
        $receipt_notice = 'N' ) {

        $quotes = array();

        foreach ( $services as $service ) {

            // Sets the get query.
            $query = http_build_query( array(
                'nCdServico' => $service,
                'nCdEmpresa' => $login,
                'sDsSenha' => $password,
                'sCepDestino' => $this->fix_zip_code( $zip_destination ),
                'sCepOrigem' => $this->fix_zip_code( $zip_origin ),
                'nVlAltura' => $this->fix_simplexml_format( $height ),
                'nVlLargura' => $this->fix_simplexml_format( $width ),
                'nVlDiametro' => $this->fix_simplexml_format( $diameter ),
                'nVlComprimento' => $this->fix_simplexml_format( $length ),
                'nVlPeso' => $this->fix_simplexml_format( $weight ),
                'nCdFormato' => $format,
                'sCdMaoPropria' => $own_hand,
                'nVlValorDeclarado' => $declared,
                'sCdAvisoRecebimento' => $receipt_notice,
                'StrRetorno' => 'xml'
            ), '', '&' );

            if ( 'yes' == $this->debug )
                $this->log->add( 'correios', 'Requesting the Correios WebServices...' );

            // Gets the WebServices response.
            $response = wp_remote_get( $this->webservice . $query, array( 'sslverify' => false, 'timeout' => 30 ) );

            if ( is_wp_error( $response ) ) {
                if ( 'yes' == $this->debug )
                    $this->log->add( 'correios', 'WP_Error: ' . $response->get_error_message() );
            } elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                $result = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );

                if ( 'yes' == $this->debug )
                    $this->log->add( 'correios', 'Correios WebServices response [' . $this->get_service_name( $service ) . ']: ' . print_r( $result->cServico, true ) );

                $quotes[ $service ] = $result->cServico;
            } else {
                if ( 'yes' == $this->debug )
                    $this->log->add( 'correios', 'Error accessing the Correios WebServices [' . $this->get_service_name( $service ) . ']: ' . $response['response']['code'] . ' - ' . $response['response']['message'] );
            }
        }

        return $quotes;
    }

    /**
     * Gets the price of shipping.
     *
     * @param  array $package Order package.
     *
     * @return array          Correios Quotes.
     */
    protected function correios_quote( $package ) {
        global $woocommerce;

        include_once WOO_CORREIOS_PATH . 'includes/class-wc-correios-cubage.php';

        // Proccess measures.
        $measures = apply_filters( 'wccorreios_default_package', $this->order_weight_dimensions( $package ) );

        // Checks if the cart is not just virtual goods.
        if ( ! empty( $measures['height'] ) && ! empty( $measures['width'] ) && ! empty( $measures['length'] ) ) {

            // Get the Cubage.
            $cubage = new WC_Correios_Cubage( $measures['height'], $measures['width'], $measures['length'] );
            $totalcubage = $cubage->cubage();

            $services = array_values( $this->correios_services() );
            $zip_destination = $package['destination']['postcode'];

            // Test min values.
            $min_height = $this->minimum_height;
            $min_width  = $this->minimum_width;
            $min_length = $this->minimum_length;

            $height = ( $totalcubage['height'] < $min_height ) ? $min_height : $totalcubage['height'];
            $width  = ( $totalcubage['width'] < $min_width ) ? $min_width : $totalcubage['width'];
            $length = ( $totalcubage['length'] < $min_length ) ? $min_length : $totalcubage['length'];

            if ( 'yes' == $this->debug ) {
                $weight_cubage = array(
                    'weight' => $measures['weight'],
                    'height' => $height,
                    'width'  => $width,
                    'length' => $length
                );

                $this->log->add( 'correios', 'Weight and cubage of the order: ' . print_r( $weight_cubage, true ) );
            }

            $declared = 0;
            if ( 'declare' == $this->declare_value )
                $declared = $woocommerce->cart->cart_contents_total;

            // Get quotes.
            $quotes = $this->correios_connect(
                $services,
                $this->zip_origin,
                $zip_destination,
                $height,
                $width,
                0,
                $length,
                $measures['weight'],
                $this->login,
                $this->password,
                $declared
            );

            return $quotes;

        } else {

            // Cart only with virtual products.
            if ( 'yes' == $this->debug )
                $this->log->add( 'correios', 'Cart only with virtual products.' );

            return array();
        }
    }

    /**
     * Calculates the shipping rate.
     *
     * @param array $package Order package.
     *
     * @return void
     */
    public function calculate_shipping( $package = array() ) {
        global $woocommerce;

        $rates = array();

        $quotes = $this->correios_quote( $package );

        if ( $quotes ) {
            foreach ( $quotes as $key => $value ) {
                $name = $this->get_service_name( $key );

                if ( 0 == $value->Erro ) {

                    $label = ( 'yes' == $this->display_date ) ? $this->estimating_delivery( $name, $value->PrazoEntrega ) : $name;
                    $cust = $this->fix_format( esc_attr( $value->Valor ) );
                    $fee = $this->get_fee( $this->fix_format( $this->fee ), $cust );

                    array_push(
                        $rates,
                        array(
                            'id'    => $name,
                            'label' => $label,
                            'cost'  => $cust + $fee,
                        )
                    );
                }
            }

            $rate = apply_filters( 'woocommerce_correios_shipping_methods', $rates, $package );

            // Register the rate.
            foreach ( $rate as $key => $value )
                $this->add_rate( $value );
        }
    }
}
