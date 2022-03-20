<?php

namespace Services;

/**
 * Class XmlReader
 * @package Services
 */
class XmlToReader
{

    /**
     * XmlToReader constructor.
     */
    public function __construct()
    {
        $this->file     = "PrixCarburants_instantane.xml";
        $this->folder   = __DIR__ ."/../tmp/";
    }

    /**
     * @return \SimpleXMLElement|string
     */
    public function read()
    {
        $fileToRead = $this->folder . $this->file;

        $xml = (file_exists($this->folder . $this->file))
            ? simplexml_load_file($this->folder . $this->file) : "Le fichier ".$this->file." n'existe pas";

        return $xml;
    }

    /**
     * Récupère les infos des différents points de ventes en HG
     * @param $liste_pdv
     * @return array
     */
    public function infos($liste_pdv)
    {
        foreach ($liste_pdv as $pdv) {
            $tmp = [];
            preg_match('/^31[0-9]{3}$/', $pdv[@cp], $matches);

            if ($matches) {
                $tmp['ville']   = (string) $pdv->ville;
                $tmp['adresse'] = (string) $pdv->adresse;
                $tmp['latitude']  = (int) $pdv[@latitude]/100000;
                $tmp['longitude']  = (int) $pdv[@longitude]/100000;

                foreach ($pdv->prix as $essence) {
                    $nom = (string) $essence[@nom];
                    $tmp[$nom] = (float)($essence[@valeur]);
                }

                $pdv_infos[] = $tmp;
            }
        }

        $count_pdv = count($pdv_infos); // On compte le nombre de point de ventes d'essence

        return $infos = [
            'count' => $count_pdv,
            'infos' => $pdv_infos
        ];
    }
}