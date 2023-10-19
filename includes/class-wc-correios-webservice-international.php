<?php
/**
 * Correios Webservice International.
 *
 * @package WooCommerce_Correios/Classes/Webservice
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Webservice International integration class.
 */
class WC_Correios_Webservice_International {

	/**
	 * Webservice URL.
	 *
	 * @var string
	 */
	private $webservice_base = 'https://cws2.correios.com.br/precoprazoservice/rs/v1/internacional/preco-prazo/';

	/**
	 * Shipping method ID.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Shipping zone instance ID.
	 *
	 * @var int
	 */
	protected $instance_id = 0;

	/**
	 * IDs from Correios services.
	 *
	 * 45209 - Exporta Fácil Econômico.
	 * 45110 - Exporta Fácil Expresso.
	 * 45195 - Exporta Fácil Premium.
	 * 45128 - Exporta Fácil Standard.
	 * 45020 - Documento Econômico.
	 * 45012 - Documento Internacional Expresso.
	 * 45179 - Documento Internacional Premium.
	 * 45039 - Documento Internacional Standard.
	 *
	 * @var string
	 */
	protected $service = '';

	/**
	 * WooCommerce package containing the products.
	 *
	 * @var array
	 */
	protected $package = null;

	/**
	 * Origin postcode.
	 *
	 * @var string
	 */
	protected $origin_postcode = '';

	/**
	 * Destination country.
	 *
	 * @var string
	 */
	protected $destination_country = '';

	/**
	 * Login.
	 *
	 * @var string
	 */
	protected $login = '';

	/**
	 * Password.
	 *
	 * @var string
	 */
	protected $password = '';

	/**
	 * Package height.
	 *
	 * @var float
	 */
	protected $height = 0;

	/**
	 * Package width.
	 *
	 * @var float
	 */
	protected $width = 0;

	/**
	 * Package diameter.
	 *
	 * @var float
	 */
	protected $diameter = 0;

	/**
	 * Package length.
	 *
	 * @var float
	 */
	protected $length = 0;

	/**
	 * Package weight.
	 *
	 * @var float
	 */
	protected $weight = 0;

	/**
	 * Minimum height.
	 *
	 * @var float
	 */
	protected $minimum_height = 2;

	/**
	 * Minimum width.
	 *
	 * @var float
	 */
	protected $minimum_width = 11;

	/**
	 * Minimum length.
	 *
	 * @var float
	 */
	protected $minimum_length = 16;

	/**
	 * Extra weight.
	 *
	 * @var float
	 */
	protected $extra_weight = 0;

	/**
	 * Declared value.
	 *
	 * @var string
	 */
	protected $declared_value = '0';

	/**
	 * Package format.
	 *
	 * 1 – envelope
	 * 2 – box/package
	 * 3 – roll/prism
	 *
	 * @var string
	 */
	protected $format = '2';

	/**
	 * Debug mode.
	 *
	 * @var string
	 */
	protected $debug = 'no';

	/**
	 * Log.
	 *
	 * @var WC_Logger
	 */
	public $log = null;

