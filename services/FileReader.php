<?php

namespace Services;

/**
 * Class FileReader
 * @package Services
 */
class FileReader
{
    /**
     * FileReader constructor.
     */
    public function __construct()
    {
        $this->path   = __DIR__ ."/../tmp/animals/";
    }

    /**
     * @param string $url
     * @return int
     */
    public function isError(string $url):int {
        $headers = get_headers($url);
        return (int)substr($headers[0], 9, 3);
    }

    /**
     * @param string $url
     * @return string $path|$message
     */
    public function toDownload(string $url):string {
        $file = basename($url); // renvoie le nom du fichier.
        $path = $this->path.$file;

        if(file_put_contents( $path,file_get_contents($url))) {
            return $path;
        } else {
            return $message = "Problème de téléchargement";
        }
    }
}