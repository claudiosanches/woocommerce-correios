<?php
/**
 * Correios WebServices Shipping API with SimpleXML.
 *
 * PHP Version 5
 *
 * @category Correios
 * @package  Correios/SimpleXML
 * @author   Infranology <claudio.sanches@infranology.com>
 * @license  http://opensource.org/licenses/mit-license.php MIT License
 * @version  GIT: v1.1
 * @link     https://github.com/Infranology/correios_soap
 */

/**
 * Method of postal Correios quotes with SimpleXML.
 *
 * List of services:
 * Code:        Service:
 * 40010        SEDEX sem contrato.
 * 40045        SEDEX a Cobrar, sem contrato.
 * 40126        SEDEX a Cobrar, com contrato.
 * 40215        SEDEX 10, sem contrato.
 * 40290        SEDEX Hoje, sem contrato.
 * 40096        SEDEX com contrato.
 * 40436        SEDEX com contrato.
 * 40444        SEDEX com contrato.
 * 40568        SEDEX com contrato.
 * 40606        SEDEX com contrato.
 * 41106        PAC sem contrato.
 * 41068        PAC com contrato.
 * 81019        e-SEDEX, com contrato.
 * 81027        e-SEDEX Prioritário, com contrato.
 * 81035        e-SEDEX Express, com contrato.
 * 81868        (Grupo 1) e-SEDEX, com contrato.
 * 81833        (Grupo 2) e-SEDEX, com contrato.
 * 81850        (Grupo 3) e-SEDEX, com contrato.
 *
 * Ref: http://www.correios.com.br/webservices/
 *
 * @category Correios
 * @package  Correios/SimpleXML
 * @author   Infranology <claudio.sanches@infranology.com>
 * @license  http://opensource.org/licenses/mit-license.php MIT License
 * @version  Release: v1.0
 * @link     https://github.com/Infranology/correios_soap
 */
class Correios_SimpleXML extends Correios_SOAP {

    /**
     * Webservices via XML.
     * @var string
     */
    protected $webservice = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';

    /**
     * Start SimpleXML.
     *
     * @param object $args Correios arguments.
     *
     * @return object.
     */
    protected function simpleXML($args)
    {

        // Built the url to request.
        $request = $this->webservice . '?' . http_build_query($args) . '&StrRetorno=xml';

        // Get contents.
        $xml = simplexml_load_file($request);

        return $xml;
    }

    /**
     * Calculate shipping.
     *
     * @return mixed
     */
    public function calculateShipping()
    {

        // Create new object to return.
        $shipping = new stdClass();

        // Get arguments
        $args = $this->constructArguments();

        // Processes data.
        foreach ($this->service as $key => $value) {
            // Set the service
            $args->nCdServico = $value;

            // Start simplexml_load_file.
            $calc = $this->simpleXML($args);

            // Get the value.
            $shipping->$value = $calc->cServico;
        }

        return $this->response($this->returnType, $shipping);
    }

}