	/**
	 * Country first city.
	 *
	 * @var array
	 */
	public $country_first_city = array(
		'AD' => '00423315',
		'AE' => '00237089',
		'AF' => '00429640',
		'AG' => '00211782',
		'AI' => '00211784',
		'AL' => '00429462',
		'AM' => '00237091',
		'AO' => '00210539',
		'AR' => '00237114',
		'AS' => '00105722',
		'AT' => '00238077',
		'AU' => '00240478',
		'AW' => '00423317',
		'AX' => '',
		'AZ' => '00120007',
		'BA' => '00245812',
		'BB' => '00120012',
		'BD' => '00245881',
		'BE' => '00150780',
		'BF' => '00245959',
		'BG' => '00209949',
		'BH' => '00112088',
		'BI' => '00210046',
		'BJ' => '00210050',
		'BL' => '',
		'BM' => '00423319',
		'BN' => '00231210',
		'BO' => '00210127',
		'BQ' => '',
		'BS' => '00246233',
		'BT' => '00210133',
		'BV' => '',
		'BW' => '00233583',
		'BY' => '00246255',
		'BZ' => '00423320',
		'CA' => '00246385',
		'CC' => '00250695',
		'CD' => '00250696',
		'CF' => '00250727',
		'CG' => '00180779',
		'CH' => '00110178',
		'CI' => '00252080',
		'CK' => '00180789',
		'CL' => '00252167',
		'CM' => '00252575',
		'CN' => '00121447',
		'CO' => '00253359',
		'CR' => '00253991',
		'CU' => '00254056',
		'CV' => '00432106',
		'CW' => '00211790',
		'CX' => '00254310',
		'CY' => '00254311',
		'CZ' => '00254826',
		'DE' => '00216904',
		'DJ' => '00206016',
		'DK' => '00214596',
		'DM' => '00071986',
		'DO' => '00206260',
		'DZ' => '00065419',
		'EC' => '00205880',
		'EE' => '00262698',
		'EG' => '00263269',
		'EH' => '',
		'ER' => '00115361',
		'ES' => '00263296',
		'ET' => '00205801',
		'FI' => '00285243',
		'FJ' => '00287679',
		'FK' => '00071313',
		'FM' => '00287691',
		'FO' => '00287693',
		'FR' => '00287758',
		'GA' => '00308047',
		'GB' => '00092222',
		'GD' => '00132035',
		'GE' => '00312851',
		'GF' => '00132368',
		'GG' => '00429353',
		'GH' => '00132697',
		'GI' => '00312861',
		'GL' => '00312862',
		'GM' => '00429273',
		'GN' => '00133030',
		'GP' => '00312891',
		'GQ' => '00312904',
		'GR' => '00115611',
		'GS' => '',
		'GT' => '00313131',
		'GU' => '00171913',
		'GW' => '00427626',
		'GY' => '00171914',
		'HK' => '00173914',
		'HM' => '',
		'HN' => '00313213',
		'HR' => '00313259',
		'HT' => '00429712',
		'HU' => '00131829',
		'ID' => '00321124',
		'IE' => '00321352',
		'IL' => '00321753',
		'IM' => '00429604',
		'IN' => '00322982',
		'IQ' => '00429701',
		'IR' => '00328084',
		'IS' => '00328396',
		'IT' => '00164251',
		'JE' => '00430238',
		'JM' => '00164264',
		'JO' => '00076832',
		'JP' => '00335781',
		'KE' => '00346064',
		'KG' => '00081112',
		'KH' => '00065423',
		'KI' => '00077150',
		'KM' => '00065496',
		'KN' => '00186263',
		'KP' => '00065505',
		'KR' => '00099342',
		'KW' => '00164965',
		'KY' => '00217344',
		'KZ' => '00348521',
		'LA' => '00160965',
		'LB' => '00432202',
		'LC' => '00186264',
		'LI' => '00161636',
		'LK' => '00186608',
		'LR' => '00161642',
		'LS' => '00112527',
		'LT' => '00348557',
		'LU' => '00162317',
		'LV' => '00348605',
		'LY' => '00158731',
		'MA' => '00349662',
		'MC' => '00426942',
		'MD' => '00349699',
		'ME' => '00429635',
		'MF' => '',
		'MG' => '00349742',
		'MH' => '00159071',
		'MK' => '00349846',
		'ML' => '00159073',
		'MM' => '00077122',
		'MN' => '00159413',
		'MO' => '00077449',
		'MP' => '00154722',
		'MQ' => '00077460',
		'MR' => '00349888',
		'MS' => '00423327',
		'MT' => '00159446',
		'MU' => '00159460',
		'MV' => '00349904',
		'MW' => '00154766',
		'MX' => '00349918',
		'MY' => '00157831',
		'MZ' => '00076276',
		'NA' => '00168852',
		'NC' => '00433641',
		'NE' => '00169201',
		'NF' => '00350209',
		'NG' => '00169871',
		'NI' => '00350213',
		'NL' => '00165907',
		'NO' => '00169055',
		'NP' => '00352135',
		'NR' => '00352137',
		'NU' => '00433767',
		'NZ' => '00158516',
		'OM' => '00167872',
		'PA' => '00429766',
		'PE' => '00352175',
		'PF' => '00433776',
		'PG' => '00166903',
		'PH' => '00216418',
		'PK' => '00108702',
		'PL' => '00161315',
		'PM' => '00353070',
		'PN' => '',
		'PR' => '00079462',
		'PS' => '00425049',
		'PT' => '00203727',
		'PW' => '00108127',
		'PY' => '00354368',
		'QA' => '00431912',
		'RE' => '00075366',
		'RO' => '00354423',
		'RS' => '00432147',
		'RU' => '00360918',
		'RW' => '00432333',
		'SA' => '00384658',
		'SB' => '00201307',
		'SC' => '00429419',
		'SD' => '00384718',
		'SE' => '00185694',
		'SG' => '00429566',
		'SH' => '',
		'SI' => '00385663',
		'SJ' => '',
		'SK' => '00216850',
		'SL' => '00386189',
		'SM' => '00386197',
		'SN' => '00386214',
		'SO' => '00114286',
		'SR' => '00386312',
		'SS' => '00432324',
		'ST' => '00433526',
		'SV' => '00212169',
		'SX' => '',
		'SY' => '00079552',
		'SZ' => '00386558',
		'TC' => '00186363',
		'TD' => '00386568',
		'TF' => '',
		'TG' => '00386608',
		'TH' => '00386628',
		'TJ' => '00387119',
		'TK' => '',
		'TL' => '00429697',
		'TM' => '00427588',
		'TN' => '00387125',
		'TO' => '00188086',
		'TR' => '00387757',
		'TT' => '00081896',
		'TV' => '00113649',
		'TW' => '00081940',
		'TZ' => '00186864',
		'UA' => '00388996',
		'UG' => '00187526',
		'UM' => '',
		'US' => '00389091',
		'UY' => '00430241',
		'UZ' => '00422535',
		'VA' => '00422547',
		'VC' => '00187857',
		'VE' => '00188874',
		'VG' => '00422618',
		'VI' => '00082946',
		'VN' => '00080978',
		'VU' => '00185615',
		'WF' => '00433475',
		'WS' => '00114662',
		'YE' => '00111875',
		'YT' => '00429581',
		'ZA' => '00205717',
		'ZM' => '00185617',
		'ZW' => '00186289',
	);

