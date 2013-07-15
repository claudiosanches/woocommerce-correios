<?php

/**
 * Cubage class file.
 *
 * PHP Version 5
 *
 * @category Correios
 * @package  Correios/Cubage
 * @author   Infranology <claudio.sanches@infranology.com>
 * @license  http://opensource.org/licenses/mit-license.php MIT License
 * @version  GIT: v1.0
 * @link     https://github.com/Infranology/correios_soap
 */

/**
 * Cubage class.
 *
 * @category Correios
 * @package  Correios/Cubage
 * @author   Infranology <claudio.sanches@infranology.com>
 * @license  http://opensource.org/licenses/mit-license.php MIT License
 * @version  Release: v1.0
 * @link     https://github.com/Infranology/correios_soap
 */
class Correios_Cubage
{
    protected $height;
    protected $width;
    protected $length;

    /**
     * __construct function.
     *
     * @param array $height Height total.
     * @param array $width  Width total.
     * @param array $length Length total.
     *
     * @return array
     */
    function __construct($height,$width,$length)
    {
        $this->height = (array) $height;
        $this->width  = (array) $width;
        $this->length = (array) $length;
    }

    /**
     * Calculates the cubage of all products.
     *
     * @return array
     */
    protected function cubageTotalProducts()
    {
        // Sets the cubage of all products.
        $all   = array();
        $total = '';

        for ($i = 0; $i < count($this->height); $i++) {
            $all[$i] = $this->height[$i] * $this->width[$i] * $this->length[$i];
        }

        foreach ($all as $value) {
            $total += $value;
        }

        return $total;
    }

    /**
     * Finds the greatest measure.
     *
     * @return array
     */
    protected function findMaxLengthProduct()
    {
        // Defines the greatest.
        $find = array(
            'height' => max($this->height),
            'width'  => max($this->width),
            'length' => max($this->length),
        );

        return $find;
    }

    /**
     * Calculates the square root of the scaling of all products.
     *
     * @return float
     */
    protected function rootProduct()
    {
        $cubageTotal = $this->cubageTotalProducts();
        $find        = $this->findMaxLengthProduct();
        $root        = 0;

        if ($cubageTotal != 0) {
            // Dividing the value of scaling of all products.
            // With the measured value of greater.
            $division = $cubageTotal / max($find);
            // Total square root.
            $root = round(sqrt($division), 1);
        }

        return $root;
    }

    /**
     * Sets the final cubage.
     *
     * @return array
     */
    public function cubage()
    {

        $cubage   = array();
        $root     = $this->rootProduct();
        $find     = $this->findMaxLengthProduct();
        $greatest = array_search(max($find), $find);

        switch ($greatest) {
        case 'height':
            $cubage = array(
                'height' => max($this->height),
                'width' => $root,
                'length' => $root,
            );
            break;

        case 'width':
            $cubage = array(
                'height' => $root,
                'width' => max($this->width),
                'length' => $root,
            );
            break;

        case 'length':
            $cubage = array(
                'height' => $root,
                'width' => $root,
                'length' => max($this->length),
            );
            break;

        default:
            break;
        }

        return $cubage;
    }

}
