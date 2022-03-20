<?php

namespace Services;

/**
 * Class Fix :
 * @package Services
 */
class Fix
{

    /**
     * Fix constructor.
     */
    public function __construct() {}

    /**
     * limite le nombre de caractères d'un Tweet
     * @param string $message
     * @param string $needle
     * @param int $length
     * @return string $result
     */
    public function numberOfCaracter(string $message, $needle = " ", int $length = 280): string {
        if(strlen($message) <= $length) {
            return $message."\n";
        } else {
            $chaine = substr($message,0, $length);
            $result = substr($chaine, 0, strrpos( $chaine, $needle)). " etc... ";

            return $result."\n";
        }
    }

    /**
     * @param array $price
     * @return string
     */
    public function min_price(array $price):string {
        $minimum_price = min($price);
        return str_replace(".", ",",$minimum_price);
    }

    /**
     * @param array $price
     * @return string
     */
    public function max_price(array $price):string {
        $price = str_replace(999, 0, $price);

        $minimum_price = max($price);
        return str_replace(".", ",",$minimum_price);
    }

    public function date_fr($date) {
        $month  = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $mois   = array("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");

        return str_replace($month, $mois, $date);
    }
}