	/**
	 * Initialize webservice.
	 *
	 * @param string $id Method ID.
	 * @param int    $instance_id Instance ID.
	 */
	public function __construct( $id = 'correios', $instance_id = 0 ) {
		$this->id          = $id;
		$this->instance_id = $instance_id;
		$this->log         = wc_get_logger();
	}

	/**
	 * Set the service.
	 *
	 * @param string $service Correios international service.
	 */
	public function set_service( $service = '' ) {
		$this->service = $service;
	}

	/**
	 * Set shipping package.
	 *
	 * @param array $package Shipping package.
	 */
	public function set_package( $package = array() ) {
		$this->package    = $package;
		$correios_package = new WC_Correios_Package( $package );

		if ( ! is_null( $correios_package ) ) {
			$data = $correios_package->get_data();

			$this->set_height( $data['height'] );
			$this->set_width( $data['width'] );
			$this->set_length( $data['length'] );
			$this->set_weight( wc_get_weight( $data['weight'], 'g', 'kg' ) );
		}

		if ( 'yes' === $this->debug ) {
			if ( ! empty( $data ) ) {
				$data = array(
					'weight' => $this->get_weight(),
					'height' => $this->get_height(),
					'width'  => $this->get_width(),
					'length' => $this->get_length(),
				);
			}

			$this->log->add( $this->id, 'Weight and cubage of the order: ' . wc_print_r( $data, true ) );
		}
	}

