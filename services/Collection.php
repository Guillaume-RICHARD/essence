<?php

namespace Services;

/**
 * Class Collection
 * @package system\libraries
 */
class Collection {

    /**
     * Collection constructor.
     */
    public function __construct() {}

    /**
     * Affiche la valeur de la clé $key
     *
     * @param $key
     * @return mixed
     */
    public function get($key) {
        $index = explode('.', $key);
        return $this->getValue($index, $this->items);
    }

    /**
     * @param string $gas
     * @param array $price_info
     * @param array $pdv_infos
     * @return string
     */
    public function unique($gas = 'Gazole', array $price_info, array $pdv_infos):string {
        $villes = $this->nbVilles($gas, $price_info, $pdv_infos);

        if (count($villes) > 5) {
            $villes = $this->minify(", ", $villes);
        } elseif (count($villes) > 1) {
            $villes = $this->implode(", ", $villes);
        } else {
            $villes = $villes[0];
        }

        return $villes;
    }

    /**
     * @param string $gas
     * @param array $price_info
     * @param array $pdv_infos
     * @return array
     */
    public function nbVilles($gas = 'Gazole', array $price_info, array $pdv_infos):array {
        $villes = [];

        foreach ($pdv_infos["infos"] as &$pdv_info) {
            $pdv_info['ville'] = ucfirst(strtolower($pdv_info['ville']));

            if (array_key_exists($gas, $pdv_info) && $pdv_info[$gas] === min($price_info)) {
                if (strpos($pdv_info['ville'], "lauragias")) {
                    $villes[] = str_replace("lauragias", "lauragais", $pdv_info['ville']);
                } else if ($pdv_info['ville'] === "Portet sur garonne") {
                    $villes[] = str_replace("Portet sur garonne", "Portet-sur-garonne", $pdv_info['ville']);
                } else {
                    $villes[] = $pdv_info['ville'];
                }
            }
            $villes = array_unique($villes);
            // sort($villes);
        }

        return $villes;
    }

    /**
     * @param string $glue
     * @param array $pieces
     * @return string
     */
    function minify(string $glue, array $pieces):string {
        $str = "";
        $max = count($pieces);

        for ($i=5; $i<=$max; $i++) { unset($pieces[$i]); }

        if ($pieces)
            $str = implode($glue, $pieces) . ", etc...";

        return $str;
    }

    /**
     * Join a string with a natural language conjunction at the end.
     * @param string $glue
     * @param array $pieces
     * @param string $conjonction
     * @return string $str
     */
    function implode(string $glue, array $pieces, string $conjonction = 'et'):string {
        $str = array_pop($pieces);
        if ($pieces) {
            $str = implode($glue, $pieces) . ' ' . $conjonction . ' ' . $str;
        }
        return $str;
    }

    /**
     * Récupère les valeurs '$value' d'une suite de clé '$indexes'
     * @param array $indexes
     * @param $value
     * @return null|Collection
     */
    public function getValue(array $indexes, $value) {
        $key = array_shift($indexes);
        if(empty($indexes)) {
            if(!array_key_exists($key, $value)){
                return null;
            }
            if(is_array($value[$key])) {
                return new Collection($value[$key]);
            } else {
                return $value[$key];
            }
        } else {
            return $this->getValue($indexes, $value[$key]);
        }
    }

    /**
     * injecte une valeur '$value' à une clé '$key' spécifique
     * @param $key
     * @param $value
     */
    public function set($key, $value) {
        $this->items[$key] = $value;
    }

    /**
     * Nous retourne si la clé existe (ou non)
     * @param $key
     * @return bool
     */
    public function has($key) {
        return array_key_exists($key, $this->items);
    }

    /**
     * Récupère les données de 2 colonnes d'un tableau
     * @param $key - données de la 1ere colonne
     * @param $value - données de la 2eme colonne
     * @return Collection
     */
    public function lists($key, $value) {
        $results = [];
        foreach($this->items as $item){
            $results[$item[$key]] = $item[$value];
        }
        return new Collection($results);
    }

    /**
     * Extrait les valeurs d'une clé $key spécifique
     * @param $key
     * @return Collection
     */
    public function extract($key) {
        $results = [];
        foreach($this->items as $item){
            $results[] = $item[$key];
        }
        return new Collection($results);
    }

    /**
     * Liste séparé par un caractère spécifique
     * @param $glue - représente le caractère
     * @return string
     */
    public function join($glue) {
        return implode($glue, $this->items);
    }

    /**
     * @param bool $key
     * @return mixed
     */
    public function max($key = false) {
        if($key) {
            return $this->extract($key)->max();
        } else {
            return max($this->items);
        }
    }

    /**
     * @param $column
     * @param int $direction
     * @return bool
     */
    public function sortByColumn($column, $direction = SORT_ASC) {
        $reference_array = [];

        foreach($this->items as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        return array_multisort($reference_array, $direction, $this->items);
    }
}