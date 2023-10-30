<?php
/**
 * Correios Web Services API.
 *
 * @package WooCommerce_Correios/Classes/Webservice
 * @since   4.0.0
 * @version 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Web Services class, integrates with the new Correios API.
 */
class WC_Correios_Cws_Calculate extends WC_Correios_Webservice {

	/**
	 * CWS Product code.
	 *
	 * @var string
	 */
	protected $product_code = '';

	/**
	 * Declared value code.
	 *
	 * @var string
	 */
	protected $declared_value_code = '';

	/**
	 * Destination country.
	 *
	 * @var string
	 */
	protected $destination_country = 'BR';

	/**
	 * Destination city code.
	 *
	 * @var string
	 */
	protected $destination_city_code = '';

	/**
	 * Country first city code.
	 *
	 * @var array
	 */
	protected $country_first_city_code = array(
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
	 * Check if is available.
	 *
	 * @return bool
	 */
	protected function is_available() {
		$origin_postcode = $this->get_origin_postcode();

		return ! empty( $this->product_code ) && ! empty( $this->destination_postcode ) && ! empty( $origin_postcode );
	}

	/**
	 * Set the CWS product code.
	 *
	 * @param string|array $code Product code.
	 */
	public function set_product_code( $code = '' ) {
		$this->product_code = $code;
	}

	/**
	 * Set declared value code.
	 *
	 * @param string $code Code.
	 */
	public function set_declared_value_code( $code ) {
		$this->declared_value_code = $code;
	}

	/**
	 * Set destination city code.
	 *
	 * @param string $code Code.
	 */
	public function set_destination_city_code( $code ) {
		$this->destination_city_code = $code;
	}

	/**
	 * Set destination country.
	 *
	 * @param string $country Country.
	 */
	public function set_destination_country( $country = 'BR' ) {
		$this->destination_country = $country;

		if ( isset( $this->country_first_city_code[ $country ] ) ) {
			$this->set_destination_city_code( $this->country_first_city_code[ $country ] );
		}
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
	 * Get destination city code.
	 *
	 * @return string
	 */
	public function get_destination_city_code() {
		return $this->destination_city_code;
	}

	/**
	 * Get weight.
	 *
	 * @return float
	 */
	public function get_weight() {
		return wc_get_weight( $this->weight + $this->extra_weight, 'g' );
	}

	/**
	 * Get receipt notice.
	 *
	 * @return bool
	 */
	public function get_receipt_notice() {
		return 'S' === $this->receipt_notice;
	}

	/**
	 * Get own hands.
	 *
	 * @return bool
	 */
	public function get_own_hands() {
		return 'S' === $this->own_hands;
	}

	/**
	 * Get declared value.
	 *
	 * @return bool
	 */
	public function get_declared_value() {
		return $this->declared_value;
	}

	/**
	 * Get product code.
	 *
	 * @return string
	 */
	public function get_product_code() {
		return $this->product_code;
	}

	/**
	 * Get WooCommerce shipping package.
	 *
	 * @return array
	 */
	public function get_package() {
		return $this->package;
	}

	/**
	 * Get height.
	 *
	 * @return float
	 */
	public function get_height() {
		return $this->minimum_height <= $this->height ? $this->height : $this->minimum_height;
	}

	/**
	 * Get width.
	 *
	 * @return float
	 */
	public function get_width() {
		return $this->minimum_width <= $this->width ? $this->width : $this->minimum_width;
	}

	/**
	 * Get diameter.
	 *
	 * @return float
	 */
	public function get_diameter() {
		return $this->diameter;
	}

	/**
	 * Get length.
	 *
	 * @return float
	 */
	public function get_length() {
		return $this->minimum_length <= $this->length ? $this->length : $this->minimum_length;
	}

	/**
	 * Get declared value code.
	 *
	 * @return string
	 */
	public function get_declared_value_code() {
		return $this->declared_value_code;
	}

	/**
	 * Get shipping prices.
	 *
	 * @param string $api_type API type.
	 * @return array
	 */
	public function get_shipping( $api_type = 'nacional' ) {
		// Checks if product code and postcode are empty.
		if ( ! $this->is_available() ) {
			return array();
		}

		$args = array(
			'cepDestino'         => wc_correios_sanitize_postcode( $this->destination_postcode ),
			'cepOrigem'          => wc_correios_sanitize_postcode( $this->get_origin_postcode() ),
			'psObjeto'           => $this->get_weight(),
			'tpObjeto'           => '2', // Defaul to package.
			'comprimento'        => $this->get_length(),
			'largura'            => $this->get_width(),
			'altura'             => $this->get_height(),
			'servicosAdicionais' => array(),
		);

		// Set receipt notice, optional, and doesn't works with Carta Registrada.
		if ( $this->get_receipt_notice() ) {
			$args['servicosAdicionais'][] = '001';
		}

		// Set own hands, optional, and doesn't work with Correios Mini Envios.
		if ( $this->get_own_hands() ) {
			$args['servicosAdicionais'][] = '002';
		}

		// Set declared value.
		if ( $this->get_declared_value() ) {
			$args['servicosAdicionais'][] = $this->get_declared_value_code();
			$args['vlDeclarado']          = $this->get_declared_value();
		}

		// Set destination country if API is international.
		if ( 'internacional' === $api_type ) {
			$args['sgPaisDestino'] = $this->get_destination_country();
		}

		$connect = new WC_Correios_Cws_Connect( $this->id, $this->instance_id );
		return $connect->get_shipping_cost( $args, $this->get_product_code(), $this->get_package(), $api_type );
	}

	/**
	 * Get shipping time.
	 *
	 * @param string $api_type API type.
	 * @return array
	 */
	public function get_time( $api_type = 'nacional' ) {
		// Checks if product code and postcode are empty.
		if ( ! $this->is_available() ) {
			return array();
		}

		$args = array(
			'cepDestino' => wc_correios_sanitize_postcode( $this->destination_postcode ),
			'cepOrigem'  => wc_correios_sanitize_postcode( $this->get_origin_postcode() ),
		);

		// Set destination country if API is international.
		if ( 'internacional' === $api_type ) {
			$args['sgPaisDestino']   = $this->get_destination_country();
			$args['coCidadeDestino'] = $this->get_destination_city_code();
		}

		$connect = new WC_Correios_Cws_Connect( $this->id, $this->instance_id );
		return $connect->get_shipping_time( $args, $this->get_product_code(), $this->get_package(), $api_type );
	}
}