	/**
	 * Set origin postcode.
	 *
	 * @param string $postcode Origin postcode.
	 */
	public function set_origin_postcode( $postcode = '' ) {
		$this->origin_postcode = $postcode;
	}

	/**
	 * Set destination country.
	 *
	 * @param string $country Destination country.
	 */
	public function set_destination_country( $country = '' ) {
		$this->destination_country = $country;
	}

	/**
	 * Set origin location.
	 *
	 * @deprecated
	 * @param string $location Origin location.
	 */
	public function set_origin_location( $location = '' ) {
	}

	/**
	 * Set origin state.
	 *
	 * @deprecated
	 * @param string $state Origin state.
	 */
	public function set_origin_state( $state = '' ) {
	}

	/**
	 * Set login.
	 *
	 * @param string $login User login.
	 */
	public function set_login( $login = '' ) {
		$this->login = $login;
	}

	/**
	 * Set password.
	 *
	 * @param string $password User login.
	 */
	public function set_password( $password = '' ) {
		$this->password = $password;
	}

	/**
	 * Set shipping package height.
	 *
	 * @param float $height Shipping package height.
	 */
	public function set_height( $height = 0 ) {
		$this->height = (float) $height;
	}

	/**
	 * Set shipping package width.
	 *
	 * @param float $width Shipping package width.
	 */
	public function set_width( $width = 0 ) {
		$this->width = (float) $width;
	}

	/**
	 * Set shipping package diameter.
	 *
	 * @param float $diameter Package diameter.
	 */
	public function set_diameter( $diameter = 0 ) {
		$this->diameter = (float) $diameter;
	}

	/**
	 * Set shipping package length.
	 *
	 * @param float $length Shipping package length.
	 */
	public function set_length( $length = 0 ) {
		$this->length = (float) $length;
	}

	/**
	 * Set shipping package weight.
	 *
	 * @param float $weight Shipping package weight.
	 */
	public function set_weight( $weight = 0 ) {
		$this->weight = (float) $weight;
	}

	/**
	 * Set minimum height.
	 *
	 * @param float $minimum_height Package minimum height.
	 */
	public function set_minimum_height( $minimum_height = 2 ) {
		$this->minimum_height = 2 <= $minimum_height ? $minimum_height : 2;
	}

	/**
	 * Set minimum width.
	 *
	 * @param float $minimum_width Package minimum width.
	 */
	public function set_minimum_width( $minimum_width = 11 ) {
		$this->minimum_width = 11 <= $minimum_width ? $minimum_width : 11;
	}

	/**
	 * Set minimum length.
	 *
	 * @param float $minimum_length Package minimum length.
	 */
	public function set_minimum_length( $minimum_length = 16 ) {
		$this->minimum_length = 16 <= $minimum_length ? $minimum_length : 16;
	}

	/**
	 * Set extra weight.
	 *
	 * @param float $extra_weight Package extra weight.
	 */
	public function set_extra_weight( $extra_weight = 0 ) {
		$this->extra_weight = (float) wc_format_decimal( $extra_weight );
	}

	/**
	 * Set declared value.
	 *
	 * @param string $declared_value Declared value.
	 */
	public function set_declared_value( $declared_value = '0' ) {
		$this->declared_value = $declared_value;
	}

	/**
	 * Set shipping package format.
	 *
	 * @param string $format Package format.
	 */
	public function set_format( $format = '2' ) {
		$this->format = $format;
	}

	/**
	 * Set the debug mode.
	 *
	 * @param string $debug Yes or no.
	 */
	public function set_debug( $debug = 'no' ) {
		$this->debug = $debug;
	}

	/**
	 * Get webservice URL.
	 *
	 * @return string
	 */
	public function get_webservice_url() {
		return apply_filters(
			'woocommerce_correios_webservice_international_url',
			$this->webservice_base . $this->service . '?',
			$this->id,
			$this->instance_id,
			$this->package
		);
	}

