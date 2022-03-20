<?php

namespace Services;

/**
 * Class XmlReader
 * @package Services
 */
class OpmlReader
{
    private $xml;

    /**
     * OpmlReader constructor.
     * @param string $folder
     */
    public function __construct($folder = __DIR__ ."/../tmp/")
    {
        $this->folder = $folder;
    }

    /**
     * @param string $file
     * @return \SimpleXMLElement
     */
    public function read($file = '')
    {
        $fileToRead = $this->folder . $file;

        $xml = (file_exists($this->folder . $file))
            ? simplexml_load_file($this->folder . $file) : "Le fichier ".$this->file." n'existe pas";

        return $xml->body->outline;
    }

    /**
     * @param $infos
     * @param $options
     * @return array
     */
    public function podcast($infos, $options){
        $data = [];

        if (empty($infos))
            return $data;

        foreach ($infos as $info) {
            try {
                $rss = simplexml_load_file($info['xmlUrl'], 'SimpleXMLElement', LIBXML_NOCDATA);
                // var_dump($rss); die;

                $data[] = [
                    'title'         => $rss->channel->title,
                    'description'   => $rss->channel->description,
                    'category'      => $rss->channel->category
                ];
                // var_dump($data); die;

            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }

        return $data;
    }
}