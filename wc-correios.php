<?php
/**
 * Plugin Name: WooCommerce Correios
 * Plugin URI: http://www.claudiosmweb.com/
 * Description: Correios para WooCommerce
 * Author: claudiosanches, rodrigoprior
 * Author URI: http://www.claudiosmweb.com/
 * Version: 1.0.1
 * License: GPLv2 or later
 * Text Domain: wccorreios
 * Domain Path: /languages/
 */

define( 'WOO_CORREIOS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * WooCommerce fallback notice.
 */
function wccorreios_woocommerce_fallback_notice() {
    $message = '<div class="error">';
        $message .= '<p>' . __( 'WooCommerce Correios depends on <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to work!' , 'wccorreios' ) . '</p>';
    $message .= '</div>';

    echo $message;
}

/**
 * WooCommerce SOAP missing notice.
 */
function wccorreios_woocommerce_soap_missing_notice() {
    $message = '<div class="error">';
        $message .= '<p>' . __( 'WooCommerce Correios depends to <a href="http://php.net/manual/en/book.soap.php">SOAP</a> to work!' , 'wccorreios' ) . '</p>';
    $message .= '</div>';

    echo $message;
}

/**
 * Load functions.
 */
add_action( 'plugins_loaded', 'wccorreios_shipping_load', 0 );

function wccorreios_shipping_load() {

    if ( !class_exists( 'WC_Shipping_Method' ) ) {
        add_action( 'admin_notices', 'wccorreios_woocommerce_fallback_notice' );

        return;
    }

    if ( !extension_loaded( 'soap' ) ) {
        add_action( 'admin_notices', 'wccorreios_woocommerce_soap_missing_notice' );

        return;
    }

    /**
     * Load textdomain.
     */
    load_plugin_textdomain( 'wccorreios', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * wccorreios_add_method function.
     *
     * @package     WooCommerce/Classes/Shipping
     * @access public
     * @param array $methods
     * @return array
     */
    function wccorreios_add_method( $methods ) {
        $methods[] = 'WC_Correios';
        return $methods;
    }

    add_filter( 'woocommerce_shipping_methods', 'wccorreios_add_method' );

    /**
     * WC_Correios class.
     */
    class WC_Correios extends WC_Shipping_Method {

        /**
         * __construct function.
         *
         * @access public
         * @return void
         */
        function __construct() {
            $this->id           = 'correios';
            $this->method_title = __('Correios', 'wccorreios');
            $this->init();
        }

        /**
         * init function.
         *
         * @access public
         * @return void
         */
        function init() {
            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user set variables
            $this->enabled            = $this->settings['enabled'];
            $this->title              = $this->settings['title'];
            $this->declare_value      = $this->settings['declare_value'];
            $this->display_date       = $this->settings['display_date'];
            $this->availability       = $this->settings['availability'];
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

            add_action( 'woocommerce_update_options_shipping_'.$this->id, array( &$this, 'process_admin_options' ) );
        }

        /**
         * init_form_fields function.
         *
         * @access public
         * @return void
         */
        function init_form_fields() {
            global $woocommerce;
            $this->form_fields = array(
                'enabled' => array(
                    'title'            => __( 'Enable', 'wccorreios' ),
                    'type'             => 'checkbox',
                    'label'            => __( 'Enable Correios', 'wccorreios' ),
                    'default'          => 'no'
                ),
                'title' => array(
                    'title'            => __( 'Title', 'wccorreios' ),
                    'type'             => 'text',
                    'description'      => __( 'This controls the title which the user sees during checkout.', 'wccorreios' ),
                    'default'          => __( 'Correios', 'wccorreios' )
                ),
                'availability' => array(
                    'title'            => __( 'Method availability', 'wccorreios' ),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'availability',
                    'options'          => array(
                        'all'          => __('All allowed countries', 'wccorreios'),
                        'specific'     => __('Specific Countries', 'wccorreios')
                    )
                ),
                'countries' => array(
                    'title'            => __( 'Specific Countries', 'wccorreios' ),
                    'type'             => 'multiselect',
                    'class'            => 'chosen_select',
                    'css'              => 'width: 450px;',
                    'default'          => '',
                    'options'          => $woocommerce->countries->countries
                ),
                'zip_origin' => array(
                    'title'            => __( 'Origin Zip Code', 'wccorreios' ),
                    'type'             => 'text',
                    'description'      => __( 'Zip Code from where the requests are sent', 'wccorreios' ),
                    'default'          => '',
                ),
                'declare_value' => array(
                    'title'            => __( 'Declare value', 'wccorreios' ),
                    'type'             => 'select',
                    'description'      => '',
                    'default'          => 'none',
                    'options'          => array(
                        'declare'      => __( 'Declare', 'wccorreios' ),
                        'none'         => __( 'None', 'wccorreios' ),
                    ),
                ),
                'display_date' => array(
                    'title'            => __( 'Estimated delivery', 'wccorreios' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Display date of estimated delivery', 'wccorreios' ),
                    'default'          => 'no',
                ),
                'services' => array(
                    'title'            => __( 'Correios Services', 'wccorreios' ),
                    'type'             => 'title',
                    'description'      => '',
                    'default'          => ''
                ),
                'corporate_service' => array(
                    'title'            => __( 'Corporate Service', 'wccorreios' ),
                    'type'             => 'select',
                    'description'      => __( 'Choose between conventional or corporate service', 'wccorreios' ),
                    'default'          => 'conventional',
                    'options'          => array(
                        'conventional' => __( 'Conventional', 'wccorreios' ),
                        'corporate'    => __( 'Corporate', 'wccorreios' ),
                    ),
                ),
                'login' => array(
                    'title'            => __( 'Administrative Code', 'wccorreios' ),
                    'type'             => 'text',
                    'description'      => __( 'Your Correios login', 'wccorreios' ),
                    'default'          => '',
                ),
                'password' => array(
                    'title'            => __( 'Administrative Password', 'wccorreios' ),
                    'type'             => 'password',
                    'description'      => __( 'Your Correios password', 'wccorreios' ),
                    'default'          => '',
                ),
                'service_pac' => array(
                    'title'            => __( 'PAC', 'wccorreios' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Shipping via PAC', 'wccorreios' ),
                    'default'          => 'no',
                ),
                'service_sedex' => array(
                    'title'            => __( 'SEDEX', 'wccorreios' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Shipping via SEDEX', 'wccorreios' ),
                    'default'          => 'no',
                ),
                'service_sedex_10' => array(
                    'title'            => __( 'SEDEX 10', 'wccorreios' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Shipping via SEDEX 10', 'wccorreios' ),
                    'default'          => 'no',
                ),
                'service_sedex_hoje' => array(
                    'title'            => __( 'SEDEX Hoje', 'wccorreios' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Shipping via SEDEX Hoje', 'wccorreios' ),
                    'default'          => 'no',
                ),
                'service_esedex' => array(
                    'title'            => __( 'e-SEDEX', 'wccorreios' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Shipping via e-SEDEX', 'wccorreios' ),
                    'default'          => 'no',
                ),
                'package_standard' => array(
                    'title' => __( 'Package Standard', 'wccorreios' ),
                    'type' => 'title',
                    'description' => __( 'Sets a minimum measure for the package', 'wccorreios' ),
                    'default' => ''
                ),
                'minimum_height' => array(
                    'title'            => __( 'Minimum Height', 'wccorreios' ),
                    'type'             => 'text',
                    'description'      => __( 'Minimum height of the package. Correios needs at least 2 cm', 'wccorreios' ),
                    'default'          => '2',
                ),
                'minimum_width' => array(
                    'title'            => __( 'Minimum Width', 'wccorreios' ),
                    'type'             => 'text',
                    'description'      => __( 'Minimum width of the package. Correios needs at least 11 cm', 'wccorreios' ),
                    'default'          => '11',
                ),
                'minimum_length' => array(
                    'title'            => __( 'Minimum Length', 'wccorreios' ),
                    'type'             => 'text',
                    'description'      => __( 'Minimum length of the package. Correios needs at least 16 cm', 'wccorreios' ),
                    'default'          => '16',
                )
            );
        }

        /**
         * admin_options function.
         *
         * @access public
         * @return void
         */
        function admin_options() {
            global $woocommerce; ?>
            <h3><?php echo $this->method_title; ?></h3>
            <p><?php _e( 'Correios is a brazilian delivery method.', 'wccorreios' ); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table>
            <script src="<?php echo plugins_url( 'js/options.js', __FILE__ ); ?>" type="text/javascript"></script>
            <?php
        }

        /**
         * is_available function.
         *
         * @access public
         * @param array $package
         * @return bool
         */
        function is_available( $package ) {
            global $woocommerce;
            $is_available = true;

            if ( $this->enabled == 'no' ) {
                $is_available = false;
            } else {
                $ship_to_countries = '';

                if ( $this->availability == 'specific' ) {
                    $ship_to_countries = $this->countries;
                } elseif ( get_option( 'woocommerce_allowed_countries' ) == 'specific' ) {
                    $ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );
                }

                if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
                    $is_available = false;
                }
            }

            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
        }

        /**
         * calculate_shipping function.
         *
         * @access public
         * @param array $package (default: array())
         * @return void
         */
        function calculate_shipping( $package = array() ) {
            global $woocommerce;

            $rate = array();

            $quotes = $this->correios_connect( $package );
            $list = $this->correios_services_list();

            foreach ( $quotes as $key => $value ) {

                if ( $value->Erro == 0 ) {

                    $label = ( $this->display_date == 'yes' ) ? $this->estimating_delivery( $list[$key], $value->PrazoEntrega ) : $list[$key];

                    array_push(
                        $rate,
                        array(
                            'id'    => $list[$key],
                            'label' => $label,
                            'cost'  => esc_attr( $value->Valor ),
                        )
                    );
                }

            }

            // Register the rate
            foreach ( $rate as $key => $value) {
                $this->add_rate( $value );
            }

        }

        /**
         * order_shipping function.
         *
         * @access public
         * @param array $package
         * @return float
         */
        function order_shipping( $package ) {
            $count     = 0;
            $height    = array();
            $width     = array();
            $length    = array();
            $weight    = '';

            // Shipping per item
            foreach ( $package['contents'] as $item_id => $values ) {
                $product = $values['data'];
                $qty = $values['quantity'];

                if ( $qty > 0 && $product->needs_shipping() ) {
                    $height[$count] = $product->height;
                    $width[$count]  = $product->width;
                    $length[$count] = $product->length;
                    $weight        += $product->weight;

                    if ( $qty > 1 ) {
                        $n = $count;
                        for ($i = 0; $i < $qty; $i++) {
                            $height[$n] = $product->height;
                            $width[$n]  = $product->width;
                            $length[$n] = $product->length;
                            $weight    += $product->weight;
                            $n++;
                        }
                        $count = $n;
                    }

                    $count++;
                }
            }

            return array(
                'height' => $height,
                'length' => $length,
                'width'  => $width,
                'weight' => $weight,
            );
        }

        /**
         * correios_services_list function.
         *
         * @access public
         * @return array
         */
        function correios_services_list() {
            $list = array(
                '41106' => 'PAC',        // sem contrato
                '40010' => 'SEDEX',      // sem contrato
                '40215' => 'SEDEX 10',   // sem contrato
                '40290' => 'SEDEX Hoje', // sem contrato
                '41068' => 'PAC',        // com contrato
                '40096' => 'SEDEX',      // com contrato
                '81019' => 'e-SEDEX',    // com contrato
            );

            return $list;
        }
        /**
         * correios_services function.
         *
         * @access public
         * @return array
         */
        function correios_services() {
            $services = array();

            $services['PAC'] = ( $this->service_pac == 'yes' ) ? '41106' : '';
            $services['SEDEX'] = ( $this->service_sedex == 'yes' ) ? '40010' : '';
            $services['SEDEX 10'] = ( $this->service_sedex_10 == 'yes' ) ? '40215' : '';
            $services['SEDEX Hoje'] = ( $this->service_sedex_hoje == 'yes' ) ? '40290' : '';

            if ( $this->corporate_service == 'corporate' ) {
                $services['PAC'] = ( $this->service_pac == 'yes' ) ? '41068' : '';
                $services['SEDEX'] = ( $this->service_sedex == 'yes' ) ? '40096' : '';
                $services['e-SEDEX'] = ( $this->service_esedex == 'yes' ) ? '81019' : '';
            }

            return array_filter( $services );
        }

        /**
         * estimating_delivery function.
         *
         * @access public
         * @param string $label
         * @param string $date
         * @return string
         */
        function estimating_delivery( $label, $date ) {
            $msg = $label;

            if ( $date > 0 ) {
                $msg = $label . ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days' ,$date , 'wccorreios' ),  $date ) . ')';
            }

            return $msg;
        }

        /**
         * correios_connect function.
         *
         * @access public
         * @param array $package (default: array())
         * @return object
         */
        function correios_connect( $package ) {
            global $woocommerce;

            include_once WOO_CORREIOS_PATH . 'Correios/SOAP.php';
            include_once WOO_CORREIOS_PATH . 'Correios/Cubage.php';

            // Proccess measures.
            $measures = $this->order_shipping( $package );
            $cubage = new Correios_Cubage( $measures['height'], $measures['width'], $measures['length'] );
            $totalcubage = $cubage->cubage();

            $services = array_values( $this->correios_services() );
            $zipDestination = $package['destination']['postcode'];

            // Test min values
            $min_height = $this->minimum_height;
            $min_width  = $this->minimum_width;
            $min_length = $this->minimum_length;

            $height = ( $totalcubage['height'] < $min_height ) ? $min_height : $totalcubage['height'];
            $width  = ( $totalcubage['width'] < $min_width ) ? $min_width : $totalcubage['width'];
            $length = ( $totalcubage['length'] < $min_length ) ? $min_length : $totalcubage['length'];

            $declared = '0';
            if ( $this->declare_value == 'declare' ) {
                $declared = $woocommerce->cart->cart_contents_total;
            }

            $quotes = new Correios_SOAP(
                $services,
                $this->zip_origin,
                $zipDestination,
                $height,
                $width,
                0,
                $length,
                $measures['weight'],
                $this->login,
                $this->password,
                $declared
            );

            return $quotes->calculateShipping();
        }

    } // class WC_Correios

} // function wccorreios_shipping_load