	/**
	 * Get allowed countries.
	 *
	 * @return string
	 */
	public function get_allowed_countries() {
		// This list of countries was obtained from https://apps2.correios.com.br/efi/app/simulaPrecoPrazoInternacional/index.php on 2020-10-08. Countries not supported by WooCommerce were removed.
		return apply_filters(
			'woocommerce_correios_international_allowed_countries',
			array(
				'AD',
				'AE',
				'AF',
				'AG',
				'AI',
				'AL',
				'AM',
				'AO',
				'AR',
				'AS',
				'AT',
				'AU',
				'AW',
				'AX',
				'AZ',
				'BA',
				'BB',
				'BD',
				'BE',
				'BF',
				'BG',
				'BH',
				'BI',
				'BJ',
				'BL',
				'BM',
				'BN',
				'BO',
				'BQ',
				'BS',
				'BT',
				'BV',
				'BW',
				'BY',
				'BZ',
				'CA',
				'CC',
				'CD',
				'CF',
				'CG',
				'CH',
				'CI',
				'CK',
				'CL',
				'CM',
				'CN',
				'CO',
				'CR',
				'CU',
				'CV',
				'CW',
				'CX',
				'CY',
				'CZ',
				'DE',
				'DJ',
				'DK',
				'DM',
				'DO',
				'DZ',
				'EC',
				'EE',
				'EG',
				'EH',
				'ER',
				'ES',
				'ET',
				'FI',
				'FJ',
				'FK',
				'FM',
				'FO',
				'FR',
				'GA',
				'GB',
				'GD',
				'GE',
				'GF',
				'GG',
				'GH',
				'GI',
				'GL',
				'GM',
				'GN',
				'GP',
				'GQ',
				'GR',
				'GS',
				'GT',
				'GU',
				'GW',
				'GY',
				'HK',
				'HM',
				'HN',
				'HR',
				'HT',
				'HU',
				'ID',
				'IE',
				'IL',
				'IM',
				'IN',
				'IQ',
				'IR',
				'IS',
				'IT',
				'JE',
				'JM',
				'JO',
				'JP',
				'KE',
				'KG',
				'KH',
				'KI',
				'KM',
				'KN',
				'KP',
				'KR',
				'KW',
				'KY',
				'KZ',
				'LA',
				'LB',
				'LC',
				'LI',
				'LK',
				'LR',
				'LS',
				'LT',
				'LU',
				'LV',
				'LY',
				'MA',
				'MC',
				'MD',
				'ME',
				'MF',
				'MG',
				'MH',
				'MK',
				'ML',
				'MM',
				'MN',
				'MO',
				'MP',
				'MQ',
				'MR',
				'MS',
				'MT',
				'MU',
				'MV',
				'MW',
				'MX',
				'MY',
				'MZ',
				'NA',
				'NC',
				'NE',
				'NF',
				'NG',
				'NI',
				'NL',
				'NO',
				'NP',
				'NR',
				'NU',
				'NZ',
				'OM',
				'PA',
				'PE',
				'PF',
				'PG',
				'PH',
				'PK',
				'PL',
				'PM',
				'PN',
				'PR',
				'PS',
				'PT',
				'PW',
				'PY',
				'QA',
				'RE',
				'RO',
				'RS',
				'RU',
				'RW',
				'SA',
				'SB',
				'SC',
				'SD',
				'SE',
				'SG',
				'SH',
				'SI',
				'SJ',
				'SK',
				'SL',
				'SM',
				'SN',
				'SO',
				'SR',
				'SS',
				'ST',
				'SV',
				'SX',
				'SY',
				'SZ',
				'TC',
				'TD',
				'TF',
				'TG',
				'TH',
				'TJ',
				'TK',
				'TL',
				'TM',
				'TN',
				'TO',
				'TR',
				'TT',
				'TV',
				'TW',
				'TZ',
				'UA',
				'UG',
				'UM',
				'US',
				'UY',
				'UZ',
				'VA',
				'VC',
				'VE',
				'VG',
				'VI',
				'VN',
				'VU',
				'WF',
				'WS',
				'YE',
				'YT',
				'ZA',
				'ZM',
				'ZW',
			)
		);
	}

