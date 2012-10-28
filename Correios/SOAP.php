<?php

/**
 * Correios WebServices Shipping API.
 *
 * PHP Version 5
 *
 * @category Correios
 * @package  Correios/SOAP
 * @author   Infranology <claudio.sanches@infranology.com>
 * @license  http://opensource.org/licenses/mit-license.php MIT License
 * @version  GIT: v1.0
 * @link     https://github.com/Infranology/correios_soap
 */

/**
 * Method of postal Correios quotes.
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
 * @package  Correios/SOAP
 * @author   Infranology <claudio.sanches@infranology.com>
 * @license  http://opensource.org/licenses/mit-license.php MIT License
 * @version  Release: v1.0
 * @link     https://github.com/Infranology/correios_soap
 */
class Correios_SOAP
{
    protected $service;
    protected $zipOrigin;
    protected $zipDestination;
    protected $height;
    protected $width;
    protected $diameter;
    protected $length;
    protected $weight;
    protected $login;
    protected $password;
    protected $declared;
    protected $returnType;
    protected $format;
    protected $ownHand;
    protected $receiptNotice;
    protected $webservice = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?WSDL';

    /**
     * __construct function.
     *
     * @param array  $service        Correios services.
     * @param mixed  $zipOrigin      Zip Code of the origin.
     * @param mixed  $zipDestination Zip Code of the destination.
     * @param float  $height         Height total.
     * @param float  $width          Width total.
     * @param float  $diameter       Diamenter total.
     * @param float  $length         Length total.
     * @param float  $weight         Weight total.
     * @param string $login          Correios user.
     * @param string $password       Correios user password.
     * @param float  $declared       Declared value.
     * @param string $returnType     Type of data return.
     * @param int    $format         Format.
     * @param string $ownHand        Own hand option.
     * @param string $receiptNotice  Notice.
     *
     * @return void
     */
    function __construct(
        $service,
        $zipOrigin,
        $zipDestination,
        $height,
        $width,
        $diameter,
        $length,
        $weight,
        $login            = null,
        $password         = null,
        $declared         = '0',
        $returnType       = 'object',
        $format           = '1',
        $ownHand          = 'N',
        $receiptNotice    = 'N'
    ) {

        $this->service        = (array) $service;
        $this->zipOrigin      = $zipOrigin;
        $this->zipDestination = $zipDestination;
        $this->height         = $height;
        $this->width          = $width;
        $this->diameter       = $diameter;
        $this->length         = $length;
        $this->weight         = $weight;
        $this->login          = $login;
        $this->password       = $password;
        $this->declared       = $declared;
        $this->returnType     = $returnType;
        $this->format         = $format;
        $this->ownHand        = $ownHand;
        $this->receiptNotice  = $receiptNotice;
    }

    /**
     * Fix Zip Code format.
     *
     * @param mixed $zip Zip Code.
     *
     * @return int
     */
    protected function fixZipCode($zip)
    {
        $fixed = preg_replace('([^0-9])', '', $zip);

        return $fixed;
    }

    /**
     * Start SOAP client.
     *
     * @param string $url Correios URL.
     *
     * @return mixed
     */
    protected function soapClient($url)
    {
        $soap = @new SoapClient(
            $url, array(
                'trace'              => true,
                'exceptions'         => true,
                'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                'connection_timeout' => 1000,
            )
        );

        return $soap;
    }

    /**
     * Set the response.
     *
     * @param string $type   Type of response.
     * @param object $object Object to convert.
     *
     * @return mixed
     */
    protected function response($type, $object)
    {
        $return = '';

        switch ($type) {
        case 'array':
            $return = (array) $object;
            break;
        case 'json':
            $return = json_encode($object);
            break;

        default:
            $return = $object;
            break;
        }

        return $return;
    }

    /**
     * Build an object with the arguments.
     *
     * @return object
     */
    protected function constructArguments()
    {
        // Creates an object.
        $args                      = new stdClass();
        // Set Correios user login.
        $args->nCdEmpresa          = $this->login;
        // Set Correios user password.
        $args->sDsSenha            = $this->password;
        // Set destination Zip Code.
        $args->sCepDestino         = $this->fixZipCode($this->zipDestination);
        // Set origin Zip Code.
        $args->sCepOrigem          = $this->fixZipCode($this->zipOrigin);

        // Cubing data.
        $args->nVlAltura           = $this->height;
        $args->nVlLargura          = $this->width;
        $args->nVlDiametro         = $this->diameter;
        $args->nVlComprimento      = $this->length;
        $args->nVlPeso             = $this->weight;

        // Others fields (mandatory even if empty).
        $args->nCdFormato          = $this->format;
        $args->sCdMaoPropria       = $this->ownHand;
        $args->nVlValorDeclarado   = $this->declared;
        $args->sCdAvisoRecebimento = $this->receiptNotice;

        return $args;
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

            // Start SOAP client.
            $soap = $this->soapClient($this->webservice);

            // Get the value.
            $calc   = $soap->CalcPrecoPrazo($args);
            $shipping->$value = $calc->CalcPrecoPrazoResult->Servicos->cServico;
        }

        return $this->response($this->returnType, $shipping);
    }

}
