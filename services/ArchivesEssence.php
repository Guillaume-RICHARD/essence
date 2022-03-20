<?php

namespace Services;

/**
 * Class Archives
 * @package services
 */
class ArchivesEssence
{
    /**
     * ArchivesEssence constructor.
     */
    public function __construct()
    {
        $this->url  = 'https://donnees.roulez-eco.fr/opendata/instantane';
        $this->path = __DIR__ ."/../tmp/";
        $this->file = "PrixCarburants_instantane.zip";
        $this->xml  = "PrixCarburants_instantane.xml";

        if (file_exists($this->path.$this->xml)){
            unlink($this->path.$this->xml);
        }
    }

    /**
     * @return bool
     */
    public function toDownload()
    {
        if (!file_exists($this->path.$this->xml)) {
            $ch = curl_init();
            $fp = fopen($this->path.$this->file, "w");

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_VERBOSE, '1');
            curl_setopt($ch, CURLOPT_CERTINFO, '1');
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $data = curl_exec($ch);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);
            fclose($fp);

            chmod($this->path.$this->file, 0777);  // notation octale : valeur du mode correcte

            if ($curl_errno > 0) {
                $subject = "Problème pour le téléchargement";
                $message = "Erreur $curl_errno : $curl_error";
                // mail("g.jf.richard@gmail.com", $subject,$message);
                return false;
            } else {
                // echo "Data received: $data\n";
                return true;
            }
        }
    }

    /**
     * Décompresser le fichier zip $file dans le répertoire de destination $path.
     *
     * @return string
     */
    public function toUnzip()
    {
        $zip = new \ZipArchive();
        $pathToFile = $this->path.$this->file;

        // Ouvrir l'archive
        if ($zip->open($pathToFile) !== true) {
            $subject = "Problème d'archive";
            $message = "Impossible d'ouvrir l'archive";
            return false;
        } else {
            $zip->extractTo($this->path); // Extraire le contenu dans le dossier de destination
            $zip->close(); // Fermer l'archive

            return $this;
        }
    }

    public function toDelete()
    {
        unlink($this->path.$this->file);
    }
}