	/**
	 * Get destination country.
	 *
	 * @return string
	 */
	public function get_destination_country() {
		return $this->destination_country;
	}

	/**
	 * Get origin postcode.
	 *
	 * @return string
	 */
	public function get_origin_postcode() {
		return apply_filters( 'woocommerce_correios_origin_postcode', $this->origin_postcode, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get login.
	 *
	 * @return string
	 */
	public function get_login() {
		return apply_filters( 'woocommerce_correios_login', $this->login, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get password.
	 *
	 * @return string
	 */
	public function get_password() {
		return apply_filters( 'woocommerce_correios_password', $this->password, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get height.
	 *
	 * @return float
	 */
	public function get_height() {
		return ( $this->minimum_height <= $this->height ? $this->height : $this->minimum_height );
	}

	/**
	 * Get width.
	 *
	 * @return float
	 */
	public function get_width() {
		return ( $this->minimum_width <= $this->width ? $this->width : $this->minimum_width );
	}

	/**
	 * Get diameter.
	 *
	 * @return float
	 */
	public function get_diameter() {
		return ( $this->diameter );
	}

	/**
	 * Get length.
	 *
	 * @return float
	 */
	public function get_length() {
		return ( $this->minimum_length <= $this->length ? $this->length : $this->minimum_length );
	}

	/**
	 * Get weight.
	 *
	 * @return float
	 */
	public function get_weight() {
		return ( $this->weight + $this->extra_weight );
	}

	/**
	 * Check if is setted.
	 *
	 * @return bool
	 */
	protected function is_setted() {
		$origin_postcode = $this->get_origin_postcode();

		return ! empty( $this->service ) || ! empty( $this->destination_country ) || ! in_array( $this->destination_country, $this->get_allowed_countries(), true ) || ! empty( $origin_postcode ) || 0 === $this->get_height();
	}

	/**
	 * Get shipping prices.
	 *
	 * @return object
	 */
	public function get_shipping() {
		$shipping = null;

		// Checks if services and postcode is empty.
		if ( ! $this->is_setted() ) {
			return $values;
		}

		$args = apply_filters(
			'woocommerce_correios_international_shipping_args',
			array(
				'cepOrigem'          => wc_correios_sanitize_postcode( $this->get_origin_postcode() ),
				'sgPaisDestino'      => $this->get_destination_country(),
				'coCidadeDestino'    => $this->country_first_city[ $this->get_destination_country() ],
				'altura'             => $this->get_height(),
				'largura'            => $this->get_width(),
				'diametro'           => $this->get_diameter(),
				'comprimento'        => $this->get_length(),
				'psObjeto'           => $this->get_weight(),
				'tpObjeto'           => $this->format,
				'servicosAdicionais' => number_format( $this->declared_value ) > 0 ? '030' : '',
				'vlDeclarado'        => round( number_format( $this->declared_value, 2, '.', '' ) ),
			),
			$this->id,
			$this->package
		);

		$url = add_query_arg( $args, $this->get_webservice_url() );

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, 'Requesting Correios WebServices: ' . $url );
		}

		// Gets the WebServices response.
		$response = wp_safe_remote_get(
			$url,
			array(
				'timeout' => 30,
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( sprintf( '%s:%s', $this->get_login(), $this->get_password() ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'WP_Error: ' . $response->get_error_message() );
			}
		} elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			try {
				$result = json_decode( $response['body'] );
				$this->log->add( $this->id, 'Success: ' . wc_print_r( $result, true ) );
			} catch ( Exception $e ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices invalid return: ' . $e->getMessage() );
				}
			}

			if ( isset( $result->precoProduto ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( 'yes' === $this->debug ) {
					$this->log->add( $this->id, 'Correios WebServices response: ' . wc_print_r( $result, true ) );
				}

				$shipping = $result->precoProduto; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		} elseif ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'Error accessing the Correios WebServices: ' . wc_print_r( $response, true ) );
		}

		return $shipping;
	}
}
