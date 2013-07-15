<?php
/**
 * WC_Correios_Cubage class.
 */
class WC_Correios_Cubage {

    /**
     * __construct function.
     *
     * @param array $height Height total.
     * @param array $width  Width total.
     * @param array $length Length total.
     *
     * @return array
     */
    function __construct( $height, $width, $length ) {
        $this->height = (array) $height;
        $this->width  = (array) $width;
        $this->length = (array) $length;
    }

    /**
     * Calculates the cubage of all products.
     *
     * @return array
     */
    protected function cubage_total() {
        // Sets the cubage of all products.
        $all   = array();
        $total = '';

        for ( $i = 0; $i < count( $this->height ); $i++ )
            $all[ $i ] = $this->height[ $i ] * $this->width[ $i ] * $this->length[ $i ];

        foreach ( $all as $value )
            $total += $value;

        return $total;
    }

    /**
     * Finds the greatest measure.
     *
     * @return array
     */
    protected function find_max_length() {
        // Defines the greatest.
        $find = array(
            'height' => max( $this->height ),
            'width'  => max( $this->width ),
            'length' => max( $this->length ),
        );

        return $find;
    }

    /**
     * Calculates the square root of the scaling of all products.
     *
     * @return float
     */
    protected function root() {
        $cubageTotal = $this->cubage_total();
        $find        = $this->find_max_length();
        $root        = 0;

        if ( 0 != $cubageTotal ) {
            // Dividing the value of scaling of all products.
            // With the measured value of greater.
            $division = $cubageTotal / max( $find );
            // Total square root.
            $root = round( sqrt( $division ), 1 );
        }

        return $root;
    }

    /**
     * Sets the final cubage.
     *
     * @return array
     */
    public function cubage() {

        $cubage   = array();
        $root     = $this->root();
        $find     = $this->find_max_length();
        $greatest = array_search( max( $find ), $find );

        switch ( $greatest ) {
            case 'height':
                $cubage = array(
                    'height' => max( $this->height ),
                    'width' => $root,
                    'length' => $root,
                );
                break;

            case 'width':
                $cubage = array(
                    'height' => $root,
                    'width' => max( $this->width ),
                    'length' => $root,
                );
                break;

            case 'length':
                $cubage = array(
                    'height' => $root,
                    'width' => $root,
                    'length' => max( $this->length ),
                );
                break;

            default:
                break;
        }

        return $cubage;
    }
}
