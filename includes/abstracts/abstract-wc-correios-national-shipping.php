<?php
/**
 * Abstract Correios national shipping method.
 *
 * @package WooCommerce_Correios/Abstracts
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Correios national shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class WC_Correios_National_Shipping extends WC_Correios_Shipping {
	/**
	 * National Registry cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const NATIONAL_REGISTRY_COST = 4.30;

	/**
	 * Reasonable Registry cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const REASONABLE_REGISTRY_COST = 2.15;

	/**
	 * Receipt Notice cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const RECEIPT_NOTICE_COST = 4.30;

	/**
	 * Own Hands cost.
	 * Cost based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-adicionais-nacionais
	 *
	 */
	const OWN_HANDS_COST = 5.50;

	/**
	 * Weight limit for reasonable registry.
	 * Value based in 25/08/2016 from:
	 * http://www.correios.com.br/para-voce/consultas-e-solicitacoes/precos-e-prazos/servicos-nacionais_pasta/impresso-normal
	 *
	 */
	const REASONABLE_REGISTRY_WEIGHT_LIMIT = 500.000;
}
